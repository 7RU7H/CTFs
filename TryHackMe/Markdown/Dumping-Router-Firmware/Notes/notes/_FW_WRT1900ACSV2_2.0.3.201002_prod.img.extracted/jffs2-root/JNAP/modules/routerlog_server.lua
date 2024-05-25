--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local function GetLogSettings(ctx)
    local routerlog = require('routerlog')

    local sc = ctx:sysctx()
    return 'OK', {
        isLoggingEnabled = routerlog.getIsLoggingEnabled(sc)
    }
end

local function SetLogSettings(ctx, input)
    local routerlog = require('routerlog')

    local sc = ctx:sysctx()
    local error = routerlog.setIsLoggingEnabled(sc, input.isLoggingEnabled)
    return error or 'OK'
end

local function GetIncomingLogEntries(ctx, input)
    local platform = require('platform')

    local sc = ctx:sysctx()
    if input.firstEntryIndex < 1 then
        return 'ErrorInvalidFirstEntryIndex'
    end
    if input.entryCount < 1 then
        return 'ErrorInvalidEntryCount'
    end
    return 'OK', {
        entries = platform.getLogEntries(sc, 'IncomingTraffic', input.firstEntryIndex, input.entryCount)
    }
end

local function GetOutgoingLogEntries(ctx, input)
    local platform = require('platform')

    local sc = ctx:sysctx()
    if input.firstEntryIndex < 1 then
        return 'ErrorInvalidFirstEntryIndex'
    end
    if input.entryCount < 1 then
        return 'ErrorInvalidEntryCount'
    end
    return 'OK', {
        entries = platform.getLogEntries(sc, 'OutgoingTraffic', input.firstEntryIndex, input.entryCount)
    }
end

local function GetSecurityLogEntries(ctx, input)
    local platform = require('platform')

    local sc = ctx:sysctx()
    if input.firstEntryIndex < 1 then
        return 'ErrorInvalidFirstEntryIndex'
    end
    if input.entryCount < 1 then
        return 'ErrorInvalidEntryCount'
    end
    return 'OK', {
        entries = platform.getLogEntries(sc, 'Security', input.firstEntryIndex, input.entryCount)
    }
end

local function GetDHCPLogEntries(ctx, input)
    local platform = require('platform')

    local sc = ctx:sysctx()
    if input.firstEntryIndex < 1 then
        return 'ErrorInvalidFirstEntryIndex'
    end
    if input.entryCount < 1 then
        return 'ErrorInvalidEntryCount'
    end
    return 'OK', {
        entries = platform.getLogEntries(sc, 'DHCPClient', input.firstEntryIndex, input.entryCount)
    }
end

local function DeleteLogEntries(ctx)
    local platform = require('platform')

    platform.deleteLog()
    return 'OK'
end

return require('libhdklua').loadmodule('jnap_routerlog'), {
    ['http://linksys.com/jnap/routerlog/DeleteLogEntries'] = DeleteLogEntries,
    ['http://linksys.com/jnap/routerlog/GetLogSettings'] = GetLogSettings,
    ['http://linksys.com/jnap/routerlog/SetLogSettings'] = SetLogSettings,
    ['http://linksys.com/jnap/routerlog/GetIncomingLogEntries'] = GetIncomingLogEntries,
    ['http://linksys.com/jnap/routerlog/GetOutgoingLogEntries'] = GetOutgoingLogEntries,
    ['http://linksys.com/jnap/routerlog/GetSecurityLogEntries'] = GetSecurityLogEntries,
    ['http://linksys.com/jnap/routerlog/GetDHCPLogEntries'] = GetDHCPLogEntries
}
