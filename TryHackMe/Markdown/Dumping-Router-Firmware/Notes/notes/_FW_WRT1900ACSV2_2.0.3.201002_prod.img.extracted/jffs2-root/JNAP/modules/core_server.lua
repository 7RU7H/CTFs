--
-- 2018 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author: hsulliva $
-- $DateTime: 2020/02/06 14:32:09 $
-- $Id: //depot-irv/lego/branches/wrt_platform_sp1/lego_overlay/proprietary/jnap/modules/core/core_server.lua#5 $
--


local function GetDeviceInfo(ctx)
    local device = require('device')
    local hdk = require('libhdklua')

    local sc = ctx:sysctx()

    local services = {}
    local services_added = {}
    for module in hdk.values(ctx.modules) do
        for service in hdk.values(module.services) do
            service_implemented = true
            for action in hdk.values(service.actions) do
                if not action.is_implemented or not action.is_jnap then
                    service_implemented = false
                    break
                end
            end
            if service_implemented and not services_added[service.uri] then
                services_added[service.uri] = 0
                services[#services + 1] = service.uri
            end
        end
    end

    table.sort(services)

    return 'OK',
    {
        manufacturer = device.getManufacturer(sc) or '',
        modelNumber = device.getModelNumber(sc) or '',
        hardwareVersion = device.getHardwareRevision(sc) or '',
        description = device.getModelDescription(sc) or '',
        serialNumber = device.getSerialNumber(sc) or '',
        firmwareVersion = device.getFirmwareVersion(sc) or '',
        firmwareDate = device.getFirmwareDate(sc) or 0,
        services = services
    }
end

local function GetAdminPasswordRestrictions(ctx)
    local platform = require('platform')
    return 'OK',
    {
        minLength = platform.MIN_ADMIN_PASSWORD_LENGTH,
        maxLength = platform.MAX_ADMIN_PASSWORD_LENGTH,
        allowedCharacters = {
            { lowCodepoint = 0x20, highCodepoint = 0x7E }
        }
    }
end

local function SetAdminPassword(ctx, input)
    local device = require('device')
    local platform = require('platform')

    local sc = ctx:sysctx()

    -- Register the logging callback for this call.
    platform.registerLoggingCallback(function(level, message) ctx:serverlog(level, message) end)

    local error = device.setAdminPassword(sc, input.adminPassword, true)
    return error or 'OK'
end

local function SetAdminPassword2(ctx, input)
    local device = require('device')
    local platform = require('platform')

    local sc = ctx:sysctx()

    -- Register the logging callback for this call.
    platform.registerLoggingCallback(function(level, message) ctx:serverlog(level, message) end)

    local error = device.setAdminPassword2(sc, input, true)
    return error or 'OK'
end

local function GetAdminPasswordHint(ctx, input)
    local sc = ctx:sysctx()
    sc:readlock()

    return 'OK', {
        passwordHint = sc:get_admin_password_hint()
    }
end

local function IsAdminPasswordDefault(ctx)
    local sc = ctx:sysctx()
    local platform = require('platform')
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    local device = require('device')
    return 'OK', {
        isAdminPasswordDefault = device.getIsAdminPasswordDefault(sc)
    }
end

local function CheckAdminPassword(ctx)
    if ctx:sessiontoken() then
        return 'ErrorCannotAuthenticateWithSessionToken'
    end
    return 'OK'
end

local function CheckAdminPassword2(ctx, input)
    local device = require('device')
    local sc = ctx:sysctx()

    return device.checkAdminPassword(sc, input.adminPassword) and 'OK' or '_ErrorUnauthorized'
end

local function GetUnsecuredWiFiWarning(ctx)
    local device = require('device')

    local sc = ctx:sysctx()
    return 'OK', {
        enabled = device.getUnsecuredWiFiWarningEnabled(sc)
    }
end

local function SetUnsecuredWiFiWarning(ctx, input)
    local device = require('device')

    local sc = ctx:sysctx()
    local error = device.setUnsecuredWiFiWarningEnabled(sc, input.enabled)
    return error or 'OK'
end

local function Reboot(ctx)
    local device = require('device')

    local sc = ctx:sysctx()
    local error = device.reboot(sc)
    return error or 'OK'
end

local function FactoryReset(ctx)
    if ctx:isremotecall() then
        return "ErrorDisallowedRemoteCall"
    end

    local platform = require('platform')

    local sc = ctx:sysctx()
    local error = platform.restoreFactoryDefaults(sc)
    return error or 'OK'
end

local function GetDataUploadUserConsent(ctx)
    local device = require('device')

    local sc = ctx:sysctx()
    return 'OK', {
        userConsent = device.getDataUploadUserConsent(sc)
    }
end

local function SetDataUploadUserConsent(ctx, input)
    local device = require('device')

    local sc = ctx:sysctx()
    local error = device.setDataUploadUserConsent(sc, input.userConsent)
    return error or 'OK'
end

local function IsServiceSupported(ctx, input)
    local serviceSupported = require('jnap').isServiceSupported(ctx, input.serviceName)

    return 'OK', { isServiceSupported = serviceSupported }
end

return require('libhdklua').loadmodule('jnap_core'), {
    ['http://linksys.com/jnap/core/CheckAdminPassword'] = CheckAdminPassword,
    ['http://linksys.com/jnap/core/CheckAdminPassword2'] = CheckAdminPassword2,
    ['http://linksys.com/jnap/core/FactoryReset'] = FactoryReset,
    ['http://linksys.com/jnap/core/GetAdminPasswordRestrictions'] = GetAdminPasswordRestrictions,
    ['http://linksys.com/jnap/core/GetDeviceInfo'] = GetDeviceInfo,
    ['http://linksys.com/jnap/core/IsAdminPasswordDefault'] = IsAdminPasswordDefault,
    ['http://linksys.com/jnap/core/GetUnsecuredWiFiWarning'] = GetUnsecuredWiFiWarning,
    ['http://linksys.com/jnap/core/SetUnsecuredWiFiWarning'] = SetUnsecuredWiFiWarning,
    ['http://linksys.com/jnap/core/Reboot'] = Reboot,
    ['http://linksys.com/jnap/core/SetAdminPassword'] = SetAdminPassword,
    ['http://linksys.com/jnap/core/SetAdminPassword2'] = SetAdminPassword2,
    ['http://linksys.com/jnap/core/GetAdminPasswordHint'] = GetAdminPasswordHint,
    ['http://linksys.com/jnap/core/SetDataUploadUserConsent'] = SetDataUploadUserConsent,
    ['http://linksys.com/jnap/core/GetDataUploadUserConsent'] = GetDataUploadUserConsent,
    ['http://linksys.com/jnap/core/IsServiceSupported'] = IsServiceSupported
}
