--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local function GetDynamicSinglePortForwardingRules(ctx)
    local dynamicportforwarding = require('dynamicportforwarding')
    local platform = require('platform')

    local sc = ctx:sysctx()
    local rules = dynamicportforwarding.getDynamicSinglePortForwardingRules(sc)
    local info = dynamicportforwarding.getDynamicPortForwardingGeneralInfo(sc)
    return 'OK', {
        rules = rules,
        maxDescriptionLength = info.maxDescriptionLength,
        maxRules = info.maxDynamicSinglePortForwardingRules
    }
end


local function GetDynamicPortRangeForwardingRules(ctx)
    local dynamicportforwarding = require('dynamicportforwarding')
    local platform = require('platform')

    local sc = ctx:sysctx()
    local rules = dynamicportforwarding.getDynamicPortRangeForwardingRules(sc)
    local info = dynamicportforwarding.getDynamicPortForwardingGeneralInfo(sc)
    return 'OK', {
        rules = rules,
        maxDescriptionLength = info.maxDescriptionLength,
        maxRules = info.maxDynamicPortRangeForwardingRules
    }
end


local function GetDynamicIPv6ConnectionRules(ctx)
    local dynamicportforwarding = require('dynamicportforwarding')
    local platform = require('platform')

    local sc = ctx:sysctx()
    local rules = dynamicportforwarding.getDynamicIPv6ConnectionRules(sc)
    local info = dynamicportforwarding.getDynamicPortForwardingGeneralInfo(sc)
    return 'OK', {
        rules = rules,
        maxDescriptionLength = info.maxDescriptionLength,
        maxRules = info.maxDynamicIPv6ConnectionRules
    }
end


local function AddDynamicSinglePortForwardingRule(ctx, input)
    local dynamicportforwarding = require('dynamicportforwarding')
    local hdk = require('libhdklua')

    local sc = ctx:sysctx()
    local error = dynamicportforwarding.validateDynamicSinglePortForwardingRule(sc, input.rule)
    if error ~= 'OK' then
        return error
    end

    local newRuleUUID = dynamicportforwarding.addDynamicSinglePortForwardingRule(sc, input.rule)

    return 'OK', {
        ruleUUID = hdk.uuid(newRuleUUID)
    }
end

local function AddDynamicPortRangeForwardingRule(ctx, input)
    local dynamicportforwarding = require('dynamicportforwarding')
    local hdk = require('libhdklua')

    local sc = ctx:sysctx()
    local error = dynamicportforwarding.validateDynamicPortRangeForwardingRule(sc, input.rule)

    if error ~= 'OK' then
        return error
    end

    local ruleUUID = dynamicportforwarding.addDynamicPortRangeForwardingRule(sc, input.rule)

    return 'OK', {
        ruleUUID = hdk.uuid(ruleUUID)
    }
end


local function AddDynamicIPv6ConnectionRule(ctx, input)
    local dynamicportforwarding = require('dynamicportforwarding')
    local hdk = require('libhdklua')

    local sc = ctx:sysctx()
    local error = dynamicportforwarding.validateDynamicIPv6ConnectionRule(sc, input.rule)

    if error ~= 'OK' then
        return error
    end

    local ruleUUID = dynamicportforwarding.addDynamicIPv6ConnectionRule(sc, input.rule)

    return 'OK', {
        ruleUUID = hdk.uuid(ruleUUID)
    }
end


local function RemoveDynamicSinglePortForwardingRule(ctx, input)
    local dynamicportforwarding = require('dynamicportforwarding')

    local sc = ctx:sysctx()
    local error = dynamicportforwarding.removeDynamicSinglePortForwardingRule(sc, input.ruleUUID)
    return error or 'OK'
end


local function RemoveDynamicPortRangeForwardingRule(ctx, input)
    local dynamicportforwarding = require('dynamicportforwarding')

    local sc = ctx:sysctx()
    local error = dynamicportforwarding.removeDynamicPortRangeForwardingRule(sc, input.ruleUUID)
    return error or 'OK'
end


local function RemoveDynamicIPv6ConnectionRule(ctx, input)
    local dynamicportforwarding = require('dynamicportforwarding')

    local sc = ctx:sysctx()
    local error = dynamicportforwarding.removeDynamicIPv6ConnectionRule(sc, input.ruleUUID)
    return error or 'OK'
end

return require('libhdklua').loadmodule('jnap_dynamicportforwarding'), {
    ['http://linksys.com/jnap/dynamicportforwarding/GetDynamicSinglePortForwardingRules'] = GetDynamicSinglePortForwardingRules,
    ['http://linksys.com/jnap/dynamicportforwarding/GetDynamicPortRangeForwardingRules'] = GetDynamicPortRangeForwardingRules,
    ['http://linksys.com/jnap/dynamicportforwarding/GetDynamicIPv6ConnectionRules'] = GetDynamicIPv6ConnectionRules,
    ['http://linksys.com/jnap/dynamicportforwarding/AddDynamicSinglePortForwardingRule'] = AddDynamicSinglePortForwardingRule,
    ['http://linksys.com/jnap/dynamicportforwarding/AddDynamicPortRangeForwardingRule'] = AddDynamicPortRangeForwardingRule,
    ['http://linksys.com/jnap/dynamicportforwarding/AddDynamicIPv6ConnectionRule'] = AddDynamicIPv6ConnectionRule,
    ['http://linksys.com/jnap/dynamicportforwarding/RemoveDynamicSinglePortForwardingRule'] = RemoveDynamicSinglePortForwardingRule,
    ['http://linksys.com/jnap/dynamicportforwarding/RemoveDynamicPortRangeForwardingRule'] = RemoveDynamicPortRangeForwardingRule,
    ['http://linksys.com/jnap/dynamicportforwarding/RemoveDynamicIPv6ConnectionRule'] = RemoveDynamicIPv6ConnectionRule,
}
