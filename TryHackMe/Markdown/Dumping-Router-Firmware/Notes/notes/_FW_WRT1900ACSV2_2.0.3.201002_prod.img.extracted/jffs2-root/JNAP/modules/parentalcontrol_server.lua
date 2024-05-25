--
-- 2018 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local parentalcontrol = require('parentalcontrol')

local function GetParentalControlSettings(ctx)
    local sc = ctx:sysctx()
    return 'OK', {
        isParentalControlEnabled = parentalcontrol.getIsEnabled(sc),
        rules = parentalcontrol.getRules(sc),
        maxRuleDescriptionLength = parentalcontrol.getMaxParentalControlRuleDescriptionLength(sc),
        maxRuleMACAddresses = parentalcontrol.getMaxParentalControlRuleMACAddresses(sc),
        maxRuleBlockedURLLength = parentalcontrol.getMaxParentalControlRuleBlockedURLLength(sc),
        maxRuleBlockedURLs = parentalcontrol.getMaxParentalControlRuleBlockedURLs(sc),
        maxRules = parentalcontrol.getMaxParentalControlRules(sc)
    }
end

local function SetParentalControlSettings(ctx, input)
    local sc = ctx:sysctx()
    local error =
        parentalcontrol.setIsEnabled(sc, input.isParentalControlEnabled) or
        parentalcontrol.setRules(sc, input.rules, false)
    return error or 'OK'
end

return require('libhdklua').loadmodule('jnap_parentalcontrol'), {
    ['http://linksys.com/jnap/parentalcontrol/GetParentalControlSettings'] = GetParentalControlSettings,
    ['http://linksys.com/jnap/parentalcontrol/SetParentalControlSettings'] = SetParentalControlSettings,
}
