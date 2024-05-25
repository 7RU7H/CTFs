--
-- 2018 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author: hesia $
-- $DateTime: 2019/07/15 12:33:28 $
-- $Id: //depot-irv/user/hesia/pinnacle_f70_auth_jnap/lego_overlay/proprietary/jnap/modules/diagnostics/diagnostics_server.lua#2 $
--

local function StartPing(ctx, input)
    local diagnostics = require('diagnostics')
    local platform = require('platform')

    platform.registerLoggingCallback(function(level, message) ctx:serverlog(level, message) end)

    local sc = ctx:sysctx()
    local error = diagnostics.startPing(sc, input.host, input.packetSizeBytes, input.pingCount)
    return error or 'OK'
end

local function StopPing(ctx)
    local diagnostics = require('diagnostics')
    local platform = require('platform')

    platform.registerLoggingCallback(function(level, message) ctx:serverlog(level, message) end)

    local sc = ctx:sysctx()
    diagnostics.stopPing(sc)
    return 'OK'
end

local function GetPingStatus(ctx)
    local diagnostics = require('diagnostics')

    local sc = ctx:sysctx()
    return 'OK', {
        isRunning = diagnostics.getRunningPingPID(sc) ~= nil,
        pingLog = diagnostics.getPingLog(sc)
    }
end

local function StartTraceroute(ctx, input)
    local diagnostics = require('diagnostics')
    local platform = require('platform')

    platform.registerLoggingCallback(function(level, message) ctx:serverlog(level, message) end)

    local sc = ctx:sysctx()
    local error = diagnostics.startTraceroute(sc, input.host)
    return error or 'OK'
end

local function StopTraceroute(ctx)
    local diagnostics = require('diagnostics')
    local platform = require('platform')

    platform.registerLoggingCallback(function(level, message) ctx:serverlog(level, message) end)

    local sc = ctx:sysctx()
    diagnostics.stopTraceroute(sc)
    return 'OK'
end

local function GetTracerouteStatus(ctx)
    local diagnostics = require('diagnostics')

    local sc = ctx:sysctx()
    return 'OK', {
        isRunning = diagnostics.getRunningTraceroutePID(sc) ~= nil,
        tracerouteLog = diagnostics.getTracerouteLog(sc)
    }
end

local function RestorePreviousFirmware(ctx)
    local diagnostics = require('diagnostics')

    local sc = ctx:sysctx()
    diagnostics.restorePreviousFirmware(sc)
    return 'OK'
end

local function GetSysinfoData(ctx)
    local diagnostics = require('diagnostics')
    local sc = ctx:sysctx()
    local error, sysinfo = diagnostics.getSysinfoData(sc)

    if error then
        return error
    end

    return 'OK', {
        sysinfo = sysinfo
    }
end

local function SendSysinfoEmail(ctx, input)
    local diagnostics = require('diagnostics')
    local sc = ctx:sysctx()
    local error = diagnostics.sendSysinfoEmail(sc, input)

    return error or 'OK'
end

local function GetSystemStats(ctx, input)
    return 'OK',  {
        uptimeSeconds = require('platform').getUptimeSeconds()
    }
end

return require('libhdklua').loadmodule('jnap_diagnostics'), {
    ['http://linksys.com/jnap/diagnostics/StartPing'] = StartPing,
    ['http://linksys.com/jnap/diagnostics/StopPing'] = StopPing,
    ['http://linksys.com/jnap/diagnostics/GetPingStatus'] = GetPingStatus,
    ['http://linksys.com/jnap/diagnostics/StartTraceroute'] = StartTraceroute,
    ['http://linksys.com/jnap/diagnostics/StopTraceroute'] = StopTraceroute,
    ['http://linksys.com/jnap/diagnostics/GetTracerouteStatus'] = GetTracerouteStatus,
    ['http://linksys.com/jnap/diagnostics/RestorePreviousFirmware'] = RestorePreviousFirmware,
    ['http://linksys.com/jnap/diagnostics/GetSysinfoData'] = GetSysinfoData,
    ['http://linksys.com/jnap/diagnostics/SendSysinfoEmail'] = SendSysinfoEmail,
    ['http://linksys.com/jnap/diagnostics/GetSystemStats'] = GetSystemStats
}
