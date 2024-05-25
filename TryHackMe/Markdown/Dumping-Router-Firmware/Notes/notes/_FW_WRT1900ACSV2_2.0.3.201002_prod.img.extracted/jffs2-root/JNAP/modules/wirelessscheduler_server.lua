--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local wirelessscheduler = require('wirelessscheduler')

local function GetWirelessSchedulerSettings(ctx)
    local sc = ctx:sysctx()
    return 'OK', {
        isWirelessSchedulerEnabled = wirelessscheduler.getIsEnabled(sc),
        wirelessSchedule = wirelessscheduler.getWirelessSchedule(sc),
        configuredRadios = wirelessscheduler.getConfiguredRadios(sc),
        configurableRadios = wirelessscheduler.getConfigurableRadios(sc)
    }
end

local function SetWirelessSchedulerSettings(ctx, input)
    local sc = ctx:sysctx()
    local error =
        wirelessscheduler.setIsEnabled(sc, input.isWirelessSchedulerEnabled) or
        wirelessscheduler.setWirelessSchedule(sc, input.wirelessSchedule) or
        wirelessscheduler.setConfiguredRadios(sc, input.configuredRadios)
    return error or 'OK'
end

return require('libhdklua').loadmodule('jnap_wirelessscheduler'), {
    ['http://linksys.com/jnap/wirelessscheduler/GetWirelessSchedulerSettings'] = GetWirelessSchedulerSettings,
    ['http://linksys.com/jnap/wirelessscheduler/SetWirelessSchedulerSettings'] = SetWirelessSchedulerSettings,
}
