--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local function AddHttpProxyRule(ctx, input)
    local sc = ctx:sysctx()

    local httpProxy = require('httpproxy')

    local error = httpProxy.validateHttpProxyRule(sc, input)

    if error and error ~= 'OK' then
        return error
    end

    local ruleUUID = httpProxy.addHttpProxyRule(sc, input)
    return 'OK', {
        ruleUUID = ruleUUID
    }
end


local function GetHttpProxyParameters(ctx)
    local httpProxy = require('httpproxy')

    local sc = ctx:sysctx()
    local parameters  = httpProxy.getHttpProxyParameters(sc)
    return 'OK', {
        httpPort = parameters.httpPort,
        httpsPort = parameters.httpsPort,
        maxHttpProxyRules = parameters.maxHttpProxyRules,
        maxACLRulesPerHttpProxyRule = parameters.maxACLRulesPerHttpProxyRule
    }
end


local function RemoveHttpProxyRule(ctx, input)
    local httpProxy = require('httpproxy')

    local sc = ctx:sysctx()

    local error = httpProxy.removeHttpProxyRule(sc, input.ruleUUID)

    return error or 'OK'
end

return require('libhdklua').loadmodule('jnap_httpproxy'), {
    ['http://linksys.com/jnap/httpproxy/AddHttpProxyRule'] = AddHttpProxyRule,
    ['http://linksys.com/jnap/httpproxy/GetHttpProxyParameters'] = GetHttpProxyParameters,
    ['http://linksys.com/jnap/httpproxy/RemoveHttpProxyRule'] = RemoveHttpProxyRule,
}
