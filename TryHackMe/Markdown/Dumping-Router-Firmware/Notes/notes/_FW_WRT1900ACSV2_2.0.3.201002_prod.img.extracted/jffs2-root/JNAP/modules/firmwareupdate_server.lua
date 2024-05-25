--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local function GetFirmwareUpdateSettings(ctx)
    local firmwareupdate = require('firmwareupdate')

    local sc = ctx:sysctx()
    return 'OK', {
        updatePolicy = firmwareupdate.getUpdatePolicy(sc),
        autoUpdateWindow = firmwareupdate.getAutoUpdateWindow(sc)
    }
end

local function SetFirmwareUpdateSettings(ctx, input)
    local firmwareupdate = require('firmwareupdate')

    local sc = ctx:sysctx()
    local error =
        firmwareupdate.setUpdatePolicy(sc, input.updatePolicy) or
        firmwareupdate.setAutoUpdateWindow(sc, input.autoUpdateWindow)
    return error or 'OK'
end

local function GetFirmwareUpdateStatus(ctx)
    local firmwareupdate = require('firmwareupdate')

    local sc = ctx:sysctx()
    return 'OK', {
        lastSuccessfulCheckTime = firmwareupdate.getLastSuccessfulCheckTime(sc),
        availableUpdate = firmwareupdate.getAvailableUpdate(sc),
        pendingOperation = firmwareupdate.getPendingOperationStatus(sc),
        lastOperationFailure = firmwareupdate.getLastOperationFailure(sc)
    }
end

local function UpdateFirmwareNow(ctx, input)
    local firmwareupdate = require('firmwareupdate')

    local sc = ctx:sysctx()
    local error = firmwareupdate.updateNow(sc, input.onlyCheck, input.updateServerURL)
    return error or 'OK'
end

return require('libhdklua').loadmodule('jnap_firmwareupdate'), {
    ['http://linksys.com/jnap/firmwareupdate/GetFirmwareUpdateSettings'] = GetFirmwareUpdateSettings,
    ['http://linksys.com/jnap/firmwareupdate/SetFirmwareUpdateSettings'] = SetFirmwareUpdateSettings,
    ['http://linksys.com/jnap/firmwareupdate/GetFirmwareUpdateStatus'] = GetFirmwareUpdateStatus,
    ['http://linksys.com/jnap/firmwareupdate/UpdateFirmwareNow'] = UpdateFirmwareNow,
}
