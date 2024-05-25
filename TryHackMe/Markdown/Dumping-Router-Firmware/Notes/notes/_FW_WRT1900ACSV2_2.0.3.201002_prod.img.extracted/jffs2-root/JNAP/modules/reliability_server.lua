--
-- 2018 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author: hsulliva $
-- $DateTime: 2020/02/06 14:32:09 $
-- $Id: //depot-irv/lego/branches/wrt_platform_sp1/lego_overlay/proprietary/jnap/modules/reliability/reliability_server.lua#1 $
--

local function GetDiagnosticsSettings(ctx)
    local diagnostics = require('diagnostics')
    local sc = ctx:sysctx()

    local settings = diagnostics.getDiagnosticsSettings(sc)

    return 'OK', settings
end

local function SetDiagnosticsSettings(ctx, input)
    local diagnostics = require('diagnostics')
    local sc = ctx:sysctx()
    local error = diagnostics.setDiagnosticsSettings(sc, input)

    return error or 'OK'
end

local function RequestSysinfoData2(ctx, input)
    local diagnostics = require('diagnostics')
    local sc = ctx:sysctx()
    local error = diagnostics.requestSysinfoData2(sc, input)

    return error or 'OK'
end

local function GetSysinfoRequestStatus2(ctx)
    local diagnostics = require('diagnostics')
    local sc = ctx:sysctx()

    return 'OK', diagnostics.getSysinfoRequestStatus2(sc)
end

local function GetSysinfoData4(ctx)
    local diagnostics = require('diagnostics')
    local sc = ctx:sysctx()

    return 'OK', {
        sysinfoData = diagnostics.getSysinfoData4(sc)
    }
end

local function GetSupportedSysinfoSections(ctx)
    local diagnostics = require('diagnostics')

    return 'OK', {
        supportedSysinfoSections = diagnostics.getSupportedSysinfoSections()
    }
end

return require('libhdklua').loadmodule('jnap_reliability'), {
    ['http://linksys.com/jnap/diagnostics/GetDiagnosticsSettings'] = GetDiagnosticsSettings,
    ['http://linksys.com/jnap/diagnostics/SetDiagnosticsSettings'] = SetDiagnosticsSettings,
    ['http://linksys.com/jnap/diagnostics/RequestSysinfoData2'] = RequestSysinfoData2,
    ['http://linksys.com/jnap/diagnostics/GetSysinfoRequestStatus2'] = GetSysinfoRequestStatus2,
    ['http://linksys.com/jnap/diagnostics/GetSysinfoData4'] = GetSysinfoData4,
    ['http://linksys.com/jnap/diagnostics/GetSupportedSysinfoSections'] = GetSupportedSysinfoSections
}
