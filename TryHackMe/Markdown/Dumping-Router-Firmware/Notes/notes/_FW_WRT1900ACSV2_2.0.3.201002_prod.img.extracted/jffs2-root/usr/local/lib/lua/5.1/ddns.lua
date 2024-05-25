--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

-- ddns.lua - library to configure DDNS state.

local platform = require('platform')
local util = require('util')

local _M = {} -- create the module

local function parseDDNSService(service)
    if service == 'dyndns' then
        return 'DynDNS', 'Dynamic'
    elseif service == 'dyndns-static' then
        return 'DynDNS', 'Static'
    elseif service == 'dyndns-custom' then
        return 'DynDNS', 'Custom'
    elseif service == 'tzo' then
        return 'TZO'
    elseif service == 'noip' then
        return 'No-IP'
    else
        return 'None'
    end
end

local function serializeDDNSService(provider, mode)
    if provider == 'DynDNS' then
        if mode == 'Dynamic' then
            return 'dyndns'
        elseif mode == 'Static' then
            return 'dyndns-static'
        elseif mode == 'Custom' then
            return 'dyndns-custom'
        end
    elseif provider == 'TZO' then
        return 'tzo'
    elseif provider == 'No-IP' then
        return 'noip'
    end
end

local function getSupportedProviders(sc)
    local providers = {}

    sc:readlock()
    providers = sc:get_ddns_supported_providers()

    for i = 1, table.getn(providers) do
        providers[i] = parseDDNSService(providers[i])
    end

    return providers
end

--
-- Get the current DDNS settings.
--
-- input = CONTEXT
--
-- output = {
--     ddnsProvider = STRING,
--     dynDNSSettings = OPTIONAL({
--         username = STRING,
--         password = STRING,
--         hostName = STRING,
--         isWildcardEnabled = BOOLEAN,
--         mode = STRING,
--         isMailExchangeEnabled = BOOLEAN,
--         mailExchangeSettings = OPTIONAL({
--             hostName = STRING,
--             isBackup = BOOLEAN
--         })
--     }),
--     tzoSettings = OPTIONAL({
--         username = STRING,
--         password = STRING,
--         hostName = STRING
--     })
-- }
--
function _M.getSettings(sc)
    sc:readlock()
    local ddnsProvider, dynDNSSettings, tzoSettings, noipSettings
    if not sc:get_ddns_enabled() then
        ddnsProvider = 'None'
    else
        local ddnsMode
        ddnsProvider, ddnsMode = parseDDNSService(sc:get_ddns_service_type())
        if ddnsProvider ~= 'None' then
            local settings = {
                username = sc:get_ddns_username(),
                password = sc:get_ddns_password(),
                hostName = sc:get_ddns_hostname()
            }
            if ddnsProvider == 'DynDNS' then
                local mailExchangeHostName = sc:get_ddns_mx_hostname()
                dynDNSSettings = settings
                dynDNSSettings.isWildcardEnabled = sc:get_ddns_wildcard_enabled()
                dynDNSSettings.mode = ddnsMode
                dynDNSSettings.isMailExchangeEnabled = (#mailExchangeHostName > 0)
                if dynDNSSettings.isMailExchangeEnabled then
                    dynDNSSettings.mailExchangeSettings = {
                        hostName = mailExchangeHostName,
                        isBackup = sc:get_ddns_mx_backup()
                    }
                end
            elseif ddnsProvider == 'TZO' then
                tzoSettings = settings
            elseif ddnsProvider == 'No-IP' then
                noipSettings = settings
            end
        end
    end
    return {
        ddnsProvider = ddnsProvider,
        dynDNSSettings = dynDNSSettings,
        tzoSettings = tzoSettings,
        noipSettings = noipSettings
    }
end

--
-- Set the current DDNS settings.
--
-- input = CONTEXT,  {
--     ddnsProvider = STRING,
--     dynDNSSettings = OPTIONAL({
--         username = STRING,
--         password = STRING,
--         hostName = STRING,
--         isWildcardEnabled = BOOLEAN,
--         mode = STRING,
--         isMailExchangeEnabled = BOOLEAN,
--         mailExchangeSettings = OPTIONAL({
--             hostName = STRING,
--             isBackup = BOOLEAN
--         })
--     }),
--     tzoSettings = OPTIONAL({
--         username = STRING,
--         password = STRING,
--         hostName = STRING
--     })
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorMissingDynDNSSettings',
--     'ErrorMissingTZOSettings',
--     'ErrorSuperfluousDynDNSSettings',
--     'ErrorSuperfluousTZOSettings',
--     'ErrorInvalidUsername',
--     'ErrorInvalidPassword',
--     'ErrorInvalidHostName',
--     'ErrorMissingMailExchangeSettings',
--     'ErrorSuperfluousMailExchangeSettings'
-- )
--
function _M.setSettings(sc, settings)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    local function findDDNSProvider(provider)
        for k, v in pairs(getSupportedProviders(sc)) do
            if v == provider then
                return true
            end
        end
    end

    sc:set_ddns_enabled(settings.ddnsProvider ~= 'None')

    if settings.dynDNSSettings and settings.ddnsProvider ~= 'DynDNS' then
        return 'ErrorSuperfluousDynDNSSettings'
    end
    if settings.tzoSettings and settings.ddnsProvider ~= 'TZO' then
        return 'ErrorSuperfluousTZOSettings'
    end
    if settings.noipSettings and settings.ddnsProvider ~= 'No-IP' then
        return 'ErrorSuperfluousNoIPSettings'
    end

    if settings.ddnsProvider ~= 'None' then
        if not findDDNSProvider(settings.ddnsProvider) then
            return 'ErrorUnsupportedDDNSProvider'
        end
        local ddnsSettings, ddnsMode
        local ddnsWildcard = false
        if settings.ddnsProvider == 'DynDNS' then
            ddnsSettings = settings.dynDNSSettings
            if not ddnsSettings then
                return 'ErrorMissingDynDNSSettings'
            end
            ddnsMode = ddnsSettings.mode
            ddnsWildcard = ddnsSettings.isWildcardEnabled
        elseif settings.ddnsProvider == 'TZO' then
            ddnsSettings = settings.tzoSettings
            if not ddnsSettings then
                return 'ErrorMissingTZOSettings'
            end
        elseif settings.ddnsProvider == 'No-IP' then
            ddnsSettings = settings.noipSettings
            if not ddnsSettings then
                return 'ErrorMissingNoIPSettings'
            end
        end
        local service = serializeDDNSService(settings.ddnsProvider, ddnsMode)
        sc:set_ddns_service_type(service)

        if #ddnsSettings.username == 0 then
            return 'ErrorInvalidUsername'
        end
        sc:set_ddns_username(ddnsSettings.username)

        if #ddnsSettings.password == 0 then
            return 'ErrorInvalidPassword'
        end
        sc:set_ddns_password(ddnsSettings.password)

        if not util.isValidHostName(ddnsSettings.hostName) then
            return 'ErrorInvalidHostName'
        end
        sc:set_ddns_hostname(ddnsSettings.hostName)

        sc:set_ddns_wildcard_enabled(ddnsWildcard)

        local mailExchangeHostName
        if ddnsSettings.isMailExchangeEnabled then
            if not ddnsSettings.mailExchangeSettings then
                return 'ErrorMissingMailExchangeSettings'
            end
            mailExchangeHostName = ddnsSettings.mailExchangeSettings.hostName
            if not util.isValidHostName(mailExchangeHostName) then
                return 'ErrorInvalidHostName'
            end
            sc:set_ddns_mx_backup(ddnsSettings.mailExchangeSettings.isBackup)
        else
            if ddnsSettings.mailExchangeSettings then
                return 'ErrorSuperfluousMailExchangeSettings'
            end
            mailExchangeHostName = ''
        end
        sc:set_ddns_mx_hostname(mailExchangeHostName)
    end
end

--
-- Get the current DDNS status.
--
-- input = CONTEXT
--
function _M.getStatus(sc)
    sc:readlock()
    if not platform.isReady(sc) then
        return nil, '_ErrorNotReady'
    end
    if not sc:get_ddns_enabled() then
        return 'NotEnabled'
    end
    local status = sc:get_ddns_status()
    if status == 'success' then
        return 'Success'
    elseif status == 'error-auth' then
        return 'AuthenticationFailed'
    elseif status == 'error-connect' or
           status == 'error-server' or
           status == 'error-abuse' or
           status == 'error-hostname' or
           status == 'error-badagent' or
           status == 'error-feature' then
        return 'Failed'
    end
    return 'Connecting'
end

--
-- Get the current DDNS status.
--
-- input = CONTEXT
--
function _M.getStatus2(sc)
    sc:readlock()
    if not platform.isReady(sc) then
        return nil, '_ErrorNotReady'
    end
    if not sc:get_ddns_enabled() then
        return 'NotEnabled'
    end
    local status = sc:get_ddns_status()
    if status == 'success' then
        return 'Success'
    elseif status == 'error-auth' then
        return 'AuthenticationFailed'
    elseif status == 'error-hostname' then
        return 'InvalidHostname'
    elseif status == 'error-connect' or
           status == 'error-server' or
           status == 'error-abuse' or
           status == 'error-badagent' or
           status == 'error-feature' then
        return 'Failed'
    end
    return 'Connecting'
end

function _M.getSupportedDDNSProviders(sc)
    return { supportedDDNSProviders = getSupportedProviders(sc) }
end

return _M -- return the module
