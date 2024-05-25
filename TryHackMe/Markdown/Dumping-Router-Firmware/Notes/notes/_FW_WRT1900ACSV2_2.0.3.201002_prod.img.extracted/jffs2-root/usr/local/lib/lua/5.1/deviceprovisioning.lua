#!/usr/bin/lua

--
-- 2019 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author: hsulliva $
-- $DateTime: 2020/02/06 14:32:09 $
-- $Id: //depot-irv/lego/branches/wrt_platform_sp1/lego_overlay/proprietary/jnap/luascript/deviceprovisioning.lua#1 $
--

-- deviceprovisioning.lua - script to handle device provisioning events

local platform = require('platform')
local sysctx = require('libsysctxlua')
local SERVICE_NAME = 'deviceprovisioning'

local eventName, eventValue = arg[1], arg[2] or ''
if not eventName then
    return
end

local function log(level, message)
    os.execute(('logger -s -t %s "%s: %s"'):format(SERVICE_NAME, level, message))
end

platform.registerLoggingCallback(log)

log(platform.LOG_INFO, string.format('Received event %s with value "%s".', eventName, eventValue))

local function setAdminPassword(sc)
    local password = ''
    local fwProductType = require('device').getFirmwareProductType()

    -- If the firmware type is production (or unknown), create a random password
    if (not fwProductType) or (fwProductType == 'production') then
        local charSet = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890'
        -- Create the random password string
        math.randomseed(os.time())
        for i = 1, 31 do
            password = password..string.char(charSet:byte(math.random(1, #charSet)))
        end
    else -- Use a fixed password for non-production firmware
        password = 'adminadmin'
    end
    log(platform.LOG_INFO, string.format('setting admin password: %s', password))

    sc:writelock()

    -- Set the admin password and clear the password hint
    local success, message = pcall(platform.setAdminPassword, sc, password)
    if not success then
        platform.logMessage(platform.LOG_ERROR, ('Failed to update admin password with error \'%s\''):format(message))
    end
    sc:set_admin_password_hint(nil)

    if require('util').isNodeUtilModuleAvailable() then
        -- A user did not set this password
        sc:set_node_user_set_admin_password('false')
    end
end

local function provisionDevice(provisionId)
    if provisionId ~= 'NULL' then
        local ownedNet = require('ownednetwork')
        local error
        local sc = sysctx.new()

        local startTime = platform.getCurrentUTCTime()
        local lastReqTime = 0

        -- Try provision requests for a maximum of 2 minutes
        while (((platform.getCurrentUTCTime() - startTime) + lastReqTime) < 115) do
            -- Don't sleep the first time through
            if error then
                os.execute('sleep 5')
            end
            local preTime = platform.getCurrentUTCTime()
            platform.logMessage(platform.LOG_INFO, ('Sending provision request %s'):format(provisionId))
            error = ownedNet.provisionDevice(sc, provisionId)
            lastReqTime = platform.getCurrentUTCTime() - preTime
            if error then
                platform.logMessage(platform.LOG_ERROR, ('Failed to provision device with error %s'):format(error))
                if (error ~= 'ErrorCloudUnavailable') then
                    break   -- Fatal error occurred
                end
            else    -- Cloud provisioning succeeded, so set a random admin password to secure the device
                setAdminPassword(sc)
                break
            end
        end
        -- Provisioning is complete (or failed), so clear the device_provision_id event
        sc:writelock()
        sc:setevent('device_provision_id', '')
        sc:commit()
    end
end

--
-- Add your event handler here
--
-- The handler will be called with the event value (or '') as the only parameter.
--
local handlers = {
    ['device_provision_id'] = provisionDevice
}


local handler = handlers[eventName]
if handler then
    local success, error = pcall(handler, eventValue)
    if success then
        log(platform.LOG_INFO, string.format('Handled event %s.\n', eventName))
    else
        log(platform.LOG_ERROR, string.format('Handler for event %s failed (%s).\n', eventName, error))
    end
else
    log(platform.LOG_ERROR, string.format('No handler found for event %s.\n', eventName))
end
