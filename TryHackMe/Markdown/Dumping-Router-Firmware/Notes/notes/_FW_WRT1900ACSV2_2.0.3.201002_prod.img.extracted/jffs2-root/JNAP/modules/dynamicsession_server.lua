--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local function StartDynamicSession(ctx, input)
    local sc = ctx:sysctx()

    local dynamicSession = require('dynamicsession')

    local requestedTimeout = input.requestedSessionTimeoutSeconds
    local error = dynamicSession.validateDynamicSession(sc, requestedTimeout)
    if error ~= 'OK' then
        return error
    end

    local sessionUUID = dynamicSession.startDynamicSession(sc, requestedTimeout)
    return 'OK', {
        sessionUUID = sessionUUID,
        remainingSessionTimeoutSeconds = requestedTimeout
    }
end

local function GetDynamicSessions(ctx)
    local dynamicSession = require('dynamicsession')
    local platform = require('platform')

    local sc = ctx:sysctx()
    local sessionUUIDs = dynamicSession.getDynamicSessions(sc)
    local info = dynamicSession.getDynamicSessionGeneralInfo(sc)
    return 'OK', {
        sessionUUIDs = sessionUUIDs,
        maxDynamicSessions = info.maxDynamicSessions,
        maxDynamicSessionTimeoutSeconds = info.maxDynamicSessionTimeoutSeconds
    }
end


local function ResetDynamicSession(ctx, input)
    local dynamicSession = require('dynamicsession')
    local platform = require('platform')

    local sc = ctx:sysctx()
    local timeout = input.requestedDynamicSessionTimeoutSeconds
    local sessionUUID = input.sessionUUID
    local error = dynamicSession.resetDynamicSession(sc, sessionUUID, timeout)
    if error ~= 'OK' then
        return error
    end
    return 'OK', { remainingDynamicSessionTimeoutSeconds = timeout }
end


local function GetDynamicSessionInfo(ctx, input)
    local dynamicSession = require('dynamicsession')

    local sc = ctx:sysctx()
    local sessionUUID = input.sessionUUID

    local sessionInfo = dynamicSession.getDynamicSessionInfo(sc, sessionUUID)
    if sessionInfo == "ErrorDynamicSessionDoesNotExist" then
        return sessionInfo
    end
    return 'OK', { sessionInfo = sessionInfo }
end


local function StopDynamicSession(ctx, input)
    local dynamicSession = require('dynamicsession')

    local sc = ctx:sysctx()

    local error = dynamicSession.stopDynamicSession(sc, input.sessionUUID)

    return error or 'OK'
end

return require('libhdklua').loadmodule('jnap_dynamicsession'), {
    ['http://linksys.com/jnap/dynamicsession/StartDynamicSession'] = StartDynamicSession,
    ['http://linksys.com/jnap/dynamicsession/GetDynamicSessions'] = GetDynamicSessions,
    ['http://linksys.com/jnap/dynamicsession/ResetDynamicSession'] = ResetDynamicSession,
    ['http://linksys.com/jnap/dynamicsession/GetDynamicSessionInfo'] = GetDynamicSessionInfo,
    ['http://linksys.com/jnap/dynamicsession/StopDynamicSession'] = StopDynamicSession,
}
