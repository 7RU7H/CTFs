--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local function GetProfiles(ctx)
    local vlantagging = require('vlantagging')

    local sc = ctx:sysctx()
    local profiles, error = vlantagging.getProfiles(sc)
    if profiles then
        return 'OK', { profiles = profiles }
    else
        return 'Error', { error = error }
    end
end

local function GetProfiles2(ctx)
    local vlantagging = require('vlantagging')

    local sc = ctx:sysctx()
    local profiles, error = vlantagging.getProfiles2(sc)
    if profiles then
        return 'OK', { profiles = profiles }
    else
        return 'Error', { error = error }
    end
end

local function GetVLANTaggingSettings(ctx)
    local vlantagging = require('vlantagging')

    local sc = ctx:sysctx()
    local isVLANTaggingEnabled = vlantagging.isVLANTaggingEnabled(sc)
    return 'OK', {
                     isEnabled = isVLANTaggingEnabled,
                     profile = vlantagging.getVLANTaggingProfile(sc),
                     maxVLANTaggingRulesPerPort = vlantagging.MAX_VLAN_RULES_PER_PORT
                 }
end

local function GetVLANTaggingSettings2(ctx)
    local vlantagging = require('vlantagging')

    local sc = ctx:sysctx()
    return 'OK', {
                     isEnabled = vlantagging.isVLANTaggingEnabled(sc),
                     isVLANPrioritySupported =  vlantagging.isVLANPrioritySupported(sc),
                     profile = vlantagging.getVLANTaggingProfile(sc),
                     maxVLANTaggingRulesPerPort = vlantagging.MAX_VLAN_RULES_PER_PORT
                 }
end

local function GetVLANTaggingSettings3(ctx)
    local vlantagging = require('vlantagging')

    local sc = ctx:sysctx()
    sc:readlock()
    return 'OK', {
                     isEnabled = vlantagging.isVLANTaggingEnabled(sc),
                     isVLANPrioritySupported =  vlantagging.isVLANPrioritySupported(sc),
                     profile = vlantagging.getVLANTaggingProfile(sc),
                     maxVLANTaggingRulesPerPort = vlantagging.MAX_VLAN_RULES_PER_PORT,
                     vlanLowerLimit = sc:get_vlan_lower_limit(),
                     vlanUpperLimit = sc:get_vlan_upper_limit()
                 }
end

local function SetVLANTaggingSettings(ctx, input)
    local vlantagging = require('vlantagging')

    local sc = ctx:sysctx()
    local error = vlantagging.setVLANTaggingEnabled(sc, input.isEnabled)
    if not error then
        error = vlantagging.setVLANTaggingProfile(sc, input.profile)
    end
    return error or 'OK'
end

local function SetVLANTaggingSettings2(ctx, input)
    local vlantagging = require('vlantagging')

    local sc = ctx:sysctx()
    local error = vlantagging.setVLANTaggingEnabled(sc, input.isEnabled)
    if not error then
        error = vlantagging.setVLANTaggingProfile2(sc, input.profile)
    end
    return error or 'OK'
end

return require('libhdklua').loadmodule('jnap_vlantagging'), {
    ['http://linksys.com/jnap/vlantagging/GetVLANTaggingSettings'] = GetVLANTaggingSettings,
    ['http://linksys.com/jnap/vlantagging/GetVLANTaggingSettings2'] = GetVLANTaggingSettings2,
    ['http://linksys.com/jnap/vlantagging/GetVLANTaggingSettings3'] = GetVLANTaggingSettings3,
    ['http://linksys.com/jnap/vlantagging/SetVLANTaggingSettings'] = SetVLANTaggingSettings,
    ['http://linksys.com/jnap/vlantagging/SetVLANTaggingSettings2'] = SetVLANTaggingSettings2,
    ['http://linksys.com/jnap/vlantagging/GetProfiles'] = GetProfiles,
    ['http://linksys.com/jnap/vlantagging/GetProfiles2'] = GetProfiles2
}
