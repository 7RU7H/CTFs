--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local function GetManagementSettings(ctx)
    local routermanagement = require('routermanagement')

    local sc = ctx:sysctx()
    return 'OK', routermanagement.getManagementSettings(sc)
end

local function GetManagementSettings2(ctx)
    local routermanagement = require('routermanagement')

    local sc = ctx:sysctx()
    return 'OK', routermanagement.getManagementSettings2(sc)
end

local function SetManagementSettings(ctx, input)
    local routermanagement = require('routermanagement')

    local sc = ctx:sysctx()
    local error = routermanagement.setManagementSettings(sc, input)
    return error or 'OK'
end

local function SetManagementSettings2(ctx, input)
    local routermanagement = require('routermanagement')

    local sc = ctx:sysctx()
    local error = routermanagement.setManagementSettings2(sc, input)
    return error or 'OK'
end

local function GetRemoteManagementStatus(ctx, input)
    local routermanagement = require('routermanagement')

    local sc = ctx:sysctx()
    return 'OK', routermanagement.getRemoteManagementStatus(sc)
end

return require('libhdklua').loadmodule('jnap_routermanagement'), {
    ['http://linksys.com/jnap/routermanagement/GetManagementSettings'] = GetManagementSettings,
    ['http://linksys.com/jnap/routermanagement/GetManagementSettings2'] = GetManagementSettings2,
    ['http://linksys.com/jnap/routermanagement/SetManagementSettings'] = SetManagementSettings,
    ['http://linksys.com/jnap/routermanagement/SetManagementSettings2'] = SetManagementSettings2,
    ['http://linksys.com/jnap/routermanagement/GetRemoteManagementStatus'] = GetRemoteManagementStatus
}
