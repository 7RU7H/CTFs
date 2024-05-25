--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local function GetTxBFSettings(ctx)
    local sc = ctx:sysctx()
    sc:readlock()

    return 'OK', {
       txbfEnabled = sc:get_wifi_mrvl_txbf_enabled(),
       txbf3x3Only = sc:get_wifi_mrvl_txbf_3x3only()
    }
end

local function SetTxBFSettings(ctx, input)
    local sc = ctx:sysctx()
    sc:writelock()

    sc:set_wifi_mrvl_txbf_enabled(input.txbfEnabled)
    sc:set_wifi_mrvl_txbf_3x3only(input.txbf3x3Only)

    return 'OK'
end

local function GetAdvancedSettings(ctx)
    local sc = ctx:sysctx()
    sc:readlock()
    local supportedRadios = require('wirelessap').getSupportedRadios(sc)
    local radios = {}
    local isMUMIMOSupported = sc:is_wifi_mrvl_mumimo_supported()
    for radioID, radio in pairs(supportedRadios) do
        local isMUMIMOEnabled = nil
        if isMUMIMOSupported then
            isMUMIMOEnabled = sc:get_wifi_mrvl_mumimo_enabled(radio.apName)
        end
        local radioOutput = {
            radioID = radioID,
            isTxBFEnabled = sc:get_wifi_mrvl_txbf_enabled(radio.apName),
            isTxBF3x3Only = sc:get_wifi_mrvl_txbf_3x3only(radio.apName),
            isMUMIMOEnabled = isMUMIMOEnabled
        }
        table.insert(radios, radioOutput)
    end
    return 'OK', {
        isMUMIMOSupported = isMUMIMOSupported,
        advancedRadioSettings = radios
    }
end

local function SetAdvancedSettings(ctx, input)
    local sc = ctx:sysctx()
    sc:writelock()
    for i, newSettings in ipairs(input.advancedRadioSettings) do
        local profile = require('wirelessap').getSupportedRadios(sc)[newSettings.radioID]
        if not profile then
            return 'ErrorUnknownRadio'
        end
        sc:set_wifi_mrvl_txbf_enabled(newSettings.isTxBFEnabled, profile.apName)
        sc:set_wifi_mrvl_txbf_3x3only(newSettings.isTxBF3x3Only, profile.apName)
        if sc:is_wifi_mrvl_mumimo_supported() and (newSettings.isMUMIMOEnabled ~= nil) then
            sc:set_wifi_mrvl_mumimo_enabled(profile.apName, newSettings.isMUMIMOEnabled)
        end
    end
    return 'OK'
end

return require('libhdklua').loadmodule('jnap_wirelessap_marvell'), {
    ['http://linksys.com/jnap/wirelessap/marvell/GetTxBFSettings'] = GetTxBFSettings,
    ['http://linksys.com/jnap/wirelessap/marvell/SetTxBFSettings'] = SetTxBFSettings,
    ['http://linksys.com/jnap/wirelessap/marvell/GetAdvancedSettings'] = GetAdvancedSettings,
    ['http://linksys.com/jnap/wirelessap/marvell/SetAdvancedSettings'] = SetAdvancedSettings
}
