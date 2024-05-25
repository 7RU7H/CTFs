--
-- 2019 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author: hsulliva $
-- $DateTime: 2020/02/06 14:32:09 $
-- $Id: //depot-irv/lego/branches/wrt_platform_sp1/lego_overlay/proprietary/jnap/modules/wirelessap/wirelessap_server.lua#4 $
--

local function GetRadioInfo(ctx)
    local wirelessap = require('wirelessap')

    local sc = ctx:sysctx()
    return 'OK', {
        radios = wirelessap.getRadioInfo(sc)
    }
end

local function GetRadioInfo3(ctx)
    local wirelessap = require('wirelessap')

    local sc = ctx:sysctx()
    return 'OK', wirelessap.getRadioInfo3(sc)
end

local function SetRadioSettings(ctx, input)
    local wirelessap = require('wirelessap')

    local sc = ctx:sysctx()
    sc:writelock()
    if sc:is_wifi_band_steering_supported() and sc:get_wifi_band_steering_enabled() then
        return 'ErrorBandSteeringIsEnabled'
    end

    local error
    for i, newSettings in ipairs(input.radios) do
        error = wirelessap.setWirelessRadioSettings(sc, newSettings.radioID, newSettings.settings)
        if error then
            return error
        end
    end
    return 'OK'
end

local function SetRadioSettings3(ctx, input)
    local wirelessap = require('wirelessap')
    local sc = ctx:sysctx()

    return wirelessap.setWirelessRadioSettings3(sc, input) or 'OK'
end

local function GetAdvancedRadioInfo(ctx)
    local wirelessap = require('wirelessap')

    local sc = ctx:sysctx()
    local radioMap = wirelessap.getWirelessRadioMap(sc)
    local radios = {}
    for radioID, radio in pairs(radioMap) do
        -- Convert the N rates to strings as the float type is not currently supported
        -- $todo: remove this once supportedNRatesMbps[] is of type float
        local supportedNRatesMbpsStrings = {}
        for _, rate in ipairs(radio.supportedNRatesMbps) do
                table.insert(supportedNRatesMbpsStrings, tostring(rate))
            end
        local radioOutput = {
            radioID = radioID,
            supportedRatesMbps = radio.supportedRatesMbps,
            supportedNRatesMbps = supportedNRatesMbpsStrings
        }
        radioOutput.settings = wirelessap.getAdvancedWirelessRadioSettings(sc, radioID)
        if radioOutput.settings then
            if radioOutput.settings.nRateMbps ~= nil then
                radioOutput.settings.nRateMbps = tostring(radioOutput.settings.nRateMbps)
            end
            table.insert(radios, radioOutput)
        end
    end
    return 'OK', {
        radios = radios
    }
end

local function SetAdvancedRadioSettings(ctx, input)
    local wirelessap = require('wirelessap')

    local sc = ctx:sysctx()
    local error
    for i, newSettings in ipairs(input.radios) do
        -- Convert the N rate to a number as the float type is not currently supported
        -- $todo: remove this once nRateMbps is of type float
        if newSettings.settings.nRateMbps ~= nil then
            newSettings.settings.nRateMbps = tonumber(newSettings.settings.nRateMbps)
        end
        error = wirelessap.setAdvancedWirelessRadioSettings(sc, newSettings.radioID, newSettings.settings)
        if error then
            return error
        end
    end
    return 'OK'
end

local function StartWPSServerSession(ctx, input)
    local wirelessap = require('wirelessap')

    local sc = ctx:sysctx()
    local error = wirelessap.startWPSServerSession(sc, input.clientPIN)
    return error or 'OK'
end

local function StopWPSServerSession(ctx)
    local wirelessap = require('wirelessap')

    local sc = ctx:sysctx()
    local error = wirelessap.stopWPSServerSession(sc)
    return error or 'OK'
end

local function GetWPSServerSessionStatus(ctx)
    local wirelessap = require('wirelessap')

    local sc = ctx:sysctx()
    return 'OK', {
        isWPSSessionInProgress = wirelessap.isWPSSessionInProgress(sc),
        serverPIN = wirelessap.getWPSServerPIN(sc),
        lastResult = wirelessap.getWPSServerResult(sc)
    }
end

local function GetWPSServerSettings(ctx)
    local wirelessap = require('wirelessap')

    local sc = ctx:sysctx()
    return 'OK', {
       enabled = wirelessap.getWPSServerEnabled(sc)
    }
end

local function SetWPSServerSettings(ctx, input)
    local wirelessap = require('wirelessap')

    local sc = ctx:sysctx()
    local error = wirelessap.setWPSServerEnabled(sc, input.enabled)
    return error or 'OK'
end

local function IsWPSServerAvailable(ctx)
    local wirelessap = require('wirelessap')

    local sc = ctx:sysctx()
    return 'OK', {
        available = wirelessap.isWPSServerAvailable(sc)
    }
end

local function GetAirtimeFairnessSettings(ctx)
    local wirelessap = require('wirelessap')

    local sc = ctx:sysctx()
    output = {}
    output.isAirtimeFairnessSupported = wirelessap.getAirtimeFairnessSupported(sc)
    if output.isAirtimeFairnessSupported then
        output.isAirtimeFairnessEnabled = wirelessap.getAirtimeFairnessSettings(sc)
    end
    return 'OK', output
end

local function SetAirtimeFairnessSettings(ctx, input)
    local wirelessap = require('wirelessap')

    local sc = ctx:sysctx()
    local error = wirelessap.setAirtimeFairnessSettings(sc, input.isAirtimeFairnessEnabled)
    return error or 'OK'
end

local function GetDFSSettings(ctx)
    local wirelessap = require('wirelessap')
    local sc = ctx:sysctx()

    return 'OK', wirelessap.getDFSSettings(sc)
end

local function SetDFSSettings(ctx, input)
    local wirelessap = require('wirelessap')
    local sc = ctx:sysctx()

    local error = wirelessap.setDFSSettings(sc, input)
    return error or 'OK'
end

return require('libhdklua').loadmodule('jnap_wirelessap'), {
    ['http://linksys.com/jnap/wirelessap/GetRadioInfo'] = GetRadioInfo,
    ['http://linksys.com/jnap/wirelessap/GetRadioInfo3'] = GetRadioInfo3,
    ['http://linksys.com/jnap/wirelessap/SetRadioSettings'] = SetRadioSettings,
    ['http://linksys.com/jnap/wirelessap/SetRadioSettings3'] = SetRadioSettings3,
    ['http://linksys.com/jnap/wirelessap/GetAdvancedRadioInfo'] = GetAdvancedRadioInfo,
    ['http://linksys.com/jnap/wirelessap/SetAdvancedRadioSettings'] = SetAdvancedRadioSettings,
    ['http://linksys.com/jnap/wirelessap/StartWPSServerSession'] = StartWPSServerSession,
    ['http://linksys.com/jnap/wirelessap/StopWPSServerSession'] = StopWPSServerSession,
    ['http://linksys.com/jnap/wirelessap/GetWPSServerSessionStatus'] = GetWPSServerSessionStatus,
    ['http://linksys.com/jnap/wirelessap/GetWPSServerSettings'] = GetWPSServerSettings,
    ['http://linksys.com/jnap/wirelessap/SetWPSServerSettings'] = SetWPSServerSettings,
    ['http://linksys.com/jnap/wirelessap/IsWPSServerAvailable'] = IsWPSServerAvailable,
    ['http://linksys.com/jnap/wirelessap/GetAirtimeFairnessSettings'] = GetAirtimeFairnessSettings,
    ['http://linksys.com/jnap/wirelessap/SetAirtimeFairnessSettings'] = SetAirtimeFairnessSettings,
    ['http://linksys.com/jnap/wirelessap/GetDFSSettings'] = GetDFSSettings,
    ['http://linksys.com/jnap/wirelessap/SetDFSSettings'] = SetDFSSettings
}
