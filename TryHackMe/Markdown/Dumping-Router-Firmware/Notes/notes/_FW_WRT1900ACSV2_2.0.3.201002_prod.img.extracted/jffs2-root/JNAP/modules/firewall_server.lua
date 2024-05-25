--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local function GetFirewallSettings(ctx)
    local firewall = require('firewall')

    local sc = ctx:sysctx()
    local settings = firewall.getFirewallSettings(sc)
    return 'OK', settings
end

local function SetFirewallSettings(ctx, input)
    local firewall = require('firewall')

    local sc = ctx:sysctx()
    local error = firewall.setFirewallSettings(sc, input)
    return error or 'OK'
end

local function GetDMZSettings(ctx)
    local firewall = require('firewall')

    local sc = ctx:sysctx()
    local settings = firewall.getDMZSettings(sc)
    return 'OK', settings
end

local function SetDMZSettings(ctx, input)
    local firewall = require('firewall')

    local sc = ctx:sysctx()
    local error = firewall.setDMZSettings(sc, input)
    return error or 'OK'
end

local function GetALGSettings(ctx)
    local firewall = require('firewall')

    local sc = ctx:sysctx()
    return 'OK', {
        isSIPEnabled = firewall.getIsSIPALGEnabled(sc)
    }
end

local function SetALGSettings(ctx, input)
    local firewall = require('firewall')

    local sc = ctx:sysctx()
    local error = firewall.setIsSIPALGEnabled(sc, input.isSIPEnabled)
    return error or 'OK'
end

local function GetSinglePortForwardingRules(ctx)
    local firewall = require('firewall')
    local platform = require('platform')

    local sc = ctx:sysctx()
    local rules = firewall.getSinglePortForwardingRules(sc)
    return 'OK', {
        rules = rules,
        maxDescriptionLength = platform.MAX_PORT_RULE_DESCRIPTION_LENGTH,
        maxRules = platform.MAX_SINGLEPORT_RULES
    }
end

local function SetSinglePortForwardingRules(ctx, input)
    local firewall = require('firewall')

    local sc = ctx:sysctx()
    local error = firewall.setSinglePortForwardingRules(sc, input.rules)
    return error or 'OK'
end

local function GetPortRangeForwardingRules(ctx)
    local firewall = require('firewall')
    local platform = require('platform')

    local sc = ctx:sysctx()
    local rules = firewall.getPortRangeForwardingRules(sc)
    return 'OK', {
        rules = rules,
        maxDescriptionLength = platform.MAX_PORT_RULE_DESCRIPTION_LENGTH,
        maxRules = platform.MAX_PORTRANGE_RULES
    }
end

local function SetPortRangeForwardingRules(ctx, input)
    local firewall = require('firewall')

    local sc = ctx:sysctx()
    local error = firewall.setPortRangeForwardingRules(sc, input.rules)
    return error or 'OK'
end

local function GetPortRangeTriggeringRules(ctx)
    local firewall = require('firewall')
    local platform = require('platform')

    local sc = ctx:sysctx()
    local rules = firewall.getPortRangeTriggeringRules(sc)
    return 'OK', {
        rules = rules,
        maxDescriptionLength = platform.MAX_PORT_RULE_DESCRIPTION_LENGTH,
        maxRules = platform.MAX_PORTTRIGGER_RULES
    }
end

local function SetPortRangeTriggeringRules(ctx, input)
    local firewall = require('firewall')

    local sc = ctx:sysctx()
    local error = firewall.setPortRangeTriggeringRules(sc, input.rules)
    return error or 'OK'
end

local function GetIPv6FirewallRules(ctx)
    local firewall = require('firewall')
    local platform = require('platform')

    local sc = ctx:sysctx()
    local rules = firewall.getIPv6FirewallRules(sc)
    return 'OK', {
        rules = rules,
        maxPortRanges = platform.MAX_IPV6FIREWALL_RULE_PORT_RANGES,
        maxDescriptionLength = platform.MAX_IPV6FIREWALL_RULE_DESCRIPTION_LENGTH,
        maxRules = platform.MAX_IPV6FIREWALL_RULES
    }
end

local function SetIPv6FirewallRules(ctx, input)
    local firewall = require('firewall')

    local sc = ctx:sysctx()
    local error = firewall.setIPv6FirewallRules(sc, input.rules)
    return error or 'OK'
end

return require('libhdklua').loadmodule('jnap_firewall'), {
    ['http://linksys.com/jnap/firewall/GetFirewallSettings'] = GetFirewallSettings,
    ['http://linksys.com/jnap/firewall/SetFirewallSettings'] = SetFirewallSettings,
    ['http://linksys.com/jnap/firewall/GetDMZSettings'] = GetDMZSettings,
    ['http://linksys.com/jnap/firewall/SetDMZSettings'] = SetDMZSettings,
    ['http://linksys.com/jnap/firewall/GetALGSettings'] = GetALGSettings,
    ['http://linksys.com/jnap/firewall/SetALGSettings'] = SetALGSettings,
    ['http://linksys.com/jnap/firewall/GetSinglePortForwardingRules'] = GetSinglePortForwardingRules,
    ['http://linksys.com/jnap/firewall/SetSinglePortForwardingRules'] = SetSinglePortForwardingRules,
    ['http://linksys.com/jnap/firewall/GetPortRangeForwardingRules'] = GetPortRangeForwardingRules,
    ['http://linksys.com/jnap/firewall/SetPortRangeForwardingRules'] = SetPortRangeForwardingRules,
    ['http://linksys.com/jnap/firewall/GetPortRangeTriggeringRules'] = GetPortRangeTriggeringRules,
    ['http://linksys.com/jnap/firewall/SetPortRangeTriggeringRules'] = SetPortRangeTriggeringRules,
    ['http://linksys.com/jnap/firewall/GetIPv6FirewallRules'] = GetIPv6FirewallRules,
    ['http://linksys.com/jnap/firewall/SetIPv6FirewallRules'] = SetIPv6FirewallRules,
}
