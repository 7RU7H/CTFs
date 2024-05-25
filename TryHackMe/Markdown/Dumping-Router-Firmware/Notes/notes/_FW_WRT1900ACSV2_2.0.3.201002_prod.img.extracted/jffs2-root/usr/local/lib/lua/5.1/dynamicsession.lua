--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

-- dynamicsession.lua - library to create and manage dynamic sessions.

local hdk = require('libhdklua')
local util = require('util')
local platform = require('platform')
local dynamicportforwarding = require('dynamicportforwarding')
local httpproxy = require('httpproxy')

local _M = {} -- create the module

_M.DYNAMIC_SESSION_POOL_NAME = 'dyn_session_info'
_M.DYNAMIC_SESSION_COUNT = 'dyn_session_count'
_M.DYNAMIC_SESSION_POOL_FORMAT = '(%x+-%x+-%x+-%x+-%x+),(%d+),(%d+)'

---------------------------------------------------------
----------------- helper functions ----------------------
---------------------------------------------------------

local function parseStrSeperatedByComma(str)
    local tbl = {}
    for word in string.gmatch(str, '([^,]*)') do
        if word ~= '' then
            table.insert(tbl, word)
        end
    end
    return tbl
end

---------------------------------------------------------
---------------- libary functions -----------------------
---------------------------------------------------------
--
-- Validate a dynamic session input
--
-- input = CONTEXT, requestedSessionTimeoutSeconds = NUMBER
--
--
function _M.validateDynamicSession(sc, requestedTimeout)
    sc:writelock()

    local timeout = requestedTimeout
    local maxTimeout = sc:getinteger('ra_maxsessiontimeout', 0)
    if timeout > maxTimeout then
        return "ErrorRequestedTimeOutTooLong"
    end

    local count = sc:getevent(_M.DYNAMIC_SESSION_COUNT)
    if count and count ~= '' then
        count = tonumber(count)
    else
        count = tonumber('0')
    end
    local maxCount = sc:getinteger('ra_maxsessions', 0)
    if count >= maxCount then
        return "ErrorTooManyDynamicSessions"
    end
    return 'OK'
end

--
-- Start a dynamic session
--
-- input = CONTEXT, requestedSessionTimeoutSeconds = NUMBER
--
-- output = {
--     sessionUUID = UUID,
-- }
--
function _M.startDynamicSession(sc, requestedSessionTimeoutSeconds)
    sc:writelock()

    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    local count = sc:getevent(_M.DYNAMIC_SESSION_COUNT)
    if count and count ~= '' then
        count = tonumber(count)
    else
        count = tonumber('0')
    end

    local sessionUUID = platform.getUUID()
    local currEpochTime = platform.getEpochTime()
    local sessionStr = sessionUUID..','..tostring(requestedSessionTimeoutSeconds)..','..tostring(currEpochTime)

    local uniqueId = sc:setuniqueevent(_M.DYNAMIC_SESSION_POOL_NAME, sessionStr)
    sc:setevent(_M.DYNAMIC_SESSION_COUNT, (count + 1))
    sc:setevent(sessionUUID, uniqueId)

    return hdk.uuid(sessionUUID)
end


--
-- Get dynamic sessions
--
-- input = CONTEXT
--
-- output = {
--     sessionUUIDs = ARRAY_OF(UUID),
-- }
--
function _M.getDynamicSessions(sc)
    sc:readlock()

    local sessionUUIDs = {}

    local session_info = sc:getuniqueevents(_M.DYNAMIC_SESSION_POOL_NAME)
    if session_info then
        for k,v in pairs(session_info) do
            local sessionUUID = string.match(v, '([^,]*)')
            if sessionUUID ~= '' then
                table.insert(sessionUUIDs, hdk.uuid(sessionUUID))
            end
        end
    end

    return sessionUUIDs
end


--
-- Get a dynamic session's info
--
-- input = CONTEXT, sessionUUID
--
-- }
--
function _M.getDynamicSessionInfo(sc, sessionUUID)
    sc:readlock()

    local sessionInfo = {}
    local found = false
    local sessionUUID = tostring(sessionUUID)
    local sessions = sc:getuniqueevents(_M.DYNAMIC_SESSION_POOL_NAME)
    if sessions and sessions ~= ''then
        for k,v in pairs(sessions) do
            local rules = {}
            local tmpSessionInfo = parseStrSeperatedByComma(v)
            if #tmpSessionInfo == 3 then
                local tmpSessionUUID = tmpSessionInfo[1]
                local tmpSessionTimeout = tonumber(tmpSessionInfo[2])
                local tmpSessionLastEpochTime = tonumber(tmpSessionInfo[3])
                if tmpSessionUUID == sessionUUID then
                    local currEpochTime = platform.getEpochTime()
                    sessionInfo.sessionUUID = hdk.uuid(tmpSessionUUID)
                    sessionInfo.remainingSessionTimeoutSeconds =
                        tmpSessionTimeout - (currEpochTime - tmpSessionLastEpochTime)
                    sessionInfo.rules = rules
                    found = true
                end
            end
            -- get ruleInfo for the session
            if found == true then
                local ruleInfo = sc:getuniqueevents(sessionUUID)
                if ruleInfo and ruleInfo ~= '' then
                    for key, value in pairs (ruleInfo) do
                        local tmpRuleInfo = parseStrSeperatedByComma(value)
                        if tmpRuleInfo and #tmpRuleInfo == 2 then
                            local rule = {}
                            rule.ruleUUID = hdk.uuid(tmpRuleInfo[1])
                            rule.ruleType = tmpRuleInfo[2]
                            table.insert(rules,rule)
                        end -- end of if tmpRuleInfo
                    end -- end of key, value
                end -- end of ruleInfo
            end -- end of if found == true
            sessionInfo.rules = rules
        end -- end of for k, v in pairs (sessions)
    end -- end of if sessions

    if found ~= true then
        return "ErrorDynamicSessionDoesNotExist"
    end

    return sessionInfo
end


--
-- Reset a dynamic session
--
-- input = CONTEXT, requestedSessionTimeoutSeconds = NUMBER
-- output = remainintdynamicSessiontimeoutSeconds
--
function _M.resetDynamicSession(sc, sessionUUID, timeout)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    local requestedTimeout = timeout
    local maxTimeout = sc:getinteger('ra_maxsessiontimeout', 0)
    if requestedTimeout > maxTimeout then
        return "ErrorRequestedTimeOutTooLong"
    end

    local sessionUUID = tostring(sessionUUID)
    local uniqueId = sc:getevent(sessionUUID)

    if uniqueId == nil or uniqueId == '' then
        return "ErrorDynamicSessionDoesNotExist"
    end

    local currEpochTime = platform.getEpochTime()
    local sessionStr = sessionUUID..','..tostring(requestedTimeout)..','..tostring(currEpochTime)

    sc:setevent(uniqueId, sessionStr)
    return "OK"
end

--
-- Stop a dynamic session
--
-- input = CONTEXT, sessionUUID
--
--
function _M.stopDynamicSession(sc, sessionUUID)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    local stopSessionUUID = tostring(sessionUUID)
    local uniqueId = sc:getevent(stopSessionUUID)

    if uniqueId == nil or uniqueId == '' then
        return "ErrorDynamicSessionDoesNotExist"
    end

    -- sessionUUID pool which has one or more "ruleUUID,ruleType"
    local rules = sc:getuniqueevents(stopSessionUUID)
    if rules then
        for key,value in pairs (rules) do
            local ruleInfo = parseStrSeperatedByComma(value)
            if #ruleInfo == 2 then
                local ruleUUID = ruleInfo[1]
                local ruleType = ruleInfo[2]

                if ruleType ==  "DynamicSinglePortForwardingRule" then
                    dynamicportforwarding.removeDynamicSinglePortForwardingRule(sc, hdk.uuid(ruleUUID))
                elseif ruleType == "DynamicPortRangeForwardingRule" then
                    dynamicportforwarding.removeDynamicPortRangeForwardingRule(sc, hdk.uuid(ruleUUID))
                elseif ruleType == "DynamicIPv6ConnectionRule" then
                    dynamicportforwarding.removeDynamicIPv6ConnectionRule(sc, hdk.uuid(ruleUUID))
                elseif ruleType == "HttpProxyRule" then
                    httpproxy.removeHttpProxyRule(sc, hdk.uuid(ruleUUID))
                end -- end of if ruleType
            end
        end
    end

    sc:setevent(uniqueId)
    sc:setevent(stopSessionUUID)

    local count = sc:getevent(_M.DYNAMIC_SESSION_COUNT)
    if count and count ~= '' then
        count = tonumber(count)
    else
        count = tonumber('0')
    end

    if count > 0 then
        sc:setevent(_M.DYNAMIC_SESSION_COUNT, (count - 1))
    end

    return 'OK'
end

--
-- expire dynamic sessions
--
-- input = CONTEXT
--
-- output = {
--     removedSessionUUIDs = ARRAY_OF(UUID),
-- }
--
function _M.expireDynamicSessions(sc)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    local removedSessionUUIDs = {}
    local currEpochTime

    local format = _M.DYNAMIC_SESSION_POOL_FORMAT
    local session_pool = sc:getuniqueevents(_M.DYNAMIC_SESSION_POOL_NAME)
    if session_pool then
        currEpochTime = platform.getEpochTime()
        for k,v in pairs(session_pool) do
            for sessionUUID, timeoutValue, lastEpochTime in string.gmatch(v, format) do
                if (currEpochTime - tonumber(lastEpochTime)) >= tonumber(timeoutValue) then
                    table.insert(removedSessionUUIDs, sessionUUID)
                    _M.stopDynamicSession(sc, hdk.uuid(sessionUUID))
                end
            end -- end of for sessionUUID, timeoutvalue...
        end -- end of for k,v...
    end

    return removedSessionUUIDs
end

--
-- Get general information (max number of dynamic sessions allowed and max
-- session time out seconds)
--
-- input = CONTEXT
--
-- output = {
--     maxDynamicSessions = NUMBER,
--     maxDynamicSessionTimeoutSeconds = NUMBER
-- }
--
function _M.getDynamicSessionGeneralInfo(sc)
    sc:readlock()

    local generalInfo = {}
    generalInfo.maxDynamicSessions = sc:getinteger('ra_maxsessions', 0)
    generalInfo.maxDynamicSessionTimeoutSeconds = sc:getinteger('ra_maxsessiontimeout', 0)

    return generalInfo
end

return _M -- return the module
