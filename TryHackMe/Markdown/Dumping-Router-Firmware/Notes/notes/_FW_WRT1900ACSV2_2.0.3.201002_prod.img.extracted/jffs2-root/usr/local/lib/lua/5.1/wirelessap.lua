--
-- 2019 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author: hsulliva $
-- $DateTime: 2020/02/06 14:32:09 $
-- $Id: //depot-irv/lego/branches/wrt_platform_sp1/lego_overlay/proprietary/jnap/lualib/wirelessap.lua#5 $
--

-- wireless.lua - library to configure wireless state.

local hdk = require('libhdklua')
local platform = require('platform')
local util = require('util')

local _M = {} -- create the module

_M.MAX_RADIUS_SHARED_KEY_LENGTH = 64

_M.RADIO_ID_2GHZ = 'RADIO_2.4GHz'
_M.RADIO_ID_5GHZ = 'RADIO_5GHz'
_M.RADIO_ID_5GHZ_2 = 'RADIO_5GHz_2'

_M.BAND_STEERING_MODE_BASIC = 'Basic'
_M.BAND_STEERING_MODE_TRIBAND = 'TriBand'

-- Rates, in Mbps. 0 indicates "auto". As defined in wlancfg/lib/wlan_api.h (see enum wifiTXRate definition)
_M.SUPPORTED_WIRELESS_RATES = { 0, 6, 9, 12, 18, 24, 36, 48, 54 }

-- Rates, in Mbps. 0 indicates "auto". As defined in wlancfg/lib/wlan_api.h (see enum wifiNTXRate definition)
_M.SUPPORTED_2GHZ_WIRELESS_N_RATES = { 0, 6.5, 13, 19.5, 26, 39, 52, 58.5, 65 }
_M.SUPPORTED_5GHZ_WIRELESS_N_RATES = { 0, 13, 26, 39, 52, 78, 104, 117, 130 }

_M.RADIO_PROFILES = {
    [_M.RADIO_ID_2GHZ] = {
        apName = 'wl0',
        band = '2.4GHz',
        supportedRatesMbps = _M.SUPPORTED_WIRELESS_RATES,
        supportedNRatesMbps = _M.SUPPORTED_2GHZ_WIRELESS_N_RATES
    },
    [_M.RADIO_ID_5GHZ] = {
        apName = 'wl1',
        band = '5GHz',
        supportedRatesMbps = _M.SUPPORTED_WIRELESS_RATES,
        supportedNRatesMbps = _M.SUPPORTED_5GHZ_WIRELESS_N_RATES
    },
    [_M.RADIO_ID_5GHZ_2] = {
        apName = 'wl2',
        band = '5GHz',
        supportedRatesMbps = _M.SUPPORTED_WIRELESS_RATES,
        supportedNRatesMbps = _M.SUPPORTED_5GHZ_WIRELESS_N_RATES
    }
}

_M.SUPPORTED_BAND_STEERING_MODES = nil

function _M.parseWirelessSecurity(security)
    if security == 'wep' then
        return 'WEP'
    elseif security == 'wpa-personal' then
        return 'WPA-Personal'
    elseif security == 'wpa-enterprise' then
        return 'WPA-Enterprise'
    elseif security == 'wpa2-personal' then
        return 'WPA2-Personal'
    elseif security == 'wpa2-enterprise' then
        return 'WPA2-Enterprise'
    elseif security == 'wpa-mixed' then
        return 'WPA-Mixed-Personal'
    elseif security == 'wpa-enterprise-mixed' then
        return 'WPA-Mixed-Enterprise'
    elseif security == 'wpa3-mixed' then
        return 'WPA2/WPA3-Mixed-Personal'
    elseif security == 'wpa3-personal' then
        return 'WPA3-Personal'
    elseif security == 'wpa3-enterprise' then
        return 'WPA3-Enterprise'
    elseif security == 'wpa3-open' then
        return 'Enhanced-Open+None'
    elseif security == 'wpa3-owe' then
        return 'Enhanced-Open-Only'
    else
        return 'None'
    end
end

local function getSupportedSecurityTypes(sc, apName)
    local types = {}

    sc:readlock()
    local values = sc:get_wifi_supported_security_types(apName)

    types = util.parseTableData(values, _M.parseWirelessSecurity)

    return types
end

local function parseBandSteeringMode(mode)
    if mode == 1 then
        return _M.BAND_STEERING_MODE_BASIC
    else -- if mode == '2' then
        return _M.BAND_STEERING_MODE_TRIBAND
    end
end

local function serializeBandSteeringMode(mode)
    if mode == _M.BAND_STEERING_MODE_BASIC then
        return 1
    else -- if mode == _M.BAND_STEERING_MODE_TRIBAND then
        return 2
    end
end

local function isBandSteeringModeSupported(sc, mode)
    for k, v in pairs(_M.getSupportedBandSteeringModes(sc)) do
        if v == mode then
            return true
        end
    end
end

--
-- When band steering is supported and is enabled, verify the followings:
-- 1. If band steering mode is set to basic, make sure both 5GHz radios have the same settings.
-- 2. If band steering mode is set to advanced, make sure all three radios have the same settings.
--
local function verifyBandSteeringRadioSettings(sc)
    if sc:is_wifi_band_steering_supported() and sc:get_wifi_band_steering_enabled() then
        local map = {}
        for id, value in pairs(_M.RADIO_PROFILES) do
            map[id] = {}
            map[id].isEnabled = sc:get_wifi_state(value.apName) == 'up'
            map[id].ssid = sc:get_wifi_ssid(value.apName)
            map[id].security = _M.parseWirelessSecurity(sc:get_wifi_security_mode(value.apName))
            map[id].broadcastSSID = sc:get_wifi_broadcast_ssid(value.apName)
            local wepSettings, wpaPersonalSettings, wpaEnterpriseSettings
            if map[id].security == 'WEP' then
                map[id].wepSettings = {
                    encryption = (sc:get_wifi_encryption(value.apName) == '128-bits') and 'WEP-128' or 'WEP-64',
                    key1 = string.upper(sc:get_wifi_wep_security_key(value.apName, 0)),
                    key2 = string.upper(sc:get_wifi_wep_security_key(value.apName, 1)),
                    key3 = string.upper(sc:get_wifi_wep_security_key(value.apName, 2)),
                    key4 = string.upper(sc:get_wifi_wep_security_key(value.apName, 3)),
                    txKey = sc:get_wifi_wep_security_tx(value.apName)
                }
            elseif _M.isSecurityWPAVariant(map[id].security) then
                if _M.isSecurityEnterpriseVariant(map[id].security) then
                    map[id].wpaEnterpriseSettings = {
                        radiusServer = hdk.ipaddress(sc:get_wifi_security_radius_server(value.apName)),
                        radiusPort = sc:get_wifi_security_radius_port(value.apName),
                        sharedKey = sc:get_wifi_security_shared(value.apName)
                    }
                else
                    map[id].wpaPersonalSettings = {
                        passphrase = sc:get_wifi_security_passphrase(value.apName),
                    }
                end
            end
        end

        if sc:get_wifi_band_steering_mode() == 1 then
            if not util.isTableEqual(map[_M.RADIO_ID_5GHZ], map[_M.RADIO_ID_5GHZ_2]) then
                return 'ErrorInvalidBandSteeringRadioSettings'
            end
        else -- if sc:get_wifi_band_steering_mode() == 2
            if not util.isTableEqual(map[_M.RADIO_ID_5GHZ], map[_M.RADIO_ID_5GHZ_2]) or
                    not util.isTableEqual(map[_M.RADIO_ID_2GHZ], map[_M.RADIO_ID_5GHZ]) then
                return 'ErrorInvalidBandSteeringRadioSettings'
            end
        end
    end
end

--
-- Returns an array of supported channels for every supported channel widths
--
local function getSupportedChannelsForChannelWidths(sc, apName)
    local supportedChannelsForChannelWidths = { }

    sc:readlock()
    sc:get_wifi_supported_channel_widths(apName):gsub('([^,]+)', function(channelWidth)
        local supportedChannelsForChannelWidth = {}
        local channels = { }
        supportedChannelsForChannelWidth.channelWidth = _M.parseWirelessChannelWidth2(channelWidth)
        sc:get_wifi_supported_channels_for_channel_width(apName, channelWidth):gsub('([^,]+)', function(channel)
            table.insert(channels, tonumber(channel))
        end)
        supportedChannelsForChannelWidth.channels = channels
        table.insert(supportedChannelsForChannelWidths, supportedChannelsForChannelWidth)
    end)

    return supportedChannelsForChannelWidths
end

--
-- Determine whether the desired channelWidth is supported for the given radio
--
-- input = STRING, STRING
--
-- output = BOOLEAN
--
local function isChannelWidthSupported(profile, channelWidth)
    local supportedChannelsForChannelWidths = profile.supportedChannelsForChannelWidths
    for i, supportedChannelsForChannelWidth in pairs(supportedChannelsForChannelWidths) do
        if channelWidth == supportedChannelsForChannelWidth.channelWidth then
            return true
        end
    end
    return false
end

--
-- Determine whether the desired channel is supported for the given radio and channel width
--
-- input = STRING, STRING, NUMBER
--
-- output = BOOLEAN
--
local function isChannelSupportedForChannelWidth(profile, channelWidth, channel)
    local supportedChannelsForChannelWidths = profile.supportedChannelsForChannelWidths
    for i, supportedChannelsForChannelWidth in pairs(supportedChannelsForChannelWidths) do
        if channelWidth == supportedChannelsForChannelWidth.channelWidth then
            for k, v in pairs(supportedChannelsForChannelWidth.channels) do
                if channel == v then
                    return true
                end
            end
        end
    end
    return false
end

local function getAvailableChannels(sc, apName)
    local availableChannels = { 0 } -- include 0 to indicate 'auto' option

    sc:readlock()
    sc:get_wifi_available_channels(apName):gsub('([^,]+)', function(channel) table.insert(availableChannels, tonumber(channel)) end)

    return availableChannels
end

local function getAvailableWideChannels(sc, apName)
    -- Assume all available channels can also be wide, except for channel 165 (RAINIER-9525)
    local availableChannels = getAvailableChannels(sc, apName)

    -- Only remove channel 165 if it's the 5GHz band
    if apName == _M.RADIO_PROFILES[_M.RADIO_ID_5GHZ].apName or apName == _M.RADIO_PROFILES[_M.RADIO_ID_5GHZ_2].apName then
        for k, v in ipairs(availableChannels) do
            if v == 165 then
                table.remove(availableChannels, k)
            end
        end
    end

    return availableChannels
end

local function getSupportedModes(sc, apName)
    local availableModes = {}

    sc:readlock()
    sc:get_wifi_network_modes(apName):gsub('([^,]+)', function(mode) table.insert(availableModes, (mode == 'mixed' and '802.11mixed' or '802.'..mode)) end)

    return availableModes
end

--
-- Retrieve the supported radios along with its capabilities.
--
-- input = CONTEXT
--
-- output = MAP_OF(STRING, {
--     apName = STRING,
--     band = STRING,
--     supportedRatesMbps = ARRAY_OF(NUMBER),
--     supportedNRatesMbps = ARRAY_OF(NUMBER),
--     supportedSecurityTypes = ARRAY_OF(STRING),
--     supportedModes = ARRAY_OF(STRING),
--     availableChannels = ARRAY_OF(NUMBER),
--     availableWideChannels = ARRAY_OF(NUMBER),
--     supportedChannelsForChannelWidths = MAP_OF(NUMBER, ARRAY_OF(NUMBER)),
--     supportedSecuritySet = MAP_OF(STRING, BOOLEAN),
--     supportedModeSet = MAP_OF(STRING, BOOLEAN),
--     supportedChannelSet = MAP_OF(NUMBER, BOOLEAN),
--     supportedWideChannelSet = MAP_OF(NUMBER, BOOLEAN),
--     supportedRateMbpsSet = MAP_OF(NUMBER, BOOLEAN),
--     supportedNRateMbpsSet = MAP_OF(NUMBER, BOOLEAN)
--
-- })
--
function _M.getSupportedRadios(sc)
    local radios = {}
    sc:readlock()
    local supported = sc:get_wifi_devices()
    for id, value in pairs(_M.RADIO_PROFILES) do
        if supported:find(value.apName, 1, true) then
            radios[id] = value
            radios[id].supportedSecurityTypes = getSupportedSecurityTypes(sc, radios[id].apName)
            radios[id].supportedModes = getSupportedModes(sc, radios[id].apName)
            radios[id].availableChannels = getAvailableChannels(sc, radios[id].apName)
            radios[id].availableWideChannels = getAvailableWideChannels(sc, radios[id].apName)
            radios[id].supportedChannelsForChannelWidths = getSupportedChannelsForChannelWidths(sc, radios[id].apName)

            radios[id].supportedSecuritySet = util.arrayToSet(radios[id].supportedSecurityTypes)
            radios[id].supportedModeSet = util.arrayToSet(radios[id].supportedModes)
            radios[id].supportedChannelSet = util.arrayToSet(radios[id].availableChannels)
            radios[id].supportedWideChannelSet = util.arrayToSet(radios[id].availableWideChannels)
            radios[id].supportedRateMbpsSet = util.arrayToSet(radios[id].supportedRatesMbps)
            radios[id].supportedNRateMbpsSet = util.arrayToSet(radios[id].supportedNRatesMbps)
        end
    end
    return radios
end

--
-- Determine whether an SSID is valid.
--
-- input = STRING
--
-- output = BOOLEAN
--
function _M.isValidSSID(ssid)
    return #ssid > 0 and #ssid <= 32
end

--
-- Determine whether an SSID is conflicting with a guest SSID.
--
-- input = CONTEXT, STRING
--
-- output = BOOLEAN
--
function _M.isConflictingSSID(sc, ssid)
    for radioID, profile in pairs(_M.getSupportedRadios(sc)) do
        if sc:get_guest_access_enabled() and sc:get_guest_ap_enabled(profile.apName) and sc:get_guest_access_ssid(profile.apName) == ssid then
            return true
        end
    end
end

--
-- Determine whether a WEP key is valid.
--
-- input = STRING, NUMBER
--
-- output = BOOLEAN
--
function _M.isValidWEPKey(key, requiredDigits)
    -- The key must be all hex digits.
    return (#key == requiredDigits and key:find('^%x*$') ~= nil)
end

--
-- Determine whether a RADIUS shared key is valid.
--
-- input = STRING
--
-- output = BOOLEAN
--
function _M.isValidRADIUSSharedKey(sharedKey)
    -- the shared key must be between 1 and max characters
    if #sharedKey < 1 or #sharedKey > _M.MAX_RADIUS_SHARED_KEY_LENGTH then
        return false
    end

    -- the shared key can only include characters in the ASCII 32-126 range
    return sharedKey:find('^[\032-\126]*$') ~= nil
end

--
-- Determine whether a security type is a WPA variant.
--
-- input = STRING
--
-- output = BOOLEAN
--
function _M.isSecurityWPAVariant(security)
    return (security:find('^WPA[23]?[-/]') ~= nil)
end

--
-- Determine whether a security type is an enterprise variant.
--
-- input = STRING
--
-- output = BOOLEAN
--
function _M.isSecurityEnterpriseVariant(security)
    return (security:find('-Enterprise$') ~= nil)
end

--
-- Determine whether a WPA or WPA2 passphrase is valid.
--
-- input = STRING
--
-- output = BOOLEAN
--
function _M.isValidWPAPassphrase(passphrase)
    -- 64 hex digits is a special case
    if #passphrase == 64 then
        return passphrase:find('^%x*$') ~= nil
    end

    -- otherwise the passphrase can be 8-63 characters
    if #passphrase < 8 or #passphrase > 63 then
        return false
    end

    -- the passphrase can only include characters in the ASCII 32-126 range
    return passphrase:find('^[\032-\126]*$') ~= nil
end

--
-- Get the wireless radio map for the local device. The keys in the map
-- are the radio IDs.
--
-- input = CONTEXT
--
-- output = MAP_OF(STRING, {
--    physicalRadioID = STRING,
--    bssid = MACADDRESS,
--    band = STRING,
--    supportedModes = ARRAY_OF(STRING),
--    supportedChannels = ARRAY_OF(NUMBER),
--    supportedWideChannels = ARRAY_OF(NUMBER)
--    supportedSecurityTypes = ARRAY_OF(STRING)
-- })
--
function _M.getWirelessRadioMap(sc)
    sc:readlock()
    local map = {}
    for radioID, profile in pairs(_M.getSupportedRadios(sc)) do
        map[radioID] = {
            physicalRadioID = sc:get_wifi_interface_name(profile.apName),
            bssid = platform.getMACAddressFromNetName(sc:get_wifi_virtual_ap_name(profile.apName)),
            band = profile.band,
            supportedModes = profile.supportedModes,
            supportedChannels = profile.availableChannels,
            supportedWideChannels = profile.availableWideChannels,
            supportedChannelsForChannelWidths = profile.supportedChannelsForChannelWidths,
            supportedSecurityTypes = profile.supportedSecurityTypes,
            supportedRatesMbps = profile.supportedRatesMbps,
            supportedNRatesMbps = profile.supportedNRatesMbps
        }
    end
    return map
end

function _M.parseWirelessRadioMode(mode)
    if mode == '11a' then
        return '802.11a'
    elseif mode == '11b' then
        return '802.11b'
    elseif mode == '11g' then
        return '802.11g'
    elseif mode == '11n' then
        return '802.11n'
    elseif mode == '11ac' then
        return '802.11ac'
    elseif mode == '11a 11n' then
        return '802.11an'
    elseif mode == '11b 11g' then
        return '802.11bg'
    elseif mode == '11b 11n' then
        return '802.11bn'
    elseif mode == '11g 11n' then
        return '802.11gn'
    elseif mode == '11a 11n 11ac' then
        return '802.11anac'
    elseif mode == '11b 11g 11n' then
        return '802.11bgn'
    elseif mode == '11b 11g 11n 11ac' then
        return '802.11bgnac'
    elseif mode == 'mixed' then
        return '802.11mixed'
    end

    error(('unexpected mode: %s'):format(mode))
end

local function serializeWirelessRadioMode(mode)
    if mode == '802.11a' then
        return '11a'
    elseif mode == '802.11b' then
        return '11b'
    elseif mode == '802.11g' then
        return '11g'
    elseif mode == '802.11n' then
        return '11n'
    elseif mode == '802.11ac' then
        return '11ac'
    elseif mode == '802.11an' then
        return '11a 11n'
    elseif mode == '802.11bg' then
        return '11b 11g'
    elseif mode == '802.11bn' then
        return '11b 11n'
    elseif mode == '802.11gn' then
        return '11g 11n'
    elseif mode == '802.11anac' then
        return '11a 11n 11ac'
    elseif mode == '802.11bgn' then
        return '11b 11g 11n'
    elseif mode == '802.11bgnac' then
        return '11b 11g 11n 11ac'
    elseif mode == '802.11mixed' then
        return 'mixed'
    end

    error(('unexpected mode: %s'):format(mode))
end

function _M.parseWirelessChannelWidth(width)
    if width == 'standard' then
        return 'Standard'
    elseif width == 'wide' then
        return 'Wide'
    elseif width == 'wide80' then
        return 'Wide80'
    elseif width == 'wide160c' then
        return 'Wide160c'
    elseif width == 'wide160nc' then
        return 'Wide160nc'
    else
        return 'Auto'
    end
end

function _M.parseWirelessChannelWidth2(channelWidth)
    if channelWidth == '20' then
        return 'Standard'
    elseif channelWidth == '40' then
        return 'Wide'
    elseif channelWidth == '80' then
        return 'Wide80'
    elseif channelWidth == '160c' then
        return 'Wide160c'
    elseif channelWidth == '160nc' then
        return 'Wide160nc'
    else
        return 'Auto'
    end
end

local function serializeWirelessChannelWidth(width)
    if width == 'Standard' then
        return 'standard'
    elseif width == 'Wide' then
        return 'wide'
    elseif width == 'Wide80' then
        return 'wide80'
    elseif width == 'Wide160c' then
        return 'wide160c'
    elseif width == 'Wide160nc' then
        return 'wide160nc'
    else
        return 'auto'
    end
end

function _M.serializeWirelessSecurity(security)
    if security == 'WEP' then
        return 'wep'
    elseif security == 'WPA-Personal' then
        return 'wpa-personal'
    elseif security == 'WPA-Enterprise' then
        return 'wpa-enterprise'
    elseif security == 'WPA2-Personal' then
        return 'wpa2-personal'
    elseif security == 'WPA2-Enterprise' then
        return 'wpa2-enterprise'
    elseif security == 'WPA-Mixed-Personal' then
        return 'wpa-mixed'
    elseif security == 'WPA-Mixed-Enterprise' then
        return 'wpa-enterprise-mixed'
    elseif security == 'WPA2/WPA3-Mixed-Personal' then
        return 'wpa3-mixed'
    elseif security == 'WPA3-Personal' then
        return 'wpa3-personal'
    elseif security == 'WPA3-Enterprise' then
        return 'wpa3-enterprise'
    elseif security == 'Enhanced-Open+None' then
        return 'wpa3-open'
    elseif security == 'Enhanced-Open-Only' then
        return 'wpa3-owe'
    else
        return 'disabled'
    end
end

--
-- Get the settings of a single wireless radio on the local device.
--
-- input = CONTEXT, STRING
--
-- output = OPTIONAL({
--     isEnabled = BOOLEAN,
--     mode = STRING,
--     ssid = STRING,
--     broadcastSSID = BOOLEAN,
--     channelWidth = STRING,
--     channel = NUMBER,
--     security = STRING,
--     wepSettings = OPTIONAL({
--         encryption = STRING,
--         key1 = STRING,
--         key2 = STRING,
--         key3 = STRING,
--         key4 = STRING,
--         txKey = NUMBER,
--     }),
--     wpaPersonalSettings = OPTIONAL({
--         passphrase = STRING
--     }),
--     wpaEnterpriseSettings = OPTIONAL({
--         radiusServer = IPADDRESS,
--         radiusPort = NUMBER,
--         sharedKey = STRING
--     })
-- })
--
function _M.getWirelessRadioSettings(sc, radioID)
    sc:readlock()
    local settings, keyRenewal
    local radios = _M.getSupportedRadios(sc)
    if radios[radioID] then
        local prefix = radios[radioID].apName

-- New function call added to demonstrate OlympusAPI
        local ssid = sc:get_wifi_ssid(prefix)
        local isEnabled = (sc:get_wifi_state(prefix) == 'up')
        local mode = _M.parseWirelessRadioMode(sc:get_wifi_network_mode(prefix))
        local broadcastSSID = sc:get_wifi_broadcast_ssid(prefix)
        local channelWidth = _M.parseWirelessChannelWidth(sc:get_wifi_channel_width(prefix))
        local channel = sc:get_wifi_channel(prefix)
        local security = _M.parseWirelessSecurity(sc:get_wifi_security_mode(prefix))
        local wepSettings, wpaPersonalSettings, wpaEnterpriseSettings
        if security == 'WEP' then
            wepSettings = {
                encryption = (sc:get_wifi_encryption(prefix) == '128-bits') and 'WEP-128' or 'WEP-64',
                key1 = string.upper(sc:get_wifi_wep_security_key(prefix, 0)),
                key2 = string.upper(sc:get_wifi_wep_security_key(prefix, 1)),
                key3 = string.upper(sc:get_wifi_wep_security_key(prefix, 2)),
                key4 = string.upper(sc:get_wifi_wep_security_key(prefix, 3)),
                txKey = sc:get_wifi_wep_security_tx(prefix)
            }
        elseif _M.isSecurityWPAVariant(security) then
            if _M.isSecurityEnterpriseVariant(security) then
                wpaEnterpriseSettings = {
                    radiusServer = hdk.ipaddress(sc:get_wifi_security_radius_server(prefix)),
                    radiusPort = sc:get_wifi_security_radius_port(prefix),
                    sharedKey = sc:get_wifi_security_shared(prefix)
                }
            else
                wpaPersonalSettings = {
                    passphrase = sc:get_wifi_security_passphrase(prefix),
                }
            end
            keyRenewal = sc:get_wifi_key_renewal(prefix)
        end
        settings = {
            isEnabled = isEnabled,
            mode = mode,
            ssid = ssid,
            broadcastSSID = broadcastSSID,
            channelWidth = channelWidth,
            channel = channel,
            security = security,
            wepSettings = wepSettings,
            wpaPersonalSettings = wpaPersonalSettings,
            wpaEnterpriseSettings = wpaEnterpriseSettings
        }
    end
    return settings, keyRenewal
end

--
-- Get supported band steering modes.
--
-- input = CONTEXT
--
-- output = ARRAY_OF(STRING)
--
function _M.getSupportedBandSteeringModes(sc)
    if _M.SUPPORTED_BAND_STEERING_MODES == nil then
        sc:readlock()
        _M.SUPPORTED_BAND_STEERING_MODES = sc:get_wifi_band_steering_supported_modes()
    end
    return _M.SUPPORTED_BAND_STEERING_MODES
end

--
-- Get the Radio info structure used JNAP.
--
-- input = CONTEXT
--
-- output = ARRAY_OF({
--     radioID = STRING,
--     physicalRadioID = STRING,
--     bssid = MACADDRESS,
--     band = STRING,
--     supportedModes = ARRAY_OF(STRING),
--     supportedChannels = ARRAY_OF(INTEGER),
--     supportedWideChannels = ARRAY_OF(INTEGER),
--     supportedSecurityTypes = ARRAY_OF(STRING),
--     maxRADIUSSharedKeyLength = INTEGER,
--     settings = OPTIONAL({
--         isEnabled = BOOLEAN,
--         mode = STRING,
--         ssid = STRING,
--         broadcastSSID = BOOLEAN,
--         channelWidth = STRING,
--         channel = NUMBER,
--         security = STRING,
--         wepSettings = OPTIONAL({
--             encryption = STRING,
--             key1 = STRING,
--             key2 = STRING,
--             key3 = STRING,
--             key4 = STRING,
--             txKey = NUMBER,
--         }),
--         wpaPersonalSettings = OPTIONAL({
--             passphrase = STRING
--         }),
--         wpaEnterpriseSettings = OPTIONAL({
--             radiusServer = IPADDRESS,
--             radiusPort = NUMBER,
--             sharedKey = STRING
--         })
--     })
-- }
--
function _M.getRadioInfo(sc)
    local radioMap = _M.getWirelessRadioMap(sc)
    local radios = {}
    for radioID, radio in pairs(radioMap) do
        local radioOutput = {
            radioID = radioID,
            physicalRadioID = radio.physicalRadioID,
            bssid = radio.bssid,
            band = radio.band,
            supportedModes = radio.supportedModes,
            supportedChannels = radio.supportedChannels,
            supportedWideChannels = radio.supportedWideChannels,
            supportedSecurityTypes = radio.supportedSecurityTypes,
            maxRADIUSSharedKeyLength = _M.MAX_RADIUS_SHARED_KEY_LENGTH
        }
        radioOutput.settings = _M.getWirelessRadioSettings(sc, radioID)
        if radioOutput.settings then
            table.insert(radios, radioOutput)
        end
    end
    return radios
end

--
-- Get the Radio info structure used JNAP.
--
-- input = CONTEXT
--
-- output = ARRAY_OF({
--     radioID = STRING,
--     physicalRadioID = STRING,
--     bssid = MACADDRESS,
--     band = STRING,
--     supportedModes = ARRAY_OF(STRING),
--     supportedChannelsForChannelWidths = ARRAY_OF({
--         channelWidth = STRING,
--         channels = ARRAY_OF(INT)
--     }),
--     supportedSecurityTypes = ARRAY_OF(STRING),
--     maxRADIUSSharedKeyLength = INTEGER,
--     settings = OPTIONAL({
--         isEnabled = BOOLEAN,
--         mode = STRING,
--         ssid = STRING,
--         broadcastSSID = BOOLEAN,
--         channelWidth = STRING,
--         channel = NUMBER,
--         security = STRING,
--         wepSettings = OPTIONAL({
--             encryption = STRING,
--             key1 = STRING,
--             key2 = STRING,
--             key3 = STRING,
--             key4 = STRING,
--             txKey = NUMBER,
--         }),
--         wpaPersonalSettings = OPTIONAL({
--             passphrase = STRING
--         }),
--         wpaEnterpriseSettings = OPTIONAL({
--             radiusServer = IPADDRESS,
--             radiusPort = NUMBER,
--             sharedKey = STRING
--         })
--     })
-- }
--
function _M.getRadioInfo3(sc)
    sc:readlock()
    local radioMap = _M.getWirelessRadioMap(sc)
    local retVal = {}
    local radios = {}
    for radioID, radio in pairs(radioMap) do
        local radioOutput = {
            radioID = radioID,
            physicalRadioID = radio.physicalRadioID,
            bssid = radio.bssid,
            band = radio.band,
            supportedModes = radio.supportedModes,
            supportedChannelsForChannelWidths = radio.supportedChannelsForChannelWidths,
            supportedSecurityTypes = radio.supportedSecurityTypes,
            maxRADIUSSharedKeyLength = _M.MAX_RADIUS_SHARED_KEY_LENGTH
        }
        radioOutput.settings = _M.getWirelessRadioSettings(sc, radioID)
        if radioOutput.settings then
            table.insert(radios, radioOutput)
        end
    end
    retVal.radios = radios

    -- Populate band steering properties
    retVal.isBandSteeringSupported = sc:is_wifi_band_steering_supported()
    if retVal.isBandSteeringSupported then
        retVal.supportedBandSteeringModes = _M.getSupportedBandSteeringModes(sc)
        retVal.isBandSteeringEnabled = sc:get_wifi_band_steering_enabled()
        if retVal.isBandSteeringEnabled then
            retVal.bandSteeringMode = parseBandSteeringMode(sc:get_wifi_band_steering_mode())
        end
    end

    table.sort(retVal.radios, function(a, b) return a.radioID < b.radioID end)
    return retVal
end

--
-- Set the settings of a single wireless radio on the local device.
--
-- input = CONTEXT, STRING, {
--     isEnabled = BOOLEAN,
--     mode = STRING,
--     ssid = STRING,
--     broadcastSSID = BOOLEAN,
--     channelWidth = STRING,
--     channel = NUMBER,
--     security = STRING,
--     wepSettings = OPTIONAL({
--         encryption = STRING,
--         key1 = STRING,
--         key2 = STRING,
--         key3 = STRING,
--         key4 = STRING,
--         txKey = NUMBER,
--     }),
--     wpaPersonalSettings = OPTIONAL({
--         passphrase = STRING
--     }),
--     wpaEnterpriseSettings = OPTIONAL({
--         radiusServer = IPADDRESS,
--         radiusPort = NUMBER,
--         sharedKey = STRING
--     })
-- }, NUMBER, BOOLEAN
--
-- output = NIL_OR_ONE_OF(
--     'ErrorUnknownRadio',
--     'ErrorUnsupportedMode',
--     'ErrorInvalidSSID',
--     'ErrorUnsupportedChannel',
--     'ErrorUnsupportedChannelWidth',
--     'ErrorUnsupportedSecurity',
--     'ErrorMissingWEPSettings',
--     'ErrorInvalidKey',
--     'ErrorInvalidTXKey',
--     'ErrorMissingWPAPersonalSettings',
--     'ErrorInvalidPassphrase',
--     'ErrorMissingWPAEnterpriseSettings',
--     'ErrorInvalidRADIUSServer',
--     'ErrorInvalidRADIUSPort',
--     'ErrorInvalidSharedKey'
-- )
--
-- Note for copySettingsTo5GhzHigh parameter:
-- VelopJr. is a dual band router and Velop is a tri-band router.
-- When VelopJr. is set as the master, we need to also set the 2nd 5GHz radio so the change gets propagated
-- to any Velop slaves. (VELOPJR-298)
function _M.setWirelessRadioSettings(sc, radioID, settings, apiVersion, copySettingsTo5GhzHigh)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local profile = _M.getSupportedRadios(sc)[radioID]
    if not profile then
        return 'ErrorUnknownRadio'
    end

    local prefix = profile.apName

    sc:set_wifi_state(prefix, settings.isEnabled and 'up' or 'down')
    if prefix == _M.RADIO_PROFILES[_M.RADIO_ID_5GHZ].apName and copySettingsTo5GhzHigh then sc:set_wifi_state(_M.RADIO_PROFILES[_M.RADIO_ID_5GHZ_2].apName, settings.isEnabled and 'up' or 'down') end

    if not profile.supportedModeSet[settings.mode] then
        return 'ErrorUnsupportedMode'
    end
    sc:set_wifi_network_mode(prefix, serializeWirelessRadioMode(settings.mode))

    if not _M.isValidSSID(settings.ssid) then
        return 'ErrorInvalidSSID'
    end
    if _M.isConflictingSSID(sc, settings.ssid) then
        return 'ErrorGuestSSIDConflict'
    end
    sc:set_wifi_ssid(prefix, settings.ssid)
    if prefix == _M.RADIO_PROFILES[_M.RADIO_ID_5GHZ].apName and copySettingsTo5GhzHigh then sc:set_wifi_ssid(_M.RADIO_PROFILES[_M.RADIO_ID_5GHZ_2].apName, settings.ssid) end
    sc:set_wifi_broadcast_ssid(prefix, settings.broadcastSSID)
    if prefix == _M.RADIO_PROFILES[_M.RADIO_ID_5GHZ].apName and copySettingsTo5GhzHigh then sc:set_wifi_broadcast_ssid(_M.RADIO_PROFILES[_M.RADIO_ID_5GHZ_2].apName, settings.broadcastSSID) end

    -- This is being used by setWirelessRadioSettings3 to determine which channel and channel width validation is being used
    if apiVersion == 2 then
        if not isChannelWidthSupported(profile, settings.channelWidth) then
            return 'ErrorUnsupportedChannelWidth'
        end

        if not isChannelSupportedForChannelWidth(profile, settings.channelWidth, settings.channel) then
            return 'ErrorUnsupportedChannel'
        end

        -- 802.11a does not support Wide channels.
        if ('802.11a' == settings.mode) and ('Wide' == settings.channelWidth) then
            return 'ErrorUnsupportedChannelWidth'
        end

        -- Only 802.11ac and 802.11anac support Wide80/Wide160c/Wide160nc channels.
        if ('802.11ac' ~= settings.mode) and ('802.11anac' ~= settings.mode) and
            (('Wide80' == settings.channelWidth) or ('Wide160c' == settings.channelWidth) or ('Wide160nc' == settings.channelWidth)) then
            return 'ErrorUnsupportedChannelWidth'
        end
    else
        if settings.channelWidth == 'Standard' then
            if not profile.supportedChannelSet[settings.channel] then
                return 'ErrorUnsupportedChannel'
            end
        else
            -- 802.11a does not support Wide channels.
            if ('802.11a' == settings.mode) and ('Wide' == settings.channelWidth) then
                return 'ErrorUnsupportedChannelWidth'
            end

            -- Only 802.11ac and 802.11anac support Wide80/Wide160c/Wide160nc channels.
            if ('802.11ac' ~= settings.mode) and ('802.11anac' ~= settings.mode) and
            (('Wide80' == settings.channelWidth) or ('Wide160c' == settings.channelWidth) or ('Wide160nc' == settings.channelWidth)) then
                return 'ErrorUnsupportedChannelWidth'
            end

            -- Note that if the channel width is Auto, the channel must be one of the
            -- supported wide channels.
            if not profile.supportedWideChannelSet[settings.channel] then
                return 'ErrorUnsupportedChannel'
            end
        end
    end

    sc:set_wifi_channel_width(prefix, serializeWirelessChannelWidth(settings.channelWidth))
    sc:set_wifi_channel(prefix, settings.channel)

    if not profile.supportedSecuritySet[settings.security] then
        return 'ErrorUnsupportedSecurity'
    end

    if settings.security == 'WEP' then
        if not settings.wepSettings then
            return 'ErrorMissingWEPSettings'
        end
        local isWEP128 = (settings.wepSettings.encryption == 'WEP-128')
        sc:set_wifi_encryption(prefix, isWEP128 and '128-bits' or '64-bits')
        if prefix == _M.RADIO_PROFILES[_M.RADIO_ID_5GHZ].apName and copySettingsTo5GhzHigh then sc:set_wifi_encryption(_M.RADIO_PROFILES[_M.RADIO_ID_5GHZ_2].apName, isWEP128 and '128-bits' or '64-bits') end
        local txKey = settings.wepSettings.txKey
        if txKey < 1 or txKey > 4 then
            return 'ErrorInvalidTXKey'
        end
        sc:set_wifi_wep_security_tx(prefix, txKey)
        if prefix == _M.RADIO_PROFILES[_M.RADIO_ID_5GHZ].apName and copySettingsTo5GhzHigh then sc:set_wifi_wep_security_tx(_M.RADIO_PROFILES[_M.RADIO_ID_5GHZ_2].apName, txKey) end
        local keyDigits = isWEP128 and 26 or 10
        for i = 1, 4 do
            local key = settings.wepSettings['key'..i]
            if not _M.isValidWEPKey(key, keyDigits) and (i == txKey or #key ~= 0) then
                return 'ErrorInvalidKey'
            end
            sc:set_wifi_wep_security_key(prefix, i, key:upper())
            if prefix == _M.RADIO_PROFILES[_M.RADIO_ID_5GHZ].apName and copySettingsTo5GhzHigh then sc:set_wifi_wep_security_key(_M.RADIO_PROFILES[_M.RADIO_ID_5GHZ_2].apName, i, key:upper()) end
        end
    elseif _M.isSecurityWPAVariant(settings.security) then
        if _M.isSecurityEnterpriseVariant(settings.security) then
            if not settings.wpaEnterpriseSettings then
                return 'ErrorMissingWPAEnterpriseSettings'
            end
            if util.isReservedAddress(settings.wpaEnterpriseSettings.radiusServer) then
                return 'ErrorInvalidRADIUSServer'
            end
            sc:set_wifi_security_radius_server(prefix, tostring(settings.wpaEnterpriseSettings.radiusServer))
            if prefix == _M.RADIO_PROFILES[_M.RADIO_ID_5GHZ].apName and copySettingsTo5GhzHigh then sc:set_wifi_security_radius_server(_M.RADIO_PROFILES[_M.RADIO_ID_5GHZ_2].apName, tostring(settings.wpaEnterpriseSettings.radiusServer)) end
            if not util.isValidPort(settings.wpaEnterpriseSettings.radiusPort) then
                return 'ErrorInvalidRADIUSPort'
            end

            sc:set_wifi_security_radius_port(prefix, settings.wpaEnterpriseSettings.radiusPort)
            if prefix == _M.RADIO_PROFILES[_M.RADIO_ID_5GHZ].apName and copySettingsTo5GhzHigh then sc:set_wifi_security_radius_port(_M.RADIO_PROFILES[_M.RADIO_ID_5GHZ_2].apName, settings.wpaEnterpriseSettings.radiusPort) end
            if not _M.isValidRADIUSSharedKey(settings.wpaEnterpriseSettings.sharedKey) then
                return 'ErrorInvalidSharedKey'
            end
            sc:set_wifi_security_shared(prefix, settings.wpaEnterpriseSettings.sharedKey)
            if prefix == _M.RADIO_PROFILES[_M.RADIO_ID_5GHZ].apName and copySettingsTo5GhzHigh then sc:set_wifi_security_shared(_M.RADIO_PROFILES[_M.RADIO_ID_5GHZ_2].apName, settings.wpaEnterpriseSettings.sharedKey) end
        else
            if not settings.wpaPersonalSettings then
                return 'ErrorMissingWPAPersonalSettings'
            end
            if not _M.isValidWPAPassphrase(settings.wpaPersonalSettings.passphrase) then
                return 'ErrorInvalidPassphrase'
            end
            sc:set_wifi_security_passphrase(prefix, settings.wpaPersonalSettings.passphrase)
            if prefix == _M.RADIO_PROFILES[_M.RADIO_ID_5GHZ].apName and copySettingsTo5GhzHigh then sc:set_wifi_security_passphrase(_M.RADIO_PROFILES[_M.RADIO_ID_5GHZ_2].apName, settings.wpaPersonalSettings.passphrase) end
        end

        local encryption
        if settings.security == 'WPA-Personal' or settings.security == 'WPA-Enterprise' then
            encryption = 'tkip+aes'
        elseif settings.security == 'WPA2-Personal' or settings.security == 'WPA2-Enterprise' then
            encryption = 'aes'
        else
            encryption = 'tkip+aes'
        end
        sc:set_wifi_encryption(prefix, encryption)
        if prefix == _M.RADIO_PROFILES[_M.RADIO_ID_5GHZ].apName and copySettingsTo5GhzHigh then sc:set_wifi_encryption(_M.RADIO_PROFILES[_M.RADIO_ID_5GHZ_2].apName, encryption) end
    end
    sc:set_wifi_security_mode(prefix, _M.serializeWirelessSecurity(settings.security))
    if prefix == _M.RADIO_PROFILES[_M.RADIO_ID_5GHZ].apName and copySettingsTo5GhzHigh then sc:set_wifi_security_mode(_M.RADIO_PROFILES[_M.RADIO_ID_5GHZ_2].apName, _M.serializeWirelessSecurity(settings.security)) end
end

--
-- Set the settings of a single wireless radio on the local device.
--
-- input = CONTEXT, STRING, {
--     isBandSteeringEnabled = OPTIONAL(BOOLEAN),
--     bandSteeringMode = OPTIONAL(STRING),
--     isEnabled = BOOLEAN,
--     mode = STRING,
--     ssid = STRING,
--     broadcastSSID = BOOLEAN,
--     channelWidth = STRING,
--     channel = NUMBER,
--     security = STRING,
--     wepSettings = OPTIONAL({
--         encryption = STRING,
--         key1 = STRING,
--         key2 = STRING,
--         key3 = STRING,
--         key4 = STRING,
--         txKey = NUMBER,
--     }),
--     wpaPersonalSettings = OPTIONAL({
--         passphrase = STRING
--     }),
--     wpaEnterpriseSettings = OPTIONAL({
--         radiusServer = IPADDRESS,
--         radiusPort = NUMBER,
--         sharedKey = STRING
--     })
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorUnknownRadio',
--     'ErrorUnsupportedMode',
--     'ErrorInvalidSSID',
--     'ErrorUnsupportedChannel',
--     'ErrorUnsupportedChannelWidth',
--     'ErrorUnsupportedSecurity',
--     'ErrorMissingWEPSettings',
--     'ErrorInvalidKey',
--     'ErrorInvalidTXKey',
--     'ErrorMissingWPAPersonalSettings',
--     'ErrorInvalidPassphrase',
--     'ErrorMissingWPAEnterpriseSettings',
--     'ErrorInvalidRADIUSServer',
--     'ErrorInvalidRADIUSPort',
--     'ErrorInvalidSharedKey',
--     'ErrorBandSteeringNotSupported',
--     'ErrorMissingIsBandSteeringEnabled',
--     'ErrorMissingBandSteeringMode',
--     'ErrorInvalidBandSteeringMode',
--     'ErrorSuperfluousBandSteeringMode',
--     'ErrorInvalidBandSteeringRadioSettings',
-- )
--
function _M.setWirelessRadioSettings3(sc, input)
    sc:writelock()

    if sc:is_wifi_band_steering_supported() then
        -- Make sure isBandSteeringEnabled and bandSteeringMode exist.
        if input.isBandSteeringEnabled == nil then
            return 'ErrorMissingIsBandSteeringEnabled'
        end
        if input.isBandSteeringEnabled and not input.bandSteeringMode then
            return 'ErrorMissingBandSteeringMode'
        end
        if not input.isBandSteeringEnabled and input.bandSteeringMode then
            return 'ErrorSuperfluousBandSteeringMode'
        end

        sc:set_wifi_band_steering_enabled(input.isBandSteeringEnabled)
        if input.isBandSteeringEnabled then
            if not isBandSteeringModeSupported(sc, input.bandSteeringMode) then
                return 'ErrorInvalidBandSteeringMode'
            end
            sc:set_wifi_band_steering_mode(serializeBandSteeringMode(input.bandSteeringMode))
        end
    else
        -- Band steering is not supported on this platform. Reject settings.
        if input.isBandSteeringEnabled or input.bandSteeringMode then
            return 'ErrorBandSteeringNotSupported'
        end
    end

    local error
    -- push wireless radio settings
    for i, newSettings in ipairs(input.radios) do
        error = _M.setWirelessRadioSettings(sc, newSettings.radioID, newSettings.settings, 2, util.isModuleAvailable('nodes_util') and require('nodes_util').isNodeAMaster(sc) and not _M.getSupportedRadios(sc)[_M.RADIO_ID_5GHZ_2] and true or false)
        if error then
            return error
        end
    end

    -- verify band steering radio settings
    error = verifyBandSteeringRadioSettings(sc)
    if error then
        return error
    end
end

--
-- WLAN API enumeration conversion helpers
--
-- These are used to convert the JNAP API input values into the expected
-- enumeration/string values expected by the wifi service
-- (init/service_wifi/service_wifi_infra_phy.sh).
--
-- Note that the supported transmission rate set differs between platforms.

--
-- wl<n>_transmission_rate
--
-- See init/service_wifi/service_wifi_infra_phy.sh, get_vendor_trans_rate()
--
-- Add table to module for use in unittests
_M.rateMbpsToSyscfg = {
    [ 0 ] = 'auto',
    [ 6 ] = '6000000',
    [ 9 ] = '9000000',
    [ 12 ] = '12000000',
    [ 18 ] = '18000000',
    [ 24 ] = '24000000',
    [ 36 ] = '36000000',
    [ 48 ] = '48000000',
    [ 54 ] = '54000000'
}


--
-- Serialize a wireless rate in the expected format for the current platform.
--
-- nil is returned if the rate is not a valid rate for the current platform.
--
-- input = CONTEXT, NUMBER
--
-- output = NIL_OR(STRING)
--
local function serializeWirelessRateMbps(sc, rateMbps)
    sc:readlock()
    if sc:get_hardware_vendor_name() == 'Broadcom' then
        -- Broadcom doesn't seem to have a specific set of allowed rates.
        -- See init/service_wifi/Broadcom/service_wifi_infra_phy.sh, get_vendor_trans_rate()
        if 0 == rateMbps then
            return 'auto'
        elseif rateMbps <= 54 then
            return tostring(rateMbps * 1000000)
        end
        -- Anything > 54Mbps on Broadcom defaults to 'auto', but isn't valid for the
        -- non-N transmission rate, so don't return anything in this case.
    else
        -- On non-Broadcom platforms, use the lookup table to determine if the rate is valid.
        local value = _M.rateMbpsToSyscfg[rateMbps]
        if value then
            return value
        end
    end
end

--
-- Parse a wireless rate from the current platform's format.
--
-- input = CONTEXT, STRING
--
-- output = NIL_OR(NUMBER)
--
local function parseWirelessRateMbps(sc, value)
    if 'auto' == value then
        return 0
    else
        sc:readlock()
        if sc:get_hardware_vendor_name() == 'Broadcom' then
            -- Broadcom allows for an arbitrary rate, so just parse the number (which is bps)
            return tonumber(value) / 1000000
        else
            -- On non-Broadcom platforms, use the lookup table to ensure the value is valid
            for rateMpbs, v in pairs(_M.rateMbpsToSyscfg) do
                if v == value then
                    return rateMpbs
                end
            end
        end
    end
end

--
-- wl<n>_n_transmission_rate
--
-- N Rates are radio-specific
-- See init/service_wifi/service_wifi_infra_phy.sh, get_vendor_n_trans_rate()
--
-- Add table to module for use in unittests
_M.nRateMbpsToSyscfg = {
    [ _M.RADIO_ID_2GHZ ] = {
        [ 0 ] = 'auto',
        [ 6.5 ] = 0,
        [ 13 ] = 1,
        [ 19.5 ] = 2,
        [ 26 ] = 3,
        [ 39 ] = 4,
        [ 52 ] = 5,
        [ 58.5 ] = 6,
        [ 65] = 7
    },
    [ _M.RADIO_ID_5GHZ ] = {
        [ 0 ] = 'auto',
        [ 13 ] = 8,
        [ 26 ] = 9,
        [ 39 ] = 10,
        [ 52 ] = 11,
        [ 78 ] = 12,
        [ 104 ] = 13,
        [ 117 ] = 14,
        [ 130 ] = 15
    }
}

local function serializeWirelessNRateMbps(nRateMbps, radioID)
    if _M.nRateMbpsToSyscfg[radioID] then
        local value = _M.nRateMbpsToSyscfg[radioID][nRateMbps]
        if value then
            return tostring(value)
        end
    end
end

local function parseWirelessNRateMbps(value, radioID)
    if 'auto' == value then
        return 0
    else
        for nRateMpbs, v in pairs(_M.nRateMbpsToSyscfg[radioID]) do
            if v == tonumber(value) then
                return nRateMpbs
            end
        end
    end

    error(('unknown syscfg n tx rate value "%s"'):format(value))
end

--
-- wl<n>_basic_rate
--
-- Add table to module for use in unittests
_M.basicRateToSyscfg = {
    [ '802.11bCompatible' ] = '1-2mbs', -- $todo: should this be '12'??,
    [ 'All' ] = 'all',
    [ 'Default' ] = 'default'
}

local function serializeWirelessBasicRate(basicRate)
    return _M.basicRateToSyscfg[basicRate]
end

local function parseWirelessBasicRate(value)
    for basicRate, v in pairs(_M.basicRateToSyscfg) do
        if v == value then
            return basicRate
        end
    end

    error(('unknown syscfg basic rate value "%s"'):format(value))
end

--
-- wl<n>_transmission_power
--
-- Add table to module for use in unittests
_M.txPowerToSyscfg = {
    [ 'Low' ] = 'low',
    [ 'Medium' ] = 'medium',
    [ 'High' ] = 'high'
}

local function serializeWirelessTxPower(txPower)
    return _M.txPowerToSyscfg[txPower]
end

local function parseWirelessTxPower(value)
    for txPower, v in pairs(_M.txPowerToSyscfg) do
        if v == value then
            return txPower
        end
    end

    error(('unknown syscfg tx power value "%s"'):format(value))
end

--
-- wl<n>_authentication_type
--
-- Add table to module for use in unittests
_M.wepAuthToSyscfg = {
    [ 'Auto' ] = 'auto',
    [ 'Open' ] = 'open_system',
    [ 'SharedKey' ] = 'shared_key'
}

local function serializeWEPAuthentication(wepAuth)
    return _M.wepAuthToSyscfg[wepAuth]
end

local function parseWEPAuthentication(value)
    for wepAuth, v in pairs(_M.wepAuthToSyscfg) do
        if v == value then
            return wepAuth
        end
    end

    error(('unknown syscfg wep authentication value "%s"'):format(tostring(value)))
end


--
-- wl<n>_sideband
--
-- Add table to module for use in unittests
_M.sidebandToSyscfg = {
    [ 'Auto' ] = 'auto',
    [ 'Upper' ] = 'upper',
    [ 'Lower' ] = 'lower'
}

local function serializeSidebandSelection(sideband)
    return _M.sidebandToSyscfg[sideband]
end

local function parseSidebandSelection(value)
    for sideband, v in pairs(_M.sidebandToSyscfg) do
        if v == value then
            return sideband
        end
    end

    error(('unknown syscfg sideband value "%s"'):format(tostring(value)))
end


--
-- Get the advanced settings of a single wireless radio on the local device.
--
-- input = CONTEXT, STRING
--
-- output = OPTIONAL({
--     apIsolation = BOOLEAN,
--     frameburst = BOOLEAN,
--     ctsProtection = BOOLEAN,
--     rateMbps = INTEGER,
--     nRateMbps = FLOAT,
--     basicRate = STRING,
--     transmissionPower = STRING,
--     greenfieldPreamble = BOOLEAN,
--     htDuplicate = BOOLEAN,
--     spaceTimeBlockCode = BOOLEAN,
--     wirelessMultimediaPowerSave = BOOLEAN,
--     beaconIntervalMilliseconds = INTEGER,
--     dtimIntervalMilliseconds = INTEGER,
--     fragmentationThresholdBytes = INTEGER,
--     rtsThresholdBytes = INTEGER,
--     wepAuthentication = STRING,
--     sidebandSelection = STRING,
--     pmfEnabled = BOOLEAN
-- })
--
function _M.getAdvancedWirelessRadioSettings(sc, radioID)
    sc:readlock()
    local settings
    local radios = _M.getSupportedRadios(sc)
    if radios[radioID] then
        local prefix = radios[radioID].apName
        local rateValue = sc:get_wifi_transmission_rate(prefix)
        local rate = parseWirelessRateMbps(sc, rateValue)
        if not rate then
            error(('unexpected value "%s" for syscfg value "get_wifi_transmission_rate"'):format(rateValue))
        end
        settings = {
            apIsolation = sc:get_wifi_ap_isolation(prefix),
            frameburst = sc:get_wifi_frame_burst(prefix),
            ctsProtection = sc:get_wifi_cts_protection_mode(prefix),
            rateMbps = rate,
            nRateMbps = parseWirelessNRateMbps(sc:get_wifi_n_transmission_rate(prefix), radioID),
            basicRate = (parseWirelessBasicRate(sc:get_wifi_basic_rate(prefix))),
            transmissionPower = (parseWirelessTxPower(sc:get_wifi_transmission_power(prefix))),
            greenfieldPreamble = sc:get_wifi_green_field_preamble(prefix),
            htDuplicate = sc:get_wifi_ht_duplicate(prefix),
            spaceTimeBlockCode = sc:get_wifi_space_time_block_code(prefix),
            wirelessMultimediaPowerSave = sc:get_wifi_multimedia_power_save(prefix),
            beaconIntervalMilliseconds = sc:get_wifi_beacon_interval(prefix),
            dtimIntervalMilliseconds = sc:get_wifi_dtim_interval(prefix),
            fragmentationThresholdBytes = sc:get_wifi_fragmentation_threshold(prefix),
            rtsThresholdBytes = sc:get_wifi_rts_threshold(prefix),
            wepAuthentication = (parseWEPAuthentication(sc:get_wifi_auth_type(prefix))),
            sidebandSelection = (parseSidebandSelection(sc:get_wifi_sideband(prefix))),
            pmfEnabled = sc:get_wifi_pmf_enabled(prefix)
        }
    end
    return settings
end

--
-- Set the advanced settings of a single wireless radio on the local device.
--
-- Valid ranges are taken from wlancfg/lib/wlan_api.h (see struct wifiAdvancedInfo definition)
--
-- input = CONTEXT, STRING, {
--     apIsolation = OPTIONAL(BOOLEAN),
--     frameburst = OPTIONAL(BOOLEAN),
--     ctsProtection = OPTIONAL(BOOLEAN),
--     rateMbps = OPTIONAL(INTEGER),
--     nRateMbps = OPTIONAL(FLOAT),
--     basicRate = OPTIONAL(STRING),
--     transmissionPower = OPTIONAL(STRING),
--     greenfieldPreamble = OPTIONAL(BOOLEAN),
--     htDuplicate = OPTIONAL(BOOLEAN),
--     spaceTimeBlockCode = OPTIONAL(BOOLEAN),
--     wirelessMultimediaPowerSave = OPTIONAL(BOOLEAN),
--     beaconIntervalMilliseconds = OPTIONAL(INTEGER),
--     dtimIntervalMilliseconds = OPTIONAL(INTEGER),
--     fragmentationThresholdBytes = OPTIONAL(INTEGER),
--     rtsThresholdBytes = OPTIONAL(INTEGER),
--     wepAuthentication = OPTIONAL(STRING),
--     sidebandSelection = OPTIONAL(STRING),
--     pmfEnabled = OPTIONAL(BOOLEAN)
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorUnknownRadio',
--     'ErrorUnsupportedRate',
--     'ErrorUnsupportedNRate',
--     'ErrorInvalidBasicRate',
--     'ErrorInvalidTransmissionPower',
--     'ErrorInvalidBeaconInterval',
--     'ErrorInvalidDTIMInterval',
--     'ErrorInvalidFragmentationThreshold',
--     'ErrorInvalidRTSThreshold'
--     'ErrorInvalidWEPAuthentication',
--     'ErrorInvalidSidebandSelection'
-- )
--
function _M.setAdvancedWirelessRadioSettings(sc, radioID, settings)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local profile = _M.getSupportedRadios(sc)[radioID]
    if not profile then
        return 'ErrorUnknownRadio'
    end

    local prefix = profile.apName

    local restartWiFi = false

    local function SetEnabledDisabledSyscfgBoolean(name, value)
        return sc:set(name, value and 'enabled' or 'disabled')
    end

    if settings.apIsolation ~= nil then
        sc:set_wifi_ap_isolation(prefix, settings.apIsolation)
    end

    if settings.frameburst ~= nil then
        sc:set_wifi_frame_burst(prefix, settings.frameburst)
    end

    if settings.ctsProtection ~= nil then
-- and 'auto' or 'disabled'
        sc:set_wifi_cts_protection_mode(prefix, settings.ctsProtection)
    end

    if settings.rateMbps ~= nil then
        if not profile.supportedRateMbpsSet[settings.rateMbps] then
            return 'ErrorUnsupportedRate'
        end
        sc:set_wifi_transmission_rate(prefix, serializeWirelessRateMbps(sc, settings.rateMbps))
    end

    if settings.nRateMbps ~= nil then
        if not profile.supportedNRateMbpsSet[settings.nRateMbps] then
            return 'ErrorUnsupportedNRate'
        end
        sc:set_wifi_n_transmission_rate(prefix, serializeWirelessNRateMbps(settings.nRateMbps, radioID))
    end

    if settings.basicRate ~= nil then
        local rate = serializeWirelessBasicRate(settings.basicRate)
        if rate == nil then
            return 'ErrorInvalidBasicRate'
        end
        sc:set_wifi_basic_rate(prefix, rate)
    end

    if settings.transmissionPower ~= nil then
        local power = serializeWirelessTxPower(settings.transmissionPower)
        if power == nil then
            return 'ErrorInvalidTransmissionPower'
        end
        sc:set_wifi_transmission_power(prefix, power)
    end

    if settings.greenfieldPreamble ~= nil then
        sc:set_wifi_green_field_preamble(prefix, settings.greenfieldPreamble)
    end

    if settings.htDuplicate ~= nil then
        sc:set_wifi_ht_duplicate(prefix, settings.htDuplicate)
    end

    if settings.spaceTimeBlockCode ~= nil then
        sc:set_wifi_space_time_block_code(prefix, settings.spaceTimeBlockCode)
    end

    if settings.wirelessMultimediaPowerSave ~= nil then
        sc:set_wifi_multimedia_power_save(prefix, settings.wirelessMultimediaPowerSave)
    end

    --
    -- Note:
    --
    -- This value is restricted to [20, 1000]. Setting it to a value outside this range will cause
    -- the actual value of 100 to be set in the driver.
    --
    -- See init.d/service_wifi/service_wifi_infra_phy.sh, get_vendor_beacon_interval()
    --
    if settings.beaconIntervalMilliseconds ~= nil then
        if 1 > settings.beaconIntervalMilliseconds or settings.beaconIntervalMilliseconds > 65535 then
            return 'ErrorInvalidBeaconInterval'
        end
        sc:set_wifi_beacon_interval(prefix, settings.beaconIntervalMilliseconds)
    end

    if settings.dtimIntervalMilliseconds ~= nil then
        if 1 > settings.dtimIntervalMilliseconds or settings.dtimIntervalMilliseconds > 255 then
            return 'ErrorInvalidDTIMInterval'
        end
        sc:set_wifi_dtim_interval(prefix, settings.dtimIntervalMilliseconds)
    end

    if settings.fragmentationThresholdBytes ~= nil then
        if 256 > settings.fragmentationThresholdBytes or settings.fragmentationThresholdBytes > 2346 then
            return 'ErrorInvalidFragmentationThreshold'
        end
        sc:set_wifi_fragmentation_threshold(prefix, settings.fragmentationThresholdBytes)
    end

    if settings.rtsThresholdBytes ~= nil then
        if 0 > settings.rtsThresholdBytes or settings.rtsThresholdBytes > 2347 then
            return 'ErrorInvalidRTSThreshold'
        end
        sc:set_wifi_rts_threshold(prefix, settings.rtsThresholdBytes)
    end

    if settings.wepAuthentication ~= nil then
        local auth = serializeWEPAuthentication(settings.wepAuthentication)
        if auth == nil then
            return 'ErrorInvalidWEPAuthentication'
        end
        sc:set_wifi_auth_type(prefix, auth)
    end

    if settings.sidebandSelection ~= nil then
        local sideband = serializeSidebandSelection(settings.sidebandSelection)
        if sideband == nil then
            return 'ErrorInvalidSidebandSelection'
        end
        sc:set_wifi_sideband(prefix, sideband)
    end

    if settings.pmfEnabled ~= nil then
        sc:set_wifi_pmf_enabled(prefix, settings.pmfEnabled)
    end
    sc:update_advanced_wifi_configuration()
end

--
-- Start a WPS session.
--
-- input = CONTEXT, OPTIONAL(STRING)
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidClientPIN',
--     'ErrorWPSSessionAlreadyInProgress',
--     'ErrorWPSServerNotEnabled'
-- )
--
function _M.startWPSServerSession(sc, clientPIN)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if clientPIN then
        if not util.isValidWPSPIN(clientPIN) then
            return 'ErrorInvalidClientPIN'
        end
    end
    if not _M.getWPSServerEnabled(sc) then
        return 'ErrorWPSServerNotEnabled'
    end
    sc:setevent('jnap_side_effects-wps_session-pin', clientPIN)
    sc:setevent('jnap_side_effects-wps_session-start')
end

--
-- Stop the WPS session currently in progress.
--
-- input = CONTEXT
--
-- output = NIL_OR_ONE_OF(
--     'ErrorWPSSessionNotInProgress'
-- )
--
function _M.stopWPSServerSession(sc)
    sc:writelock()
    if not _M.isWPSSessionInProgress(sc) then
        return 'ErrorWPSSessionNotInProgress'
    end
    sc:setevent('jnap_side_effects-wps_session-stop')
end

--
-- Get whether a WPS session is currently in progress.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.isWPSSessionInProgress(sc)
    sc:readlock()
    return sc:get_wifi_wps_status() == 'running'
end

--
-- Get the WPS server PIN.
--
-- input = CONTEXT
--
-- output = STRING
--
function _M.getWPSServerPIN(sc)
    sc:readlock()
    return sc:get_wifi_wps_pin()
end

--
-- Get the last WPS server result.
--
-- input = CONTEXT
--
-- output = STRING
--
function _M.getWPSServerResult(sc)
    sc:readlock()
    local status = sc:get_wifi_wps_status()
    if status == 'success' then
        return 'Succeeded'
    elseif status == 'failed' then
        return 'Failed'
    end
end

--
-- Get whether or not WPS server is enabled.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.getWPSServerEnabled(sc)
    sc:readlock()
    -- Assume enabled unless explicitly set to 'disabled'. This matches the
    -- logic used in init/init.d/service_wifi/service_wifi_user.sh
    return sc:get_wifi_wps_enabled() ~= 'disabled'
end

--
-- Enable or disable WPS server.
--
-- input = CONTEXT
--
function _M.setWPSServerEnabled(sc, enabled)
   sc:writelock()
   sc:set_wifi_wps_enabled(enabled and 'enabled' or 'disabled')
end

--
-- Get whether or not WPS server is available.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.isWPSServerAvailable(sc)
    if require('macfilter').getMode(sc) ~= 'Disabled' then
        return false
    end
    -- enabled radio bands cannot have wep/wpa security
    for radioID, profile in pairs(_M.getSupportedRadios(sc)) do
        local prefix = profile.apName
        local isEnabled = (sc:get_wifi_state(prefix) == 'up')
        if isEnabled then
            local security = _M.parseWirelessSecurity(sc:get_wifi_security_mode(prefix))
            if security == 'WEP' or security == 'WPA-Personal' or security == 'WPA3-Personal' or security == 'WPA-Enterprise' then
               return false
            end
        end
    end
    return true
end

--
-- Get whether or not airtime fairness feature is supported.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.getAirtimeFairnessSupported(sc)
    sc:readlock()
    return sc:get_wifi_atf_supported()
end

--
-- Get whether or not airtime fairness feature is enabled.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.getAirtimeFairnessSettings(sc)
    sc:readlock()
    return sc:get_wifi_atf_enabled()
end

--
-- Enable or disable airtime fairness feature.
--
-- input = CONTEXT
--
function _M.setAirtimeFairnessSettings(sc, enabled)
   sc:writelock()
   if not sc:get_wifi_atf_supported() then
       return 'ErrorAirtimeFairnessNotSupported'
   end
   sc:set_wifi_atf_enabled(enabled)
end

--
-- Get the dynamic frequency selection (DFS) settings
--
-- input = CONTEXT
--
-- output = {
--     isDFSSupported = BOOLEAN,
--     isDFSEnabled = OPTIONAL(BOOLEAN)
--  }
--
function _M.getDFSSettings(sc)
    sc:readlock()

    local dfsSupported = sc:get_wifi_dfs_supported()
    local dfsEnabled
    if dfsSupported then
        dfsEnabled = sc:get_wifi_dfs_enabled()
    end
    return {
        isDFSSupported = dfsSupported,
        isDFSEnabled = dfsEnabled
    }
end

--
-- Set the dynamic frequency selection (DFS) settings
--
-- input = CONTEXT, {
--     isDFSEnabled = BOOLEAN
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorDeviceNotInMasterMode',
--     'ErrorDFSNotSupported'
-- )
--
function _M.setDFSSettings(sc, input)
   sc:writelock()
   if util.isNodeUtilModuleAvailable() and not require('nodes_util').isNodeAMaster(sc) then
      return 'ErrorDeviceNotInMasterMode'
   end
   if not sc:get_wifi_dfs_supported() then
       return 'ErrorDFSNotSupported'
   end
   sc:set_wifi_dfs_enabled(input.isDFSEnabled)
end

return _M -- return the module
