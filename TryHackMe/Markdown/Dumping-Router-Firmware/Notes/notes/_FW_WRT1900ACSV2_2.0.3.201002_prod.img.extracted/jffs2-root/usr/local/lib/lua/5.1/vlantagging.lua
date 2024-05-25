--
-- 2019 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

-- powermodem.lua - library to configure upstream power modem.

local platform = require('platform')
local util = require('util')

local _M = {} -- create the module

_M.ISP_PROFILES_DB = '/etc/VLANTagging_ISP_Profiles.json'

_M.IPTV = 'IPTV'
_M.VOIP = 'VOIP'

_M.PORTMAPPINGS = {
    [_M.VOIP] = {
        ivlan = 1,
        port = 3
    },
    [_M.IPTV] = {
        ivlan = 2,
        port = 4
    }
}

_M.MAX_VLAN_RULES_PER_PORT = 5

--
-- Utility function to return the parsed ISP Profiles.
--
local function getParsedISPProfiles()
    local json = require('libhdkjsonlua')

    local fh = io.open(_M.ISP_PROFILES_DB, 'r')
    if not fh then
        return nil, 'Failed to open ISP profiles database file "'.._M.ISP_PROFILES_DB..'"'
    end

    local profiles_str = fh:read('*a')
    if not profiles_str then
        return nil, 'Failed to read ISP profiles database file'
    end

    local profiles = json.parse(profiles_str)
    if not profiles then
        return nil, 'Failed to parse ISP profiles database file'
    end

    return profiles.ispProfiles
end

--
-- Get the list of pre-defined ISP Profiles for VLAN tagging.
--
-- input = CONTEXT
--
-- output = {
--     profiles = ARRAY_OF({
--         name = STRING,
--         vlanTaggingSettings = {
--             wanSettings = {
--                 vlanID = INT,
--                 vlanPriority = INT,
--                 vlanStatus = STRING
--             },
--             ipTVSettings = [{
--                 vlanID = INT,
--                 vlanPriority = INT,
--                 vlanStatus = STRING
--             }],
--             voIPSettings = [{
--                 vlanID = INT,
--                 vlanPriority = INT,
--                 vlanStatus = STRING
--             }]
--         }
--     })
-- }
--
function _M.getProfiles(sc)
    sc:readlock()

    local profiles, error = getParsedISPProfiles()
    if not profiles then
        return nil, error
    end

    return profiles
end

function _M.getProfiles2(sc)
    sc:readlock()

    local profiles, error = getParsedISPProfiles()
    if not profiles then
        return nil, error
    end

    -- If the platform does not support VLAN tagging priority
    -- then remove the vlanPriority table entries from the profiles
    if not _M.isVLANPrioritySupported(sc) then
        for i = 1, #profiles do
            profiles[i].vlanTaggingSettings.wanSettings.vlanPriority = nil

            local ipTVSettings = profiles[i].vlanTaggingSettings.ipTVSettings
            for j = 1, #ipTVSettings do
                ipTVSettings[j].vlanPriority = nil
            end

            local voIPSettings = profiles[i].vlanTaggingSettings.voIPSettings
            for j = 1, #voIPSettings do
                voIPSettings[j].vlanPriority = nil
            end
        end
    end

    return profiles
end

function _M.isValidVLANID(sc, value)
    if value >= sc:get_vlan_lower_limit() and value <= sc:get_vlan_upper_limit() then
        return true
    end
    return false
end

function _M.isValidWANVLANID(sc, value)
    if value >= sc:get_wan_vlan_lower_limit() and value <= sc:get_wan_vlan_upper_limit() then
        return true
    end
    return false
end

function _M.isValidVLANPriority(value)
    if value >= 0 and value <= 7 then
        return true
    end
    return false
end

local function isValidProfileName(value)
        return #value >= 0 and #value <= 64
    end

local function validateSettings(sc, vlanSettings)
    local validateDuplicateVLANIDs = function()
        -- build a table with all VLANs
        local tempVLANs = {}
        for i, vlan in ipairs(vlanSettings.ipTVSettings) do
            table.insert(tempVLANs, vlan)
        end
        for i, vlan in ipairs(vlanSettings.voIPSettings) do
            table.insert(tempVLANs, vlan)
        end
        -- look for priority values for duplicated VLAN IDs
        local tempVLAN = table.remove(tempVLANs)
        while tempVLAN do
            for i, vlan in ipairs(tempVLANs) do
                if tempVLAN.vlanID == vlan.vlanID and tempVLAN.vlanPriority ~= vlan.vlanPriority then
                    return 'ErrorDuplicateVLANIDs'
                end
            end
            tempVLAN = table.remove(tempVLANs)
        end
    end

    local validateSettingsHelper = function(settings)
        if not _M.isValidVLANID(sc, settings.vlanID) then
            return 'ErrorInvalidVLANID'
        end
        if settings.vlanPriority and not _M.isValidVLANPriority(settings.vlanPriority) then
            return 'ErrorInvalidVLANPriority'
        end
    end

    if (#vlanSettings.ipTVSettings) > _M.MAX_VLAN_RULES_PER_PORT or (#vlanSettings.voIPSettings) > _M.MAX_VLAN_RULES_PER_PORT then
            return 'ErrorTooManyVLANTaggingRules'
    end

    local error = validateDuplicateVLANIDs(vlanSettings)
    if not error then
        error = validateSettingsHelper(vlanSettings.wanSettings)
    end
    if not error then
        for i, settings in ipairs(vlanSettings.ipTVSettings) do
            error = validateSettingsHelper(settings)
        end
    end
    if not error then
        for i, settings in ipairs(vlanSettings.voIPSettings) do
            error = validateSettingsHelper(settings)
        end
    end
    return error
end

function _M.parseTaggingStatus(taggingStatus)
    if taggingStatus == 'untagged' then
        return 'Untagged'
    elseif taggingStatus == 'tagged' then
        return 'Tagged'
    else
        assert(taggingStatus, 'Invalid tagging status!')
    end
end

function _M.serializeTaggingStatus(taggingStatus)
    if taggingStatus == 'Untagged' then
        return 'untagged'
    elseif taggingStatus == 'Tagged' then
        return 'tagged'
    else
        assert(taggingStatus, 'Invalid tagging status!')
    end
end

--
-- Whether the VLAN tagging feature is enabled.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.isVLANTaggingEnabled(sc)
    sc:readlock()
    local enabled = sc:get_vlan_tagging_enabled()
    return (enabled == nil and false or enabled)
end

--
-- Enable/Disable the VLAN tagging feature.
--
-- input = CONTEXT, BOOLEAN
--
function _M.setVLANTaggingEnabled(sc, enabled)
    sc:writelock()
    sc:set_vlan_tagging_enabled(enabled)
end

--
-- Whether VLAN tagging priority is supported on the platform.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.isVLANPrioritySupported(sc)
    -- VLAN priority is now supported on all platforms
    return true
end


--
-- Set the VLAN tagging settings.
--
-- input = CONTEXT, {
--     name = STRING,
--     vlanTaggingSettings = {
--         wanSettings = {
--             vlanID = INT,
--             vlanPriority = INT,
--             vlanStatus = STRING
--         },
--         ipTVSettings = [{
--             vlanID = INT,
--             vlanPriority = INT,
--             vlanStatus = STRING
--         }],
--         voIPSettings = [{
--             vlanID = INT,
--             vlanPriority = INT,
--             vlanStatus = STRING
--         }]
--     }
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidVLANID',
--     'ErrorInvalidVLANPriority'
-- )
--
function _M.setVLANTaggingProfile(sc, profile)
    local setWANPacketTagging = function(sc, wanSettings)
        sc:set_wan_tagging_id(wanSettings.vlanID)
        -- vlanPriority is optional, as it's not supported on some platforms
        if (wanSettings.vlanPriority) then
            sc:set_wan_tagging_priority(wanSettings.vlanPriority)
        end
        sc:set_wan_tagging(_M.serializeTaggingStatus(wanSettings.vlanStatus))
    end

    local setVLANTagging = function(sc, vlanSettings, port)
        for i, vlanSetting in ipairs(vlanSettings) do
            sc:set_vlan_tagging_id(_M.PORTMAPPINGS[port].ivlan, i, vlanSetting.vlanID)
            -- vlanPriority is optional, as it's not supported on some platforms
            if (vlanSetting.vlanPriority) then
                sc:set_vlan_tagging_priority(_M.PORTMAPPINGS[port].ivlan, i, vlanSetting.vlanPriority)
            end
            sc:set_vlan_port_tagging(_M.PORTMAPPINGS[port].ivlan, i, _M.PORTMAPPINGS[port].port, _M.serializeTaggingStatus(vlanSetting.vlanStatus))
        end
        sc:set_vlan_tag_count(_M.PORTMAPPINGS[port].ivlan, #vlanSettings)
    end

    sc:writelock()

    if not isValidProfileName(profile.name) then
        return 'ErrorInvalidVLANProfileName'
    end
    local error = validateSettings(sc, profile.vlanTaggingSettings)
    if error then
        return error
    end

    sc:init_vlan_tagging_settings()
    sc:set_vlan_tagging_name(profile.name)

    setWANPacketTagging(sc, profile.vlanTaggingSettings.wanSettings)
    setVLANTagging(sc, profile.vlanTaggingSettings.ipTVSettings, _M.IPTV)
    setVLANTagging(sc, profile.vlanTaggingSettings.voIPSettings, _M.VOIP)
end

-- This version checks to ensure that vlanPriority is not in the input profile if it's not supported
function _M.setVLANTaggingProfile2(sc, profile)
    sc:readlock()

    if not _M.isVLANPrioritySupported(sc) then
        if profile.vlanTaggingSettings.wanSettings.vlanPriority then
            return "ErrorVLANPriorityNotSupported"
        end
        local ipTVSettings = profile.vlanTaggingSettings.ipTVSettings
        for j = 1, #ipTVSettings do
            if ipTVSettings[j].vlanPriority then
                return "ErrorVLANPriorityNotSupported"
            end
        end
        local voIPSettings = profile.vlanTaggingSettings.voIPSettings
        for j = 1, #voIPSettings do
            if voIPSettings[j].vlanPriority then
                return "ErrorVLANPriorityNotSupported"
            end
        end
    end

    _M.setVLANTaggingProfile(sc, profile)
end

--
-- Get the current VLAN tagging profile
--
-- input = CONTEXT
--
-- output = {
--     name = STRING,
--     vlanTaggingSettings = {
--         wanSettings = {
--             vlanID = INT,
--             vlanPriority = INT,
--             vlanStatus = STRING
--         },
--         ipTVSettings = [{
--             vlanID = INT,
--             vlanPriority = INT,
--             vlanStatus = STRING
--         }],
--         voIPSettings = [{
--             vlanID = INT,
--             vlanPriority = INT,
--             vlanStatus = STRING
--         }]
--     }
-- }
--
function _M.getVLANTaggingProfile(sc)
    local getVLANTagging = function(sc, port)
        local vlanSettings = {}
        local count = sc:get_vlan_tag_count(_M.PORTMAPPINGS[port].ivlan)
        for i = 1, count do
            local vlanID = sc:get_vlan_tagging_id(_M.PORTMAPPINGS[port].ivlan, i)
            if vlanID and _M.isValidVLANID(sc, vlanID) then
                local vlanSetting = {
                    vlanID = vlanID,
                    vlanPriority = sc:get_vlan_tagging_priority(_M.PORTMAPPINGS[port].ivlan, i),
                    vlanStatus = _M.parseTaggingStatus(sc:get_vlan_port_tagging(_M.PORTMAPPINGS[port].ivlan, i, _M.PORTMAPPINGS[port].port))
                }
                table.insert(vlanSettings, vlanSetting)
            end
        end
        return vlanSettings
    end

    sc:readlock()
    local name = sc:get_vlan_tagging_name()
    local vlanTaggingSettings = {
        wanSettings = {
            vlanID = sc:get_wan_tagging_id(),
            vlanPriority = sc:get_wan_tagging_priority(),
            vlanStatus = _M.parseTaggingStatus(sc:get_wan_tagging())
        },
        ipTVSettings = getVLANTagging(sc, _M.IPTV),
        voIPSettings = getVLANTagging(sc, _M.VOIP)
    }

    if name and name ~= '' then
        return {
            name = name,
            vlanTaggingSettings = vlanTaggingSettings
        }
    else
        return nil
    end
end

return _M -- return the module
