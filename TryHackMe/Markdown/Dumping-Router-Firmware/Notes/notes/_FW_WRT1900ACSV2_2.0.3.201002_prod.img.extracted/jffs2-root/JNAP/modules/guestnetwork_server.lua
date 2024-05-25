--
-- 2019 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author: hsulliva $
-- $DateTime: 2020/02/06 14:32:09 $
-- $Id: //depot-irv/lego/branches/wrt_platform_sp1/lego_overlay/proprietary/jnap/modules/guestnetwork/guestnetwork_server.lua#3 $
--

local function GetGuestNetworkSettings(ctx)
    local guestnetwork = require('guestnetwork')

    local sc = ctx:sysctx()
    return 'OK', {
        isGuestNetworkEnabled = guestnetwork.getIsGuestNetworkEnabled(sc),
        broadcastGuestSSID = guestnetwork.getIsSSIDBroadcastEnabled(sc, guestnetwork.GUEST_RADIO_ID_2GHZ),
        guestSSID = guestnetwork.getGuestRadioSSID(sc, guestnetwork.GUEST_RADIO_ID_2GHZ),
        guestPassword = guestnetwork.getPassword(sc, guestnetwork.GUEST_RADIO_ID_2GHZ),
        maxSimultaneousGuests = guestnetwork.getMaxSimultaneousGuests(sc),
        canEnableGuestNetwork = guestnetwork.canGuestRadioBeEnabled(sc, guestnetwork.GUEST_RADIO_ID_2GHZ),
        guestPasswordRestrictions = guestnetwork.getGuestPasswordRestrictions(sc),
        maxSimultaneousGuestsLimit = guestnetwork.getMaxSimultaneousGuestsLimit(sc)
    }
end

local function GetGuestNetworkSettings2(ctx)
    local guestnetwork = require('guestnetwork')

    local sc = ctx:sysctx()
    local guestLANSettings = guestnetwork.getGuestLANSettings(sc)
    return 'OK', {
        ipAddress = guestLANSettings.ipAddress,
        networkPrefixLength = guestLANSettings.networkPrefixLength,
        minNetworkPrefixLength = guestnetwork.MIN_GUEST_LAN_PREFIX_LENGTH,
        maxNetworkPrefixLength = guestnetwork.MAX_GUEST_LAN_PREFIX_LENGTH,
        minAllowedLeaseHours = guestnetwork.MIN_GUEST_LEASE_HOURS,
        maxAllowedLeaseHours = guestnetwork.MAX_GUEST_LEASE_HOURS,
        leaseHours = guestLANSettings.leaseHours
    }
end

local function SetGuestNetworkSettings(ctx, input)
    local guestnetwork = require('guestnetwork')

    local sc = ctx:sysctx()
    local error =
        guestnetwork.setGuestRadioSettings(sc, {
            isGuestNetworkEnabled = input.isGuestNetworkEnabled,
            radios = {{
                radioID = guestnetwork.GUEST_RADIO_ID_2GHZ,
                isEnabled = input.isGuestNetworkEnabled,
                guestSSID = input.guestSSID,
                broadcastGuestSSID = input.broadcastGuestSSID,
                guestPassword = input.guestPassword
            }},
            maxSimultaneousGuests = input.maxSimultaneousGuests
        }, 1)
    return error or 'OK'
end

local function SetGuestNetworkSettings2(ctx, input)
    local guestnetwork = require('guestnetwork')

    local sc = ctx:sysctx()
    local error = guestnetwork.setGuestLANSettings(sc, input)
    return error or 'OK'
end

local function SetGuestNetworkSettings3(ctx, input)
    local guestnetwork = require('guestnetwork')
    local sc = ctx:sysctx()

    local error = SetGuestNetworkSettings(ctx, input)
    if error == 'OK' then
        local err = guestnetwork.restartWifiGuest(sc)
        if err then
            return err
        else
            return 'OK'
        end
    else
        return error
    end
end

local function GetGuestNetworkClients(ctx)
    local guestnetwork = require('guestnetwork')

    local sc = ctx:sysctx()
    return 'OK', {
        clients = guestnetwork.getAuthorizedClients(sc)
    }
end

local function SetGuestRadioSettings(ctx, input)
    local guestnetwork = require('guestnetwork')

    local sc = ctx:sysctx()
    local error = guestnetwork.setGuestRadioSettings(sc, input, 1)
    return error or 'OK'
end

local function SetGuestRadioSettings2(ctx, input)
    local guestnetwork = require('guestnetwork')

    local sc = ctx:sysctx()
    local error = guestnetwork.setGuestRadioSettings(sc, input, 2)
    return error or 'OK'
end

local function GetGuestRadioSettings(ctx)
    local guestnetwork = require('guestnetwork')

    local sc = ctx:sysctx()
    local guestRadioSettings = {
        isGuestNetworkEnabled = guestnetwork.getIsGuestNetworkEnabled(sc),
        radios = guestnetwork.getGuestRadioSettings(sc, 1),
        maxSimultaneousGuests = guestnetwork.getMaxSimultaneousGuests(sc),
        guestPasswordRestrictions = guestnetwork.getGuestPasswordRestrictions(sc),
        maxSimultaneousGuestsLimit = guestnetwork.getMaxSimultaneousGuestsLimit(sc)
    }

    return 'OK', guestRadioSettings
end

local function GetGuestRadioSettings2(ctx)
    local guestnetwork = require('guestnetwork')

    local sc = ctx:sysctx()
    local guestRadioSettings = {
        isGuestNetworkACaptivePortal = guestnetwork.getIsGuestNetworkACaptivePortal(sc),
        isGuestNetworkEnabled = guestnetwork.getIsGuestNetworkEnabled(sc),
        radios = guestnetwork.getGuestRadioSettings(sc, 2)
    }
    if guestnetwork.getIsGuestNetworkACaptivePortal(sc) then
        guestRadioSettings.guestPasswordRestrictions = guestnetwork.getGuestPasswordRestrictions(sc)
        guestRadioSettings.maxSimultaneousGuests = guestnetwork.getMaxSimultaneousGuests(sc)
        guestRadioSettings.maxSimultaneousGuestsLimit = guestnetwork.getMaxSimultaneousGuestsLimit(sc)
    end

    return 'OK', guestRadioSettings
end

return require('libhdklua').loadmodule('jnap_guestnetwork'), {
    ['http://linksys.com/jnap/guestnetwork/GetGuestNetworkSettings'] = GetGuestNetworkSettings,
    ['http://linksys.com/jnap/guestnetwork/SetGuestNetworkSettings'] = SetGuestNetworkSettings,
    ['http://linksys.com/jnap/guestnetwork/GetGuestNetworkSettings2'] = GetGuestNetworkSettings2,
    ['http://linksys.com/jnap/guestnetwork/SetGuestNetworkSettings2'] = SetGuestNetworkSettings2,
    ['http://linksys.com/jnap/guestnetwork/SetGuestNetworkSettings3'] = SetGuestNetworkSettings3,
    ['http://linksys.com/jnap/guestnetwork/GetGuestNetworkClients'] = GetGuestNetworkClients,
    ['http://linksys.com/jnap/guestnetwork/GetGuestRadioSettings'] = GetGuestRadioSettings,
    ['http://linksys.com/jnap/guestnetwork/SetGuestRadioSettings'] = SetGuestRadioSettings,
    ['http://linksys.com/jnap/guestnetwork/GetGuestRadioSettings2'] = GetGuestRadioSettings2,
    ['http://linksys.com/jnap/guestnetwork/SetGuestRadioSettings2'] = SetGuestRadioSettings2,
}
