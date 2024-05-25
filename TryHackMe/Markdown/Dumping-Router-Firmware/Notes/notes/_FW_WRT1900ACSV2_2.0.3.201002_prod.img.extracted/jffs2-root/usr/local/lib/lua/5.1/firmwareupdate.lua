--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

-- firmwareupdate.lua - library to configure firmware update state.

local platform = require('platform')

local _M = {} -- create the module


--
-- Get the firmware update policy of the local device.
--
-- input = CONTEXT
--
-- output = STRING
--
function _M.getUpdatePolicy(sc)
    sc:readlock()
    local flag = sc:get_firmware_autoupdate_policy()
    local policy
    if flag == 1 then
        policy = 'AutomaticallyCheck'
    elseif flag == 2 then
        policy = 'AutomaticallyCheckAndInstall'
    else
        policy = 'Manual'
    end
    return policy
end

--
-- Set the firmware update policy of the local device.
--
-- input = CONTEXT, STRING
--

function _M.setUpdatePolicy(sc, policy)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local flag
    if policy == 'AutomaticallyCheck' then
        flag = 1
    elseif policy == 'AutomaticallyCheckAndInstall' then
        flag = 2
    else
        flag = 0
    end
    sc:set_firmware_autoupdate_policy(flag)
end

--
-- Get the firmware auto update window of the local device.
--
-- input = CONTEXT
--
-- output = {
--     startMinute = NUMBER,
--     durationMinutes = NUMBER
-- }
--
function _M.getAutoUpdateWindow(sc)
    sc:readlock()
    return {
        -- fwup_periodic_checktime is stored as time_t
        startMinute = math.floor(sc:get_firmware_autoupdate_check_interval() / 60),
        -- fwup_checklimit is stored in seconds (int)
        durationMinutes = math.floor(sc:get_firmware_autoupdate_check_limit() / 60)
    }
end

--
-- Set the firmware auto update window of the local device.
--
-- input = CONTEXT, {
--     startMinute = NUMBER,
--     durationMinutes = NUMBER
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidAutoUpdateWindowStartMinute',
--     'ErrorInvalidAutoUpdateWindowDurationMinutes'
-- )
--
function _M.setAutoUpdateWindow(sc, window)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if window.startMinute < 0 or window.startMinute > 1439 then
        return 'ErrorInvalidAutoUpdateWindowStartMinute'
    end
    if window.durationMinutes < 0 or window.durationMinutes > 1440 then
        return 'ErrorInvalidAutoUpdateWindowDurationMinutes'
    end

    sc:set_firmware_autoupdate_check_interval(window.startMinute * 60)
    sc:set_firmware_autoupdate_check_limit(window.durationMinutes * 60)
end

--
-- Get the last successful firmware update check time.
--
-- input = CONTEXT
--
-- output = NUMBER
--
function _M.getLastSuccessfulCheckTime(sc)
    sc:readlock()
    local timet = sc:get_firmware_last_check_time()
    if not timet or timet == 0 then
        timet = sc:get_firmware_date()
    end
    return timet
end

--
-- Get information about the currently available firmware update, if any.
--
-- input = CONTEXT
--
-- output = OPTIONAL({
--     firmwareVersion = STRING,
--     firmwareDate = NUMBER,
--     description = STRING
-- })
--
function _M.getAvailableUpdate(sc)
    sc:readlock()
    local fwversion = sc:get_new_firmware_version()
    local fwdate = sc:get_new_firmware_date()
    if fwversion and #fwversion > 0 and fwdate and fwdate ~= 0 then
        local fwupdate = {
            firmwareVersion = fwversion,
            firmwareDate = fwdate,
            description = sc:get_new_firmware_details()
        }
        return fwupdate
    end
end

--
-- Get the status of the pending firmware update operation.
--
-- input = CONTEXT
--
-- output = OPTIONAL({
--     operation = STRING,
--     progressPercent = NUMBER
-- })
--
function _M.getPendingOperationStatus(sc)
    sc:readlock()
    local state = tonumber(sc:get_firmware_status())
    if state then
        local op
        if state == 1 then
            op = 'Checking'
        elseif state == 3 then
            op = 'Downloading'
        elseif state == 4 then
            op = 'Installing'
        elseif state == 5 then
            op = 'Rebooting'
        end
        if op then
            return {
                operation = op,
                progressPercent = sc:get_firmware_progress()
            }
        end
    end
end

--
-- Get the last firmware update operation failure.
--
-- input = CONTEXT
--
-- output = OPTIONAL(STRING)
--
function _M.getLastOperationFailure(sc)
    sc:readlock()
    local details = sc:get_firmware_status_details()
    if details and details:find('ERROR') then
        if details:find('server') then
            return 'CheckFailed'
        elseif details:find('Downloading') then
            return 'DownloadFailed'
        else
            return 'InstallFailed'
        end
    end
end

--
-- Trigger a firmware update or a check for an available update.
--
-- input = CONTEXT, BOOLEAN, OPTIONAL(STRING)
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidUpdateServerURL'
-- )
--
function _M.updateNow(sc, onlyCheck, updateServerURL)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local mode
    if onlyCheck then
        mode = '1'
    else
        mode = '2'
    end
    if updateServerURL then
        local protocol = string.lower(string.sub(updateServerURL, 1, 6))
        if protocol ~= 'https:' then
            protocol = string.sub(protocol, 1, 5)
            if protocol ~= 'http:' then
                return 'ErrorInvalidUpdateServerURL'
            end
        end
        sc:set_firmware_alternate_server_url(updateServerURL)
    else
        sc:set_firmware_alternate_server_url('')
    end
    sc:update_firmware_now(mode)
end


return _M -- return the module
