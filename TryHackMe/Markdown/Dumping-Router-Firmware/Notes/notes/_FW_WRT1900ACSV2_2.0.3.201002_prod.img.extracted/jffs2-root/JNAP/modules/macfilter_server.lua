--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local function GetMACFilterSettings(ctx)
    local macfilter = require('macfilter')
    local platform = require('platform')

    local sc = ctx:sysctx()
    return 'OK', {
        macFilterMode = macfilter.getMode(sc),
        macAddresses = macfilter.getMACAddresses(sc),
        maxMACAddresses = platform.MAX_MACFILTER_ADDRESSES
    }
end

local function SetMACFilterSettings(ctx, input)
    local macfilter = require('macfilter')

    local sc = ctx:sysctx()
    local error =
        macfilter.setMode(sc, input.macFilterMode) or
        macfilter.setMACAddresses(sc, input.macAddresses)
    return error or 'OK'
end

return require('libhdklua').loadmodule('jnap_macfilter'), {
    ['http://linksys.com/jnap/macfilter/GetMACFilterSettings'] = GetMACFilterSettings,
    ['http://linksys.com/jnap/macfilter/SetMACFilterSettings'] = SetMACFilterSettings,
}
