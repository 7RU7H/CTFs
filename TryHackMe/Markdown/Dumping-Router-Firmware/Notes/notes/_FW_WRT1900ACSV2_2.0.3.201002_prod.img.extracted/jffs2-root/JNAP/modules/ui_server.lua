--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--


local function GetRemoteSetting(ctx)
    local device = require('device')
    local sc = ctx:sysctx()

    return 'OK',
    {
        isEnabled = device.getRemoteUIEnabled(sc) or false
    }
end

local function SetRemoteSetting(ctx, input)
    local device = require('device')
    local sc = ctx:sysctx()

    local error = device.setRemoteUIEnabled(sc, input.isEnabled)
    return error or 'OK'
end

local function GetCloudServerStatus(ctx)
    local cloud = require('cloud')
    local sc = ctx:sysctx()

    return 'OK',
    {
        isAccessable = cloud.getServerStatus(sc) or false
    }
end

return require('libhdklua').loadmodule('jnap_ui'), {
    ['http://linksys.com/jnap/ui/GetRemoteSetting'] = GetRemoteSetting,
    ['http://linksys.com/jnap/ui/SetRemoteSetting'] = SetRemoteSetting,
    ['http://linksys.com/jnap/ui/GetCloudServerStatus'] = GetCloudServerStatus
}
