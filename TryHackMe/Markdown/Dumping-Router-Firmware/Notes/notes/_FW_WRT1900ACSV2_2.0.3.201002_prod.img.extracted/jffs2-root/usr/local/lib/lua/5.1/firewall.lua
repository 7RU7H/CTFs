--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

-- firewall.lua - library to configure firewall state.

local hdk = require('libhdklua')
local device = require('device')
local util = require('util')
local platform = require('platform')

local _M = {} -- create the module


local function parseIPProtocol(protocol)
    if protocol == 'tcp' then
        return 'TCP'
    elseif protocol == 'udp' then
        return 'UDP'
    elseif protocol == 'both' then
        return 'Both'
    else
        return nil
    end
end

local function serializeIPProtocol(protocol)
    if protocol == 'TCP' then
        return 'tcp'
    elseif protocol == 'UDP' then
        return 'udp'
    elseif protocol == 'Both' then
        return 'both'
    else
        return nil
    end
end

local function parsePortRange(range)
    return range:match('^(%d+)%s+(%d+)$')
end

local function serializePortRange(firstPort, lastPort)
    return string.format('%d %d', firstPort, lastPort)
end

--
-- Determine if a port range is in a given set of ranges.
--
-- Adds the range to the set if it did not exist
--
-- Returns two values: a boolean indicating if the range was added to the set,
-- and the ip associated with range
--
local function addRangeToList(ranges, first, last, ip)
    local function isBetween(x, a, b)
        return x >= a and x <= b
    end

    for i, range in ipairs(ranges) do
        if (isBetween(first, range.first, range.last) or
            isBetween(last, range.first, range.last) or
            isBetween(range.first, first, last)) then
               return false, range.ip
        end
    end

    table.insert(ranges, { first = first, last = last, ip = ip })
    return true, ip
end

--
-- Get the single-port forwarding rules of the local device.
--
-- input = CONTEXT
--
-- output = ARRAY_OF({
--     isEnabled = BOOLEAN,
--     externalPort = NUMBER,
--     protocol = STRING,
--     internalServerIPAddress = IPADDRESS,
--     internalPort = NUMBER,
--     description = STRING
-- })
--
function _M.getSinglePortForwardingRules(sc)
    sc:readlock()
    local lanPrefixLength = util.subnetMaskToNetworkPrefixLength(hdk.ipaddress(sc:get_lan_subnet_mask()))
    local networkID = hdk.ipaddress(sc:get_lan_ipaddress()):networkid(lanPrefixLength)
    local rules = {}
    for i = 1, sc:get_port_forward_count() do
            local externalPort = sc:get_port_forward_external_port(i)
            local protocol = parseIPProtocol(sc:get_port_forward_protocol(i))
            local internalServerIPAddress = networkID:bitwiseor(util.parseAbbreviatedDotDecimal(sc:get_port_forward_internal_ip_address(i)))
            local internalPort = sc:get_port_forward_internal_port(i)
            if externalPort and protocol and internalServerIPAddress and internalPort then
                table.insert(rules, {
                    isEnabled = sc:get_port_forward_enabled(i),
                    externalPort = externalPort,
                    protocol = protocol,
                    internalServerIPAddress = internalServerIPAddress,
                    internalPort = internalPort,
                    description = sc:get_port_forward_description(i)
                })
            end
    end
    return rules
end

--
-- Set the single-port forwarding rules of the local device.
--
-- input = CONTEXT, ARRAY_OF({
--     isEnabled = BOOLEAN,
--     externalPort = NUMBER,
--     protocol = STRING,
--     internalServerLastIPAddress = IPADDRESS,
--     internalPort = NUMBER,
--     description = STRING
-- })
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidExternalPort',
--     'ErrorInvalidInternalServerIPAddress',
--     'ErrorInvalidInternalPort',
--     'ErrorDescriptionTooLong',
--     'ErrorRulesOverlap',
--     'ErrorTooManyRules'
-- )
--
function _M.setSinglePortForwardingRules(sc, rules)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if #rules > platform.MAX_SINGLEPORT_RULES then
        return 'ErrorTooManyRules'
    end

    local tcpRanges, udpRanges = {}, {}
    for i, rule in ipairs(_M.getPortRangeForwardingRules(sc)) do
        if rule.protocol == 'TCP' or rule.protocol == 'Both' then
            addRangeToList(tcpRanges, rule.firstExternalPort, rule.lastExternalPort)
        end
        if rule.protocol == 'UDP' or rule.protocol == 'Both' then
            addRangeToList(udpRanges, rule.firstExternalPort, rule.lastExternalPort)
        end
    end

    local lanPrefixLength = util.subnetMaskToNetworkPrefixLength(hdk.ipaddress(sc:get_lan_subnet_mask()))
    local routerHostAddress = hdk.ipaddress(sc:get_lan_ipaddress())

    -- update to new rule count
    sc:set_port_forward_count(#rules)

    for i, rule in ipairs(rules) do
        if not util.isValidPort(rule.externalPort) then
            return 'ErrorInvalidExternalPort'
        end

        if not util.inSameSubnet(rule.internalServerIPAddress, routerHostAddress, lanPrefixLength) or
            util.isReservedSubnetAddress(rule.internalServerIPAddress, lanPrefixLength) or
            (rule.internalServerIPAddress == routerHostAddress) then
            return 'ErrorInvalidInternalServerIPAddress'
        end

        if not util.isValidPort(rule.internalPort) then
            return 'ErrorInvalidInternalPort'
        end

        if #rule.description > platform.MAX_PORT_RULE_DESCRIPTION_LENGTH then
            return 'ErrorDescriptionTooLong'
        end

        if rule.protocol == 'TCP' or rule.protocol == 'Both' then
            if not addRangeToList(tcpRanges, rule.externalPort, rule.externalPort) then
                return 'ErrorRulesOverlap'
            end
        end
        if rule.protocol == 'UDP' or rule.protocol == 'Both' then
            if not addRangeToList(udpRanges, rule.externalPort, rule.externalPort) then
                return 'ErrorRulesOverlap'
            end
        end

        sc:set_port_forward_enabled(i, rule.isEnabled)
        sc:set_port_forward_external_port(i, rule.externalPort)
        sc:set_port_forward_protocol(i, serializeIPProtocol(rule.protocol))
        -- Note: the abbreviate dot-decimal notation is used here for maximum backwards compatibility should the user decide to downgrade
        sc:set_port_forward_internal_ip_address(i , util.serializeAbbreviatedDotDecimal(rule.internalServerIPAddress:hostid(lanPrefixLength)))
        sc:set_port_forward_internal_port(i, rule.internalPort)
        sc:set_port_forward_description(i, rule.description)
    end
end

--
-- Get the port range forwarding rules of the local device.
--
-- input = CONTEXT
--
-- output = ARRAY_OF({
--     isEnabled = BOOLEAN,
--     firstExternalPort = NUMBER,
--     lastExternalPort = NUMBER,
--     protocol = STRING,
--     internalServerIPAddress = IPADDRESS,
--     description = STRING
-- })
--
function _M.getPortRangeForwardingRules(sc)
    sc:readlock()
    local lanPrefixLength = util.subnetMaskToNetworkPrefixLength(hdk.ipaddress(sc:get_lan_subnet_mask()))
    local networkID = hdk.ipaddress(sc:get_lan_ipaddress()):networkid(lanPrefixLength)
    local rules = {}
    for i = 1, sc:get_port_range_forward_count() do
        local firstExternalPort, lastExternalPort = sc:get_port_range_forward_external_port(i)
        local protocol = parseIPProtocol(sc:get_port_range_forward_protocol(i))
        local internalServerIPAddress = networkID:bitwiseor(util.parseAbbreviatedDotDecimal(sc:get_port_range_forward_internal_ip_address(i)))
        if firstExternalPort and lastExternalPort and protocol and internalServerIPAddress then
            table.insert(rules, {
                isEnabled = sc:get_port_range_forward_enabled(i),
                firstExternalPort = firstExternalPort,
                lastExternalPort = lastExternalPort,
                protocol = protocol,
                internalServerIPAddress = internalServerIPAddress,
                description = sc:get_port_range_forward_description(i)
            })
        end
    end
    return rules
end

--
-- Set the port range forwarding rules of the local device.
--
-- input = CONTEXT, ARRAY_OF({
--     isEnabled = BOOLEAN,
--     firstExternalPort = NUMBER,
--     lastExternalPort = NUMBER,
--     protocol = STRING,
--     internalServerIPAddress = NUMBER,
--     description = STRING
-- })
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidExternalPort',
--     'ErrorInvalidExternalPortRange',
--     'ErrorInvalidInternalServerIPAddress',
--     'ErrorDescriptionTooLong',
--     'ErrorRulesOverlap',
--     'ErrorTooManyRules'
-- )
--
function _M.setPortRangeForwardingRules(sc, rules)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if #rules > platform.MAX_PORTRANGE_RULES then
        return 'ErrorTooManyRules'
    end

    local tcpRanges, udpRanges = {}, {}
    for i, rule in ipairs(_M.getSinglePortForwardingRules(sc)) do
        if rule.protocol == 'TCP' or rule.protocol == 'Both' then
            addRangeToList(tcpRanges, rule.externalPort, rule.externalPort)
        end
        if rule.protocol == 'UDP' or rule.protocol == 'Both' then
            addRangeToList(udpRanges, rule.externalPort, rule.externalPort)
        end
    end

    local lanPrefixLength = util.subnetMaskToNetworkPrefixLength(hdk.ipaddress(sc:get_lan_subnet_mask()))
    local routerHostAddress = hdk.ipaddress(sc:get_lan_ipaddress())

    -- update to new rule count
    sc:set_port_range_forward_count(#rules)

    for i, rule in ipairs(rules) do
        if not util.isValidPort(rule.firstExternalPort) or not util.isValidPort(rule.lastExternalPort) then
            return 'ErrorInvalidExternalPort'
        end

        if rule.firstExternalPort > rule.lastExternalPort then
            return 'ErrorInvalidExternalPortRange'
        end

        if not util.inSameSubnet(rule.internalServerIPAddress, routerHostAddress, lanPrefixLength) or
            util.isReservedSubnetAddress(rule.internalServerIPAddress, lanPrefixLength) or
            (rule.internalServerIPAddress == routerHostAddress) then
            return 'ErrorInvalidInternalServerIPAddress'
        end

        if #rule.description > platform.MAX_PORT_RULE_DESCRIPTION_LENGTH then
            return 'ErrorDescriptionTooLong'
        end

        if rule.protocol == 'TCP' or rule.protocol == 'Both' then
            if not addRangeToList(tcpRanges, rule.firstExternalPort, rule.lastExternalPort) then
                return 'ErrorRulesOverlap'
            end
        end
        if rule.protocol == 'UDP' or rule.protocol == 'Both' then
            if not addRangeToList(udpRanges, rule.firstExternalPort, rule.lastExternalPort) then
                return 'ErrorRulesOverlap'
            end
        end

        sc:set_port_range_forward_enabled(i, rule.isEnabled)
        sc:set_port_range_forward_external_port(i, rule.firstExternalPort, rule.lastExternalPort)
        sc:set_port_range_forward_protocol(i, serializeIPProtocol(rule.protocol))
        -- Note: the abbreviate dot-decimal notation is used here for maximum backwards compatibility should the user decide to downgrade
        sc:set_port_range_forward_internal_ip_address(i, util.serializeAbbreviatedDotDecimal(rule.internalServerIPAddress:hostid(lanPrefixLength)))
        sc:set_port_range_forward_internal_port(i, rule.firstExternalPort)
        sc:set_port_range_forward_description(i, rule.description)

    end
end

--
-- Get the port range triggering rules of the local device.
--
-- input = CONTEXT
--
-- output = ARRAY_OF({
--     isEnabled = BOOLEAN,
--     firstTriggerPort = NUMBER,
--     lastTriggerPort = NUMBER,
--     firstForwardedPort = NUMBER,
--     lastForwardedPort = NUMBER,
--     description = STRING
-- })
--
function _M.getPortRangeTriggeringRules(sc)
    sc:readlock()
    local rules = {}
    for i = 1, sc:get_port_trigger_count() do
        local firstTriggerPort, lastTriggerPort = sc:get_port_trigger_port_range(i)
        local firstForwardedPort, lastForwardedPort = sc:get_port_trigger_forward_range(i)
        if firstTriggerPort and lastTriggerPort and firstForwardedPort and lastForwardedPort then
            table.insert(rules, {
                isEnabled = sc:get_port_trigger_enabled(i),
                firstTriggerPort = firstTriggerPort,
                lastTriggerPort = lastTriggerPort,
                firstForwardedPort = firstForwardedPort,
                lastForwardedPort = lastForwardedPort,
                description = sc:get_port_trigger_description(i)
            })
        end
    end
    return rules
end

--
-- Set the port range triggering rules of the local device.
--
-- input = CONTEXT, ARRAY_OF({
--     isEnabled = BOOLEAN,
--     firstTriggerPort = NUMBER,
--     lastTriggerPort = NUMBER,
--     firstForwardedPort = NUMBER,
--     lastForwardedPort = NUMBER,
--     description = STRING
-- })
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidTriggerPort',
--     'ErrorInvalidTriggerPortRange',
--     'ErrorInvalidForwardedPort',
--     'ErrorInvalidForwardedPortRange',
--     'ErrorDescriptionTooLong',
--     'ErrorRulesOverlap',
--     'ErrorTooManyRules'
-- )
--
function _M.setPortRangeTriggeringRules(sc, rules)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if #rules > platform.MAX_PORTTRIGGER_RULES then
        return 'ErrorTooManyRules'
    end

    -- update to new rule count
    sc:set_port_trigger_count(#rules)

    local triggers, fwds = {}, {}
    for i, rule in ipairs(rules) do
        if not util.isValidPort(rule.firstTriggerPort) or not util.isValidPort(rule.lastTriggerPort) then
            return 'ErrorInvalidTriggerPort'
        end

        if rule.firstTriggerPort > rule.lastTriggerPort then
            return 'ErrorInvalidTriggerPortRange'
        end

        if not util.isValidPort(rule.firstForwardedPort) or not util.isValidPort(rule.lastForwardedPort) then
            return 'ErrorInvalidForwardedPort'
        end

        if rule.firstForwardedPort > rule.lastForwardedPort then
            return 'ErrorInvalidForwardedPortRange'
        end

        if #rule.description > platform.MAX_PORT_RULE_DESCRIPTION_LENGTH then
            return 'ErrorDescriptionTooLong'
        end

        if (not addRangeToList(triggers, rule.firstTriggerPort, rule.lastTriggerPort) or
            not addRangeToList(fwds, rule.firstForwardedPort, rule.lastForwardedPort)) then
            return 'ErrorRulesOverlap'
        end

        sc:set_port_trigger_id(i, i)
        sc:set_port_trigger_enabled(i, rule.isEnabled)
        sc:set_port_trigger_port_range(i, rule.firstTriggerPort, rule.lastTriggerPort)
        sc:set_port_trigger_forward_range(i, rule.firstForwardedPort, rule.lastForwardedPort)
        sc:set_port_trigger_protocol(i, 'both')
        sc:set_port_trigger_forward_protocol(i, 'both')
        sc:set_port_trigger_description(i, rule.description)

    end
end

--
-- Get the IPv6 firewall rules of the local device.
--
-- input = CONTEXT
--
-- output = ARRAY_OF({
--     isEnabled = BOOLEAN,
--     ipv6Address = IPV6ADDRESS,
--     portRanges = ARRAY_OF({
--         protocol = STRING,
--         firstPort = NUMBER,
--         lastPort = NUMBER
--     }),
--     description = STRING
-- })
--
function _M.getIPv6FirewallRules(sc)
    sc:readlock()
    local rules = {}
    local lowLevelRuleIndex = 1
    local rulesString = sc:get('filter_ipv6_list')
    if rulesString then
        for name, ip, prot1, first1, last1, prot2, first2, last2 in rulesString:gmatch('([^;]*);(%x+:%x+:%x+:%x+:%x+:%x+:%x+:%x+);(%a+);(%d+);(%d+);(%a+);(%d*);(%d*)/') do
            local isEnabled = sc:get_ipv6_port_forward_enabled(lowLevelRuleIndex)
            local portRanges = {
                {
                    protocol = parseIPProtocol(prot1),
                    firstPort = tonumber(first1),
                    lastPort = tonumber(last1)
                }
            }
            if #first2 > 0 then
                table.insert(portRanges, {
                    protocol = parseIPProtocol(prot2),
                    firstPort = tonumber(first2),
                    lastPort = tonumber(last2)
                })
            end
            table.insert(rules, {
                isEnabled = isEnabled,
                ipv6Address = hdk.ipv6address(ip),
                portRanges = portRanges,
                description = name,
            })
            lowLevelRuleIndex = lowLevelRuleIndex + #portRanges
        end
        assert(#rules == sc:getinteger('filter_ipv6_cnt'))
    end
    return rules
end

-- New implementation ??? - should work, but requires changes to the unit tests
function _M.getIPv6FirewallRules_new(sc)
    sc:readlock()
    local rules = {}
    local rule = nil
    for i = 1, sc:get_ipv6_port_forward_count() do
        local isEnabled = sc:get_ipv6_port_forward_enabled(i)
        local name = sc:get_ipv6_port_forward_description(i)
        local ip = sc:get_ipv6_port_forward_ip_address(i)
        local protocol = parseIPProtocol(sc:get_ipv6_port_forward_protocol(i))
        local firstPort, lastPort = sc:get_ipv6_port_forward_range(i)
        local portRange = {
            protocol = protocol,
            firstPort = firstPort,
            lastPort = lastPort
        }

        rule = {
            isEnabled = isEnabled,
            ipv6Address = hdk.ipv6address(ip),
            description = name,
            portRanges = { portRange }
        }
        table.insert(rules, rule)
    end

    return rules
end

--
-- Set the IPv6 firewall rules of the local device.
--
-- input = CONTEXT, ARRAY_OF({
--     isEnabled = BOOLEAN,
--     ipv6Address = IPV6ADDRESS,
--     portRanges = ARRAY_OF({
--         protocol = STRING,
--         firstPort = NUMBER,
--         lastPort = NUMBER
--     }),
--     description = STRING
-- })
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidPort',
--     'ErrorInvalidPortRange',
--     'ErrorNoPortRanges',
--     'ErrorTooManyPortRanges',
--     'ErrorDescriptionTooLong',
--     'ErrorRulesOverlap',
--     'ErrorTooManyRules'
-- )
--
function _M.setIPv6FirewallRules(sc, rules)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if #rules > platform.MAX_IPV6FIREWALL_RULES then
        return 'ErrorTooManyRules'
    end

    local restartFirewall = false
    local lowLevelRuleCount = 0
    local rulesString = ''
    local tcpRanges, udpRanges = {}, {}
    for i, rule in ipairs(rules) do
        if #rule.portRanges == 0 then
            return 'ErrorNoPortRanges'
        end

        if #rule.portRanges > platform.MAX_IPV6FIREWALL_RULE_PORT_RANGES then
            return 'ErrorTooManyPortRanges'
        end

        if #rule.description > platform.MAX_IPV6FIREWALL_RULE_DESCRIPTION_LENGTH then
            return 'ErrorDescriptionTooLong'
        end

        -- HACK: The persisted representation of the rules does not make
        -- allowance for escaping ; and /, which are used as separators.
        -- Therefore we have to ensure that those characters never make it
        -- into the stored description field. We replace them with spaces.
        local description = rule.description:gsub('[;/]', ' ')
        rulesString = rulesString..description..';'.. tostring(rule.ipv6Address)
        for j, portRange in ipairs(rule.portRanges) do
            lowLevelRuleCount = lowLevelRuleCount + 1

            if not util.isValidPort(portRange.firstPort) or not util.isValidPort(portRange.lastPort) then
                return 'ErrorInvalidPort'
            end

            if portRange.firstPort > portRange.lastPort then
                return 'ErrorInvalidPortRange'
            end

            if portRange.protocol == 'TCP' or portRange.protocol == 'Both' then
                local newRange, ipv6Address = addRangeToList(tcpRanges, portRange.firstPort, portRange.lastPort, rule.ipv6Address)
                if not newRange and (ipv6Address == rule.ipv6Address) then
                    return 'ErrorRulesOverlap'
                end
            end

            if portRange.protocol == 'UDP' or portRange.protocol == 'Both' then
                local newRange, ipv6Address = addRangeToList(udpRanges, portRange.firstPort, portRange.lastPort, rule.ipv6Address)
                if not newRange and (ipv6Address == rule.ipv6Address) then
                    return 'ErrorRulesOverlap'
                end
            end

            rulesString = rulesString..';'..serializeIPProtocol(portRange.protocol)..';'..portRange.firstPort..';'..portRange.lastPort

            sc:set_ipv6_port_forward_enabled(lowLevelRuleCount, rule.isEnabled)
            sc:set_ipv6_port_forward_description(lowLevelRuleCount, description)
            sc:set_ipv6_port_forward_protocol(lowLevelRuleCount, serializeIPProtocol(portRange.protocol))
            sc:set_ipv6_port_forward_range(lowLevelRuleCount, portRange.firstPort, portRange.lastPort)
            sc:set_ipv6_port_forward_ip_address(lowLevelRuleCount, tostring(rule.ipv6Address))
        end
        rulesString = rulesString..string.rep(';tcp;;', platform.MAX_IPV6FIREWALL_RULE_PORT_RANGES - #rule.portRanges)..'/'
    end

    sc:set_ipv6_port_forward_count(lowLevelRuleCount)

    -- The firewall restart events are not set here because they are set by the above individual settings
    -- These settings could be deprecated with the implementation of the new getIPv6FirewallRules()
    sc:setinteger('filter_ipv6_cnt', #rules)
    sc:set('filter_ipv6_list', rulesString)
end

--
-- Parse the DMZ source restrictions from syscfg
-- Possible syscfg values are:
--  Single/Netmask: <IP address>(/<netmask>)
--  Range: <IP address>-<IP address>
--  Wildcard: *
--
-- input = STRING
--
-- output = NIL_OR({
--      firstIPAddress = IPADDRESS,
--      lastIPAddress = OPTIONAL(IPADDRESS)
-- })
--
local function parseDMZSourceRange(range)
    if nil == range or '' == range or '*' == range then
        return nil
    end

    local addresses = {}
    local parsed = util.splitOnDelimiter(range, '-')
    if parsed[2] then
        addresses[1] = hdk.ipaddress(parsed[1])
        addresses[2] = hdk.ipaddress(parsed[2])
    else
        -- Check for a supplied netmask
        parsed = util.splitOnDelimiter(parsed[1], '/')
        if parsed[2] then
            -- Subnet mask provided; use the entire subnet as the range.
            local address = hdk.ipaddress(parsed[1])
            local prefixLength = parsed[2]
            addresses[1] = address:networkid(prefixLength)
            addresses[2] = address:broadcast(prefixLength)
        else
            addresses[1] = hdk.ipaddress(parsed[1])
        end
    end

    return {
        firstIPAddress = addresses[1],
        lastIPAddress = addresses[2]
    }
end

local function serializeDMZSourceRange(first, last)
    return string.format('%s-%s', tostring(first), tostring(last))
end

--
-- Get the DMZ settings of the local device.
--
-- input = CONTEXT
--
-- output = {
--     isDMZEnabled = BOOLEAN,
--     sourceRestriction = OPTIONAL({
--         firstIPAddress = IPADDRESS,
--         lastIPAddress = OPTIONAL(IPADDRESS)
--     }),
--     destinationIPAddress = OPTIONAL(IPADDRESS),
--     destinationMACAddress = OPTIONAL(MACADDRESS)
-- })
--
function _M.getDMZSettings(sc)
    sc:readlock()
    local isDMZEnabled = sc:get_dmz_enabled()
    local sourceRestriction, destinationIPAddress, destinationMACAddress
    if isDMZEnabled then
        local source = sc:get_dmz_source()
        sourceRestriction = parseDMZSourceRange(source)
        local destination = sc:get_dmz_mac_destination()
        if destination and destination ~= '' then
            destinationMACAddress = hdk.macaddress(destination)
        else
            local lanPrefixLength = util.subnetMaskToNetworkPrefixLength(hdk.ipaddress(sc:get_lan_subnet_mask()))
            local networkID = hdk.ipaddress(sc:get_lan_ipaddress()):networkid(lanPrefixLength)
            destinationIPAddress = networkID:bitwiseor(util.parseAbbreviatedDotDecimal(sc:get_dmz_ipaddr_destination()))
        end
    end
    return {
        isDMZEnabled = isDMZEnabled,
        sourceRestriction = sourceRestriction,
        destinationIPAddress = destinationIPAddress,
        destinationMACAddress = destinationMACAddress
    }
end

--
-- Set the DMZ settings of the local device.
--
-- input = CONTEXT, {
--     isDMZEnabled = BOOLEAN,
--     sourceRestriction = OPTIONAL({
--         firstIPAddress = IPADDRESS,
--         lastIPAddress = OPTIONAL(IPADDRESS)
--     }),
--     destinationIPAddress = OPTIONAL(IPADDRESS),
--     destinationMACAddress = OPTIONAL(MACADDRESS)
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidSourceRestriction',
--     'ErrorInvalidDestinationIPAddress',
--     'ErrorInvalidDestinationMACAddress',
--     'ErrorMissingDestination',
--     'ErrorMultipleDestinations',
-- )
--
function _M.setDMZSettings(sc, settings)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    sc:set_dmz_enabled(settings.isDMZEnabled)
    if settings.isDMZEnabled then
        local source
        if settings.sourceRestriction then
            local firstSource, lastSource = settings.sourceRestriction.firstIPAddress, settings.sourceRestriction.lastIPAddress
            if util.isReservedAddress(firstSource) then
                return 'ErrorInvalidSourceRestriction'
            end
            if lastSource and (lastSource ~= firstSource) then
                if util.isReservedAddress(lastSource) or
                    (lastSource < firstSource) then
                    return 'ErrorInvalidSourceRestriction'
                end
                source = serializeDMZSourceRange(firstSource, lastSource)
            else
                source = tostring(firstSource)
            end
        else
            source = '*'
        end
        sc:set_dmz_source(source)
        local destinationIPAddress
        local destinationMACAddress
        if settings.destinationMACAddress and settings.destinationIPAddress then
            return 'ErrorMultipleDestinations'
        elseif settings.destinationMACAddress then
            destinationIPAddress = nil -- clear the IP if using a MAC address

            destinationMACAddress = settings.destinationMACAddress
            if destinationMACAddress:iszero() or destinationMACAddress[1] % 2 ~= 0 then
                return 'ErrorInvalidDestinationMACAddress'
            end
            destinationMACAddress = tostring(destinationMACAddress)
        elseif settings.destinationIPAddress then
            destinationMACAddress = nil -- clear MAC if using an IP address

            local lanPrefixLength = util.subnetMaskToNetworkPrefixLength(hdk.ipaddress(sc:get_lan_subnet_mask()))
            local routerHostAddress = hdk.ipaddress(sc:get_lan_ipaddress())

            if not util.inSameSubnet(settings.destinationIPAddress, routerHostAddress, lanPrefixLength) or
                util.isReservedSubnetAddress(settings.destinationIPAddress, lanPrefixLength) or
                (settings.destinationIPAddress == routerHostAddress) then
                return 'ErrorInvalidDestinationIPAddress'
            end

            -- The destination IP is really the network host ID (e.g. 1.100)
            -- Note: the abbreviate dot-decimal notation is used here for maximum backwards compatibility should the user decide to downgrade
            destinationIPAddress = util.serializeAbbreviatedDotDecimal(settings.destinationIPAddress:hostid(lanPrefixLength))
        else
            return 'ErrorMissingDestination'
        end
        sc:set_dmz_mac_destination(destinationMACAddress)
        sc:set_dmz_ipaddr_destination(destinationIPAddress)
    end
end

--
-- Get whether the SIP ALG of the local device is enabled.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.getIsSIPALGEnabled(sc)
    sc:readlock()
    return sc:get_sip_alg_enabled()
end

--
-- Set whether the SIP ALG of the local device is enabled.
--
-- input = CONTEXT, BOOLEAN
--
function _M.setIsSIPALGEnabled(sc, enabled)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    sc:set_sip_alg_enabled(enabled)
end

--
-- Get the firewall settings of the local device.
--
-- input = CONTEXT
--
-- output = {
--     isIPv4FirewallEnabled = BOOLEAN,
--     isIPv6FirewallEnabled = BOOLEAN,
--     blockMulticast = BOOLEAN,
--     blockNATRedirection = BOOLEAN,
--     blockIDENT = BOOLEAN,
--     blockAnonymousRequests = BOOLEAN,
--     blockIPSec = BOOLEAN,
--     blockPPTP = BOOLEAN,
--     blockL2TP = BOOLEAN
-- })
--
function _M.getFirewallSettings(sc)
    sc:readlock()
    return {
        isIPv4FirewallEnabled = sc:get_firewall_ipv4_enabled(),
        isIPv6FirewallEnabled = sc:get_firewall_ipv6_enabled(),
        blockMulticast = sc:get_firewall_block_multicast(),
        blockNATRedirection = sc:get_firewall_block_nat_redirection(),
        blockIDENT = sc:get_firewall_block_ident(),
        blockAnonymousRequests = sc:get_firewall_block_anonymous_request(),
        blockIPSec = sc:get_firewall_ipsec_is_blocked(),
        blockPPTP = sc:get_firewall_pptp_is_blocked(),
        blockL2TP = sc:get_firewall_l2tp_is_blocked()
    }
end

--
-- Set the firewall settings of the local device.
--
-- input = CONTEXT, {
--     isIPv4FirewallEnabled = BOOLEAN,
--     isIPv6FirewallEnabled = BOOLEAN,
--     blockMulticast = BOOLEAN,
--     blockNATRedirection = BOOLEAN,
--     blockIDENT = BOOLEAN,
--     blockAnonymousRequests = BOOLEAN,
--     blockIPSec = BOOLEAN,
--     blockPPTP = BOOLEAN,
--     blockL2TP = BOOLEAN
-- }
--
function _M.setFirewallSettings(sc, settings)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    sc:set_firewall_ipv4_enabled(settings.isIPv4FirewallEnabled)
    sc:set_firewall_ipv6_enabled(settings.isIPv6FirewallEnabled)

    sc:set_firewall_block_multicast(settings.blockMulticast)


    sc:set_firewall_block_nat_redirection(settings.blockNATRedirection)
    sc:set_firewall_block_ident(settings.blockIDENT)
    sc:set_firewall_block_anonymous_request(settings.blockAnonymousRequests)

    local blockRules = ""
    if settings.blockIPSec then
        blockRules = "ipsec"
    end
    if settings.blockPPTP then
        if blockRules ~= "" then
            blockRules = blockRules .. ','
        end
        blockRules = blockRules .. "pptp"
    end
    if settings.blockL2TP then
        if blockRules ~= "" then
            blockRules = blockRules .. ','
        end
        blockRules = blockRules .. "l2tp"
    end

    sc:set_firewall_blocking_rules(blockRules)
end


return _M -- return the module
