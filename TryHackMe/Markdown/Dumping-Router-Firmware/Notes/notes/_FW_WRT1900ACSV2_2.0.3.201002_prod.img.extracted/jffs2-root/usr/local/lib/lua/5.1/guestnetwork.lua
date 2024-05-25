--
-- 2019 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author: hsulliva $
-- $DateTime: 2020/02/06 14:32:09 $
-- $Id: //depot-irv/lego/branches/wrt_platform_sp1/lego_overlay/proprietary/jnap/lualib/guestnetwork.lua#3 $
--

-- guestnetwork.lua - library to configure guest network state.

local hdk = require('libhdklua')
local platform = require('platform')
local wirelessap = require('wirelessap')
local util = require('util')
local device = require('device')

local _M = {} -- create the module

_M.WIFI_STATUS = 'wifi-status'

_M.MIN_GUEST_PASSWORD_LENGTH_WPA = 8
_M.MAX_GUEST_PASSWORD_LENGTH_WPA = 64

_M.MIN_GUEST_PASSWORD_LENGTH_CAPTIVE_PORTAL = 4
_M.MAX_GUEST_PASSWORD_LENGTH_CAPTIVE_PORTAL = 32

-- Limits for the guest LAN prefix length.
-- This is dependent on underlying firmware support, which may not be present on all platforms.
-- Currently users are not allowed to change the subnet size.
_M.MIN_GUEST_LAN_PREFIX_LENGTH = 24
_M.MAX_GUEST_LAN_PREFIX_LENGTH = 24

_M.MIN_GUEST_LEASE_HOURS = 1 -- arbitrary
_M.MAX_GUEST_LEASE_HOURS = 24 -- arbitrary

_M.GUEST_RADIO_ID_2GHZ = 'RADIO_2.4GHz'
_M.GUEST_RADIO_ID_5GHZ = 'RADIO_5GHz'

_M.GUEST_RADIO_PROFILES = {
    [_M.GUEST_RADIO_ID_2GHZ] = {
        ifname = 'guest_wifi_phy_ifname',
        band = '2.4GHz',
        isEnabled = 'wl0_guest_enabled',
        guestSSID = 'guest_ssid',
        broadcastGuestSSID = 'guest_ssid_broadcast',
        guestPassword = 'guest_password',
        apName = 'wl0'
    },
    [_M.GUEST_RADIO_ID_5GHZ] = {
        ifname = 'wl1_guest_wifi_phy_ifname',
        band = '5GHz',
        isEnabled = 'wl1_guest_enabled',
        guestSSID = 'wl1_guest_ssid',
        broadcastGuestSSID = 'wl1_guest_ssid_broadcast',
        guestPassword = 'wl1_guest_password',
        apName = 'wl1'
    }
}

local function getSupportedRadios(sc)
    local radios = {}
    sc:readlock()
    local supported = sc:get_wifi_devices()
    for id, value in pairs(_M.GUEST_RADIO_PROFILES) do
        if supported:find(value.apName, 1, true) then
            radios[id] = value
        end
    end
    return radios
end

local function getMinGuestPasswordLength(sc)
    if _M.getIsGuestNetworkACaptivePortal(sc) then
        return _M.MIN_GUEST_PASSWORD_LENGTH_CAPTIVE_PORTAL
    else
        return _M.MIN_GUEST_PASSWORD_LENGTH_WPA
    end
end

local function getMaxGuestPasswordLength(sc)
    if _M.getIsGuestNetworkACaptivePortal(sc) then
        return _M.MAX_GUEST_PASSWORD_LENGTH_CAPTIVE_PORTAL
    else
        return _M.MAX_GUEST_PASSWORD_LENGTH_WPA
    end
end

function _M.getGuestPasswordRestrictions(sc)
    sc:readlock()
    return {
        minLength = getMinGuestPasswordLength(sc),
        maxLength = getMaxGuestPasswordLength(sc),
        allowedCharacters = {
            { lowCodepoint = 0x30, highCodepoint = 0x39 },
            { lowCodepoint = 0x41, highCodepoint = 0x5A },
            { lowCodepoint = 0x61, highCodepoint = 0x7A }
        }
    }
end

--
-- Determine whether a guest password is valid.
--
-- input = CONTEXT, STRING
--
-- output = BOOLEAN
--
function _M.isValidGuestPassword(sc, password)
    if type(password) ~= 'string' then
        return false
    end

    if #password < getMinGuestPasswordLength(sc) or #password > getMaxGuestPasswordLength(sc) then
        return false
    end

    -- the password can only include alpha-numeric characters
    return password:find('^[%w]*$') ~= nil
end

--
-- Get whether the guest network can be enabled given the router's current configuration.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.canGuestRadioBeEnabled(sc, radioID)
    sc:readlock()
    return (sc:get_guest_access_if_state(_M.GUEST_RADIO_PROFILES[radioID].apName) == 'up') or false
end

--
-- Get whether the guest network is enabled.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.getIsGuestNetworkEnabled(sc)
    sc:readlock()
    return sc:get_guest_access_enabled()
end

--
-- Get whether the wireless network uses a captive portal.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.getIsGuestNetworkACaptivePortal(sc)
    sc:readlock()
    return (not sc:is_guest_access_secured())
end

--
-- Set whether the guest network is enabled.
--
-- input = CONTEXT, BOOLEAN
--
function _M.setIsGuestNetworkEnabled(sc, isGuestNetworkEnabled)
    sc:writelock()
    if not platform.isReady(sc) or sc:getevent(_M.WIFI_STATUS) ~= 'started' then
        return '_ErrorNotReady'
    end
    sc:set_guest_access_enabled(isGuestNetworkEnabled)
end

--
-- Set whether the guest network is enabled. and set wifi_guest-restart
--
-- input = CONTEXT, BOOLEAN
--
function _M.restartWifiGuest(sc)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    sc:set_wifi_guest_restart()
end

--
-- Check if guest radio settings are valid when guest network is enabled.
--
-- input = CONTEXT
--
-- output = NIL_OR_ONE_OF(
--     'ErrorCannotEnableGuestNetwork',
--     'ErrorGuestSSIDConflict'
-- )
--
function _M.validateGuestRadioSettings(sc)
    sc:readlock()
    if _M.getIsGuestNetworkEnabled(sc) then
        for guestRadioID, guestProfile in pairs(_M.GUEST_RADIO_PROFILES) do
            if _M.getIsGuestRadioEnabled(sc, guestRadioID) and not _M.canGuestRadioBeEnabled(sc, guestRadioID) then
                return 'ErrorCannotEnableGuestNetwork'
            end
            for wirelessRadioID, wirelessProfile in pairs(wirelessap.RADIO_PROFILES) do
                if _M.getIsGuestRadioEnabled(sc, guestRadioID) and sc:get_wifi_ssid(wirelessProfile.apName) == _M.getGuestRadioSSID(sc, guestRadioID) then
                    return 'ErrorGuestSSIDConflict'
                end
            end
        end
    end
end

--
-- Get whether the guest radio is enabled.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.getIsGuestRadioEnabled(sc, radioID)
    sc:readlock()
    return sc:get_guest_ap_enabled(_M.GUEST_RADIO_PROFILES[radioID].apName)
end

--
-- Set whether the guest radio is enabled.
--
-- input = CONTEXT, BOOLEAN
--
-- output = NIL_OR_ONE_OF(
--     'ErrorCannotEnableGuestNetwork'
-- )
--
function _M.setIsGuestRadioEnabled(sc, radioID, isRadioEnabled)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if _M.getIsGuestNetworkEnabled(sc) and isRadioEnabled and not _M.canGuestRadioBeEnabled(sc, radioID) then
        return 'ErrorCannotEnableGuestNetwork'
    end
    sc:set_guest_ap_enabled(_M.GUEST_RADIO_PROFILES[radioID].apName, isRadioEnabled)
end

--
-- Get whether the guest network SSID broadcast is enabled.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.getIsSSIDBroadcastEnabled(sc, radioID)
    sc:readlock()
    return sc:get_guest_access_broadcast_ssid(_M.GUEST_RADIO_PROFILES[radioID].apName)
end

--
-- Set whether the guest network SSID broadcast is enabled.
--
-- input = CONTEXT, BOOLEAN
--
function _M.setIsSSIDBroadcastEnabled(sc, radioID, isEnabled)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    sc:set_guest_access_broadcast_ssid(_M.GUEST_RADIO_PROFILES[radioID].apName, isEnabled)
end

--
-- Get the guest network SSID.
--
-- input = CONTEXT
--
-- output = STRING
--
function _M.getGuestRadioSSID(sc, radioID)
    sc:readlock()
    return sc:get_guest_access_ssid(_M.GUEST_RADIO_PROFILES[radioID].apName)
end

--
-- Set the guest network SSID.
--
-- input = CONTEXT, STRING
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidGuestSSID',
--     'ErrorGuestSSIDConflict'
-- )
--
function _M.setGuestRadioSSID(sc, radioID, ssid)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if not wirelessap.isValidSSID(ssid) then
        return 'ErrorInvalidGuestSSID'
    end
    for wirelessRadioID, wirelessProfile in pairs(wirelessap.RADIO_PROFILES) do
        if _M.getIsGuestNetworkEnabled(sc) and _M.getIsGuestRadioEnabled(sc, radioID) and sc:get_wifi_ssid(wirelessProfile.apName) == ssid then
            return 'ErrorGuestSSIDConflict'
        end
    end
    sc:set_guest_access_ssid(_M.GUEST_RADIO_PROFILES[radioID].apName, ssid)
end

--
-- Get the guest network password.
--
-- input = CONTEXT
--
-- output = STRING
--
function _M.getPassword(sc, radioID)
    sc:readlock()
    return sc:get_guest_access_password(_M.GUEST_RADIO_PROFILES[radioID].apName)
end

--
-- Set the guest network password.
--
-- input = CONTEXT, STRING
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidGuestPassword'
-- )
--
function _M.setPassword(sc, radioID, password)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    if not _M.getIsGuestNetworkACaptivePortal(sc) then
        if not wirelessap.isValidWPAPassphrase(password) then
            return 'ErrorInvalidGuestPassword'
        end
    else
        if not _M.isValidGuestPassword(sc, password) then
            return 'ErrorInvalidGuestPassword'
        end
    end

    sc:set_guest_access_password(_M.GUEST_RADIO_PROFILES[radioID].apName, password)
end

--
-- Get the guest network maximum simultaneous guests.
--
-- input = CONTEXT
--
-- output = NUMBER
--
function _M.getMaxSimultaneousGuests(sc)
    sc:readlock()
    return sc:get_guest_access_max_devices_allowed()
end

--
-- Set the guest network maximum simultaneous guests.
--
-- input = CONTEXT, STRING
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidMaxSimultaneousGuests'
-- )
--
function _M.setMaxSimultaneousGuests(sc, maxSimultaneousGuests, apiVersion)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if apiVersion == 2 then
        if not _M.getIsGuestNetworkACaptivePortal(sc) then
            if maxSimultaneousGuests then
                return 'ErrorSuperfluousMaxSimultaneousGuests'
            else
                return
            end
        else
            if not maxSimultaneousGuests then
                return 'ErrorMissingMaxSimultaneousGuests'
            end
        end
    end

    if maxSimultaneousGuests < 1 or
    maxSimultaneousGuests > _M.getMaxSimultaneousGuestsLimit(sc) then
        return 'ErrorInvalidMaxSimultaneousGuests'
    end
    sc:set_guest_access_max_devices_allowed(maxSimultaneousGuests)
end

--
-- Get the upper limit on the maximum simultaneous guests setting.
--
-- input = CONTEXT
--
-- output = NUMBER
--
function _M.getMaxSimultaneousGuestsLimit(sc)
    sc:readlock()
    return sc:get_guest_access_max_devices_limit()
end

--
-- Get the guest LAN network settings.
--
-- input = CONTEXT
--
-- output = {
--     ipAddress = IPADDRESS,
--     networkPrefixLength = INTEGER,
--     leaseHours = INTEGER
-- }
--
function _M.getGuestLANSettings(sc)
    sc:readlock()
    return {
        ipAddress = hdk.ipaddress(sc:get_guest_access_lan_ipaddress()),
        networkPrefixLength = util.subnetMaskToNetworkPrefixLength(hdk.ipaddress(sc:get_guest_access_lan_subnet_mask())),
        leaseHours = sc:get_guest_access_max_duration() -- 'guest_max_duration' is in hours
    }
end

--
-- Set the guest LAN network settings.
--
-- input = CONTEXT, {
--     ipAddress = IPADDRESS,
--     networkPrefixLength = INTEGER,
--     leaseHours = INTEGER
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidIPAddress'
--     'ErrorInvalidNetworkPrefixLength'
--     'ErrorInvalidLeaseLength'
-- )
--
function _M.setGuestLANSettings(sc, settings)
    sc:writelock()

    if not platform.isSupportedLANPrefixLength(settings.networkPrefixLength) then
        return 'ErrorInvalidNetworkPrefixLength'
    end
    if util.isReservedAddress(settings.ipAddress, settings.networkPrefixLength) or
        require('router').isInRouterSubnet(sc, settings.ipAddress) then
        return 'ErrorInvalidIPAddress'
    end

    if _M.MIN_GUEST_LAN_PREFIX_LENGTH > settings.networkPrefixLength or
        _M.MAX_GUEST_LAN_PREFIX_LENGTH < settings.networkPrefixLength then
        return 'ErrorInvalidNetworkPrefixLength'
    end

    if _M.MIN_GUEST_LEASE_HOURS > settings.leaseHours or
        _M.MAX_GUEST_LEASE_HOURS < settings.leaseHours then
        return 'ErrorInvalidLeaseLength'
    end

    sc:set_guest_access_max_duration(settings.leaseHours)
    sc:set_guest_access_lan_ipaddress(tostring(settings.ipAddress))
    sc:set_guest_access_lan_subnet_mask(tostring(util.networkPrefixLengthToSubnetMask(settings.networkPrefixLength)))
    sc:set_guest_access_subnet(tostring(settings.ipAddress:networkid(settings.networkPrefixLength)))

end

--
-- Authenticate a device for WAN access via the guest network.
--
-- Note: The current implementation of the guest_access service does not verify
-- the client's IP address is in the guest WLAN subnet. It only checks that it is
-- present in the DHCP lease file and therefore will allow client's on the LAN to
-- also be authenticated on the guest WLAN.
--
-- input = CONTEXT, MACADDRESS, IPADDRESS, STRING
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidClient',
--     'ErrorInvalidPassword',
--     'ErrorNetworkDisabled'
-- )
function _M.authenticate(sc, macAddress, ipAddress, password)

    -- Retrieve the MAC address from the ARP table if it was not supplied.
    if not macAddress then
        arpTable = platform.getARPTable()
        for _, entry in ipairs(arpTable) do
            if entry.ipAddress == ipAddress then
                macAddress = entry.macAddress
                break
            end
        end
    end
    if not macAddress then
        return 'ErrorInvalidClient'
    end

    -- The guestaccess lua library expects strings as inputs (so it doesn't depend on libhdklua)
    local result = require('libguest_access').authenticate(tostring(macAddress), tostring(ipAddress), password)
    if result == 'guest_access_result_success' then
        return nil
    end
    if result == 'guest_access_result_invalid_password' then
        return 'ErrorInvalidPassword'
    elseif result == 'guest_access_result_invalid_ip' then
        return 'ErrorInvalidClient'
    else
        -- result == 'guest_access_result_service_not_running' or result == 'guest_access_result_error'
        return 'ErrorNetworkDisabled'
    end
end

--
-- Get the list of authorized guest network clients.
--
-- input = CONTEXT
--
-- output = ARRAY_OF({
--     ipAddress = IPADDRESS,
--     macAddress = MACADDRESS,
--     entryTime = DATETIME,
--     expiryTime = DATETIME
-- })
--
function _M.getAuthorizedClients(sc)
    local guestList = {}

    -- If guest access is not enabled and the service is not running, there can't be any clients.
    if _M.getIsGuestNetworkEnabled(sc) and sc:get_guest_access_status() == 'started' then
        for i, guest in ipairs(require('libguest_access').guests()) do
            table.insert(guestList, {
                ipAddress = hdk.ipaddress(guest.ip),
                macAddress = hdk.macaddress(guest.mac),
                entryTime = guest.entrytime,
                expiryTime = guest.expirytime
            })
        end
    end

    return guestList
end

function _M.setGuestRadioSettings(sc, input, apiVersion)
    sc:writelock()
    local error = _M.setIsGuestNetworkEnabled(sc, input.isGuestNetworkEnabled)
    if not error then
        if #input.radios == 0 then
            error = _M.validateGuestRadioSettings(sc)
        else
            for i, newSettings in ipairs(input.radios) do
                error = _M.setGuestWirelessRadioSettings(sc, newSettings, apiVersion)
                if error then
                    return error
                end
            end
        end
    end

    if not error then
        error = _M.setMaxSimultaneousGuests(sc, input.maxSimultaneousGuests, apiVersion)
    end

    return error
end

function _M.getGuestRadioSettings(sc, apiVersion)
    sc:readlock()
    local radios = {}
    for radioID, profile in pairs(getSupportedRadios(sc)) do
        local radio = _M.getGuestWirelessRadioSettings(sc, radioID, apiVersion)
        table.insert(radios, radio)
    end
    return radios
end

function _M.setGuestWirelessRadioSettings(sc, newSettings, apiVersion)
    sc:writelock()
    local profile = getSupportedRadios(sc)[newSettings.radioID]
    if not profile then
        return 'ErrorUnknownRadio'
    end

    local guestPassword = newSettings.guestPassword

    -- Validate whether we need guestPassword or guestWPAPassphrase
    if apiVersion == 2 then
        if _M.getIsGuestNetworkACaptivePortal(sc) then
            if not newSettings.guestPassword then
                return 'ErrorMissingGuestPassword'
            end
            if newSettings.guestWPAPassphrase then
                return 'ErrorSuperfluousGuestWPAPasphrase'
            end
        else
            if not newSettings.guestWPAPassphrase then
                return 'ErrorMissingGuestWPAPasphrase'
            end
            if newSettings.guestPassword then
                return 'ErrorSuperfluousGuestPassword'
            end
            -- For non-captive portal guest network, use WPA passphrase
            guestPassword = newSettings.guestWPAPassphrase
        end
    end

    local error =
        _M.setIsGuestRadioEnabled(sc, newSettings.radioID, newSettings.isEnabled) or
        _M.setIsSSIDBroadcastEnabled(sc, newSettings.radioID, newSettings.broadcastGuestSSID) or
        _M.setGuestRadioSSID(sc, newSettings.radioID, newSettings.guestSSID) or
        _M.setPassword(sc, newSettings.radioID, guestPassword)
    return error
end

function _M.getGuestWirelessRadioSettings(sc, radioID, apiVersion)
    sc:readlock()
    if _M.GUEST_RADIO_PROFILES[radioID] then
        local guestWirelessRadioSettings = {
            radioID = radioID,
            isEnabled = _M.getIsGuestRadioEnabled(sc, radioID),
            broadcastGuestSSID = _M.getIsSSIDBroadcastEnabled(sc, radioID),
            guestSSID = _M.getGuestRadioSSID(sc, radioID),
            canEnableRadio = _M.canGuestRadioBeEnabled(sc, radioID)
        }
        if apiVersion == 2 and not _M.getIsGuestNetworkACaptivePortal(sc) then
            guestWirelessRadioSettings.guestWPAPassphrase = _M.getPassword(sc, radioID)
        else
            guestWirelessRadioSettings.guestPassword = _M.getPassword(sc, radioID)
        end

        return guestWirelessRadioSettings
    end
end

return _M -- return the module
