--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local function GetUPnPSettings(ctx)
    local routerupnp = require('routerupnp')

    local sc = ctx:sysctx()
    return 'OK', {
        isUPnPEnabled = routerupnp.getIsEnabled(sc),
        canUsersConfigure = routerupnp.getCanUsersConfigure(sc),
        canUsersDisableWANAccess = routerupnp.getCanUsersDisableWANAccess(sc)
    }
end

local function SetUPnPSettings(ctx, input)
    local routerupnp = require('routerupnp')

    local sc = ctx:sysctx()
    local error =
        routerupnp.setIsEnabled(sc, input.isUPnPEnabled) or
        routerupnp.setCanUsersConfigure(sc, input.canUsersConfigure) or
        routerupnp.setCanUsersDisableWANAccess(sc, input.canUsersDisableWANAccess)
    return error or 'OK'
end

return require('libhdklua').loadmodule('jnap_routerupnp'), {
    ['http://linksys.com/jnap/routerupnp/GetUPnPSettings'] = GetUPnPSettings,
    ['http://linksys.com/jnap/routerupnp/SetUPnPSettings'] = SetUPnPSettings,
}
