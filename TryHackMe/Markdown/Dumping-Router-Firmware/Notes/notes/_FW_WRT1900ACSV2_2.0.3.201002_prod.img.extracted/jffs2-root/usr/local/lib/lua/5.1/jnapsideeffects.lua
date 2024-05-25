#!/usr/bin/lua

--
-- 2016 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

-- jnapsideeffects.lua - script to execute side effects of JNAP actions that
-- need to happen after the JNAP transaction is complete and the sysctx lock
-- is no longer held by the JNAP CGI

local util = require('util')
local platform = require('platform')
local sysctx = require('libsysctxlua')

local eventName, eventValue = arg[1], arg[2] or ''
if not eventName then
    return
end

local logFile
local logPath = os.getenv('JNAP_SIDE_EFFECTS_LOG_PATH')
if logPath then
    logFile = io.open(logPath, 'a+')
end

local function log(level, message)
    if logFile then
        logFile:write(level..': '..message)
        logFile:write('\n')
    else
        io.stderr:write(level..': '..message..'\n')
    end
end

platform.registerLoggingCallback(log)

log(platform.LOG_INFO, string.format('Received event %s with value "%s".', eventName, eventValue))

local function setAdminPassword(password)
    local sc = sysctx.new()

    password = util.unwrap(password)

    sc:writelock()

    sc:setevent('jnap_side_effects-setpassword-status', 'changing...')

    local success, message = pcall(platform.setAdminPassword, sc, password)
    if not success then
        platform.logMessage(platform.LOG_ERROR, ('Failed to update admin password with error "%s"\n'):format(message))
    end

    sc:setevent('jnap_side_effects-setpassword-status', 'done')
    sc:commit()
end

local function restorePreviousFirmware()
    local sc = sysctx.new()

    sc:writelock()

    local success, message = pcall(platform.restorePreviousFirmware)
    if not success then
        platform.logMessage(platform.LOG_ERROR, ('Failed to restore previous firmware with error "%s"\n'):format(message))
    end
    sc:commit()
end

--
-- Add your event handler here, and in
-- lego_overlay/proprietary/init/c_registration/xx_jnap_side_effects.c
--
-- The handler will be called with the event value (or '') as the only parameter.
--
local handlers = {
    ['jnap_side_effects-setpassword'] = setAdminPassword,
    ['jnap_side_effects-restorefirmware'] = restorePreviousFirmware
}


local handler = handlers[eventName]
if handler then
    local success, error = pcall(handler, eventValue)
    if success then
        log(platform.LOG_INFO, string.format('Handled event %s.', eventName))
    else
        log(platform.LOG_ERROR, string.format('Handler for event %s failed (%s).', eventName, error))
    end
else
    log(platform.LOG_ERROR, string.format('No handler found for event %s.', eventName))
end

if logFile then
    logFile:close()
end
