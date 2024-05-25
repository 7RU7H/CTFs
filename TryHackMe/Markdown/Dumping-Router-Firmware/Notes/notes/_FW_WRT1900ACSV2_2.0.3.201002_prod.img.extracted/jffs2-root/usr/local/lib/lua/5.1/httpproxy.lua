--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

-- httpproxy.lua - library to create and manage http proxy rules.

local hdk = require('libhdklua')
local platform = require('platform')
local util = require('util')

local _M = {} -- create the module

_M.HTTPPROXY_POOL_NAME = 'httpproxy_rule'
_M.HTTPPROXY_RULE_SESSION_POOL_NAME = 'httpproxy_rule_session'
_M.HTTPPROXY_RULE_COUNT = 'httpproxy_rule_count'
_M.HTTPPROXY_RULE_TYPE = 'HttpProxyRule'
_M.DYNAMIC_SESSION_POOL_NAME = 'dyn_session_info'
_M.HTTPPROXY_RULE_VERSION = 1
_M.HTTPPROXY_ACL_VERSION = 1

---------------------------------------------------------
----------------- helper functions ----------------------
---------------------------------------------------------

-- escape CVS(Comma-Separated Values) string. For details, please
-- refer to rfc4180
local function escapeCSV(str)
    if string.find(str, '[,"]') then
        str = '"'..string.gsub(str, '"', '""')..'"'
    end
    return str
end

local function parseStrSeperatedByComma(str)
    local tbl = {}
    for word in string.gmatch(str, '([^,]*)') do
        if word ~= '' then
            table.insert(tbl, word)
        end
    end
    return tbl
end

local function  checkSessionExist(sc, testSessionUUID)
    local session_info = sc:getuniqueevents(_M.DYNAMIC_SESSION_POOL_NAME)
    if session_info then
        for k,v in pairs(session_info) do
            local sessionUUID = string.match(v, '([^,]*)')
            if sessionUUID ~= '' and sessionUUID == testSessionUUID then
                return true
            end
        end
    end
    return false
end

local function isValidACLType (aclType)
    if (aclType == 'Allow' or aclType =='Deny') then
        return true
    end
    return false
end

---------------------------------------------------------
---------------- libary functions -----------------------
---------------------------------------------------------
--
-- validate a http proxy rule
--
-- input = CONTEXT,rule
--
--
function _M.validateHttpProxyRule(sc, rule)
    sc:writelock()
   if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    local count = sc:getevent(_M.HTTPPROXY_RULE_COUNT)
    if count and count ~= '' then
        count = tonumber(count)
    else
        count = tonumber('0')
    end
    local maxCount = sc:get_max_http_proxy_rules()
    if count >= maxCount then
        return "ErrorTooManyHttpProxyRules"
    end

    if rule.sessionUUID ~= nil then
        if checkSessionExist(sc, tostring(rule.sessionUUID)) == false then
            return "ErrorSessionDoesNotExist"
        end
    end

    local maxRulesPerAcl = sc:get_max_acls_per_http_proxy_rule()
    if (#rule.acls) > maxRulesPerAcl then
        return "ErrorTooManyAclsPerHttpProxyRule"
    end

    for i, v in ipairs(rule.acls) do
        if v.aclType and not isValidACLType(v.aclType) then
            return  "ErrorInvalidACLType"
        end
        if v.ipv4PrefixLength and
           (v.ipv4PrefixLength < 0 or v.ipv4PrefixLength > 32) then
            return "ErrorInvalidIpv4PrefixLength"
        end
        if v.ipv6PrefixLength and
           (v.ipv6PrefixLength < 0 or v.ipv6PrefixLength > 64) then
            return "ErrorInvalidIpv6PrefixLength"
        end
        if v.destPort and not util.isValidPort(v.destPort) then
            return 'ErrorInvalidDestPort'
        end
    end

    return 'OK'
end
--
-- Add a http proxy rule
--
-- input = CONTEXT,rule
--
-- output = ruleUUID
--
function _M.addHttpProxyRule(sc, rule)
    sc:writelock()

    local sessionUUID = tostring(rule.sessionUUID)

    local ruleStr = ''

    -- concat headerToken
    local ruleStr = ruleStr.._M.HTTPPROXY_RULE_VERSION

    -- concat headerToken
    if rule.headerToken and rule.headerToken ~= '' then
        local headerToken = escapeCSV(rule.headerToken)
        ruleStr = ruleStr..','..headerToken
    else
        ruleStr = ruleStr..','
    end

    -- concat passPhrase
    if rule.passPhrase  and rule.passPhrase ~= '' then
        local passPhrase = escapeCSV(rule.passPhrase)
        ruleStr = ruleStr..','..passPhrase
    else
        ruleStr = ruleStr..','
    end

    -- concat useSSL
    if rule.useSSL == true then
        ruleStr = ruleStr..','..tostring(1)
    else
        ruleStr = ruleStr..','..tostring(0)
    end

    -- concat acl rules, whose data member is sperate by space
    local acls = rule.acls
    local aclStr = ',"'
    for i, v in ipairs(acls) do
        aclStr = aclStr..tostring(_M.HTTPPROXY_ACL_VERSION)..','
        if (v.aclType == 'Allow') then
            aclStr = aclStr..'A'
        else
            aclStr = aclStr..'D'
        end
        if v.ipv4Address and tostring(v.ipv4Address) ~= '' then
            aclStr = aclStr..','..tostring(v.ipv4Address)
        else
            aclStr = aclStr..','
        end
        if v.ipv4PrefixLength and tostring(v.ipv4PrefixLength) ~= '' then
            aclStr = aclStr..','..tostring(v.ipv4PrefixLength)
        else
            aclStr = aclStr..','
        end
        if v.ipv6Address and tostring(v.ipv6Address) ~= '' then
            aclStr = aclStr..','..tostring(v.ipv6Address)
        else
            aclStr = aclStr..','
        end
        if v.ipv6PrefixLength and tostring(v.ipv6PrefixLength) ~= '' then
            aclStr = aclStr..','..tostring(v.ipv6PrefixLength)
        else
            aclStr = aclStr..','
        end
        if v.destPort and tostring(v.destPort) ~= '' then
            aclStr = aclStr..','..tostring(v.destPort)
        else
            aclStr = aclStr..''
        end
        aclStr = aclStr..";"
    end

    -- concat ACLs
    ruleStr = ruleStr..aclStr..'"'

    -- concat sessionUUID
    ruleStr = ruleStr..','..sessionUUID

    -- add rule to 'httpproxy_rule' pool
    local ruleUUID = platform.getUUID()
    local uniqueId = sc:setuniqueevent(_M.HTTPPROXY_POOL_NAME, ruleStr)
    sc:setevent(ruleUUID, uniqueId)

    -- add 'ruleUUID,HttpProxyRule' to 'sessionUUID' pool
    local ruleInfo = ruleUUID..','.._M.HTTPPROXY_RULE_TYPE
    sc:setuniqueevent(sessionUUID, ruleInfo)

    -- add 'ruleUUID,sessionUUID'  to 'httpproxy_rule_session' pool
    local ruleSession = ruleUUID..','..sessionUUID
    sc:setuniqueevent(_M.HTTPPROXY_RULE_SESSION_POOL_NAME, ruleSession)

    -- update count
    local count = sc:getevent(_M.HTTPPROXY_RULE_COUNT)
    if count and count ~= '' then
        count = tonumber(count)
    else
        count = tonumber('0')
    end

    sc:setevent(_M.HTTPPROXY_RULE_COUNT, (count + 1))

    return hdk.uuid(ruleUUID)
end

--
-- Get http proxy related parameters
--
-- input = CONTEXT, ruleUUID
--
--
function _M.getHttpProxyParameters(sc)
    sc:readlock()

    local parameters = {}

    parameters.httpPort = sc:get_http_proxy_port()
    parameters.httpsPort = sc:get_https_proxy_port()
    parameters.maxHttpProxyRules = sc:get_max_http_proxy_rules()
    parameters.maxACLRulesPerHttpProxyRule= sc:get_max_acls_per_http_proxy_rule()

    return parameters
end

--
-- Remove a http proxy rule
--
-- input = CONTEXT, ruleUUID
--
--
function _M.removeHttpProxyRule(sc, ruleUUID)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    local removeRuleUUID = tostring(ruleUUID)

    -- remove rule from 'httpproxy_rule' pool
    local uniqueId = sc:getevent(removeRuleUUID)
    if uniqueId and uniqueId ~= '' then
        sc:setevent(uniqueId)
        sc:setevent(removeRuleUUID)
    end

    local found = false
    local sessionUUID = ''

    -- remove 'ruleUUID,sessionUUID' pair from 'httpproxy_rule_session' pool
    local ruleSession = sc:getuniqueevents(_M.HTTPPROXY_RULE_SESSION_POOL_NAME)
    if ruleSession then
        for k,v in pairs(ruleSession) do
            local ruleSessionPair = parseStrSeperatedByComma(v)
            if removeRuleUUID == ruleSessionPair[1] then
                found = true
                -- remove rule from "httpproxy_rule_session" pool
                sessionUUID = ruleSessionPair[2]
                sc:setevent(k)
                break
            end
        end
    end

    if found ~= true then
        return "ErrorHttpProxyRuleDoesNotExist"
    end

    -- remove the 'ruleUUID,ruleType' from 'sessionUUID' pool
    if seesionUUID ~= '' then
        local ruleInfo = sc:getuniqueevents(sessionUUID)
        for key,value in pairs(ruleInfo) do
            local pair = parseStrSeperatedByComma(value)
            if pair[1] == removeRuleUUID then
                sc:setevent(key)
                break
            end
        end
    end

    -- update count
    local count = sc:getevent(_M.HTTPPROXY_RULE_COUNT)
    if count and count ~= '' then
        count = tonumber(count)
    else
        count = tonumber('0')
    end

    if count > 0 then
        sc:setevent(_M.HTTPPROXY_RULE_COUNT, (count - 1))
    end
end

return _M -- return the module
