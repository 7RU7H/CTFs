--
-- 2018 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author: hsulliva $
-- $DateTime: 2020/02/06 14:32:09 $
-- $Id: //depot-irv/lego/branches/wrt_platform_sp1/lego_overlay/proprietary/jnap/lualib/router.lua#10 $
--

-- router.lua - library to configure router state.

local hdk = require('libhdklua')
local util = require('util')
local platform = require('platform')
local device = require('device')
local wirelessap = require('wirelessap')

local _M = {} -- create the module

_M.supportedWANTypes = nil
_M.supportedIPv6WANTypes = nil
_M.supportedWirelessModeSecurities = nil

local function serializeOptionalDHCPServer(ip)
    ip = ip or hdk.ipaddress(0, 0, 0, 0)
    return tostring(ip)
end

local function parseDHCPReservation(sc, index, networkID)
    local macAddress = sc:get_static_host_mac_address(index)
    local abbreviatedDotDecimal = sc:get_static_host_ip_address(index)
    local description = sc:get_static_host_device_name(index)
    return {
        macAddress = hdk.macaddress(macAddress),
        ipAddress = networkID:bitwiseor(util.parseAbbreviatedDotDecimal(abbreviatedDotDecimal)),
        description = description
    }
end

local function serializeDHCPReservation(reservation, lanPrefixLength)
    -- Note: the abbreviate dot-decimal notation is used here for maximum backwards compatibility should the user decide to downgrade
    return tostring(reservation.macAddress)..','..util.serializeAbbreviatedDotDecimal(reservation.ipAddress:hostid(lanPrefixLength))..','..reservation.description
end

local function parseDHCPLeaseTime(lease)
    local amount, units = string.match(lease, '^(%d+)(.)$')
    if amount then
        amount = tonumber(amount)
        if units == 'h' then
            amount = amount * 60
        end
    else
        amount = 0
    end
    return amount
end

local function serializeDHCPLeaseTime(minutes)
    if (minutes % 60) == 0 then
        return tostring(minutes / 60)..'h'
    else
        return tostring(minutes)..'m'
    end
end

local function parseOptionalDHCPServer(server)
    if not server then
        return nil
    end
    local ipaddress = hdk.ipaddress(server)
    if (ipaddress[1] == 0 and
        ipaddress[2] == 0 and
        ipaddress[3] == 0 and
        ipaddress[4] == 0) then
        return nil
    end
    return ipaddress
end

local function parseWANType(wanType)
    if wanType == 'dhcp' then
        return 'DHCP'
    elseif wanType == 'static' then
        return 'Static'
    elseif wanType == 'pppoe' then
        return 'PPPoE'
    elseif wanType == 'pptp' then
        return 'PPTP'
    elseif wanType == 'l2tp' then
        return 'L2TP'
    elseif wanType == 'bridge' then
        return 'Bridge'
    elseif wanType == 'telstra' then
        return 'Telstra'
    elseif wanType == 'wirelessbridge' then
        return 'WirelessBridge'
    elseif wanType == 'wirelessrepeater' then
        return 'WirelessRepeater'
    else
        return 'Invalid'
    end
end

local function parseIPv6WANType(wanType)
    if wanType == 'dhcpv6' then
        return 'DHCPv6'
    elseif wanType == 'automatic' then
        return 'Automatic'
    elseif wanType == 'static' then
        return 'Static'
    elseif wanType == 'pppoe' then
        return 'PPPoE'
    elseif wanType == '6rd' then
        return '6rd Tunnel'
    elseif wanType == 'pass-through' then
        return 'Pass-through'
    else
        return 'Invalid'
    end
end

local function parsePPPConnState(state)
    if state == 'up' then
        return 'Connected'
    elseif state == 'authfail' then
        return 'AuthenticationFailure'
    else
        return 'Connecting'
    end
end

local function conflictsWithVPNAddress(sc, ipAddress, networkPrefixLength)
    if (sc:is_openvpn_supported()) then
        local openvpn = require('openvpn')
        if openvpn.isConflictingAddress(sc, ipAddress, networkPrefixLength) then
            return true
        end
    end
    return false
end

function _M.getSupportedWANTypes(sc)
    if _M.supportedWANTypes == nil then
        sc:readlock()
        _M.supportedWANTypes = {}
        local wanTypes = sc:get_supported_wan_types() -- { 'dhcp static pppoe pptp l2tp bridge wirelessbridge wirelessrepeater' }
        for i = 1, table.getn(wanTypes) do
            local wanType = parseWANType(wanTypes[i])
            if wanType ~= 'Invalid' then
                _M.supportedWANTypes[i] = wanType
            end
        end
    end
    return _M.supportedWANTypes
end

function _M.getSupportedIPv6WANTypes(sc)
    if _M.supportedIPv6WANTypes == nil then
        sc:readlock()
        _M.supportedIPv6WANTypes = {}
        local wanTypes = sc:get_supported_ipv6_wan_types() -- { 'automatic', 'pppoe', 'pass-through' }
        for i = 1, table.getn(wanTypes) do
            local wanType = parseIPv6WANType(wanTypes[i])
            if wanType ~= 'Invalid' then
                _M.supportedIPv6WANTypes[i] = wanType
            end
        end
    end
    return _M.supportedIPv6WANTypes
end

--
-- Get all the current Ethernet port connections.
--
-- input = CONTEXT, OPTIONAL(STRING)
--
-- output = {
--     wanPortConnection = STRING,
--     lanPortConnections = ARRAY_OF(STRING)
-- }
--
function _M.getEthernetPortConnections(sc)
    local function speedMbpsToString(speedMbps)
        assert(type(speedMbps) == 'number')
        if 1000 == speedMbps then
            return '1Gbps'
        elseif 100 == speedMbps then
            return '100Mbps'
        elseif 10 ==  speedMbps then
            return '10Mbps'
        else
            return 'None'
        end
    end

    local wanPortConnection = speedMbpsToString(platform.getWANPortLinkSpeed(sc))
    local lanPortConnections = {}
    for i, port in ipairs(platform.getLANPortLinkSpeeds(sc)) do
        table.insert(lanPortConnections, speedMbpsToString(port.speedMbps))
    end
    return {
        wanPortConnection = wanPortConnection,
        lanPortConnections = lanPortConnections
    }
end

--
-- Get the network ID of the LAN
--
-- input = CONTEXT, IPADDRESS
--
-- output = BOOLEAN
--
function _M.isInRouterSubnet(sc, ipAddress)
    sc:readlock()
    local lanPrefixLength = util.subnetMaskToNetworkPrefixLength(hdk.ipaddress(sc:get_lan_subnet_mask()))
    return hdk.ipaddress(sc:get_lan_ipaddress()):networkid(lanPrefixLength) == ipAddress:networkid(lanPrefixLength)
end

--
-- Get the current LAN settings.
--
-- input = CONTEXT
--
-- output = {
--     ipAddress = IPADDRESS,
--     networkPrefixLength = NUMBER,
--     minNetworkPrefixLength = NUMBER,
--     maxNetworkPrefixLength = NUMBER,
--     hostName = STRING
-- }
--
function _M.getLANSettings(sc)
    sc:readlock()
    return {
        ipAddress = hdk.ipaddress(sc:get_lan_ipaddress()),
        networkPrefixLength = util.subnetMaskToNetworkPrefixLength(hdk.ipaddress(sc:get_lan_subnet_mask())),
        minNetworkPrefixLength = platform.MIN_LAN_PREFIX_LENGTH,
        maxNetworkPrefixLength = platform.MAX_LAN_PREFIX_LENGTH,
        hostName = device.getHostName(sc)
    }
end

--
-- Set the LAN settings.
--
-- input = CONTEXT, {
--     ipAddress = IPADDRESS,
--     networkPrefixLength = NUMBER,
--     hostName = STRING
-- }
--
-- output = STRING, BOOLEAN
--
function _M.setLANSettings(sc, settings)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if not platform.isSupportedLANPrefixLength(settings.networkPrefixLength) then
        return 'ErrorInvalidNetworkPrefixLength'
    end
    if util.isReservedAddress(settings.ipAddress, settings.networkPrefixLength) or
        conflictsWithVPNAddress(sc, settings.ipAddress, settings.networkPrefixLength) then
        return 'ErrorInvalidIPAddress'
    end

    sc:set_lan_ipaddress(tostring(settings.ipAddress))
    sc:set_lan_subnet_mask(tostring(util.networkPrefixLengthToSubnetMask(settings.networkPrefixLength)))
    return device.setHostName(sc, settings.hostName)
end

--
-- Get the current DHCP settings.
--
-- input = CONTEXT
--
-- output = {
--     isDHCPEnabled = BOOLEAN,
--     settings = {
--         leaseMinutes = NUMBER,
--         firstClientIPAddress = IPADDRESS,
--         lastClientIPAddress = IPADDRESS,
--         dnsServer1 = OPTIONAL(IPADDRESS),
--         dnsServer2 = OPTIONAL(IPADDRESS),
--         dnsServer3 = OPTIONAL(IPADDRESS),
--         winsServer = OPTIONAL(IPADDRESS),
--         reservations = ARRAY_OF({
--             macAddress = MACADDRESS,
--             lastOctet = NUMBER,
--             description = STRING
--         })
--     }
-- }
--
function _M.getDHCPSettings(sc)
    sc:readlock()

    local lanPrefixLength = util.subnetMaskToNetworkPrefixLength(hdk.ipaddress(sc:get_lan_subnet_mask()))
    local reservations = {}
    for i = 1, sc:get_dhcp_static_host_count() do
        table.insert(reservations, parseDHCPReservation(sc, i, hdk.ipaddress(sc:get_lan_ipaddress()):networkid(lanPrefixLength)))
    end
    -- The 'dhcp_start' and 'dhcp_end' values use an abbreviated dot-decimal notation for the hostid.
    local networkID = hdk.ipaddress(sc:get_lan_ipaddress()):networkid(lanPrefixLength)
    local dhcpStartHostID = util.parseAbbreviatedDotDecimal(sc:get_dhcp_start_ip_address())
    local dhcpEndHostID
    local dhcp_end = sc:get_dhcp_end_ip_address()
    if '' == dhcp_end then
        -- When dhcp_end is not set, infer it from dhcp_num.
        local dhcp_num = sc:get_dhcp_ip_range_count()
        assert(dhcp_num, ('dhcp_num is not set'))
        dhcpEndHostID = hdk.ipaddress(dhcpStartHostID[1], dhcpStartHostID[2], dhcpStartHostID[3], dhcpStartHostID[4] + sc:get_dhcp_ip_range_count() - 1)
    else
        dhcpEndHostID = util.parseAbbreviatedDotDecimal(dhcp_end)
    end
    return {
        isDHCPEnabled = sc:get_dhcp_enabled(),
        settings = {
            leaseMinutes = parseDHCPLeaseTime(sc:get_dhcp_lease_time()),
            firstClientIPAddress = networkID:bitwiseor(dhcpStartHostID),
            lastClientIPAddress = networkID:bitwiseor(dhcpEndHostID),
            dnsServer1 = parseOptionalDHCPServer(sc:get_dhcp_nameserver(1)),
            dnsServer2 = parseOptionalDHCPServer(sc:get_dhcp_nameserver(2)),
            dnsServer3 = parseOptionalDHCPServer(sc:get_dhcp_nameserver(3)),
            winsServer = parseOptionalDHCPServer(sc:get_dhcp_wins_server()),
            reservations = reservations
        }
    }
end

--
-- Set the DHCP settings.
--
-- input = CONTEXT, {
--     isDHCPEnabled = BOOLEAN,
--     settings = OPTIONAL({
--         leaseMinutes = NUMBER,
--         firstClientIPAddress = IPADDRESS,
--         lastClientIPAddress = IPADDRESS,
--         dnsServer1 = OPTIONAL(IPADDRESS),
--         dnsServer2 = OPTIONAL(IPADDRESS),
--         dnsServer3 = OPTIONAL(IPADDRESS),
--         winsServer = OPTIONAL(IPADDRESS),
--         reservations = ARRAY_OF({
--             macAddress = MACADDRESS,
--             lastOctet = NUMBER,
--             description = STRING
--         })
--     })
-- }
--
-- output = STRING, BOOLEAN
--
function _M.setDHCPSettings(sc, settings)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    if settings.isDHCPEnabled and not settings.settings then
        return 'ErrorMissingDHCPSettings'
    end

    local restartDHCPServer = sc:set_dhcp_enabled(settings.isDHCPEnabled)

    if settings.settings then
        if settings.settings.leaseMinutes < platform.MIN_DHCP_LEASE_MINUTES or
            (platform.MAX_DHCP_LEASE_MINUTES and settings.settings.leaseMinutes > platform.MAX_DHCP_LEASE_MINUTES) then
            return 'ErrorInvalidLeaseMinutes'
        end

        local lanPrefixLength = util.subnetMaskToNetworkPrefixLength(hdk.ipaddress(sc:get_lan_subnet_mask()))
        local routerHostAddress = hdk.ipaddress(sc:get_lan_ipaddress())
        if not util.inSameSubnet(settings.settings.firstClientIPAddress, routerHostAddress, lanPrefixLength) or
            util.isReservedSubnetAddress(settings.settings.firstClientIPAddress, lanPrefixLength) or
            conflictsWithVPNAddress(sc, settings.settings.firstClientIPAddress, lanPrefixLength) then
            return 'ErrorInvalidFirstClientIPAddress'
        end

        if not util.inSameSubnet(settings.settings.lastClientIPAddress, routerHostAddress, lanPrefixLength) or
            util.isReservedSubnetAddress(settings.settings.lastClientIPAddress, lanPrefixLength) or
            not (settings.settings.firstClientIPAddress <= settings.settings.lastClientIPAddress) or
            conflictsWithVPNAddress(sc, settings.settings.lastClientIPAddress, lanPrefixLength) then
            return 'ErrorInvalidLastClientIPAddress'
        end

        if settings.settings.dnsServer1 and util.isReservedAddress(settings.settings.dnsServer1) then
            return 'ErrorInvalidPrimaryDNSServer'
        end
        if settings.settings.dnsServer2 and util.isReservedAddress(settings.settings.dnsServer2) then
            return 'ErrorInvalidSecondaryDNSServer'
        end
        if settings.settings.dnsServer3 and util.isReservedAddress(settings.settings.dnsServer3) then
            return 'ErrorInvalidTertiaryDNSServer'
        end

        if settings.settings.winsServer and util.isReservedAddress(settings.settings.winsServer) then
            return 'ErrorInvalidWINSServer'
        end

        restartDHCPServer = sc:set_dhcp_lease_time(serializeDHCPLeaseTime(settings.settings.leaseMinutes)) or restartDHCPServer

        local oldReservationCount = sc:get_dhcp_static_host_count()

        -- Note: the abbreviate dot-decimal notation is used here for maximum backwards compatibility should the user decide to downgrade
        restartDHCPServer = sc:set_dhcp_start_ip_address(util.serializeAbbreviatedDotDecimal(settings.settings.firstClientIPAddress:hostid(lanPrefixLength))) or restartDHCPServer
        restartDHCPServer = sc:set_dhcp_end_ip_address(util.serializeAbbreviatedDotDecimal(settings.settings.lastClientIPAddress:hostid(lanPrefixLength))) or restartDHCPServer

        -- Set 'dhcp_num' as well, for compatibility purposes only.
        local maxClients = util.ipAddressToInteger(settings.settings.lastClientIPAddress) - util.ipAddressToInteger(settings.settings.firstClientIPAddress) + 1
        restartDHCPServer = sc:set_dhcp_ip_range_count(maxClients) or restartDHCPServer

        restartDHCPServer = sc:set_dhcp_nameserver(1, serializeOptionalDHCPServer(settings.settings.dnsServer1)) or restartDHCPServer
        restartDHCPServer = sc:set_dhcp_nameserver(2, serializeOptionalDHCPServer(settings.settings.dnsServer2)) or restartDHCPServer
        restartDHCPServer = sc:set_dhcp_nameserver(3, serializeOptionalDHCPServer(settings.settings.dnsServer3)) or restartDHCPServer
        restartDHCPServer = sc:set_dhcp_wins_server(serializeOptionalDHCPServer(settings.settings.winsServer)) or restartDHCPServer

        local macs = {}
        local reservedIPAddressSet = {}
        restartDHCPServer = sc:set_dhcp_static_host_count(#settings.settings.reservations) or restartDHCPServer
        for i, reservation in ipairs(settings.settings.reservations) do
            if reservation.macAddress:iszero() then
                return 'ErrorInvalidReservationMACAddress'
            end
            local mac = tostring(reservation.macAddress)
            if macs[mac] then
                return 'ErrorReservationsOverlap'
            end
            macs[mac] = true
            if not util.inSameSubnet(reservation.ipAddress, routerHostAddress, lanPrefixLength) or
                util.isReservedSubnetAddress(reservation.ipAddress, lanPrefixLength) or
                (routerHostAddress == reservation.ipAddress) then
                return 'ErrorInvalidReservationIPAddress'
            end

            if reservedIPAddressSet[tostring(reservation.ipAddress)] then
                return 'ErrorReservationsOverlap'
            end

            if #reservation.description > platform.MAX_DHCP_RESERVATION_DESCRIPTION_LENGTH then
                return 'ErrorReservationDescriptionTooLong'
            end

            if #reservation.description > 0 then
                if not util.isValidHostNameLabel(reservation.description, true) then
                    return 'ErrorReservationDescriptionInvalid'
                end
            end

            reservedIPAddressSet[tostring(reservation.ipAddress)] = true
            restartDHCPServer = sc:set_static_host(i, tostring(reservation.macAddress), util.serializeAbbreviatedDotDecimal(reservation.ipAddress:hostid(lanPrefixLength)), reservation.description) or restartDHCPServer
        end
    end

    return nil, restartDHCPServer
end

--
-- Get the current IPv6 settings.
--
-- input = CONTEXT
--
-- output = {
--     isIPv6AutomaticEnabled = BOOLEAN,
--     ipv6rdTunnelMode = OPTIONAL(STRING),
--     ipv6rdTunnelSettings = OPTIONAL({
--         prefix = IPV6ADDRESS,
--         prefixLength = NUMBER,
--         borderRelay = IPADDRESS,
--         borderRelayPrefixLength = NUMBER
--     })
-- }
--
function _M.getIPv6Settings(sc)
    sc:readlock()
    local isIPv6AutomaticEnabled = sc:get_wan_ipv6_automatic()
    local ipv6rdTunnelMode, ipv6rdTunnelSettings
    if not isIPv6AutomaticEnabled then
        if sc:get_wan_6rd_enabled() then
            if sc:get_wan_6rd_automatic() then
                ipv6rdTunnelMode = 'Automatic'
            else
                ipv6rdTunnelMode = 'Manual'
                ipv6rdTunnelSettings = {
                    prefix = hdk.ipv6address(sc:get_wan_6rd_zone()),
                    prefixLength = sc:get_wan_6rd_zone_length(),
                    borderRelay = hdk.ipaddress(sc:get_wan_6rd_relay()),
                    borderRelayPrefixLength = sc:get_wan_6rd_relay_prefix_length()
                }
            end
        else
            ipv6rdTunnelMode = 'Disabled'
        end
    end
    return {
        isIPv6AutomaticEnabled = isIPv6AutomaticEnabled,
        ipv6rdTunnelMode = ipv6rdTunnelMode,
        ipv6rdTunnelSettings = ipv6rdTunnelSettings
    }
end

function _M.getIPv6Settings2(sc)
    sc:readlock()
    local wanType, ipv6AutomaticSettings

    if sc:get_wan_ipv6_passthrough_enable() then
        wanType = 'Pass-through'
    elseif sc:get_wan_ipv6_pppoe_enable() then
        wanType = 'PPPoE'
    else
        wanType = 'Automatic'
        ipv6AutomaticSettings = _M.getIPv6Settings(sc)
    end

    return {
        wanType = wanType,
        ipv6AutomaticSettings = ipv6AutomaticSettings
    }
end

--
-- Set the current IPv6 settings.
--
-- input = CONTEXT, {
--     isIPv6AutomaticEnabled = BOOLEAN,
--     ipv6rdTunnelMode = OPTIONAL(STRING),
--     ipv6rdTunnelSettings = OPTIONAL({
--         prefix = IPV6ADDRESS,
--         prefixLength = NUMBER,
--         borderRelay = IPADDRESS,
--         borderRelayPrefixLength = NUMBER
--     })
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorMissingIPv6rdTunnelMode',
--     'ErrorMissingIPv6rdTunnelSettings',
--     'ErrorSuperfluousIPv6rdTunnelMode',
--     'ErrorSuperfluousIPv6rdTunnelSettings',
--     'ErrorInvalidPrefix',
--     'ErrorInvalidPrefixLength',
--     'ErrorInvalidBorderRelay',
--     'ErrorInvalidBorderRelayPrefixLength'
-- )
--
function _M.setIPv6Settings(sc, settings)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    local isIPv6Auto = settings.isIPv6AutomaticEnabled
    local is6rdEnabled, is6rdAuto = false, false

    if isIPv6Auto then

        if settings.ipv6rdTunnelMode then
            return 'ErrorSuperfluousIPv6rdTunnelMode'
        end

        if settings.ipv6rdTunnelSettings then
            return 'ErrorSuperfluousIPv6rdTunnelSettings'
        end
    else
        if not settings.ipv6rdTunnelMode then
            return 'ErrorMissingIPv6rdTunnelMode'
        end

        if settings.ipv6rdTunnelMode == 'Manual' then
            if not settings.ipv6rdTunnelSettings then
                return 'ErrorMissingIPv6rdTunnelSettings'
            end

            is6rdEnabled = true

            if (settings.ipv6rdTunnelSettings.prefixLength < 0 or
                settings.ipv6rdTunnelSettings.prefixLength > 64) then
                return 'ErrorInvalidPrefixLength'
            end
            sc:set_wan_6rd_zone_length(settings.ipv6rdTunnelSettings.prefixLength)

            if not util.isValidIPv6Prefix(settings.ipv6rdTunnelSettings.prefix, settings.ipv6rdTunnelSettings.prefixLength) then
                return 'ErrorInvalidPrefix'
            end
            sc:set_wan_6rd_zone(tostring(settings.ipv6rdTunnelSettings.prefix))

            if settings.ipv6rdTunnelSettings.borderRelay:iszero() then
                return 'ErrorInvalidBorderRelay'
            end
            sc:set_wan_6rd_relay(tostring(settings.ipv6rdTunnelSettings.borderRelay))

            if not util.isValidNetworkPrefixLength(settings.ipv6rdTunnelSettings.borderRelayPrefixLength) or
                (settings.ipv6rdTunnelSettings.borderRelayPrefixLength > settings.ipv6rdTunnelSettings.prefixLength) or
                (settings.ipv6rdTunnelSettings.prefixLength - settings.ipv6rdTunnelSettings.borderRelayPrefixLength > 32) then
                return 'ErrorInvalidBorderRelayPrefixLength'
            end
            sc:set_wan_6rd_relay_prefix_length(settings.ipv6rdTunnelSettings.borderRelayPrefixLength)
        else
            if settings.ipv6rdTunnelSettings then
                return 'ErrorSuperfluousIPv6rdTunnelSettings'
            end

            if settings.ipv6rdTunnelMode == 'Automatic' then
                is6rdEnabled = true
                is6rdAuto = true
            end
        end
    end

    sc:set_wan_ipv6_enabled(isIPv6Auto or is6rdEnabled or settings.isIPv6Enabled)
    sc:set_wan_ipv6_automatic(isIPv6Auto)
    sc:set_wan_dhcpv6c_enabled(isIPv6Auto and 3 or 0)
    sc:set_router_adv_provisioning_enabled(isIPv6Auto)

    -- all paths in the traditional webui seem to end with ipv6_static_enable set to 0
    sc:set_wan_ipv6_static_enabled(false)

    sc:set_wan_6rd_enabled(is6rdEnabled)
    sc:set_wan_6rd_automatic(is6rdAuto)
end

function _M.setIPv6Settings2(sc, settings)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    if settings.wanType ~= 'Automatic' then
        if settings.ipv6AutomaticSettings then
            return 'ErrorSuperfluousIPv6AutomaticSettings'
        end
        settings.ipv6AutomaticSettings = {
            isIPv6Enabled = true, -- this value is used by _M.setIPv6Settings to enable the IPv6
            isIPv6AutomaticEnabled = settings.wanType == 'PPPoE',
            ipv6rdTunnelMode = settings.wanType ~= 'PPPoE' and 'Disabled' or nil
        }
    else
        if not settings.ipv6AutomaticSettings then
            return 'ErrorMissingIPv6AutomaticSettings'
        end
    end

    local  error = _M.setIPv6Settings(sc, settings.ipv6AutomaticSettings)
    if error then
        return error
    end

    sc:set_wan_ipv6_passthrough_enable(settings.wanType == 'Pass-through')
    sc:set_wan_dhcpv6s_enabled(settings.wanType ~= 'Pass-through')
    sc:set_wan_router_adv_enabled(settings.wanType ~= 'Pass-through')
    sc:set_wan_ipv6_pppoe_enable(settings.wanType == 'PPPoE')
end

--
-- Get the DHCPv6 DUID.
--
-- input = CONTEXT
--
-- output = STRING
--
function _M.getDUID(sc)
    sc:readlock()
    return sc:get_dhcpv6_duid()
end

--
-- Get the WAN Type. Version 2 includes WirelessBridge and WirelessRepeater
--
-- input = CONTEXT, NUMBER
--
-- output = STRING, BOOLEAN
--
local function getWANType(sc, version)
    local wanProto = sc:get_wan_protocol()
    local bridgeMode = sc:get_bridge_mode()
    if bridgeMode ~= 0 then
        local wanType = 'Bridge'
        if version and version == 2 then
            local wanTypesArray = util.arrayToSet(_M.getSupportedWANTypes(sc))
            if (wanTypesArray['WirelessRepeater'] or wanTypesArray['WirelessBridge']) then
                local wifiBridgeMode = sc:get_wifibridge_mode()
                if wifiBridgeMode ~= 0 then
                    wanType = wifiBridgeMode == 1 and 'WirelessBridge' or 'WirelessRepeater'
                end
            end
        end
        return wanType, bridgeMode == 2
    elseif wanProto == 'dhcp' then
        return 'DHCP'
    elseif wanProto == 'static' then
        return 'Static', true
    elseif wanProto == 'pppoe' then
        return 'PPPoE'
    elseif wanProto == 'pptp' then
        return 'PPTP', sc:get_wan_pptp_address_static()
    elseif wanProto == 'l2tp' then
        return 'L2TP', sc:get_wan_l2tp_address_static()
    elseif wanProto == 'telstra' then
        return 'Telstra'
    end

    -- If we are in 'power modem' mode, then set the wan type to 'DHCP'
    -- otherwise return ''
    if sc:get_modem_enabled() then
        return 'DHCP'
    end

    return ''
end

local function getRouterStaticSettings(sc)
       return {
           ipAddress = util.addressify(sc:get_wan_static_ipaddress()),
           networkPrefixLength = util.subnetMaskToNetworkPrefixLength(util.addressify(sc:get_wan_static_subnet())),
           gateway = util.addressify(sc:get_wan_static_gatewayip()),
           dnsServer1 = util.addressify(sc:get_wan_static_dns(1)),
           dnsServer2 = util.addressify(sc:get_wan_static_dns(2)),
           dnsServer3 = util.addressify(sc:get_wan_static_dns(3)),
           domainName = sc:get_lan_domain()
        }
end

local function getBridgeStaticSettings(sc)
        return {
                ipAddress = util.addressify(sc:get_bridge_static_ipaddress()),
                networkPrefixLength = util.subnetMaskToNetworkPrefixLength(util.addressify(sc:get_bridge_static_subnet())),
                gateway = util.addressify(sc:get_bridge_static_gatewayip()),
                dnsServer1 = util.addressify(sc:get_bridge_static_dns(1)),
                dnsServer2 = util.addressify(sc:get_bridge_static_dns(2)),
                dnsServer3 = util.addressify(sc:get_bridge_static_dns(3)),
                domainName = sc:get_bridge_domain()
        }
end

--
-- output = {
--     IPADDRESS,
--     NUMBER,
--     IPADDRESS,
--     IPADDRESS,
--     IPADDRESS,
--     IPADDRESS,
--     STRING
-- }
--
local function getWANStaticSettings(sc, keys)
        if keys == 'Bridge' then
            return getBridgeStaticSettings(sc)
        end

        return getRouterStaticSettings(sc)
end

local function checkStaticSettings(settings)
    if settings then
        if not platform.isAllowedNetworkPrefixLength(settings.networkPrefixLength) then
            return 'ErrorInvalidNetworkPrefixLength'
        end

        if util.isReservedAddress(settings.ipAddress, settings.networkPrefixLength) then
            return 'ErrorInvalidIPAddress'
        end
        if util.isReservedAddress(settings.gateway, settings.networkPrefixLength) then
            return 'ErrorInvalidGateway'
        end
        if not util.inSameSubnet(settings.ipAddress, settings.gateway, settings.networkPrefixLength) then
            return 'ErrorInvalidGateway'
        end
        if settings.ipAddress == settings.gateway then
            return 'ErrorInvalidGateway'
        end

        if util.isReservedAddress(settings.dnsServer1) then
            return 'ErrorInvalidPrimaryDNSServer'
        end
        if settings.dnsServer2 and util.isReservedAddress(settings.dnsServer2) then
            return 'ErrorInvalidSecondaryDNSServer'
        end
        if settings.dnsServer3 and util.isReservedAddress(settings.dnsServer3) then
            return 'ErrorInvalidTertiaryDNSServer'
        end

        if settings.domainName ~= nil and not util.isValidHostName(settings.domainName) then
            return 'ErrorInvalidDomainName'
        end
        end
end

local function setBridgeStaticSettings(sc, settings)
        local result

        result = checkStaticSettings(settings)
        if result then
            return result
        end

        sc:set_bridge_static_dns(1, tostring(settings.dnsServer1))
        sc:set_bridge_static_dns(2, settings.dnsServer2 and tostring(settings.dnsServer2) or nil)
        sc:set_bridge_static_dns(3, settings.dnsServer3 and tostring(settings.dnsServer3) or nil)

        sc:set_bridge_static_ipaddress(tostring(settings.ipAddress))
        sc:set_bridge_static_subnet(tostring(util.networkPrefixLengthToSubnetMask(settings.networkPrefixLength)))
        sc:set_bridge_static_gatewayip(tostring(settings.gateway))

        if (settings.domainName and sc:set_bridge_domain(settings.domainName)) or
        (not settings.domainName and sc:set_bridge_domain(nil)) then
            -- Clear the dhcp-assigned domain name
            sc:set_wan_dhcp_domain('')
        end

end

local function setWANStaticSettings(sc, settings)
        local result

        result = checkStaticSettings(settings)
        if result then
            return result
        end

        sc:set_wan_static_dns(1, tostring(settings.dnsServer1))
        sc:set_wan_static_dns(2, settings.dnsServer2 and tostring(settings.dnsServer2) or nil)
        sc:set_wan_static_dns(3, settings.dnsServer3 and tostring(settings.dnsServer3) or nil)

        sc:set_wan_static_ipaddress(tostring(settings.ipAddress))
        sc:set_wan_static_subnet(tostring(util.networkPrefixLengthToSubnetMask(settings.networkPrefixLength)))
        sc:set_wan_static_gatewayip(tostring(settings.gateway))

        if (settings.domainName and sc:set_lan_domain(settings.domainName)) or
        (not settings.domainName and sc:set_lan_domain(nil)) then
            -- Clear the dhcp-assigned domain name
            sc:set_wan_dhcp_domain('')
        end
end

local syscfgStaticKeys = {
    ipaddr = 'wan_ipaddr',
    netmask = 'wan_netmask',
    gateway = 'wan_default_gateway',
    dns1 = 'nameserver1',
    dns2 = 'nameserver2',
    dns3 = 'nameserver3',
    event = 'wan-restart'
}

local syscfgStaticBridgeKeys = {
    ipaddr = 'bridge_ipaddr',
    netmask = 'bridge_netmask',
    gateway = 'bridge_default_gateway',
    dns1 = 'bridge_nameserver1',
    dns2 = 'bridge_nameserver2',
    dns3 = 'bridge_nameserver3',
    event = 'forwarding-restart'
}

function _M.GetMTURange(wanType)
    if wanType == 'DHCP' or wanType == 'Static' then
        return 576, 1500
    elseif wanType == 'PPPoE' then
        return 576, 1492
    elseif wanType == 'PPTP' then
        return 576, 1464
    elseif wanType == 'L2TP' then
        return 576, 1460
    end
end

--
-- Get the current WAN settings.
--
-- input = CONTEXT, NUMBER
--
-- output = {
--     wanType = STRING,
--     pppoeSettings = OPTIONAL({
--         username = STRING,
--         password = STRING,
--         serviceName = STRING,
--         behavior = STRING,
--         maxIdleMinutes = OPTIONAL(NUMBER),
--         reconnectAfterSeconds = OPTIONAL(NUMBER)
--     }),
--     tpSettings = OPTIONAL({
--         useStaticSettings = BOOLEAN,
--         staticSettings = OPTIONAL({
--             ipAddress = IPADDRESS,
--             networkPrefixLength = NUMBER,
--             gateway = IPADDRESS,
--             dnsServer1 = IPADDRESS,
--             dnsServer2 = OPTIONAL(IPADDRESS),
--             dnsServer3 = OPTIONAL(IPADDRESS)
--         }),
--         server = IPADDRESS,
--         username = STRING,
--         password = STRING,
--         behavior = STRING,
--         maxIdleMinutes = OPTIONAL(NUMBER),
--         reconnectAfterSeconds = OPTIONAL(NUMBER)
--     }),
--     telstraSettings = OPTIONAL({
--         server = STRING,
--         username = STRING,
--         password = STRING
--     }),
--     staticSettings = OPTIONAL({
--         ipAddress = IPADDRESS,
--         networkPrefixLength = NUMBER,
--         gateway = IPADDRESS,
--         dnsServer1 = IPADDRESS,
--         dnsServer2 = OPTIONAL(IPADDRESS),
--         dnsServer3 = OPTIONAL(IPADDRESS)
--     }),
--     bridgeSettings = OPTIONAL({
--         useStaticSettings = BOOLEAN,
--         staticSettings = OPTIONAL({
--             ipAddress = IPADDRESS,
--             networkPrefixLength = NUMBER,
--             gateway = IPADDRESS,
--             dnsServer1 = IPADDRESS,
--             dnsServer2 = OPTIONAL(IPADDRESS),
--             dnsServer3 = OPTIONAL(IPADDRESS)
--         })
--     }),
--     domainName = OPTIONAL(STRING),
--     mtu = NUMBER
-- }
--
function _M.getWANSettings(sc, version)
    sc:readlock()
    if not platform.isReady(sc) then
        return nil, '_ErrorNotReady'
    end
    local getConnectBehavior = function(sc)
        if sc:get_wan_connection_method() == 'demand' then
            return 'ConnectOnDemand'
        end
        return 'KeepAlive'
    end
    local getShared = function(sc)
        return {
            username = sc:get_wan_protocol_username(),
            password = sc:get_wan_protocol_password(),
            behavior = getConnectBehavior(sc),
            maxIdleMinutes = sc:get_wan_protocol_idle_time(),
            reconnectAfterSeconds = sc:get_wan_protocol_reconnect_interval()
        }
    end
    local getBridgeSettings = function(sc, useStaticSettings)
        local bridgeSettings = {
            useStaticSettings = useStaticSettings
        }
        if useStaticSettings then
            bridgeSettings.staticSettings = getWANStaticSettings(sc, 'Bridge')
        end
        return bridgeSettings
    end

    local settings = {}

    local wanType, useStaticSettings = getWANType(sc, (version and version == 1) and 1 or 2)

    settings.wanType = wanType

    if wanType == 'Bridge' then
        settings.bridgeSettings = getBridgeSettings(sc, useStaticSettings)

    elseif wanType == 'Static' then
        assert(useStaticSettings)
        settings.staticSettings = getWANStaticSettings(sc, 'Router')

    elseif wanType == 'PPPoE' then
        settings.pppoeSettings = getShared(sc)
        settings.pppoeSettings.serviceName = sc:get_wan_pppoe_service_name()

    elseif wanType == 'PPTP' or wanType == 'L2TP' then
        settings.tpSettings = getShared(sc)
        settings.tpSettings.server = hdk.ipaddress(sc:get_wan_pptp_server_address())
        settings.tpSettings.useStaticSettings = useStaticSettings
        if useStaticSettings then
            settings.tpSettings.staticSettings = getWANStaticSettings(sc, 'Router')
        end

    elseif wanType == 'Telstra' then
        settings.telstraSettings = {
            server = hdk.ipaddress(sc:get_wan_telstra_server_address()),
            username = sc:get_wan_protocol_username(),
            password = sc:get_wan_protocol_password()
        }

    elseif version and version >= 2 and (wanType == 'WirelessBridge' or wanType == 'WirelessRepeater') then
        settings.wirelessModeSettings = {
            ssid = sc:get_wifibridge_ssid(),
            band = sc:get_wifibridge_device(),
            security = wirelessap.parseWirelessSecurity(sc:get_wifibridge_security_type()),
            password = sc:get_wifibridge_passphrase()
        }
        local bssid = sc:get_wifibridge_bssid()
        if #bssid > 0 then
            settings.wirelessModeSettings.bssid = hdk.macaddress(bssid)
        end
        settings.bridgeSettings = getBridgeSettings(sc, useStaticSettings)
    end

    -- Use 'router_dns_domain' for all statically set configurations when set, otherwise use the DHCP-assigned 'dhcp_domain' value
    if useStaticSettings then
        settings.domainName = sc:get_wan_static_dns_domain()
    else
        settings.domainName = sc:get_wan_dhcp_domain()
    end
    settings.mtu = sc:get_wan_mtu()

    -- Adds support for WAN port VLAN tagging
    if version and version >= 3 and sc:is_wan_vlan_tagging_supported() then
        local wanTaggingSettings = {}
        local vltSettings = {}

        wanTaggingSettings.isEnabled = sc:get_wan_vlan_tagging_enabled()
        vltSettings.vlanStatus = require('vlantagging').parseTaggingStatus(sc:get_wan_vlan_tagging_status())
        vltSettings.vlanID = sc:get_wan_vlan_id()
        vltSettings.vlanPriority = sc:get_wan_vlan_priority()
        if version >= 4 then
            wanTaggingSettings.vlanLowerLimit = sc:get_wan_vlan_lower_limit()
            wanTaggingSettings.vlanUpperLimit = sc:get_wan_vlan_upper_limit()
        end
        wanTaggingSettings.vlanTaggingSettings = vltSettings
        settings.wanTaggingSettings = wanTaggingSettings
    end

    return settings
end

--
-- Set the current WAN settings.
--
-- input = CONTEXT, {
--     wanType = STRING,
--     pppoeSettings = OPTIONAL({
--         username = STRING,
--         password = STRING,
--         serviceName = STRING,
--         behavior = STRING,
--         maxIdleMinutes = OPTIONAL(NUMBER),
--         reconnectAfterSeconds = OPTIONAL(NUMBER)
--     }),
--     tpSettings = OPTIONAL({
--         useStaticSettings = BOOLEAN,
--         staticSettings = OPTIONAL({
--             ipAddress = IPADDRESS,
--             networkPrefixLength = NUMBER,
--             gateway = IPADDRESS,
--             dnsServer1 = IPADDRESS,
--             dnsServer2 = OPTIONAL(IPADDRESS),
--             dnsServer3 = OPTIONAL(IPADDRESS),
--             domainName = OPTIONAL(STRING)
--         }),
--         server = IPADDRESS,
--         username = STRING,
--         password = STRING,
--         behavior = STRING,
--         maxIdleMinutes = OPTIONAL(NUMBER),
--         reconnectAfterSeconds = OPTIONAL(NUMBER)
--     }),
--     telstraSettings = OPTIONAL({
--         server = STRING,
--         username = STRING,
--         password = STRING
--     }),
--     staticSettings = OPTIONAL({
--         ipAddress = IPADDRESS,
--         networkPrefixLength = NUMBER,
--         gateway = IPADDRESS,
--         dnsServer1 = IPADDRESS,
--         dnsServer2 = OPTIONAL(IPADDRESS),
--         dnsServer3 = OPTIONAL(IPADDRESS),
--         domainName = OPTIONAL(STRING)
--     }),
--     bridgeSettings = OPTIONAL({
--         useStaticSettings = BOOLEAN,
--         staticSettings = OPTIONAL({
--             ipAddress = IPADDRESS,
--             networkPrefixLength = NUMBER,
--             gateway = IPADDRESS,
--             dnsServer1 = IPADDRESS,
--             dnsServer2 = OPTIONAL(IPADDRESS),
--             dnsServer3 = OPTIONAL(IPADDRESS),
--             domainName = OPTIONAL(STRING)
--         })
--     }),
--     mtu = NUMBER
-- }
--
function _M.setWANSettings(sc, settings)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if settings.staticSettings and settings.wanType ~= 'Static' then
        return 'ErrorSuperfluousStaticSettings'
    end
    if settings.pppoeSettings and settings.wanType ~= 'PPPoE' then
        return 'ErrorSuperfluousPPPoESettings'
    end
    if settings.tpSettings and settings.wanType ~= 'PPTP' and settings.wanType ~= 'L2TP' then
        return 'ErrorSuperfluousTPSettings'
    end
    if settings.bridgeSettings and settings.wanType ~= 'Bridge' then
        return 'ErrorSuperfluousBridgeSettings'
    end
    if settings.telstraSettings and settings.wanType ~= 'Telstra' then
        return 'ErrorSuperfluousTelstraSettings'
    end

    local setProto = function(sc, proto)
        sc:set_bridge_mode(0)
        if sc:set_wan_protocol(proto) then
            return true
        end
    end
    local setPPPConfig = function(sc, settings)
        if not util.isValidPPPAuthenticationPeerID(settings.username) then
            return 'ErrorInvalidUsername'
        end
        if not util.isValidPPPAuthenticationPassword(settings.password) then
            return 'ErrorInvalidPassword'
        end

        sc:set_wan_protocol_username(settings.username)
        sc:set_wan_protocol_password(settings.password)
        if settings.behavior == 'ConnectOnDemand' then
            sc:set_wan_connection_method('demand')
            if not settings.maxIdleMinutes or ((settings.maxIdleMinutes < 1) or (settings.maxIdleMinutes > 9999)) then
                return 'ErrorInvalidMaxIdleMinutes'
            end
            sc:set_wan_protocol_idle_time(settings.maxIdleMinutes)
        else
            sc:set_wan_connection_method('redial')
            if not settings.reconnectAfterSeconds or ((settings.reconnectAfterSeconds < 20) or (settings.reconnectAfterSeconds > 180)) then
                return 'ErrorInvalidReconnectAfterSeconds'
            end
            sc:set_wan_protocol_reconnect_interval(settings.reconnectAfterSeconds)
        end
    end

    if not util.arrayToSet(_M.getSupportedWANTypes(sc))[settings.wanType] then
        return 'ErrorUnsupportedWANType'
    end

    local result

    if settings.wanType == 'DHCP' then
        setProto(sc, 'dhcp')

    elseif settings.wanType == 'Static' then
        if settings.staticSettings == nil then
            return 'ErrorMissingStaticSettings'
        end

        setProto(sc, 'static')
        result = setWANStaticSettings(sc, settings.staticSettings)
        if result then
            return result
        end

    elseif settings.wanType == 'PPPoE' then
        if settings.pppoeSettings == nil then
            return 'ErrorMissingPPPoESettings'
        end

        setProto(sc, 'pppoe')
        result = setPPPConfig(sc, settings.pppoeSettings)
        if result then
            return result
        end
        if not util.isValidPPPoEServiceName(settings.pppoeSettings.serviceName) then
            return 'ErrorInvalidServiceName'
        end
        sc:set_wan_pppoe_service_name(settings.pppoeSettings.serviceName)

    elseif settings.wanType == 'PPTP' or settings.wanType == 'L2TP' then
        if settings.tpSettings == nil then
            return 'ErrorMissingTPSettings'
        end
        if util.isReservedAddress(settings.tpSettings.server) then
            return 'ErrorInvalidServer'
        end

        local proto = settings.wanType:lower()
        setProto(sc, proto)
        result = setPPPConfig(sc, settings.tpSettings)
        if result then
            return result
        end
        sc:set_wan_pptp_server_address(tostring(settings.tpSettings.server))
        if settings.wanType == 'PPTP' then
            sc:set_wan_pptp_address_static(settings.tpSettings.useStaticSettings)
        else
            sc:set_wan_l2tp_address_static(settings.tpSettings.useStaticSettings)
        end

        if settings.tpSettings.useStaticSettings then
            if settings.tpSettings.staticSettings == nil then
                return 'ErrorMissingStaticSettings'
            end
            result = setWANStaticSettings(sc, settings.tpSettings.staticSettings)
            if result then
                return result
            end
        elseif settings.tpSettings.staticSettings then
            return 'ErrorSuperfluousStaticSettings'
        end

    elseif settings.wanType == 'Bridge' then
        if settings.bridgeSettings == nil then
            return 'ErrorMissingBridgeSettings'
        end

        local bridgeRestart = false
        local bridgeMode = sc:get_bridge_mode()

        if settings.bridgeSettings.useStaticSettings then
            if settings.bridgeSettings.staticSettings == nil then
                return 'ErrorMissingStaticSettings'
            end

            sc:set_bridge_mode(2)
            bridgeRestart = true;
            result = setBridgeStaticSettings(sc, settings.bridgeSettings.staticSettings)
            if result then
                return result
            end
        else
            if settings.bridgeSettings.staticSettings then
                return 'ErrorSuperfluousStaticSettings'
            end
            bridgeRestart = true
            sc:set_bridge_mode(1)
        end

        if bridgeRestart then
            require('device').setRemoteUIEnabled(sc, false) -- remote ui cannot be enabled in bridge mode --
        end

    elseif settings.wanType == 'Telstra' then
        if settings.telstraSettings == nil then
            return 'ErrorMissingTelstraSettings'
        end
        if util.isReservedAddress(settings.telstraSettings.server) then
            return 'ErrorInvalidServer'
        end
        if not util.isValidPPPAuthenticationPeerID(settings.telstraSettings.username) then
            return 'ErrorInvalidUsername'
        end
        if not util.isValidPPPAuthenticationPassword(settings.telstraSettings.password) then
            return 'ErrorInvalidPassword'
        end

        setProto(sc, 'telstra')

        sc:set_wan_telstra_server_address(tostring(settings.telstraSettings.server))
        sc:set_wan_protocol_username(settings.telstraSettings.username)
        sc:set_wan_protocol_password(settings.telstraSettings.password)

    else
        return 'ErrorUnsupportedWANType'
    end

    local mtuMin, mtuMax = _M.GetMTURange(settings.wanType)
    if settings.mtu ~= 0 and (mtuMin == nil or settings.mtu < mtuMin or settings.mtu > mtuMax) then
        return 'ErrorInvalidMTU'
    end
    sc:set_wan_mtu(settings.mtu)
end

local function getStaticRouteEntries(sc)
    local entries = {}
    for i = 1, sc:get_static_route_count() do
            local gw = sc:get_static_route_gateway(i)
            local gateway = gw and hdk.ipaddress(gw)
            table.insert(entries, {
                name = sc:get_static_route_name(i),
                settings = {
                    interface = (sc:get_static_route_interface(i) == 'wan') and 'Internet' or 'LAN',
                    destinationLAN = hdk.ipaddress(sc:get_static_route_lan_destination(i)),
                    networkPrefixLength = util.subnetMaskToNetworkPrefixLength(hdk.ipaddress(sc:get_static_route_subnet_mask(i))),
                    gateway = (gateway and not gateway:iszero()) and gateway or nil -- treat 0.0.0.0 the same as nil
                }
            })
    end

    return entries
end

local function setStaticRouteEntries(sc, entries)
    if #entries > platform.MAX_STATIC_ROUTE_ENTRIES then
        return 'ErrorTooManyEntries'
    end

    sc:set_static_route_count(#entries)

    local names = {}
    for i, entry in ipairs(entries) do
        if names[entry.name] then
            return 'ErrorDuplicateEntryName'
        end
        names[entry.name] = true

        local useWANInterface = (entry.settings.interface == 'Internet')

        if not platform.isAllowedNetworkPrefixLength(entry.settings.networkPrefixLength) then
            return 'ErrorInvalidNetworkPrefixLength'
        end

        if util.isReservedAddress(entry.settings.destinationLAN) then
            return 'ErrorInvalidIPAddress'
        end

        local lanPrefixLength = util.subnetMaskToNetworkPrefixLength(hdk.ipaddress(sc:get_lan_subnet_mask()))
        local routerHostAddress = hdk.ipaddress(sc:get_lan_ipaddress())

        if not useWANInterface then
            -- LAN entry

            -- Gateway is required
            if not entry.settings.gateway then
                return 'ErrorInvalidGateway'
            end

            -- Gateway must be in local subnet and not a reserved subnet address.
            if not util.inSameSubnet(entry.settings.gateway, routerHostAddress, lanPrefixLength) or
                util.isReservedSubnetAddress(entry.settings.gateway, lanPrefixLength) then
                return 'ErrorInvalidGateway'
            end
        else
            -- WAN entry
            if entry.settings.gateway then
                -- Gateway must not be in local subnet
                if util.isReservedAddress(entry.settings.gateway) or
                    util.inSameSubnet(entry.settings.gateway, routerHostAddress, lanPrefixLength) then
                    return 'ErrorInvalidGateway'
                end
            end
        end

        sc:set_static_route_name(i, entry.name)
        sc:set_static_route_interface(i, useWANInterface and 'wan' or 'lan')
        sc:set_static_route_lan_destination(i, tostring(entry.settings.destinationLAN))
        sc:set_static_route_subnet_mask(i, tostring(util.networkPrefixLengthToSubnetMask(entry.settings.networkPrefixLength)))
        if entry.settings.gateway then
            sc:set_static_route_gateway(i, tostring(entry.settings.gateway))
        else
            sc:set_static_route_gateway(i, nil)
        end
    end

end

--
-- Get the current routing settings.
--
-- input = CONTEXT
--
-- output = {
--     isNATEnabled = BOOLEAN,
--     isDynamicRoutingEnabled = BOOLEAN,
--     staticRouteEntries = ARRAY_OF({
--         name = STRING,
--         interface = STRING,
--         destinationLAN = IPADDRESS,
--         networkPrefixLength = NUMBER,
--         gateway = IPADDRESS
--     }),
--     maxStaticRouteEntries = NUMBER
-- }
--
function _M.getRoutingSettings(sc)
    sc:readlock()
    return {
        isNATEnabled = sc:get_nat_enabled(),
        isDynamicRoutingEnabled = sc:get_rip_enabled(),
        entries = getStaticRouteEntries(sc),
        maxStaticRouteEntries = platform.MAX_STATIC_ROUTE_ENTRIES
    }
end

--
-- Set the current routing settings.
--
-- input = CONTEXT, {
--     isNATEnabled = BOOLEAN,
--     isDynamicRoutingEnabled = BOOLEAN,
--     staticRouteEntries = ARRAY_OF({
--         name = STRING,
--         interface = STRING,
--         destinationLAN = IPADDRESS,
--         networkPrefixLength = NUMBER,
--         gateway = IPADDRESS
--     })
-- }
--
function _M.setRoutingSettings(sc, input)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if input.isNATEnabled and input.isDynamicRoutingEnabled then
        return 'ErrorInvalidDynamicRoutingEnabled'
    end
    if not input.isNATEnabled and not input.isDynamicRoutingEnabled then
        return 'ErrorInvalidDynamicRoutingNotEnabled'
    end

    sc:set_nat_enabled(input.isNATEnabled)
    sc:set_rip_enabled(input.isDynamicRoutingEnabled)
    if input.isDynamicRoutingEnabled then
        sc:set_rip_lan_interface_enabled(input.isDynamicRoutingEnabled)
        sc:set_rip_wan_interface_enabled(input.isDynamicRoutingEnabled)
        sc:set_rip_text_password('')
        sc:set_rip_md5_password('')
        sc:set_rip_no_split_horizon_enabled(false)
    end

    return setStaticRouteEntries(sc, input.entries)
end

function _M.connectPPPWAN(sc)
    sc:readlock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local wanType = getWANType(sc, 1)
    if wanType ~= 'PPPoE' and wanType ~= 'PPTP' and wanType ~= 'L2TP' then
        return 'ErrorInvalidWANType'
    end
    sc:connect_ppp_wan()
end

function _M.disconnectPPPWAN(sc)
    sc:readlock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local wanType = getWANType(sc, 1)
    if wanType ~= 'PPPoE' and wanType ~= 'PPTP' and wanType ~= 'L2TP' then
        return 'ErrorInvalidWANType'
    end
    sc:disconnect_ppp_wan()
end

function _M.releaseDHCPWANLease(sc)
    sc:readlock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local wanType = getWANType(sc, 1)
    if wanType ~= 'DHCP' then
        return 'ErrorInvalidWANType'
    end
    sc:release_wan_dhcp_lease()
end

function _M.renewDHCPWANLease(sc)
    sc:readlock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local wanType = getWANType(sc, 1)
    if wanType ~= 'DHCP' then
        return 'ErrorInvalidWANType'
    end
    sc:renew_wan_dhcp_lease()
end

function _M.releaseDHCPIPv6WANLease(sc)
    sc:readlock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local type = sc:get_wan_ipv6_connection_type()
    if type == nil or type == '' or (string.find(type, 'IPv6', 1, true) == nil and type ~= 'Pass-through') then
        return 'ErrorInvalidIPv6WANType'
    end
    sc:release_wan_ipv6_dhcp_lease()
end

function _M.renewDHCPIPv6WANLease(sc)
    sc:readlock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local type = sc:get_wan_ipv6_connection_type()
    if type == nil or type == '' or (string.find(type, 'IPv6', 1, true) == nil and type ~= 'Pass-through') then
        return 'ErrorInvalidIPv6WANType'
    end
    sc:renew_wan_ipv6_dhcp_lease()
end

function _M.reconnect6rdTunnel(sc)
    sc:readlock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local type = sc:get_wan_ipv6_connection_type()
    if string.find(type, '6rd', 1, true) == nil then
        return 'ErrorInvalidIPv6WANType'
    end
    sc:reconnect_wan_6rd_tunnnel()
end

--
-- Get the current MAC address clone settings.
--
-- input = CONTEXT
--
-- output = {
--     isMACAddressCloneEnabled = BOOLEAN,
--     macAddress = OPTIONAL(MACADDRESS)
-- }
--
function _M.getMACAddressCloneSettings(sc)
    sc:readlock()
    local override = sc:get_wan_mac_address_clone()
    return {
        isMACAddressCloneEnabled = (override ~= nil),
        macAddress = (override ~= nil) and hdk.macaddress(override) or nil
    }
end

--
-- Set the MAC address clone settings.
--
-- input = CONTEXT, {
--     isMACAddressCloneEnabled = BOOLEAN,
--     macAddress = OPTIONAL(MACADDRESS)
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorMissingMACAddress',
--     'ErrorSuperfluousMACAddress',
--     'ErrorInvalidMACAddress'
-- )
--
function _M.setMACAddressCloneSettings(sc, settings)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    if settings.isMACAddressCloneEnabled then
        if not settings.macAddress then
            return 'ErrorMissingMACAddress'
        end

        if settings.macAddress:iszero() or
            settings.macAddress:ismulticast() then
            return 'ErrorInvalidMACAddress'
        end

        sc:set_wan_mac_address_clone(tostring(settings.macAddress))
    else
        if settings.macAddress then
            return 'ErrorSuperfluousMACAddress'
        end

        sc:set_wan_mac_address_clone(nil)
    end
end

function _M.refreshWirelessNetworks(sc)
    if not util.arrayToSet(_M.getSupportedWANTypes(sc))['WirelessBridge'] and not util.arrayToSet(_M.getSupportedWANTypes(sc))['WirelessRepeater'] then
        return '_ErrorUnknownAction'
    end
    -- $todo: need to implement?
end

-- Per Hank Yin's (from Marvell) email:
-- -92dbm and below should be treated as 5%.
-- -54dbm and above should be treated as 100%
-- between -54dbm and -92dbm whould be scaled to 100%
function _M.convertSignalStrenghFromDecibelsToPercentage(value)
    local minThreshold = -54
    local maxThreshold = -92
    if (value > minThreshold) then return 100
    elseif (value < maxThreshold) then return 5
    else
        return math.floor((value - maxThreshold) / 38 * 95) + 5
    end
end

function _M.getWirelessNetworksFromOlympusAPI(sc, band)
    sc:readlock()
    return sc:get_wifibridge_wireless_networks(band)
end

function _M.getWirelessNetworks(sc)
    sc:readlock()
    if not util.arrayToSet(_M.getSupportedWANTypes(sc))['WirelessBridge'] and not util.arrayToSet(_M.getSupportedWANTypes(sc))['WirelessRepeater'] then
        return nil, '_ErrorUnknownAction'
    end
    local data
    local wirelessNetworks = {}
    local supported = sc:get_wifi_devices() -- 'wl0 wl1'
    for id, value in pairs(wirelessap.RADIO_PROFILES) do
        if supported:find(value.apName, 1, true) then
            data = _M.getWirelessNetworksFromOlympusAPI(sc, value.band)
            for i, wirelessNetwork in pairs(data) do
                wirelessNetwork.bssid = hdk.macaddress(tostring(wirelessNetwork.bssid))
                wirelessNetwork.band = wirelessNetwork.band
                wirelessNetwork.signalStrength = _M.convertSignalStrenghFromDecibelsToPercentage(wirelessNetwork.signalStrength)
                wirelessNetwork.security = wirelessap.parseWirelessSecurity(wirelessNetwork.security)
                table.insert(wirelessNetworks, wirelessNetwork)
            end
        end
    end

    return {
        wirelessNetworks = wirelessNetworks
    }
end

local function getSupportedWirelessSecurityTypes(sc, band)
    local types = {}
    sc:readlock()
    types = sc:get_wifibridge_supported_sec_types(band)

    for i = 1, table.getn(types) do
        types[i] = wirelessap.parseWirelessSecurity(types[i])
    end

    return types
end

local function getSupportedWirelessModeSecurities(sc)
    if _M.supportedWirelessModeSecurities == nil then
        sc:readlock()
        _M.supportedWirelessModeSecurities = {}
        local supported = sc:get_wifi_devices() -- 'wl0 wl1'
        for id, value in pairs(wirelessap.RADIO_PROFILES) do
            if supported:find(value.apName, 1, true) then
                local radio = { band = value.band, supportedSecurityTypes = getSupportedWirelessSecurityTypes(sc, value.band) }
                table.insert(_M.supportedWirelessModeSecurities, radio)
            end
        end
    end
    return _M.supportedWirelessModeSecurities
end

local function validateWirelessSettings(sc, settings)
    if not wirelessap.isValidSSID(settings.ssid) then
        return 'ErrorInvalidSSID'
    end

    local isWirelessModeSecuritySupportedForRadio = function(band, security)
        if supportedWirelessModeSecurities == nil then
            supportedWirelessModeSecurities = getSupportedWirelessModeSecurities(sc)
        end
        for i, radio in pairs(supportedWirelessModeSecurities) do
            if radio.band == band then
                local supportedSecuritySet = util.arrayToSet(radio.supportedSecurityTypes)
                if supportedSecuritySet[security] then
                    return true
                end
            end
        end
        return false
    end

    if not isWirelessModeSecuritySupportedForRadio(settings.band, settings.security) then
        return 'ErrorUnsupportedSecurity'
    end
    if settings.security == 'WEP' then
        if not wirelessap.isValidWEPKey(settings.password, 26) or not wirelessap.isValidWEPKey(settings.password, 10) then
            return 'ErrorInvalidKey'
        end
    elseif wirelessap.isSecurityWPAVariant(settings.security) then
        if not wirelessap.isValidWPAPassphrase(settings.password) then
            return 'ErrorInvalidPassphrase'
        end
    end
end

function _M.getWirelessConnectionInfo(sc)
    return {
        ssid = sc:get_wifibridge_conn_ssid(),
        bssid = hdk.macaddress(sc:get_wifibridge_conn_bssid()),
        band = sc:get_wifibridge_conn_radio_band(),
        mode = wirelessap.parseWirelessRadioMode(sc:get_wifibridge_conn_network_mode()),
        channelWidth = wirelessap.parseWirelessChannelWidth(sc:get_wifibridge_conn_channel_width()),
        channel = sc:get_wifibridge_conn_channel(),
        signalStrength = _M.convertSignalStrenghFromDecibelsToPercentage(sc:get_wifibridge_conn_signal_strength())
    }
end

function _M.isWifiBridgeConnected(sc)
    return sc:get_wifibridge_is_connected()
end

--
-- Get the current WAN status.
--
-- input = CONTEXT
--
-- output = {
--     supportedWANTypes = ARRAY_OF(STRING),
--     isDetectingWANType = BOOLEAN,
--     detectedWANType = OPTIONAL(STRING),
--     wanStatus = STRING,
--     wanConnection = OPTIONAL({
--         wanType = STRING,
--         ipAddress = IPADDRESS,
--         networkPrefixLength = NUMBER,
--         gateway = IPADDRESS,
--         mtu = NUMBER,
--         dhcpLeaseMinutes = NUMBER,
--         dnsServer1 = IPADDRESS,
--         dnsServer2 = OPTIONAL(IPADDRESS),
--         dnsServer3 = OPTIONAL(IPADDRESS)
--      }),
--      state = OPTIONAL(STRING),
--      wanIPv6Status = STRING,
--      linkLocalIPv6Address = OPTIONAL(IPV6ADDRESS),
--      wanIPv6Connection = OPTIONAL({
--          wanType = STRING,
--          networkInfo = OPTIONAL({
--              ipAddress = IPV6ADDRESS,
--              gateway = IPV6ADDRESS,
--              dhcpLeaseMinutes = NUMBER,
--              dnsServer1 = IPV6ADDRESS,
--              dnsServer2 = OPTIONAL(IPV6ADDRESS),
--              dnsServer3 = OPTIONAL(IPV6ADDRESS)
--          })
--      }),
--      macAddress = MACADDRESS
-- }
--
function _M.getWANStatus(sc, version)
    local getLease = function(value)
        local lease = tonumber(value) or 0
        if lease ~= 0 then
            lease = math.max(1, math.floor(lease / 60))
        else
            lease = platform.DEFAULT_WAN_DHCP_LEASE
        end
        return lease
    end

    sc:readlock()
    local wanStatus, connection, state
    local wanType, useStaticSettings = getWANType(sc, 2)
    local status = sc:get_wan_connection_status()
    local ipaddr = sc:get_wan_current_ip_address()

    if status == 'stopped' or
       status == 'stopping' or
       (wanType ~= 'WirelessBridge' and wanType ~= 'WirelessRepeater' and sc:get_wan_phylink_status() == 'down') then
        wanStatus = 'Disconnected'
    elseif status == 'starting' then
        wanStatus = 'Connecting'
    elseif status == 'started' then
        local dns

        connection = {}
        if useStaticSettings then
            connection = getWANStaticSettings(sc, wanType == 'Bridge' and 'Bridge' or 'Router')
            connection.domainName = nil -- clear domainName, which is not in the output
        else
            if wanType == 'PPPoE' then
                state = parsePPPConnState(sc:get_wan_ppp_status())
            end

            connection.ipAddress = util.addressify(ipaddr)
            subnetMask = util.addressify(sc:get_wan_current_subnet_mask())
            connection.networkPrefixLength = subnetMask and util.subnetMaskToNetworkPrefixLength(subnetMask) or nil
            connection.gateway = util.addressify(sc:get_wan_current_gateway())
            connection.dnsServer1 = util.addressify(sc:get_wan_current_dns(1))
            connection.dnsServer2 = util.addressify(sc:get_wan_current_dns(2))
            connection.dnsServer3 = util.addressify(sc:get_wan_current_dns(3))
            if wanType == 'DHCP' or wanType == 'Bridge'  or wanType == 'WirelessBridge' or wanType == 'WirelessRepeater' then
               connection.dhcpLeaseMinutes = getLease(sc:get_wan_dhcp_lease_time())
            end

            -- Workaround for COCOON-73. When the ISP isn't handing out DNS server, set this to 0.0.0.0
            if wanType == 'PPPoE' and connection.dnsServer1 == nil then
                connection.dnsServer1 = hdk.ipaddress('0.0.0.0')
            end
        end

        -- Verify all "required" values are set before status is considered 'Connected'
        if connection.ipAddress ~= nil and
           connection.networkPrefixLength ~= nil and
           connection.gateway ~= nil and
           connection.dnsServer1 ~= nil then

            connection.wanType = wanType
            connection.mtu = sc:get_wan_mtu()

            wanStatus = 'Connected'
        else
            connection = nil
            wanStatus = 'Connecting'
        end
    else
        wanStatus = 'Indeterminate'
    end

    local ipv6Connection
    local dns1, dns2, dns3
    local wanIPv6Type = sc:get_wan_ipv6_connection_type() or ''
    local wanIfName = sc:get_wan_current_ifname()
    assert(wanIfName, 'current WAN interface is not set') -- an internal error

    --
    -- DHCPv6 enabled state:
    -- 0: disabled
    -- 1: enabled for PD
    -- 2: enabled for IA
    -- 3: enabled for PD and IA
    --

    -- Methods for retrieving configuration-specific IPv6 values and connection state.
    local getConnectionState = nil -- if nil is returned, default will be 'Disconnected'
    local getWANIPv6NetworkInfo = nil

    -- Default method for getting the configuration-specific IPv6 WAN connection values
    local getWANIPv6NetworkInfoDefault = function()
        return {
            ipAddress = hdk.ipv6address(sc:get_wan_current_ipv6address())
        }
    end

    -- Default method for getting the IPv6 WAN connection state.
    local getConnectionStateDefault = function()
        return sc:get_wan_current_ipv6address() and 'Connected' or 'Connecting'
    end

    if string.find(wanIPv6Type, 'IPv6', 1, true) then
        dns1 = sc:get_wan_current_ipv6_dns(1)
        dns2 = sc:get_wan_current_ipv6_dns(2)
        dns3 = sc:get_wan_current_ipv6_dns(3)

         -- Assume SLAAC unless a DHCPv6 address is set.
        if sc:get_wan_dhcpv6_addr_and_prefix() then
            wanIPv6Type = 'DHCPv6'

            -- Since DHCPv6 is only returned when a DHCPv6 address is present, always return 'Connected' if the service is running
            getConnectionState = function()
                if (sc:get_wan_dhcpv6c_enabled() > 0) then
                    return 'Connected'
                end
            end
            getWANIPv6NetworkInfo = function()
                local dhcpv6AddressAndPrefix = sc:get_wan_dhcpv6_addr_and_prefix()
                return {
                    ipAddress = hdk.ipv6address(dhcpv6AddressAndPrefix:match('^[^/]*')),
                    gateway = platform.getIPv6DefaultRoute(sc, wanIfName),
                    dhcpLeaseMinutes = getLease(sc:get_wan_dhcpv6_lease_time()),
                    dnsServer1 = dns1 and hdk.ipv6address(dns1),
                    dnsServer2 = dns2 and hdk.ipv6address(dns2),
                    dnsServer3 = dns3 and hdk.ipv6address(dns3)
                }
            end
        else
            wanIPv6Type = 'SLAAC'

            getConnectionState = function()
                if (sc:get_wan_dhcpv6c_enabled() > 0) then
                    return getConnectionStateDefault()
                end
            end
            getWANIPv6NetworkInfo = function()
                return {
                    ipAddress = hdk.ipv6address(sc:get_wan_current_ipv6address()),
                    gateway = platform.getIPv6DefaultRoute(sc, wanIfName),
                    dnsServer1 = dns1 and hdk.ipv6address(dns1),
                    dnsServer2 = dns2 and hdk.ipv6address(dns2),
                    dnsServer3 = dns3 and hdk.ipv6address(dns3)
                }
            end
        end
    elseif string.find(wanIPv6Type, '6rd', 1, true) then
        wanIPv6Type = '6rd Tunnel'

        getConnectionState = function()
            if sc:get_wan_6rd_enabled() then
                return getConnectionStateDefault()
            end
        end
        getWANIPv6NetworkInfo = function()
            return {
                ipAddress = hdk.ipv6address(sc:get_wan_current_ipv6address()),
                gateway = platform.getIPv6DefaultRoute(sc, wanIfName)
            }
        end
    elseif wanIPv6Type == 'Bridge' then

        getConnectionState = function()
            if sc:get_wan_ipv6_bridging_enabled() then
                return getConnectionStateDefault()
            end
        end
        -- Return default connection info
        getWANIPv6NetworkInfo = getWANIPv6NetworkInfoDefault
    elseif wanIPv6Type == 'Static' then

        getConnectionState = function()
            -- Return connected if the statically configured matches the sysevent one.
            local static = sc:get_wan_static_ipv6address()
            local current = sc:get_wan_current_ipv6address()
            if sc:get_wan_ipv6_static_enabled() then
                return static and current and (hdk.ipv6address(static) == hdk.ipv6address(current)) and 'Connected' or 'Connecting'
            end
        end
        -- Return statically set values
        getWANIPv6NetworkInfo = function()
            return {
                ipAddress = hdk.ipv6address(sc:get_wan_static_ipv6address()),
                gateway = hdk.ipv6address(sc:get_wan_ipv6_static_gateway())
            }
        end
    elseif wanIPv6Type == 'PPPoE' then
        local lanPrefixAddress
        if version == 3 then
            lanPrefixAddress = sc:get_wan_dhcpv6_lan_prefix_address()
        end
        return {
            lanPrefixAddress = lanPrefixAddress and hdk.ipv6address(lanPrefixAddress) or nil
        }
    elseif wanIPv6Type == 'Pass-through' then
        getConnectionState = function()
            return sc:get_wan_phylink_status() == 'up' and 'Connected' or 'Connecting'
        end
    else
        getConnectionState = function() return 'Indeterminate' end
    end

    -- If the current IPv6 WAN is 'up', check the configuration-specific status, otherwise disconnected.
    wanIPv6Status = (sc:get_current_ipv6_wan_state() == 'up') and getConnectionState() or 'Disconnected'

    -- Get the current link local address for the WAN interface. This is allowed to be nil.
    linkLocalIPv6Address = platform.getIPv6LinkLocalAddress(sc, sc:get_lan_interface_name())

    -- Get the current IPv6 LAN prefix address. This is allowed to be nil.
    local lanIPv6PrefixAddress
    if (version == 3) then
        lanIPv6PrefixAddress = sc:get_wan_dhcpv6_lan_prefix_address()
    end
    lanIPv6PrefixAddress = lanIPv6PrefixAddress and hdk.ipv6address(lanIPv6PrefixAddress) or nil

    -- Include the connection info if the connection state is 'Connected'
    if wanIPv6Status == 'Connected' then
        ipv6Connection = {
            wanType = wanIPv6Type,
            networkInfo = getWANIPv6NetworkInfo and getWANIPv6NetworkInfo()
        }
    end

    local macAddress
    local mac = sc:get_wan_mac_address() or sc:get_wan_mac_address_clone()
    if mac then
        macAddress = hdk.macaddress(mac)
    else
        macAddress = platform.getMACAddressFromNetName(sc:get_wan_current_ifname())
    end

    return {
        supportedWANTypes = _M.getSupportedWANTypes(sc),
        isDetectingWANType = sc:get_wan_auto_detect_enabled(),
        detectedWANType = wanType,
        wanStatus = wanStatus,
        wanConnection = connection,
        wanIPv6Status = wanIPv6Status,
        linkLocalIPv6Address = linkLocalIPv6Address,
        lanPrefixAddress = lanIPv6PrefixAddress,
        wanIPv6Connection = ipv6Connection,
        state = state,
        macAddress = macAddress
    }
end

function _M.getWANStatus2(sc, version)
    local retVal = _M.getWANStatus(sc, version and version or 2)
    local wanTypesArray = util.arrayToSet(_M.getSupportedWANTypes(sc))
    if (wanTypesArray['WirelessRepeater'] or wanTypesArray['WirelessBridge']) then
        retVal.supportedWirelessModeSecurities = getSupportedWirelessModeSecurities(sc)
    end
    if (retVal.detectedWANType == 'WirelessBridge' or retVal.detectedWANType == 'WirelessRepeater') and _M.isWifiBridgeConnected(sc) then
        retVal.wirelessConnection = _M.getWirelessConnectionInfo(sc)
    end
    return retVal
end

function _M.getWANStatus3(sc)
    local retVal = _M.getWANStatus2(sc, 3)
    retVal.supportedIPv6WANTypes = _M.getSupportedIPv6WANTypes(sc)
    retVal.supportedWANCombinations = {
        { wanType = 'DHCP', wanIPv6Type = 'Automatic' },
        { wanType = 'Static', wanIPv6Type = 'Automatic' },
        { wanType = 'PPPoE', wanIPv6Type = 'Automatic' },
        { wanType = 'L2TP', wanIPv6Type = 'Automatic' },
        { wanType = 'PPTP', wanIPv6Type = 'Automatic' },
        { wanType = 'Bridge', wanIPv6Type = 'Automatic' },
        { wanType = 'DHCP', wanIPv6Type = 'Pass-through' },
        { wanType = 'PPPoE', wanIPv6Type = 'PPPoE' }
    }
    if util.arrayToSet(retVal.supportedWANTypes)['WirelessBridge'] then
        table.insert(retVal.supportedWANCombinations, { wanType = 'WirelessBridge', wanIPv6Type = 'Automatic' })
    end
    if util.arrayToSet(retVal.supportedWANTypes)['WirelessRepeater'] then
        table.insert(retVal.supportedWANCombinations, { wanType = 'WirelessRepeater', wanIPv6Type = 'Automatic' })
    end
    return retVal
end

function _M.setWANSettings2(sc, settings)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if settings.staticSettings and settings.wanType ~= 'Static' then
        return 'ErrorSuperfluousStaticSettings'
    end
    if settings.pppoeSettings and settings.wanType ~= 'PPPoE' then
        return 'ErrorSuperfluousPPPoESettings'
    end
    if settings.tpSettings and settings.wanType ~= 'PPTP' and settings.wanType ~= 'L2TP' then
        return 'ErrorSuperfluousTPSettings'
    end
    if settings.bridgeSettings and (settings.wanType ~= 'Bridge' and settings.wanType ~= 'WirelessBridge' and settings.wanType ~= 'WirelessRepeater') then
        return 'ErrorSuperfluousBridgeSettings'
    end
    if settings.telstraSettings and settings.wanType ~= 'Telstra' then
        return 'ErrorSuperfluousTelstraSettings'
    end
    if settings.wirelessModeSettings and (settings.wanType ~= 'WirelessBridge' and settings.wanType ~= 'WirelessRepeater') then
        return 'ErrorSuperfluousWirelessModeSettings'
    end

    local setProto = function(sc, proto)
        sc:set_wifibridge_mode(0)
        if (proto ~= 'bridge') then
            sc:set_bridge_mode(0)
            if sc:set_wan_protocol(proto) then
                return true
            end
        end
    end
    local setPPPConfig = function(sc, settings)
        if not util.isValidPPPAuthenticationPeerID(settings.username) then
            return 'ErrorInvalidUsername'
        end
        if not util.isValidPPPAuthenticationPassword(settings.password) then
            return 'ErrorInvalidPassword'
        end

        sc:set_wan_protocol_username(settings.username)
        sc:set_wan_protocol_password(settings.password)
        if settings.behavior == 'ConnectOnDemand' then
            sc:set_wan_connection_method('demand')
            if not settings.maxIdleMinutes or ((settings.maxIdleMinutes < 1) or (settings.maxIdleMinutes > 9999)) then
                return 'ErrorInvalidMaxIdleMinutes'
            end
            sc:set_wan_protocol_idle_time(settings.maxIdleMinutes)
        else
            sc:set_wan_connection_method('redial')
            if not settings.reconnectAfterSeconds or ((settings.reconnectAfterSeconds < 20) or (settings.reconnectAfterSeconds > 180)) then
                return 'ErrorInvalidReconnectAfterSeconds'
            end
            sc:set_wan_protocol_reconnect_interval(settings.reconnectAfterSeconds)
        end
    end
    local setWANBridgeSettings = function(sc, bridgeSettings)
        local bridgeRestart = false
        local bridgeMode = sc:get_bridge_mode()

        if bridgeSettings.useStaticSettings then
            if bridgeSettings.staticSettings == nil then
                return 'ErrorMissingStaticSettings'
            end

            sc:set_bridge_mode(2)
            bridgeRestart = true;
            result = setBridgeStaticSettings(sc, bridgeSettings.staticSettings)
            if result then
                return result
            end
        else
            if bridgeSettings.staticSettings then
                return 'ErrorSuperfluousStaticSettings'
            end
            bridgeRestart = true
            sc:set_bridge_mode(1)
        end

        if bridgeRestart then
            require('device').setRemoteUIEnabled(sc, false) -- remote ui cannot be enabled in bridge mode --
        end
    end

    if not util.arrayToSet(_M.getSupportedWANTypes(sc))[settings.wanType] then
        return 'ErrorUnsupportedWANType'
    end

    local result

    if settings.wanType == 'DHCP' then
        setProto(sc, 'dhcp')

    elseif settings.wanType == 'Static' then
        if settings.staticSettings == nil then
            return 'ErrorMissingStaticSettings'
        end

        setProto(sc, 'static')
        result = setWANStaticSettings(sc, settings.staticSettings)
        if result then
            return result
        end

    elseif settings.wanType == 'PPPoE' then
        if settings.pppoeSettings == nil then
            return 'ErrorMissingPPPoESettings'
        end

        setProto(sc, 'pppoe')
        result = setPPPConfig(sc, settings.pppoeSettings)
        if result then
            return result
        end
        if not util.isValidPPPoEServiceName(settings.pppoeSettings.serviceName) then
            return 'ErrorInvalidServiceName'
        end
        sc:set_wan_pppoe_service_name(settings.pppoeSettings.serviceName)

    elseif settings.wanType == 'PPTP' or settings.wanType == 'L2TP' then
        if settings.tpSettings == nil then
            return 'ErrorMissingTPSettings'
        end
        if util.isReservedAddress(settings.tpSettings.server) then
            return 'ErrorInvalidServer'
        end

        local proto = settings.wanType:lower()
        setProto(sc, proto)
        result = setPPPConfig(sc, settings.tpSettings)
        if result then
            return result
        end
        sc:set_wan_pptp_server_address(tostring(settings.tpSettings.server))
        if settings.wanType == 'PPTP' then
            sc:set_wan_pptp_address_static(settings.tpSettings.useStaticSettings)
        else
            sc:set_wan_l2tp_address_static(settings.tpSettings.useStaticSettings)
        end

        if settings.tpSettings.useStaticSettings then
            if settings.tpSettings.staticSettings == nil then
                return 'ErrorMissingStaticSettings'
            end
            result = setWANStaticSettings(sc, settings.tpSettings.staticSettings)
            if result then
                return result
            end
        elseif settings.tpSettings.staticSettings then
            return 'ErrorSuperfluousStaticSettings'
        end

    elseif settings.wanType == 'Bridge' then
        if settings.bridgeSettings == nil then
            return 'ErrorMissingBridgeSettings'
        else
            setProto(sc, 'bridge')
            setWANBridgeSettings(sc, settings.bridgeSettings)
        end

    elseif settings.wanType == 'Telstra' then
        if settings.telstraSettings == nil then
            return 'ErrorMissingTelstraSettings'
        end
        if util.isReservedAddress(settings.telstraSettings.server) then
            return 'ErrorInvalidServer'
        end
        if not util.isValidPPPAuthenticationPeerID(settings.telstraSettings.username) then
            return 'ErrorInvalidUsername'
        end
        if not util.isValidPPPAuthenticationPassword(settings.telstraSettings.password) then
            return 'ErrorInvalidPassword'
        end

        setProto(sc, 'telstra')

        sc:set_wan_telstra_server_address(tostring(settings.telstraSettings.server))
        sc:set_wan_protocol_username(settings.telstraSettings.username)
        sc:set_wan_protocol_password(settings.telstraSettings.password)

     elseif settings.wanType == 'WirelessBridge' or settings.wanType == 'WirelessRepeater' then
        if settings.wirelessModeSettings == nil then
            return 'ErrorMissingWirelessModeSettings'
        end

        local error = validateWirelessSettings(sc, settings.wirelessModeSettings)
        if error then
            return error
        end

        sc:set_wifibridge_device(settings.wirelessModeSettings.band)
        sc:set_wifibridge_ssid(settings.wirelessModeSettings.ssid)
        sc:set_wifibridge_passphrase(settings.wirelessModeSettings.password)
        sc:set_wifibridge_security_type(wirelessap.serializeWirelessSecurity(settings.wirelessModeSettings.security))
        sc:set_wifibridge_mode(settings.wanType == 'WirelessBridge' and 1 or 2) -- 1 = wireless bridge, 2 = wireless repeater

        if settings.bridgeSettings == nil then
            return 'ErrorMissingBridgeSettings'
        else
            setWANBridgeSettings(sc, settings.bridgeSettings)
        end

    else
        return 'ErrorUnsupportedWANType'
    end

    local mtuMin, mtuMax = _M.GetMTURange(settings.wanType)
    if settings.mtu ~= 0 and (mtuMin == nil or settings.mtu < mtuMin or settings.mtu > mtuMax) then
        return 'ErrorInvalidMTU'
    end
    sc:set_wan_mtu(settings.mtu)
end

function _M.setWANSettings3(sc, settings)
    local output = {}

    sc:writelock()
    if not platform.isReady(sc) then
        return nil, '_ErrorNotReady'
    end
    if settings.staticSettings and settings.wanType ~= 'Static' then
        return nil, 'ErrorSuperfluousStaticSettings'
    end
    if settings.pppoeSettings and settings.wanType ~= 'PPPoE' then
        return nil, 'ErrorSuperfluousPPPoESettings'
    end
    if settings.tpSettings and settings.wanType ~= 'PPTP' and settings.wanType ~= 'L2TP' then
        return nil, 'ErrorSuperfluousTPSettings'
    end
    if settings.bridgeSettings and (settings.wanType ~= 'Bridge' and settings.wanType ~= 'WirelessBridge' and settings.wanType ~= 'WirelessRepeater') then
        return nil, 'ErrorSuperfluousBridgeSettings'
    end
    if settings.telstraSettings and settings.wanType ~= 'Telstra' then
        return nil, 'ErrorSuperfluousTelstraSettings'
    end
    if settings.wirelessModeSettings and (settings.wanType ~= 'WirelessBridge' and settings.wanType ~= 'WirelessRepeater') then
        return nil, 'ErrorSuperfluousWirelessModeSettings'
    end

    local setProto = function(sc, proto)
        sc:set_wifibridge_mode(0)
        if (proto ~= 'bridge') then
            sc:set_bridge_mode(0)
            if sc:set_wan_protocol(proto) then
                return true
            end
        end
    end
    local setPPPConfig = function(sc, settings)
        if not util.isValidPPPAuthenticationPeerID(settings.username) then
            return 'ErrorInvalidUsername'
        end
        if not util.isValidPPPAuthenticationPassword(settings.password) then
            return 'ErrorInvalidPassword'
        end

        sc:set_wan_protocol_username(settings.username)
        sc:set_wan_protocol_password(settings.password)
        if settings.behavior == 'ConnectOnDemand' then
            sc:set_wan_connection_method('demand')
            if not settings.maxIdleMinutes or ((settings.maxIdleMinutes < 1) or (settings.maxIdleMinutes > 9999)) then
                return 'ErrorInvalidMaxIdleMinutes'
            end
            sc:set_wan_protocol_idle_time(settings.maxIdleMinutes)
        else
            sc:set_wan_connection_method('redial')
            if not settings.reconnectAfterSeconds or ((settings.reconnectAfterSeconds < 20) or (settings.reconnectAfterSeconds > 180)) then
                return 'ErrorInvalidReconnectAfterSeconds'
            end
            sc:set_wan_protocol_reconnect_interval(settings.reconnectAfterSeconds)
        end
    end
    local setWANBridgeSettings = function(sc, bridgeSettings)
        local bridgeRestart = false

        if bridgeSettings.useStaticSettings then
            if bridgeSettings.staticSettings == nil then
                return 'ErrorMissingStaticSettings'
            end

            sc:set_bridge_mode(2)
            bridgeRestart = true;
            result = setBridgeStaticSettings(sc, bridgeSettings.staticSettings)
            if result then
                return result
            end
        else
            if bridgeSettings.staticSettings then
                return 'ErrorSuperfluousStaticSettings'
            end
            bridgeRestart = true
            sc:set_bridge_mode(1)
        end

        if bridgeRestart then
            require('device').setRemoteUIEnabled(sc, false) -- remote ui cannot be enabled in bridge mode --
        end
        -- we're now in bridge mode, so return the host name and domain to direct the UI accordingly
        return {
            redirection = {
                hostName = device.getHostName(sc),
                domain = sc:get_router_local_domain()
            }
        }
    end

    if not util.arrayToSet(_M.getSupportedWANTypes(sc))[settings.wanType] then
        return nil, 'ErrorUnsupportedWANType'
    end

    local result

    if settings.wanType == 'DHCP' then
        setProto(sc, 'dhcp')

    elseif settings.wanType == 'Static' then
        if settings.staticSettings == nil then
            return nil, 'ErrorMissingStaticSettings'
        end

        setProto(sc, 'static')
        result = setWANStaticSettings(sc, settings.staticSettings)
        if result then
            return nil, result
        end

    elseif settings.wanType == 'PPPoE' then
        if settings.pppoeSettings == nil then
            return nil, 'ErrorMissingPPPoESettings'
        end

        setProto(sc, 'pppoe')
        result = setPPPConfig(sc, settings.pppoeSettings)
        if result then
            return nil, result
        end
        if not util.isValidPPPoEServiceName(settings.pppoeSettings.serviceName) then
            return nil, 'ErrorInvalidServiceName'
        end
        sc:set_wan_pppoe_service_name(settings.pppoeSettings.serviceName)

    elseif settings.wanType == 'PPTP' or settings.wanType == 'L2TP' then
        if settings.tpSettings == nil then
            return nil, 'ErrorMissingTPSettings'
        end
        if util.isReservedAddress(settings.tpSettings.server) then
            return nil, 'ErrorInvalidServer'
        end

        local proto = settings.wanType:lower()
        setProto(sc, proto)
        result = setPPPConfig(sc, settings.tpSettings)
        if result then
            return nil, result
        end
        sc:set_wan_pptp_server_address(tostring(settings.tpSettings.server))
        if settings.wanType == 'PPTP' then
            sc:set_wan_pptp_address_static(settings.tpSettings.useStaticSettings)
        else
            sc:set_wan_l2tp_address_static(settings.tpSettings.useStaticSettings)
        end

        if settings.tpSettings.useStaticSettings then
            if settings.tpSettings.staticSettings == nil then
                return nil, 'ErrorMissingStaticSettings'
            end
            result = setWANStaticSettings(sc, settings.tpSettings.staticSettings)
            if result then
                return nil, result
            end
        elseif settings.tpSettings.staticSettings then
            return nil, 'ErrorSuperfluousStaticSettings'
        end

    elseif settings.wanType == 'Bridge' then
        if settings.bridgeSettings == nil then
            return nil, 'ErrorMissingBridgeSettings'
        else
            setProto(sc, 'bridge')
            output = setWANBridgeSettings(sc, settings.bridgeSettings)
        end

    elseif settings.wanType == 'Telstra' then
        if settings.telstraSettings == nil then
            return nil, 'ErrorMissingTelstraSettings'
        end
        if util.isReservedAddress(settings.telstraSettings.server) then
            return nil, 'ErrorInvalidServer'
        end
        if not util.isValidPPPAuthenticationPeerID(settings.telstraSettings.username) then
            return nil, 'ErrorInvalidUsername'
        end
        if not util.isValidPPPAuthenticationPassword(settings.telstraSettings.password) then
            return nil, 'ErrorInvalidPassword'
        end

        setProto(sc, 'telstra')

        sc:set_wan_telstra_server_address(tostring(settings.telstraSettings.server))
        sc:set_wan_protocol_username(settings.telstraSettings.username)
        sc:set_wan_protocol_password(settings.telstraSettings.password)

     elseif settings.wanType == 'WirelessBridge' or settings.wanType == 'WirelessRepeater' then
        if settings.wirelessModeSettings == nil then
            return nil, 'ErrorMissingWirelessModeSettings'
        end

        local error = validateWirelessSettings(sc, settings.wirelessModeSettings)
        if error then
            return nil, error
        end

        sc:set_wifibridge_device(settings.wirelessModeSettings.band)
        sc:set_wifibridge_ssid(settings.wirelessModeSettings.ssid)
        sc:set_wifibridge_passphrase(settings.wirelessModeSettings.password)
        sc:set_wifibridge_security_type(wirelessap.serializeWirelessSecurity(settings.wirelessModeSettings.security))
        sc:set_wifibridge_mode(settings.wanType == 'WirelessBridge' and 1 or 2) -- 1 = wireless bridge, 2 = wireless repeater

        if settings.bridgeSettings == nil then
            return nil, 'ErrorMissingBridgeSettings'
        else
            output = setWANBridgeSettings(sc, settings.bridgeSettings)
        end

    else
        return nil, 'ErrorUnsupportedWANType'
    end

    local mtuMin, mtuMax = _M.GetMTURange(settings.wanType)
    if settings.mtu ~= 0 and (mtuMin == nil or settings.mtu < mtuMin or settings.mtu > mtuMax) then
        return nil, 'ErrorInvalidMTU'
    end
    sc:set_wan_mtu(settings.mtu)

    -- if output is not a table, then an error occurred
    if type(output) ~= "table" then
        -- return nil and the resulting error string contained in ouput
        return nil, output
    end
    return output
end

-- Adds support for WAN port VLAN tagging
function _M.setWANSettings4(sc, settings)
    sc:writelock()

    if settings.wanTaggingSettings then
        if not sc:is_wan_vlan_tagging_supported() then
            return nil, 'ErrorWANTaggingNotSupported'
        end

        local wanTaggingSettings = settings.wanTaggingSettings
        if wanTaggingSettings.isEnabled and not wanTaggingSettings.vlanTaggingSettings then
           return nil, 'ErrorMissingVLANTaggingSettings'
        end
        sc:set_wan_vlan_tagging_enabled(wanTaggingSettings.isEnabled)

        if wanTaggingSettings.vlanTaggingSettings then
            local vltSettings = wanTaggingSettings.vlanTaggingSettings
            local vlt = require('vlantagging')

            if not vlt.isValidWANVLANID(sc, vltSettings.vlanID) then
                return nil, 'ErrorInvalidVLANID'
            end
            if (vltSettings.vlanPriority and not vlt.isValidVLANPriority(vltSettings.vlanPriority)) then
                return nil, 'ErrorInvalidVLANPriority'
            end
            sc:set_wan_vlan_tagging_status(vlt.serializeTaggingStatus(vltSettings.vlanStatus))
            sc:set_wan_vlan_id(vltSettings.vlanID)
            sc:set_wan_vlan_priority(vltSettings.vlanPriority or 0)
        end
    end

    return _M.setWANSettings3(sc, settings)
end

-- This function sets the WAN settings, and then redirects the UI to the local domain if the WAN connection type is Bridge.
-- For routers without the Wireless Bridge/Repeater feature (Router3)
function _M.setWANSettingsWithRedirection(sc, settings)
    local output = {}
    local error = _M.setWANSettings(sc, settings)
    if error then
        return nil, error
    end
    if (settings.wanType == 'Bridge') then
        -- we're now in bridge mode, so return the host name and domain to direct the UI accordingly
        output = {
            redirection = {
                hostName = device.getHostName(sc),
                domain = sc:get_router_local_domain()
            }
        }
    end
    return output
end

function _M.initiateWirelessConnection(sc, settings)
    sc:writelock()
    if not util.arrayToSet(_M.getSupportedWANTypes(sc))['WirelessBridge'] and not util.arrayToSet(_M.getSupportedWANTypes(sc))['WirelessRepeater'] then
        return '_ErrorUnknownAction'
    end
    local error = validateWirelessSettings(sc, settings)
    if error then
        return error
    end

    sc:init_wifibridge_connection({
        ssid = settings.ssid,
        band = settings.band,
        bssid = "",
        password = settings.password,
        security = wirelessap.serializeWirelessSecurity(settings.security)
    })
end

function _M.getWirelessConnectionStatus(sc)
    local parseWirelessConnectionStatus = function(value)
        if value == 'failed' then
            return 'Failed'
        elseif value == 'connecting' then
            return 'Connecting'
        elseif value == 'success' then
            return 'Success'
        else
            return 'Idle'
        end
    end
    sc:readlock()
    if not util.arrayToSet(_M.getSupportedWANTypes(sc))['WirelessBridge'] and not util.arrayToSet(_M.getSupportedWANTypes(sc))['WirelessRepeater'] then
        return nil, '_ErrorUnknownAction'
    end

    local retVal =  { wirelessConnectionState = parseWirelessConnectionStatus(sc:get_wifibridge_conn_status()) }
    return retVal
end

return _M -- return the module
