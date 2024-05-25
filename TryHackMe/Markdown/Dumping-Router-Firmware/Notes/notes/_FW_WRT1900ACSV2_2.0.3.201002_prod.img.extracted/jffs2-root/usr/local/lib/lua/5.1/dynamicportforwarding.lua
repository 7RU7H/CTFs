--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

-- dynamicportforwarding.lua - library to configure firewall state.

local hdk = require('libhdklua')
local device = require('device')
local util = require('util')
local platform = require('platform')

local _M = {} -- create the module

---------------------------------------------------------------
-- Dynamic Port Forwarding Specific Constants
---------------------------------------------------------------

_M.DPF_SINGLEPORT_POOL_NAME = 'portmap_dyn_pool'
_M.DPF_PORTRANGE_POOL_NAME = 'portrangemap_dyn_pool'
_M.DPF_IPV6_CONNECTION_POOL_NAME = 'ipv6_connection_allow'

_M.DPF_SINGLEPORT_RULE_SESSION_POOL_NAME = 'dpf_singleport_rule'
_M.DPF_PORTRANGE_RULE_SESSION_POOL_NAME = 'dpf_portrange_rule'
_M.DPF_IPV6_CONNECTION_RULE_SESSION_POOL_NAME = 'dpf_ipv6connection_rule'

_M.DPF_SINGLEPORT_RULE_COUNT = 'dpf_singleport_count'
_M.DPF_PORTRANGE_RULE_COUNT = 'dpf_portrange_count'
_M.DPF_IPV6_CONNECTION_RULE_COUNT = 'dpf_ipv6connection_count'

_M.DYNAMIC_SINGLEPORT_FORWARDING_RULE_FORMAT =
    '(%a+),(%d+.%d+.%d+.%d+),(%d+),(%d+.%d+.%d+.%d+),(%d+),(%a+),(%d+),(%d+),(%a+)'
_M.DYNAMIC_PORTRANGE_FORWARDING_RULE_FORMAT =
    '(%a+),(%d+.%d+.%d+.%d+),(%d+%s+%d+),(%d+.%d+.%d+.%d+),(%a+),(%d+),(%d+),(%a+)'
_M.DYNAMIC_IPV6_CONNECTION_RULE_FORMAT =
    '(%a+),(%x+:%x+:%x+:%x+:%x+:%x+:%x+:%x+),(%d+%s+%d+),(%x+:%x+:%x+:%x+:%x+:%x+:%x+:%x+),(%d+),(%d+),(%a+)'

_M.DYNAMIC_SESSION_POOL_NAME = 'dyn_session_info'

---------------------------------------------------------
---------------- local helper functions -----------------
---------------------------------------------------------

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

local function serializeEnableDisable(enable)
    if enable == true then
        return 'enabled'
    else
        return 'disabled'
    end
end

local function getBoolean(enable)
    if enable == 'enabled' then
        return true
    else
        return false
    end
end

local function serializePortRange(firstPort, lastPort)
    return string.format('%d %d', firstPort, lastPort)
end

local function parsePortRange(range)
    return range:match('^(%d+)%s+(%d+)$')
end

local function isInRange(target, first, last)
    return (target >= first and target <= last)
end


local function isRangeOverlap(first1, last1, first2, last2)
    return (first1 <= last2 and last1 >= first2)
end

local function getRuleUUIDSessionUUID(ruleSession)
    local uuids = {}
    for uuid in string.gmatch(ruleSession, '([^,]*)') do
        if uuid ~= '' then
            table.insert(uuids, uuid)
        end
    end
    return uuids
end

local function  checkSessionExist(sc, testSessionUUID)
    local session_info = sc:getuniqueevents(_M.DYNAMIC_SESSION_POOL_NAME)
    if session_info then
        for k,v in pairs(session_info) do
            local sessionUUID = string.match(v, '([^,]*)')
            if sessionUUID ~= '' and sessionUUID == testSessionUUID then
                return true
            end
        end
    end
    return false
end

---------------------------------------------------------
---------------- libary functions -----------------------
---------------------------------------------------------

--
-- Get all dynamic single-port forwarding rules of the local device.
--
-- input = CONTEXT
--
-- output = {
--     rules = ARRAY_OF({
--         ruleUUID = UUID,
--         rule = {
--             isEnabled = BOOL,
--             srcIPAddress = IPADDRESS,
--             externalPort = NUMBER,
--             protocol = STRING,
--             internalServerIPAddress = IPADDRESS,
--             internalPort = NUMBER,
--             description = STRING,
--             sessionUUID = UUID
--         }
--     })
-- }
--
function _M.getDynamicSinglePortForwardingRules(sc)
    sc:readlock()

    local ruleInfoTable = {}

    -- get dynamic single-port forwarding 'ruleUUID,sessionUUID' pool
    local ruleSession = sc:getuniqueevents(_M.DPF_SINGLEPORT_RULE_SESSION_POOL_NAME)

    if ruleSession then
        for k,v in pairs(ruleSession) do

            -- get ruleUUID and sessionUUID
            local uuids = getRuleUUIDSessionUUID(v)
            local ruleUUID = uuids[1]
            local sessionUUID = uuids[2]

            -- get the rule string
            local uniqueId = sc:getevent(ruleUUID)
            local ruleStr = sc:getevent(uniqueId)

            -- get the rule
            local format = _M.DYNAMIC_SINGLEPORT_FORWARDING_RULE_FORMAT
            for enable, srcIP, srcPort, destIP, destPort,protocol,leaseDuration,lastEpochTime,description
                in string.gmatch(ruleStr, format) do

                local rule = {
                    isEnabled = getBoolean(enable),
                    srcIPAddress = hdk.ipaddress(srcIP),
                    externalPort = tonumber(srcPort),
                    internalServerIPAddress = hdk.ipaddress(destIP),
                    internalPort = tonumber(destPort),
                    protocol = parseIPProtocol(protocol),
                    description = description,
                }
                if sessionUUID and sessionUUID ~= '' then
                    rule.sessionUUID = hdk.uuid(sessionUUID)
                end

                local ruleInfo = {
                    ruleUUID = hdk.uuid(ruleUUID),
                    rule = rule
                }

                -- insert the ruleInfo into ruleInfoTable
                table.insert(ruleInfoTable, ruleInfo)
            end -- end of for loop gmatch
        end -- end of pairs(ruleSession)
    end -- end of if
    return ruleInfoTable
end


--
-- Get all dynamic port range forwarding rules of the local device.
--
-- input = CONTEXT
--
-- output = {
--     rules = ARRAY_OF({
--         ruleUUID = UUID,
--         rule = {
--             isEnabled = BOOL,
--             srcIPAddress = IPADDRESS,
--             firstExternalPort = NUMBER,
--             lastExternalPort = NUMBER,
--             protocol = STRING,
--             internalServerIPAddress = IPADDRESS,
--             description = STRING,
--             sessionUUID = UUID
--         }
--     })
-- }
--
function _M.getDynamicPortRangeForwardingRules(sc)
    sc:readlock()

    local ruleInfoTable = {}

    -- get dynamic port range forwarding 'ruleUUID,sessionUUID' pool
    local ruleSession = sc:getuniqueevents(_M.DPF_PORTRANGE_RULE_SESSION_POOL_NAME)

    -- get dynamic port range forwarding rules
    if ruleSession then
        for k,v in pairs(ruleSession) do

            -- get ruleUUID and sessionUUID
            local uuids = getRuleUUIDSessionUUID(v)
            local ruleUUID = uuids[1]
            local sessionUUID = uuids[2]

            -- get the rule string
            local uniqueId = sc:getevent(ruleUUID)
            local ruleStr = sc:getevent(uniqueId)

            -- get the rule
            local format = _M.DYNAMIC_PORTRANGE_FORWARDING_RULE_FORMAT
            for enable, srcIP, portRange, destIP, protocol,leaseDuration,lastEpochTime,description
                in string.gmatch(ruleStr, format) do

                -- get the first and last external ports from portRange string "10 20"
                local firstExternalPort, lastExternalPort = parsePortRange(portRange)

                local rule = {
                    isEnabled = getBoolean(enable),
                    srcIPAddress = hdk.ipaddress(srcIP),
                    firstExternalPort = tonumber(firstExternalPort),
                    lastExternalPort = tonumber(lastExternalPort),
                    internalServerIPAddress = hdk.ipaddress(destIP),
                    protocol = parseIPProtocol(protocol),
                    description = description,
                }
                if sessionUUID and sessionUUID ~= '' then
                    rule.sessionUUID = hdk.uuid(sessionUUID)
                end

                ruleInfo = {
                    ruleUUID = hdk.uuid(ruleUUID),
                    rule = rule
                }

                -- insert the sessionInfo into rule info table
                table.insert(ruleInfoTable, ruleInfo)
            end -- end of for loop gmatch
        end -- end of ipairs(ruleSession)
    end

    return ruleInfoTable
end


--
-- Get all dynamic IPv6 connection rules of the local device.
--
-- input = CONTEXT
--
-- output = {
--     rules = ARRAY_OF({
--         ruleUUID = UUID,
--         rule = {
--             isEnabled = BOOL,
--             srcIPAddress = IPV6ADDRESS,
--             firstExternalPort = NUMBER,
--             lastExternalPort = NUMBER,
--             destIPv6Address = IPV6ADDRESS,
--             description = STRING,
--             sessionUUID = UUID
--         }
--     })
-- }
--
function _M.getDynamicIPv6ConnectionRules(sc)
    sc:readlock()

    local ruleInfoTable = {}

    -- get dynamic ipv6 connection 'ruleUUID,sessionUUID' pool
    local ruleSession = sc:getuniqueevents(_M.DPF_IPV6_CONNECTION_RULE_SESSION_POOL_NAME)

    -- get dynamic IPv6 connection rules
    if ruleSession then
        for k,v in pairs(ruleSession) do

            -- get ruleUUID and sessionUUID
            local uuids = getRuleUUIDSessionUUID(v)
            local ruleUUID = uuids[1]
            local sessionUUID = uuids[2]

            -- get the rule string
            local uniqueId = sc:getevent(ruleUUID)
            local ruleStr = sc:getevent(uniqueId)

            -- get the rule
            local format = _M.DYNAMIC_IPV6_CONNECTION_RULE_FORMAT
            for enable, srcIPv6Addr, portRange, destIPv6Addr, leaseDuration, lastEpochTime, description
                in string.gmatch(ruleStr, format) do

                -- get the first and last external ports from portRange string "10 20"
                local firstExternalPort, lastExternalPort = parsePortRange(portRange)

                local rule = {
                    isEnabled = getBoolean(enable),
                    srcIPv6Address = hdk.ipv6address(srcIPv6Addr),
                    firstExternalPort = tonumber(firstExternalPort),
                    lastExternalPort = tonumber(lastExternalPort),
                    destIPv6Address = hdk.ipv6address(destIPv6Addr),
                    description = description,
                }
                if sessionUUID and sessionUUID ~= '' then
                    rule.sessionUUID = hdk.uuid(sessionUUID)
                end

                local ruleInfo = {
                    ruleUUID = hdk.uuid(ruleUUID),
                    rule = rule
                }

                -- insert the ruleInfo into rule info table
                table.insert(ruleInfoTable, ruleInfo)
            end -- end of for loop gmatch
        end -- end of pairs(ruleSession)
    end
    return ruleInfoTable
end


--
-- validate a dynamic single-port forwarding rule
--
-- input =
--     CONTEXT,
--     rule = {
--         isEnabled = BOOL,
--         srcIPAddress = IPADDRESS,
--         externalPort = NUMBER,
--         protocol = STRING,
--         internalServerIPAddress = IPADDRESS,
--         internalPort = NUMBER,
--         description = STRING,
--         sessionUUID = UUID
--     }
--
-- result = OK_OR_ONE_OF(
--    "OK",
--    "ErrorInvalidSourceIPAddress",
--    "ErrorInvalidExternalPort",
--    "ErrorInvalidInternalServerIPAddress",
--    "ErrorInvalidInternalPort",
--    "ErrorDescriptionTooLong",
--    "ErrorSessionDoesNotExist",
--    "ErrorRuleAlreadyExist",
--    "ErrorTooManyRules"
-- )
--
function _M.validateDynamicSinglePortForwardingRule(sc, rule)
    sc:writelock()

    local count = sc:getevent(_M.DPF_SINGLEPORT_RULE_COUNT)
    if count and count ~= '' then
        count = tonumber(count)
    else
        count = tonumber('0')
    end

    local maxCount = sc:getinteger('ra_maxsingleportrules', 0)
    if count >= maxCount then
        return "ErrorTooManyRules"
    end

    if not util.isValidPort(rule.externalPort) then
        return 'ErrorInvalidExternalPort'
    end

    if rule.sessionUUID ~= nil then
        if checkSessionExist(sc, tostring(rule.sessionUUID)) == false then
            return "ErrorSessionDoesNotExist"
        end
    end

    local lanPrefixLength = util.subnetMaskToNetworkPrefixLength(hdk.ipaddress(sc:get_lan_subnet_mask()))
    local routerHostAddress = hdk.ipaddress(sc:get_lan_ipaddress())

    if not util.inSameSubnet(rule.internalServerIPAddress, routerHostAddress, lanPrefixLength) or
        util.isReservedSubnetAddress(rule.internalServerIPAddress, lanPrefixLength) then
        return 'ErrorInvalidInternalServerIPAddress'
    end

    if not util.isValidPort(rule.internalPort) then
        return 'ErrorInvalidInternalPort'
    end

    local maxDescriptionLength = sc:getinteger('ra_maxruledescription', 0)
    if #rule.description > maxDescriptionLength then
        return 'ErrorDescriptionTooLong'
    end


    --check whether the rule is a duplicate of existing single port forwarding rules
    local existingSingleRules = _M.getDynamicSinglePortForwardingRules(sc)
    if existingSingleRules and #existingSingleRules > 0 then
        for i, existing in ipairs(existingSingleRules) do
            if ((rule.srcIPAddress == existing.rule.srcIPAddress) and
                (rule.externalPort == existing.rule.externalPort) and
                ((rule.protocol == existing.rule.protocol) or (existing.rule.protocol == 'Both') or (rule.protocol == 'Both'))) then
                return "ErrorRuleAlreadyExist"
            end
        end
    end

    -- check whether the rule is a duplicate of existing port range forwarding rules
    local existingRangeRules = _M.getDynamicPortRangeForwardingRules(sc)
    if existingRangeRules and #existingRangeRules > 0 then
        for i, existing in ipairs (existingRangeRules) do
            if ((rule.srcIPAddress == existing.rule.srcIPAddress) and
                ((rule.protocol == existing.rule.protocol) or (existing.rule.protocol == 'Both') or (rule.protocol == 'Both')) and
                (isInRange(rule.externalPort, existing.rule.firstExternalPort, existing.rule.lastExternalPort))) then
                return "ErrorRuleAlreadyExist"
            end
        end
    end
    return "OK"
end


--
-- add a dynamic single-port forwarding rule
--
-- input =
--     CONTEXT,
--     rule = {
--         isEnabled = BOOL,
--         srcIPAddress = IPADDRESS,
--         externalPort = NUMBER,
--         protocol = STRING,
--         internalServerIPAddress = IPADDRESS,
--         internalPort = NUMBER,
--         description = STRING,
--         sessionUUID = UUID
--     }
--
-- output = ruleID
--
--
function _M.addDynamicSinglePortForwardingRule(sc, rule)
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    local restartFirewall = false

    -- lease duration and currEpochTime
    local leaseDuration = platform.MAX_DPF_SESSION_TIMEOUT_SECONDS
    local currEpochTime = platform.getEpochTime()

    -- concat the rule
    local ruleStr = serializeEnableDisable(rule.isEnabled)..','..tostring(rule.srcIPAddress)..','..tostring(rule.externalPort)..','..tostring(rule.internalServerIPAddress)..','..tostring(rule.internalPort)..','..serializeIPProtocol(rule.protocol)..','..leaseDuration..','..currEpochTime..','..rule.description

    -- add dynamic single port forwarding rule to 'portmap_dyn_pool'
    local uniqueId = sc:setuniqueevent(_M.DPF_SINGLEPORT_POOL_NAME, ruleStr)

    if uniqueId and uniqueId ~= '' then
        restartFirewall = true
    end

    -- set ruleUUID/uniqueId pair
    local ruleUUID = platform.getUUID()
    sc:setevent(ruleUUID, uniqueId)

    -- add 'ruleUUID,sessionUUID' to dpf_singleport_rule pool and
    -- add 'ruleUUID,ruleType' to sessionUUID pool
    if rule.sessionUUID ~= nil then
        local sessionUUID = tostring(rule.sessionUUID)
        local ruleSession = ruleUUID..','..sessionUUID
        sc:setuniqueevent(_M.DPF_SINGLEPORT_RULE_SESSION_POOL_NAME, ruleSession)
        sc:setuniqueevent(sessionUUID, ruleUUID..','..'DynamicSinglePortForwardingRule')
    else
        sc:setuniqueevent(_M.DPF_SINGLEPORT_RULE_SESSION_POOL_NAME, ruleUUID)
    end

    -- update dynamic single port forwarding rule count
    local count = sc:getevent(_M.DPF_SINGLEPORT_RULE_COUNT)

    if count and count ~= '' then
        count = tonumber(count)
    else
        count = tonumber("0")
    end

    sc:setevent(_M.DPF_SINGLEPORT_RULE_COUNT, (count + 1))

    if restartFirewall then
        sc:setevent('firewall-restart')
    end

    return ruleUUID
end

--
-- validate a dynamic port range forwarding rule
--
-- input =
--     CONTEXT,
--     rule = {
--         isEnabled = BOOL,
--         srcIPAddress = IPADDRESS,
--         externalPort = NUMBER,
--         protocol = STRING,
--         internalServerIPAddress = IPADDRESS,
--         internalPort = NUMBER,
--         description = STRING,
--         sessionUUID = UUID
--     }
--
-- result = OK_OR_ONE_OF(
--    "OK",
--    "ErrorInvalidSourceIPAddress",
--    "ErrorInvalidExternalPort",
--    "ErrorInvalidInternalServerIPAddress",
--    "ErrorInvalidInternalPort",
--    "ErrorDescriptionTooLong",
--    "ErrorSessionDoesNotExist",
--    "ErrorRuleAlreadyExist",
--    "ErrorTooManyRules"
-- )
--
function _M.validateDynamicPortRangeForwardingRule(sc, rule)
    sc:writelock()

    local count = sc:getevent(_M.DPF_PORTRANGE_RULE_COUNT)
    if count and count ~= '' then
        count = tonumber(count)
    else
        count = tonumber('0')
    end

    local maxCount = sc:getinteger('ra_maxportrangerules', 0)
    if count >= maxCount then
        return "ErrorTooManyRules"
    end

    if not util.isValidPort(rule.firstExternalPort) then
        return 'ErrorInvalidExternalPort'
    end

    if not util.isValidPort(rule.lastExternalPort) then
        return 'ErrorInvalidExternalPort'
    end

    if rule.firstExternalPort > rule.lastExternalPort then
        return 'ErrorInvalidExternalPortRange'
    end

    if rule.sessionUUID ~= nil then
        if checkSessionExist(sc, tostring(rule.sessionUUID)) == false then
            return "ErrorSessionDoesNotExist"
        end
    end

    local lanPrefixLength = util.subnetMaskToNetworkPrefixLength(hdk.ipaddress(sc:get_lan_subnet_mask()))
    local routerHostAddress = hdk.ipaddress(sc:get_lan_ipaddress())

    if not util.inSameSubnet(rule.internalServerIPAddress, routerHostAddress, lanPrefixLength) or
        util.isReservedSubnetAddress(rule.internalServerIPAddress, lanPrefixLength) then
        return 'ErrorInvalidInternalServerIPAddress'
    end

    local maxDescriptionLength = sc:getinteger('ra_maxruledescription', 0)
    if #rule.description > maxDescriptionLength then
        return 'ErrorDescriptionTooLong'
    end

    -- check whether the rule is a duplicate of existing single port forwarding rules
    local existingSingleRules = _M.getDynamicSinglePortForwardingRules(sc)
    if existingSingleRules and #existingSingleRules > 0 then
        for i, existing in ipairs(existingSingleRules) do
            if ((rule.srcIPAddress == existing.rule.srcIPAddress) and
                (isInRange(existing.rule.externalPort, rule.firstExternalPort, rule.lastExternalPort)) and
                ((rule.protocol == existing.rule.protocol) or (existing.rule.protocol == 'Both') or (rule.protocol == 'Both'))) then
                return "ErrorRuleAlreadyExist"
            end
        end
    end

    -- check whether the rule is a duplicate of existing port range forwarding rules
    local existingRangeRules = _M.getDynamicPortRangeForwardingRules(sc)
    if existingRangeRules and #existingRangeRules > 0 then
        for i, existing in ipairs (existingRangeRules) do
            if ((rule.srcIPAddress == existing.rule.srcIPAddress) and
                ((rule.protocol == existing.rule.protocol) or (existing.rule.protocol == 'Both') or (rule.protocol == 'Both')) and
                (isRangeOverlap(rule.firstExternalPort, rule.lastExternalPort, existing.rule.firstExternalPort, existing.rule.lastExternalPort))) then
                return "ErrorRuleAlreadyExist"
            end
        end
    end
    return "OK"
end


--
-- Add a dynamic port range forwarding rule
--
-- input =
--     CONTEXT,
--     rule = {
--         isEnabled = BOOL,
--         srcIPAddress = IPADDRESS,
--         externalPort = NUMBER,
--         protocol = STRING,
--         internalServerIPAddress = IPADDRESS,
--         internalPort = NUMBER,
--         description = STRING,
--         sessionUUID = UUID
--     }
--
-- output = ruleUUID
--
--
function _M.addDynamicPortRangeForwardingRule(sc, rule)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    local restartFirewall = false

    -- get duration and current epoch time
    local leaseDuration = platform.MAX_DPF_SESSION_TIMEOUT_SECONDS
    local currEpochTime = platform.getEpochTime()

    -- concat the rule
    local externalPorts = serializePortRange(rule.firstExternalPort, rule.lastExternalPort)
    local ruleStr = serializeEnableDisable(rule.isEnabled)..','..tostring(rule.srcIPAddress)..','..externalPorts..','..tostring(rule.internalServerIPAddress)..','..serializeIPProtocol(rule.protocol)..','..leaseDuration..','..currEpochTime..','..rule.description

    -- add dynamic port range forwarding rule to 'portrangemap_dyn_pool'
    local uniqueId = sc:setuniqueevent(_M.DPF_PORTRANGE_POOL_NAME, ruleStr)
    if uniqueId and uniqueId ~= '' then
        restartFirewall = true
    end

    -- set ruleUUID/uniqueId pair
    local ruleUUID = platform.getUUID()
    sc:setevent(ruleUUID, uniqueId)

    -- add 'ruleUUID,sessionUUID' to dpf_portrange_rule pool and
    -- add 'ruleUUID,ruleType' to sessionUUID pool
    if rule.sessionUUID ~= nil then
        local sessionUUID = tostring(rule.sessionUUID)
        local ruleSession = ruleUUID..','..sessionUUID
        sc:setuniqueevent(_M.DPF_PORTRANGE_RULE_SESSION_POOL_NAME, ruleSession)
        sc:setuniqueevent(sessionUUID, ruleUUID..','..'DynamicPortRangeForwardingRule')
    else
        sc:setuniqueevent(_M.DPF_PORTRANGE_RULE_SESSION_POOL_NAME, ruleUUID)
    end

    local count = sc:getevent(_M.DPF_PORTRANGE_RULE_COUNT)
    if count and count ~= '' then
        count = tonumber(count)
    else
        count = tonumber("0")
    end

    sc:setevent(_M.DPF_PORTRANGE_RULE_COUNT, (count + 1))

    if restartFirewall then
        sc:setevent('firewall-restart')
    end

    return ruleUUID
end


--
-- validate a dynamic IPv6 connection rule
--
-- input =
--     CONTEXT,
--     rule = {
--         isEnabled = BOOL,
--         srcIPv6Address = IPV6ADDRESS,
--         firstExternalPort = NUMBER,
--         lastExternalPort = NUMBER,
--         protocol = STRING,
--         destIPv6Address = IPV6ADDRESS,
--         description = STRING,
--         sessionUUID = UUID
--     }
--
-- result = OK_OR_ONE_OF(
--    "OK",
--    "ErrorInvalidSourceIPAddress",
--    "ErrorInvalidExternalPort",
--    "ErrorInvalidInternalServerIPAddress",
--    "ErrorInvalidInternalPort",
--    "ErrorDescriptionTooLong",
--    "ErrorSessionDoesNotExist"
--    "ErrorRuleAlreadyExist",
--    "ErrorTooManyRules"
-- )
--
function _M.validateDynamicIPv6ConnectionRule(sc, rule)
    sc:writelock()

    local count = sc:getevent(_M.DPF_IPV6_CONNECTION_RULE_COUNT)
    if count and count ~= '' then
        count = tonumber(count)
    else
        count = tonumber('0')
    end

    local maxCount = sc:getinteger('ra_maxipv6rules', 0)
    if count >= maxCount then
        return "ErrorTooManyRules"
    end

    if not util.isValidPort(rule.firstExternalPort) then
        return 'ErrorInvalidExternalPort'
    end

    if not util.isValidPort(rule.lastExternalPort) then
        return 'ErrorInvalidExternalPort'
    end

    if rule.firstExternalPort > rule.lastExternalPort then
        return 'ErrorInvalidExternalPortRange'
    end

    local maxDescriptionLength = sc:getinteger('ra_maxruledescription', 0)
    if #rule.description > maxDescriptionLength then
        return 'ErrorDescriptionTooLong'
    end

    if rule.sessionUUID ~= nil then
        if checkSessionExist(sc, tostring(rule.sessionUUID)) == false then
            return "ErrorSessionDoesNotExist"
        end
    end

    -- check whether the rule is a duplicate of existing IPv6 connection rules
    local existingIPv6Rules = _M.getDynamicIPv6ConnectionRules(sc)
    if existingIPv6Rules and #existingIPv6Rules > 0 then
        for i, existingRule in ipairs(existingIPv6Rules) do
            if ((rule.srcIPv6Address == existingRule.rule.srcIPv6Address) and
                (isRangeOverlap(rule.firstExternalPort, rule.lastExternalPort, existingRule.rule.firstExternalPort, existingRule.rule.lastExternalPort))) then
                return "ErrorRuleAlreadyExist"
            end
        end
    end
    return "OK"
end

--
-- Add a dynamic IPv6 connection rule
--
-- input =
--     CONTEXT,
--     rule = {
--         isEnabled = BOOL,
--         srcIPv6Address = IPV6ADDRESS,
--         firstExternalPort = NUMBER,
--         lastExternalPort = NUMBER,
--         destIPv6Address = IPV6ADDRESS,
--         description = STRING,
--         sessionUUID = UUID
--     }
--
-- output = ruleUUID
--
--
function _M.addDynamicIPv6ConnectionRule(sc, rule)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    local restartFirewall = false

    -- concat the rule
    local leaseDuration = platform.MAX_DPF_SESSION_TIMEOUT_SECONDS
    local currEpochTime = platform.getEpochTime()

    -- concat the rule
    local portRange = serializePortRange(rule.firstExternalPort, rule.lastExternalPort)
    local ruleStr = serializeEnableDisable(rule.isEnabled)..','..tostring(rule.srcIPv6Address)..','..portRange..','..tostring(rule.destIPv6Address)..','..leaseDuration..','..currEpochTime..','..rule.description

    -- add dynamic ipv6 connection rule to 'ipv6_connection_allow'
    local uniqueId = sc:setuniqueevent(_M.DPF_IPV6_CONNECTION_POOL_NAME, ruleStr)
    if uniqueId and uniqueId ~= '' then
        restartFirewall = true
    end

    -- set ruleUUID/uniqueId pair
    local ruleUUID = platform.getUUID()
    sc:setevent(ruleUUID, uniqueId)

    -- add 'ruleUUID,sessionUUID' to dpf_ipv6connection_rule pool and
    -- add 'ruleUUID,ruleType' to sessionUUID pool
    if rule.sessionUUID ~= nil then
        local sessionUUID = tostring(rule.sessionUUID)
        local ruleSession = ruleUUID..','..sessionUUID
        sc:setuniqueevent(_M.DPF_IPV6_CONNECTION_RULE_SESSION_POOL_NAME, ruleSession)
        sc:setuniqueevent(sessionUUID, ruleUUID..','..'DynamicIPv6ConnectionRule')
    else
        sc:setuniqueevent(_M.DPF_IPV6_CONNECTION_RULE_SESSION_POOL_NAME, ruleUUID)
    end

    local count = sc:getevent(_M.DPF_IPV6_CONNECTION_RULE_COUNT)
    if count and count ~= '' then
        count = tonumber(count)
    else
        count = tonumber('0')
    end
    sc:setevent(_M.DPF_IPV6_CONNECTION_RULE_COUNT, (count + 1))

    if restartFirewall then
        sc:setevent('firewall-restart')
    end

    return ruleUUID
end


function _M.removeDynamicSinglePortForwardingRule(sc, ruleUUID)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    local removeRuleUUID = tostring(ruleUUID)
    local found = false
    local restartFirewall = false

    -- get dynamic single-port forwarding ruleUUID and sessionUUID
    local ruleSession = sc:getuniqueevents(_M.DPF_SINGLEPORT_RULE_SESSION_POOL_NAME)
    if not ruleSession or ruleSession == '' then
        return "ErrorUnknownRule"
    end

    for k,v in pairs(ruleSession) do

        -- get ruleUUID and sessionUUID
        local uuids = getRuleUUIDSessionUUID(v)
        local ruleUUID = uuids[1]
        local sessionUUID = uuids[2]

        if removeRuleUUID == ruleUUID then
            found = true
            restartFirewall = true

            -- remove rule from 'portmap_dyn_pool' pool
            local uniqueId = sc:getevent(removeRuleUUID)
            sc:setevent(uniqueId)
            sc:setevent(removeRuleUUID)

            -- remove the rule from 'dpf_singleport_rule' pool
            sc:setevent(k)

            -- remove the 'ruleUUID,ruleType' from the associated session's sessionUUID pool
            if sessionUUID and sessionUUID ~= '' then
                local sessionRules = sc:getuniqueevents(sessionUUID)
                if sessionRules and sessionRules ~= '' then
                    for key,value in pairs(sessionRules) do
                        sessionRuleUUID = string.match(value, '([^,]*)')
                        if sessionRuleUUID == ruleUUID then
                            sc:setevent(key)
                            break
                        end
                    end -- end of pairs(sessionRules)a
                end
            end -- end of if sessionUUID
            break
        end -- end of removeRuleUUID == ruleUUID
    end -- end of pairs(ruleSession)

    if found == false then
        return "ErrorUnknownRule"
    end

    -- update count
    local count = sc:getevent(_M.DPF_SINGLEPORT_RULE_COUNT)
    if count and count ~= '' then
        count = tonumber(count)
    else
        count = tonumber('0')
    end
    if count > 0 then
        sc:setevent(_M.DPF_SINGLEPORT_RULE_COUNT, (count-1))
    end
    if restartFirewall then
        sc:setevent('firewall-restart')
    end
    return 'OK'
end


function _M.removeDynamicPortRangeForwardingRule(sc, ruleUUID)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    local removeRuleUUID = tostring(ruleUUID)
    local found = false
    local restartFirewall = false

    -- get dynamic port range forwarding ruleUUID and sessionUUID
    local ruleSession = sc:getuniqueevents(_M.DPF_PORTRANGE_RULE_SESSION_POOL_NAME)
    if not ruleSession or ruleSession == '' then
        return "ErrorUnknownRule"
    end

    for k,v in pairs(ruleSession) do

        -- get ruleUUID and sessionUUID
        local uuids = getRuleUUIDSessionUUID(v)
        local ruleUUID = uuids[1]
        local sessionUUID = uuids[2]

        if removeRuleUUID == ruleUUID then
            found = true
            restartFirewall = true

            -- remove rule from 'portrangemap_dyn_pool' pool
            local uniqueId = sc:getevent(removeRuleUUID)
            sc:setevent(uniqueId)
            sc:setevent(removeRuleUUID)

            -- remove the 'ruleUUID,sessionUUID' from 'dpf_portrange_rule'
            sc:setevent(k)

            -- remove the rule from the associated session
            if sessionUUID and sessionUUID ~= '' then
                local sessionRules = sc:getuniqueevents(sessionUUID)
                for key,value in pairs(sessionRules) do
                    sessionRuleUUID = string.match(value, '([^,]*)')
                    if sessionRuleUUID == ruleUUID then
                        sc:setevent(key)
                        break
                    end
                end -- end of pairs(sessionRules)
            end -- end of if sessionUUID
            break
        end -- end of if removeRuleUUID == ruleUUID
    end -- end of pairs(ruleSession)

    if found == false then
        return "ErrorUnknownRule"
    end

    -- update count
    local count = sc:getevent(_M.DPF_PORTRANGE_RULE_COUNT)
    if count and count ~= '' then
        count = tonumber(count)
    else
        count = tonumber('0')
    end
    if count > 0 then
        sc:setevent(_M.DPF_PORTRANGE_RULE_COUNT, (count-1))
    end
    if restartFirewall then
        sc:setevent('firewall-restart')
    end
    return 'OK'
end

function _M.removeDynamicIPv6ConnectionRule(sc, ruleUUID)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    local removeRuleUUID = tostring(ruleUUID)
    local found = false
    local restartFirewall = false

    -- get dynamic IPv6 connection ruleUUID and sessionUUID
    local ruleSession = sc:getuniqueevents(_M.DPF_IPV6_CONNECTION_RULE_SESSION_POOL_NAME)
    if not ruleSession or ruleSession == '' then
        return "ErrorUnknownRule"
    end

    for k,v in pairs(ruleSession) do

        -- get ruleUUID and sessionUUID
        local uuids = getRuleUUIDSessionUUID(v)
        local ruleUUID = uuids[1]
        local sessionUUID = uuids[2]

        if removeRuleUUID == ruleUUID then
            found = true
            restartFirewall = true
            -- remove the rule 'ipv6_connection_allow' pool
            local uniqueId = sc:getevent(removeRuleUUID)
            sc:setevent(uniqueId)
            sc:setevent(removeRuleUUID)

            -- remove the 'ruleUUID,sessionUUID' from 'dpf_ipv6connection_rule' pool
            sc:setevent(k)

            -- remove the rule from the associated session
            if sessionUUID and sessionUUID ~= '' then
                local sessionRules = sc:getuniqueevents(sessionUUID)
                for key,value in pairs(sessionRules) do
                    sessionRuleUUID = string.match(value, '([^,]*)')
                    if sessionRuleUUID == ruleUUID then
                        sc:setevent(key)
                        break
                    end
                end -- end of pairs(sessionRules)
            end -- end of if sessionUUID
            break
        end -- end of removeRuleUUID == ruleUUID
    end -- end of pairs(ruleSession)

    if found == false then
        return "ErrorUnknownRule"
    end

    -- update count
    local count = sc:getevent(_M.DPF_IPV6_CONNECTION_RULE_COUNT)
    if count and count ~= '' then
        count = tonumber(count)
    else
        count = tonumber('0')
    end
    if count > 0 then
        sc:setevent(_M.DPF_IPV6_CONNECTION_RULE_COUNT, (count-1))
    end
    if restartFirewall then
        sc:setevent('firewall-restart')
    end
    return 'OK'
end

--
-- Get general information about dynamic port forwarding.
--
-- input = CONTEXT
--
-- output = {
--     maxDescriptionLength = NUMBER,
--     maxDynamicSinglePortForwardingRules = NUMBER,
--     maxDynamicPortRangeForwardingRules = NUMBER,
--     maxDynamicIPv6ConnectionRules = NUMBER
-- }
--
function _M.getDynamicPortForwardingGeneralInfo(sc)
    sc:readlock()

    local info = {}
    info.maxDescriptionLength = sc:getinteger('ra_maxruledescription', 0)
    info.maxDynamicSinglePortForwardingRules = sc:getinteger('ra_maxsingleportrules', 0)
    info.maxDynamicPortRangeForwardingRules = sc:getinteger('ra_maxportrangerules', 0)
    info.maxDynamicIPv6ConnectionRules = sc:getinteger('ra_maxipv6rules', 0)

    return info
end

return _M -- return the module
