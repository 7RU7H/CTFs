--
-- 2014 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$ -- $Id$
--

-- openvpn.lua - library to configure the OpenVPN server.

local platform = require('platform')
local util = require('util')
local hdk = require('libhdklua')

local _M = {} -- create the module

function _M.isConflictingAddress(sc, ipAddress, prefixLength)
    if sc:get_openvpn_enabled() then
        local vpnStartIPAddress = hdk.ipaddress(sc:get_openvpn_client_ip_start_addr())
        if util.inSameSubnet(vpnStartIPAddress, ipAddress, prefixLength) then
            return true
        end
    end
    return false
end

--
-- Get the current OpenVPN settings.
--
-- input = CONTEXT
--
-- output = {
--     isOpenVPNEnabled = BOOLEAN,
--     allowRemoteConfigure = BOOLEAN,
--     connectionAccess = STRING,
--     serverIPAddress = OPTIONAL(IPADDRESS)
--     hostname = OPTIONAL(STRING),
--     serverPort = INTEGER,
--     protocol = STRING,
--     clientIPStartAddress = IPADDRESS
--     profiles = ARRAY_OF({
--         username = STRING,
--         password = STRING
--     })
--     maxProfiles = INTEGER,
--     maxConcurrentConnections = INTEGER,
--     clientProfileUpdated = BOOLEAN
-- }
--
function _M.getSettings(sc)
    local settings = {}
    local hostname, serverIPAddress
    sc:readlock()
    if not platform.isReady(sc) then
        return nil, '_ErrorNotReady'
    end

    local settings = {
        isOpenVPNEnabled = sc:get_openvpn_enabled(),
        allowRemoteConfigure = sc:get_openvpn_allow_remote_config(),
        serverPort = sc:get_openvpn_server_port(),
        protocol = sc:get_openvpn_protocol(),
        clientIPStartAddress = hdk.ipaddress(sc:get_openvpn_client_ip_start_addr()),
        profiles = sc:get_openvpn_user_profiles(),
        maxProfiles = sc:get_openvpn_max_user_profiles(),
        maxConcurrentConnections = sc:get_openvpn_max_connections(),
        clientProfileUpdated = sc:get_openvpn_client_profile_updated()
    }
    hostname = sc:get_openvpn_server_hostname()
    if #hostname > 0 then
       settings.hostname = hostname
    end
    serverIPAddress = sc:get_openvpn_server_ipaddr()
    if #serverIPAddress > 0 then
       settings.serverIPAddress = hdk.ipaddress(serverIPAddress)
    end

    return settings
end

--
-- Set the OpenVPN settings.
--
-- input = {
--     isOpenVPNEnabled = BOOLEAN,
--     allowRemoteConfigure = BOOLEAN,
--     serverIPAddress = OPTIONAL(IPADDRESS)
--     hostname = OPTIONAL(STRING),
--     serverPort = INTEGER,
--     protocol = STRING,
--     clientIPStartAddress = IPADDRESS
--     profiles = ARRAY_OF({
--         username = STRING,
--         password = STRING
--     })
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorDuplicateUserNames',
--     'ErrorInvalidHostName',
--     'ErrorInvalidPassword',
--     'ErrorInvalidPort',
--     'ErrorInvalidUsername',
--     'ErrorInvalidServerIPAddress',
--     'ErrorInvalidClientIPAddress',
--     'ErrorSuperfluousServerIPAddress',
-- )
--
function _M.setSettings(sc, settings)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    local profiles = settings.profiles

    local function isInGuestSubnet(ipAddress)
        local lanPrefixLength = util.subnetMaskToNetworkPrefixLength(hdk.ipaddress(sc:get_guest_access_lan_subnet_mask()))
        return hdk.ipaddress(sc:get_guest_access_lan_ipaddress()):networkid(lanPrefixLength) == ipAddress:networkid(lanPrefixLength)
    end

    local function isValidIPAddress(ipAddress)
        local router = require('router')
        return (not util.isReservedAddress(ipAddress) and
                not isInGuestSubnet(ipAddress) and
                not router.isInRouterSubnet(sc, ipAddress))
    end

    for i = 1, #profiles do
        for j  = i + 1, #profiles do
            if profiles[j].username == profiles[i].username then
                return "ErrorDuplicateUsernames"
            end
        end
        -- Username must be 8 to 64 chars in length, and can only contain alpha numeric, '_', '-', '.', or '@'
        if (#profiles[i].username < 8) or (#profiles[i].username > 64) or
           (profiles[i].username:find('^[%w_%-%.@]*$') == nil) then
            return 'ErrorInvalidUsername'
        end
        -- Password must be 8 to 64 chars in length, and can only contain alpha numeric, '_', '-', '.', or '@'
        if (#profiles[i].password < 8) or (#profiles[i].password > 64) or
           (profiles[i].password:find('^[%w_%-%.@]*$') == nil) then
            return 'ErrorInvalidPassword'
        end
    end

    if settings.hostname and not util.isValidHostName(settings.hostname) then
        return 'ErrorInvalidHostName'
    end
    if not util.isValidPort(settings.serverPort) then
        return 'ErrorInvalidPort'
    end
    if not isValidIPAddress(settings.clientIPStartAddress) then
        return 'ErrorInvalidClientIPAddress'
    end
    if settings.serverIPAddress and not isValidIPAddress(settings.serverIPAddress) then
        return 'ErrorInvalidServerIPAddress'
    end
    if (settings.serverIPAddress and settings.hostname) then
        return 'ErrorSuperfluousServerIPAddress'
    end

    sc:set_openvpn_enabled(settings.isOpenVPNEnabled)
    sc:set_openvpn_allow_remote_config(settings.allowRemoteConfigure)
    if (settings.serverIPAddress) then
        sc:set_openvpn_server_ipaddr(tostring(settings.serverIPAddress))
    end
    if (settings.hostname) then
        sc:set_openvpn_server_hostname(settings.hostname)
    end
    sc:set_openvpn_server_port(settings.serverPort)
    sc:set_openvpn_protocol(settings.protocol)
    sc:set_openvpn_client_ip_start_addr(tostring(settings.clientIPStartAddress))
    sc:set_openvpn_user_profiles(settings.profiles)
end

--
-- Get the OpenVPN Client connection profile
--
-- input = CONTEXT
--
-- ouput =  STRING
--
function _M.getClientConnectionProfile(sc)
    sc:readlock()
    if not platform.isReady(sc) then
        return nil, '_ErrorNotReady'
    end

    return sc:get_openvpn_client_connection_profile(sc)
end

return _M -- return the module
