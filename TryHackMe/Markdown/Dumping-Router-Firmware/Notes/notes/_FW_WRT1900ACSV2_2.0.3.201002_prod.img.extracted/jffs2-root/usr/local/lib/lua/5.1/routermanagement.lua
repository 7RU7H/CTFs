--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

-- routermanagement.lua - library to configure router management state.

local hdk = require('libhdklua')
local util = require('util')
local platform = require('platform')

local _M = {} -- create the module


--
-- Get the management settings of the local device.
--
-- input = CONTEXT
--
-- output = {
--     canManageUsingHTTP = BOOLEAN,
--     canManageUsingHTTPS = BOOLEAN,
--     canManageWirelessly = BOOLEAN,
--     canManageRemotely = BOOLEAN
-- }
--
function _M.getManagementSettings(sc)
    sc:readlock()
    return {
        canManageUsingHTTP = sc:get_http_management_enabled(),
        canManageUsingHTTPS = sc:get_https_management_enabled(),
        canManageWirelessly = sc:get_wifi_management_enabled(),
        canManageRemotely = sc:get_xmpp_management_enabled() -- 'true' is the default if not set (see xmppraclient/xrac.lua)
    }
end

-- This version includes new output field isManageWirelesslySupported,
-- and makes canManageWirelessly optional, depending on isManageWirelesslySupported.
function _M.getManagementSettings2(sc)
    sc:readlock()

    local canManageWirelessly
    local isManageWirelesslySupported = sc:get_wifi_management_supported()
    if isManageWirelesslySupported then
        canManageWirelessly = sc:get_wifi_management_enabled()
    end

    return {
        canManageUsingHTTP = sc:get_http_management_enabled(),
        canManageUsingHTTPS = sc:get_https_management_enabled(),
        isManageWirelesslySupported = isManageWirelesslySupported,
        canManageWirelessly = canManageWirelessly,
        canManageRemotely = sc:get_xmpp_management_enabled() -- 'true' is the default if not set (see xmppraclient/xrac.lua)
    }
end

--
-- Set the management settings of the local device.
--
-- input = CONTEXT, {
--     canManageUsingHTTP = BOOLEAN,
--     canManageUsingHTTPS = BOOLEAN,
--     canManageWirelessly = BOOLEAN,
--     canManageRemotely = BOOLEAN
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorCannotDisableLocalConfiguration',
-- )
function _M.setManagementSettings(sc, settings)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if not settings.canManageUsingHTTP and not settings.canManageUsingHTTPS then
        return 'ErrorCannotDisableLocalConfiguration'
    end

    sc:set_http_management_enabled(settings.canManageUsingHTTP)
    sc:set_https_management_enabled(settings.canManageUsingHTTPS)
    sc:set_wifi_management_enabled(settings.canManageWirelessly)
    sc:set_xmpp_management_enabled(settings.canManageRemotely)
end

function _M.setManagementSettings2(sc, settings)
    sc:writelock()

    local isManageWirelesslySupported = sc:get_wifi_management_supported()
    if isManageWirelesslySupported then
        if settings.canManageWirelessly == nil then
            return "ErrorMissingManageWirelessly"
        end
    else
        if settings.canManageWirelessly ~= nil then
            return "ErrorManageWirelesslyNotSupported"
        end
    end
    if not settings.canManageUsingHTTP and not settings.canManageUsingHTTPS then
        return 'ErrorCannotDisableLocalConfiguration'
    end
    sc:set_http_management_enabled(settings.canManageUsingHTTP)
    sc:set_https_management_enabled(settings.canManageUsingHTTPS)
    if isManageWirelesslySupported then
        sc:set_wifi_management_enabled(settings.canManageWirelessly)
    end
    sc:set_xmpp_management_enabled(settings.canManageRemotely)
end

--
-- Get the remote management status of the local device.
--
-- output = {
--     serviceState = STRING
-- }
--
function _M.getRemoteManagementStatus(sc)
    sc:readlock()

    local serviceState

    if _M.getManagementSettings(sc).canManageRemotely then
        local xracStatus = sc:get_xrac_status()
        if 'started' == xracStatus then
            serviceState = 'Connected'
        elseif 'starting' == xracStatus or 'sleeping' == xracStatus then
            serviceState = 'Connecting'
        else
            -- For unexpected states, assume stopped
            --assert('stopped' == xracStatus, 'unexpected xrac service state')
            serviceState = 'Stopped'
        end
    else
        serviceState = 'AdministrativelyDisabled'
    end
    return {
        serviceState = serviceState
    }
end


return _M -- return the module
