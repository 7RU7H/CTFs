--
-- 2018 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author: hsulliva $
-- $DateTime: 2020/02/06 14:32:09 $
-- $Id: //depot-irv/lego/branches/wrt_platform_sp1/lego_overlay/proprietary/jnap/lualib/parentalcontrol.lua#3 $
--

-- parentalcontrol.lua - library to configure parental control state.

local hdk = require('libhdklua')
local platform = require('platform')
local util = require('util')

local _M = {} -- create the module


--
-- Get whether parental control is enabled.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.getIsEnabled(sc)
    sc:readlock()
    return sc:get_parentalcontrol_enabled()
end

--
-- Set whether parental control is enabled.
--
-- input = CONTEXT, BOOLEAN
--
function _M.setIsEnabled(sc, isEnabled)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    sc:set_parentalcontrol_enabled(isEnabled)
end

--
-- Get the parental control rules.
--
-- input = CONTEXT
--
-- output = ARRAY_OF({
--     isEnabled = BOOLEAN,
--     description = STRING,
--     macAddresses = ARRAY_OF(MACADDRESS),
--     wanSchedule = {
--         sunday = STRING,
--         monday = STRING,
--         tuesday = STRING,
--         wednesday = STRING,
--         thursday = STRING,
--         friday = STRING,
--         saturday = STRING,
--     },
--     blockedURLs = ARRAY_OF(STRING)
-- })
--
function _M.getRules(sc)
    sc:readlock()
    local rules = {}
    local ruleNumbers = {}
    for i = 1, sc:get_parentalcontrol_policy_count() do
        local macAddresses = {}
        for j = 1, sc:get_parentalcontrol_blocked_device_count(i) do
            table.insert(macAddresses, hdk.macaddress(sc:get_parentalcontrol_blocked_device(i, j)))
        end
        local blockedURLs = {}
        for j = 1, sc:get_parentalcontrol_blocked_url_count(i) do
            table.insert(blockedURLs, sc:get_parentalcontrol_blocked_url(i, j))
        end
        table.insert(rules, {
            isEnabled = sc:get_parentalcontrol_policy_enabled(i), -- the status may be set to 'enabled', by the legacy HNAP implementation
            description = sc:get_parentalcontrol_name(i),
            macAddresses = macAddresses,
            blockedURLs = blockedURLs,
            wanSchedule = {
                sunday = sc:get_parentalcontrol_time_blocks(i, 'sunday'),
                monday = sc:get_parentalcontrol_time_blocks(i, 'monday'),
                tuesday = sc:get_parentalcontrol_time_blocks(i, 'tuesday'),
                wednesday = sc:get_parentalcontrol_time_blocks(i, 'wednesday'),
                thursday = sc:get_parentalcontrol_time_blocks(i, 'thursday'),
                friday = sc:get_parentalcontrol_time_blocks(i, 'friday'),
                saturday = sc:get_parentalcontrol_time_blocks(i, 'saturday'),
            }
        })
        table.insert(ruleNumbers, tonumber(sc:get_parentalcontrol_number(i)) or i)
    end
    return rules, ruleNumbers
end

--
-- Set the parental control rules.
--
-- input = CONTEXT, ARRAY_OF({
--     isEnabled = BOOLEAN,
--     description = STRING,
--     macAddresses = ARRAY_OF(MACADDRESS),
--     wanSchedule = {
--         sunday = STRING,
--         monday = STRING,
--         tuesday = STRING,
--         wednesday = STRING,
--         thursday = STRING,
--         friday = STRING,
--         saturday = STRING,
--     },
--     blockedURLs = ARRAY_OF(STRING)
-- })
--
-- output = NIL_OR_ONE_OF(
--     'ErrorDescriptionTooLong',
--     'ErrorInvalidDescription',
--     'ErrorInvalidMACAddress',
--     'ErrorTooManyMACAddresses',
--     'ErrorInvalidWANSchedule',
--     'ErrorBlockedURLTooLong',
--     'ErrorTooManyBlockedURLs',
--     'ErrorRulesOverlap'
--     'ErrorTooManyRules'
-- )
--
function _M.setRules(sc, rules, fromNetworkSecurity)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    local isNetworkSecurityAvailable = util.isModuleAvailable('networksecurity')

    if isNetworkSecurityAvailable then
        -- If Shield's license ID is set, then we tell user to use SetNetworkSecuritySettings
        local licenseID = sc:get_shield_licenseid()
        if not fromNetworkSecurity and licenseID and #licenseID > 0 then
            return 'ErrorDeprecatedAction'
        end
    end

    local maxRuleDescriptionLength = _M.getMaxParentalControlRuleDescriptionLength(sc)
    local maxRuleMACAddresses = _M.getMaxParentalControlRuleMACAddresses(sc)
    local maxRuleBlockedURLLength = _M.getMaxParentalControlRuleBlockedURLLength(sc)
    local maxRuleBlockedURLs = _M.getMaxParentalControlRuleBlockedURLs(sc)
    local maxRules = _M.getMaxParentalControlRules(sc)

    local macAddressSet = {}

    for i, rule in pairs(rules) do
        if #rule.description > maxRuleDescriptionLength then
            return 'ErrorDescriptionTooLong'
        end
        if not _M.isValidDescription(rule.description) then
            return 'ErrorInvalidDescription'
        end
        sc:set_parentalcontrol_name(i, rule.description)

        sc:set_parentalcontrol_policy_enabled(i, rule.isEnabled)

        for j, macAddress in ipairs(rule.macAddresses) do
            if macAddress:iszero() then
                return 'ErrorInvalidMACAddress'
            end
            local macAddressString = tostring(macAddress)
            if macAddressSet[macAddressString] then
                return 'ErrorRulesOverlap'
            end
            macAddressSet[macAddressString] = true
            sc:set_parentalcontrol_blocked_device(i, j, macAddressString)
        end
        if #rule.macAddresses > maxRuleMACAddresses then
            return 'ErrorTooManyMACAddresses'
        end
        sc:set_parentalcontrol_blocked_device_count(i, #rule.macAddresses)

        for j, day in ipairs({'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'}) do
            local schedule = rule.wanSchedule[day]
            if #schedule ~= 48 or schedule:find('^[01]*$') == nil then
                return 'ErrorInvalidWANSchedule'
            end
            sc:set_parentalcontrol_time_blocks(i, day, schedule)
        end

        local blockedURLCount = 0
        for j, blockedURL in ipairs(rule.blockedURLs) do
            if #blockedURL > maxRuleBlockedURLLength then
                return 'ErrorBlockedURLTooLong'
            end
            -- Ignore empty strings
            if #blockedURL > 0 then
                sc:set_parentalcontrol_blocked_url(i, j, blockedURL)
                blockedURLCount = blockedURLCount + 1
            end
        end
        if #rule.blockedURLs > maxRuleBlockedURLs then
            return 'ErrorTooManyBlockedURLs'
        end
        sc:set_parentalcontrol_blocked_url_count(i, blockedURLCount)

        -- These are written for compatibility with the underlying guardian
        -- implementation, but are never referenced by the JNAP API.

        sc:set_parentalcontrol_number(i, rule.number or i)
        sc:set_parentalcontrol_blocked_port_count(i, 0)
    end

    if #rules > maxRules then
        return 'ErrorTooManyRules'
    end

    sc:set_parentalcontrol_policy_count(#rules)

    if not fromNetworkSecurity and isNetworkSecurityAvailable then
        local networksecurity = require('networksecurity')

        local newSettings = {
            profiles = {
                deviceSpecificProfiles = networksecurity.convertToNetworkSecurityRules(rules)
            },
            internetBlockScheduleExceptions = {
                ignoreScheduleAndBlockMACAddresses = {},
                ignoreScheduleAndAllowMACAddresses = {}
            }
        }
        -- need to fire config changed to copy the config file from /tmp to /var and trigger the backup feature.
        networksecurity.fireConfigChangedSysevent(sc, newSettings)
        return networksecurity.writeConfigurationSettings(sc, newSettings)
    end
end

--
-- Get the maximum length of parental control rule description.
--
-- input = CONTEXT
--
-- output = NUMBER
--
function _M.getMaxParentalControlRuleDescriptionLength(sc)
    return sc:get_parentalcontrol_maxpolicynamelength()
end

--
-- Get the maximum number of MAC addresses in a parental control rule.
--
-- input = CONTEXT
--
-- output = NUMBER
--
function _M.getMaxParentalControlRuleMACAddresses(sc)
    sc:readlock()
    return sc:get_parentalcontrol_maxapplieddevices(0)
end

--
-- Get the maximum length of a blocked URL in a parental control rule.
--
-- input = CONTEXT
--
-- output = NUMBER
--
function _M.getMaxParentalControlRuleBlockedURLLength(sc)
    sc:readlock()
    return sc:get_parentalcontrol_maxblockedurllength()
end

--
-- Get the maximum number of blocked URLs in a parental control rule.
--
-- input = CONTEXT
--
-- output = NUMBER
--
function _M.getMaxParentalControlRuleBlockedURLs(sc)
    sc:readlock()
    return sc:get_parentalcontrol_maxblockedurls()
end

--
-- Get the maximum number of parental control rules.
--
-- input = CONTEXT
--
-- output = NUMBER
--
function _M.getMaxParentalControlRules(sc)
    sc:readlock()
    return sc:get_parentalcontrol_maxrules()
end

--
-- Determine whether a policy description is valid
--
-- input = STRING
--
-- output = BOOLEAN
--
function _M.isValidDescription(description)
    if type(description) ~= 'string' then
        return false
    end

    -- the description can only include characters in the ASCII 32-126 range
    return description:find('^[\032-\126]*$') ~= nil
end

return _M -- return the module
