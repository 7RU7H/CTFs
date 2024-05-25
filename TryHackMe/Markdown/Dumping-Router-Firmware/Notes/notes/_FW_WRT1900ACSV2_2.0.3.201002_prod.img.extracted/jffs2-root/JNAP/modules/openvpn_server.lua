--
-- 2014 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local function GetOpenVPNSettings(ctx)
    local openvpn = require('openvpn')

    local sc = ctx:sysctx()
    return 'OK', openvpn.getSettings(sc)
end

local function SetOpenVPNSettings(ctx, input)
    local openvpn = require('openvpn')

    local sc = ctx:sysctx()
    local error = openvpn.setSettings(sc, input)
    return error or 'OK'
end

local function DownloadClientConnectionProfile(ctx)
    local openvpn = require('openvpn')

    local sc = ctx:sysctx()
    local profile, err = openvpn.getClientConnectionProfile(sc)
    return err or 'OK', nil == err and {
        clientConnectionProfile = profile
    } or nil
end

return require('libhdklua').loadmodule('jnap_openvpn'), {
    ['http://linksys.com/jnap/openvpn/GetOpenVPNSettings'] = GetOpenVPNSettings,
    ['http://linksys.com/jnap/openvpn/SetOpenVPNSettings'] = SetOpenVPNSettings,
    ['http://linksys.com/jnap/openvpn/DownloadClientConnectionProfile'] = DownloadClientConnectionProfile,
}
