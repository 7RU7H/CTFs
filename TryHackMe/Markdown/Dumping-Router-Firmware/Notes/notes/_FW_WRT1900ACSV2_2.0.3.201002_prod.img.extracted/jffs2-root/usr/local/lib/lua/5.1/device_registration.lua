#!/usr/bin/lua

--
-- 2017 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

-- device_registration.lua - script to handle registration of the device with the cloud

local platform = require('platform')
local device = require('device')
local cloud = require('cloud')
local sysctx = require('libsysctxlua')

local SERVICE_NAME = 'device_registration'
local RETRY_INTERVAL = 10

local function usage()
    io.stderr:write(('Usage: %s <register|renew>\n'):format(arg[0]))
end

local action, param = arg[1], arg[2]
if not action then
    io.stderr:write(SERVICE_NAME..': ERROR missing parameter\n')
    usage()
    os.exit(3)
end

local logFile
local logPath = os.getenv('DEVICE_REGISTRATION_LOG_PATH')
if logPath then
    logFile = io.open(logPath, 'a+')
end

local function log(level, message)
    if logFile then
        logFile:write(SERVICE_NAME..'.'..level..': '..message..'\n')
    else
        os.execute(('logger -s -t %s "%s: %s"'):format(SERVICE_NAME, level, message))
    end
end

platform.registerLoggingCallback(log)

local function getToken(sc, type)
    sc:readlock()

    local cloudHost = device.getCloudHost(sc)
    local serialNumber = device.getSerialNumber(sc)
    local macAddress = device.getMACAddress(sc)
    local token, modelNumber, hwVersion, fwVersion, uuid, mfgDate
    if (type == 'register') then
        modelNumber = sc:get_modelnumber()
        hwVersion = device.getHardwareRevision(sc)
        fwVersion = device.getFirmwareVersion(sc)
        uuid = device.getUUID(sc)
        mfgDate = device.getManufactureDate(sc)
    else
        token = device.getLinksysToken(sc)
    end

    -- Roll back the ctx context before making the cloud call
    sc:rollback()

    local error
    if (type == 'register') then
        error, token = cloud.registerDevice(cloudHost, serialNumber, macAddress, modelNumber, hwVersion, fwVersion, uuid, mfgDate, true)
    else
        error, token = cloud.getNewToken(cloudHost, serialNumber, macAddress, token, true)
    end
    if error then
        platform.logMessage(platform.LOG_ERROR, ('Failed to get token with error %s'):format(error))
    else
        -- Save the token
        sc:writelock()
        sc:set_linksys_token(token, type)
    end

    return error
end

local function registerDevice(sc)

    local isCriticalError = function(error)
        return (error ~= 'ErrorCloudUnavailable')
    end

    -- Try 5 quick registration attempts
    local error
    for i = 1, 5 do
        error = getToken(sc, 'register')
        if error and not isCriticalError(error) then
            os.execute('sleep 5')
        else
            break
        end
    end

    -- If not successfull, then continue trying at a regular interval
    while error and not isCriticalError(error) do
        error = getToken(sc, 'register')
        if error then
            os.execute('sleep '..RETRY_INTERVAL)
        end
    end

    return error
end

-----------------------------------------------------
-- Main
-----------------------------------------------------

local error
local sc = sysctx.new()

if (action == 'register') then
    error = registerDevice(sc)
elseif (action == 'renew') then
    error = getToken(sc, action)
else
    platform.logMessage(platform.LOG_ERROR, ('Invalid parameter "%s"'):format(action))
    usage()
end

sc:setevent(SERVICE_NAME..'-errinfo', error)

if logFile then
    logFile:close()
end

sc:commit()

os.exit(error and 3 or 0)
