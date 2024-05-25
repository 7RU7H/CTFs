--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local function GetDDNSSettings(ctx)
    local ddns = require('ddns')

    local sc = ctx:sysctx()
    return 'OK', ddns.getSettings(sc)
end

local function SetDDNSSettings(ctx, input)
    local ddns = require('ddns')

    local sc = ctx:sysctx()
    local error = ddns.setSettings(sc, input)
    return error or 'OK'
end

local function GetDDNSStatus(ctx)
    local ddns = require('ddns')

    local sc = ctx:sysctx()
    local status, err = ddns.getStatus(sc)
    return err or 'OK', nil == err and {
        status = status
    } or nil
end

local function GetDDNSStatus2(ctx)
    local ddns = require('ddns')

    local sc = ctx:sysctx()
    local status, err = ddns.getStatus2(sc)
    return err or 'OK', nil == err and {
        status = status
    } or nil
end

local function GetSupportedDDNSProviders(ctx)
    local ddns = require('ddns')

    local sc = ctx:sysctx()
    return 'OK', ddns.getSupportedDDNSProviders(sc)
end

return require('libhdklua').loadmodule('jnap_ddns'), {
    ['http://linksys.com/jnap/ddns/GetDDNSSettings'] = GetDDNSSettings,
    ['http://linksys.com/jnap/ddns/SetDDNSSettings'] = SetDDNSSettings,
    ['http://linksys.com/jnap/ddns/GetDDNSStatus'] = GetDDNSStatus,
    ['http://linksys.com/jnap/ddns/GetDDNSStatus2'] = GetDDNSStatus2,
    ['http://linksys.com/jnap/ddns/GetSupportedDDNSProviders'] = GetSupportedDDNSProviders
}
