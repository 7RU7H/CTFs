--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local function GetRouterLEDSettings(ctx)
    local routerleds = require('routerleds')

    local sc = ctx:sysctx()
    return 'OK', {
        isSwitchportLEDEnabled = routerleds.getIsSwitchportLEDEnabled(sc) or false
    }
end

local function SetRouterLEDSettings(ctx, input)
    local routerleds = require('routerleds')

    local sc = ctx:sysctx()
    return routerleds.setIsSwitchportLEDEnabled(sc, input.isSwitchportLEDEnabled) or 'OK'
end

return require('libhdklua').loadmodule('jnap_routerleds'), {
    ['http://linksys.com/jnap/routerleds/GetRouterLEDSettings'] = GetRouterLEDSettings,
    ['http://linksys.com/jnap/routerleds/SetRouterLEDSettings'] = SetRouterLEDSettings,
}
