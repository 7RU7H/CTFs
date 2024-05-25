--
-- 2018 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author: hesia $
-- $DateTime: 2019/07/15 12:33:28 $
-- $Id: //depot-irv/user/hesia/pinnacle_f70_auth_jnap/lego_overlay/proprietary/jnap/lualib/diagnostics.lua#2 $
--

-- diagnostics.lua - library to run diagnostic utilites.

local platform = require('platform')
local util = require('util')
local hdk = require('libhdklua')

local _M = {} -- create the module

_M.unittest = {} -- unit test override data store

_M.SYSINFO_FILE_PATH = '/tmp/sysinfo.txt'
_M.SYSINFO_CMD = 'sysinfo.cgi'
_M.SYSINFO_REQUEST_CMD = '/etc/init.d/run_sysinfo.sh %s %s &'
_M.SYSINFO_TIMEOUT = 120
_M.SYSINFO_CONFIG_FILE="/etc/sysinfo/sysinfo.cfg"

_M.supportedSysinfoSections = nil

--
-- Start a ping test on the local device.
--
-- input = CONTEXT, STRING, NUMBER, OPTIONAL(NUMBER)
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidHost',
--     'ErrorInvalidPacketSizeBytes',
--     'ErrorInvalidPingCount'
-- )
--
function _M.startPing(sc, host, packetSizeBytes, pingCount)
    if #host == 0 then
        return 'ErrorInvalidHost'
    end
    local isIPv4Address = pcall(hdk.ipaddress, host)
    local isIPv6Address = pcall(hdk.ipv6address, host)
    if not isIPv4Address and not isIPv6Address and not util.isValidHostName(host) then
        return 'ErrorInvalidHost'
    end

    assert('number' == type(packetSizeBytes))
    if packetSizeBytes < 32 or packetSizeBytes > 65500 then
        return 'ErrorInvalidPacketSizeBytes'
    end
    assert(nil == pingCount or 'number' == type(pingCount))
    if pingCount and pingCount < 1 then
        return 'ErrorInvalidPingCount'
    end

    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    -- Stop the currently-running test, if any
    _M.stopPing(sc)

    -- Create the script to start the test
    local file = io.open(platform.START_PING_FILEPATH, 'w')
    if file then
        file:write(isIPv6Address and platform.START_PING6_SCRIPT or platform.START_PING_SCRIPT)
        file:close()
        os.execute('chmod a+x '..platform.START_PING_FILEPATH)
    end

    -- Run the script to start the test, and record the test PID in sysevent
    local command = table.concat(
        {
            platform.START_PING_FILEPATH,
            platform.PING_LOG_FILEPATH,
            util.shellEscape(host),
            packetSizeBytes,
            pingCount
        }, ' ')
    file = io.popen(command)
    if file then
        local pid = file:read()
        file:close()
        sc:setevent('ping_test_pid', pid)

        platform.logMessage(platform.LOG_INFO, ('started ping process %d\n'):format(pid))
    end
end

local function clearPingProcessPID(sc)
    sc:writelock()
    sc:setevent('ping_test_pid', '')
end

--
-- Stop a ping test on the local device.
--
-- input = CONTEXT
--
function _M.stopPing(sc)
    local pid = _M.getRunningPingPID(sc)
    if pid then
        -- Use SIGINT to stop the ping process so it will have a chance to dump statistics
        platform.killProcess(pid, 'INT')

        platform.logMessage(platform.LOG_INFO, ('stopped ping process %d\n'):format(pid))
        clearPingProcessPID(sc)
    end
end

--
-- Get whether a ping test is running on the local device.
--
-- input = CONTEXT
--
-- output = OPTIONAL(NUMBER)
--
function _M.getRunningPingPID(sc)
    sc:writelock()
    local pid = sc:getevent('ping_test_pid')
    local runningPID
    if pid and #pid > 0 then
        pid = tonumber(pid)
        if platform.isProcessRunning(pid) then
            runningPID = pid
        else
            clearPingProcessPID(sc)
        end
    end
    return runningPID
end

--
-- Get the ping log.
--
-- input = CONTEXT
--
-- output = STRING
--
function _M.getPingLog(sc)
    return platform.getFileContents(platform.PING_LOG_FILEPATH) or ''
end

--
-- Start a traceroute test on the local device.
--
-- input = CONTEXT, STRING
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidHost'
-- )
--
function _M.startTraceroute(sc, host)
    if #host == 0 or (not pcall(hdk.ipaddress, host) and not pcall(hdk.ipv6address, host) and not util.isValidHostName(host)) then
        return 'ErrorInvalidHost'
    end

    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    -- Stop the currently-running test, if any
    _M.stopTraceroute(sc)

    -- Create the script to start the test
    local file = io.open(platform.START_TRACEROUTE_FILEPATH, 'w')
    if file then
        file:write(platform.START_TRACEROUTE_SCRIPT)
        file:close()
        os.execute('chmod a+x '..platform.START_TRACEROUTE_FILEPATH)
    end

    -- Run the script to start the test, and record the test PID in sysevent
    local command = table.concat(
        {
            platform.START_TRACEROUTE_FILEPATH,
            platform.TRACEROUTE_LOG_FILEPATH,
            util.shellEscape(host)
        }, ' ')
    file = io.popen(command)
    if file then
        local pid = file:read()
        file:close()
        sc:setevent('traceroute_test_pid', pid)

        platform.logMessage(platform.LOG_INFO, ('started traceroute process %d\n'):format(pid))
    end
end

local function clearTracerouteProcessPID(sc)
    sc:writelock()
    sc:setevent('traceroute_test_pid', '')
end

--
-- Stop a traceroute test on the local device.
--
-- input = CONTEXT
--
function _M.stopTraceroute(sc)
    local pid = _M.getRunningTraceroutePID(sc)
    if pid then
        platform.killProcess(pid)

        platform.logMessage(platform.LOG_INFO, ('stopped traceroute process %d\n'):format(pid))
        clearTracerouteProcessPID(sc)
    end
end

--
-- Get whether a traceroute test is running on the local device.
--
-- input = CONTEXT
--
-- output = OPTIONAL(NUMBER)
--
function _M.getRunningTraceroutePID(sc)
    sc:writelock()
    local pid = sc:getevent('traceroute_test_pid')
    local runningPID
    if pid and #pid > 0 then
        pid = tonumber(pid)
        if platform.isProcessRunning(pid) then
            runningPID = pid
        else
            clearTracerouteProcessPID(sc)
        end
    end
    return runningPID
end

--
-- Get the traceroute log.
--
-- input = CONTEXT
--
-- output = STRING
--
function _M.getTracerouteLog(sc)
    return platform.getFileContents(platform.TRACEROUTE_LOG_FILEPATH) or ''
end

--
-- Restore previous firmware image.
--
-- input = CONTEXT
--
function _M.restorePreviousFirmware(sc)
    sc:writelock()
    sc:setevent('jnap_side_effects-restorefirmware')
end

local function runSysinfo(cmd, args, pid)
    local file, line
    local running = true

    -- Save the current working directory
    local cwd = lfs.currentdir()

    -- Append the process Id to the Sysinfo file path to create unique file name
    local sysinfoFile=_M.SYSINFO_FILE_PATH..'.'..pid

    -- Change to the CGI directory and run sysinfo
    lfs.chdir(platform.CGI_DIR)
    local rc = os.execute('./'..cmd..' '..args..' >'..sysinfoFile..' 2>/dev/null &')
    if (rc ~= 0) then
        platform.logMessage(platform.LOG_ERROR, ('Failed executing Sysinfo command (%d)'):format(rc))
        return 'ErrorSysinfoFailed'
    end
    local i = 0
    while running and (i < _M.SYSINFO_TIMEOUT) do
        file = io.popen('ps')
        if file then
            running = false
            for line in file:lines() do
                if line:find(cmd) then
                    running = true
                    break
                end
            end
            file:close()
        end
        if running then os.execute('sleep 1') end
        i = i + 1
    end
    -- If sysinfo is still running, then it's exceeded the timeout
    -- so kill it
    if running then
        os.execute('killall '..cmd)
        platform.logMessage(platform.LOG_WARNING, 'sysinfo timed out')
    end

    -- Change back to the saved working directory
    lfs.chdir(cwd)

    os.execute('sleep 1')
    -- Read the Sysinfo data
    file = io.open(sysinfoFile, 'r')
    if not file then
        platform.logMessage(platform.LOG_ERROR, ('Failed to open Sysinfo data file: %s\n'):format(sysinfoFile))
        return 'ErrorSysinfoFailed'
    end

    local buff = file:read('*a')
    file:close()
    os.remove(sysinfoFile)

    return nil, buff
end

--
-- Return the supported sysinfo sections.
--
-- output = ARRAY_OF(STRING)
--
function _M.getSupportedSysinfoSections()
    if _M.supportedSysinfoSections == nil then
        _M.supportedSysinfoSections = {}
        local file = io.open(_M.SYSINFO_CONFIG_FILE)
        if file then
            for line in file:lines() do
                local secName = line:match('^(%a+):.+')
                if secName then
                    table.insert(_M.supportedSysinfoSections, secName)
                end
            end
            file:close()
        end
    end
    return _M.supportedSysinfoSections
end

--
-- Get the sysinfo diagnostics data
--
-- input = CONTEXT
--
-- output = STRING
--
function _M.getSysinfoData(sc)
    local ownedsc

    -- Create an owned sysctx to read from so that we can rollback
    -- to release the lock before running the sysinfo process
    if _M.unittest.ctx then
        ownedsc = _M.unittest.ctx:sysctx()
    else
        ownedsc = require('libsysctxlua').new()
    end
    ownedsc:readlock()

    local pid = ownedsc:get_process_id()

    -- We're done reading the sysctx context, so release the lock
    ownedsc:rollback()

    local error, sysinfo = runSysinfo(_M.SYSINFO_CMD, '', pid)

    if error then
        return error
    end

    return nil, sysinfo
end

--
-- Send the sysinfo diagnostic data as an email attachment to a list of addresses.
--
-- input = CONTEXT, {
--      addressList = ARRAY(STRING),
--      description = OPTIONAL(STRING)
--  }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorEmptyAddressList',
--     'ErrorInvalidEmailAddress',
--     'ErrorSendFailed'
-- )
--
function _M.sendSysinfoEmail(sc, input)

    if #input.addressList == 0 then
        return 'ErrorEmptyAddressList'
    end
    for i = 1, #input.addressList do
        if not util.isValidEmailAddress(input.addressList[i]) then
            return 'ErrorInvalidEmailAddress'
        end
    end
    if not sc:send_sysinfo_email(input.addressList, input.description) then
        return "ErrorSendFailed"
    end
end

--
-- Get the system diagnostics settings.
--
-- input = CONTEXT
--
-- output = {
--      autoUploadEnabled = BOOLEAN,
--      dataTypes = ARRAY(STRING),
--      sysinfoSettings = {
--          periodicUploadEnabled = BOOLEAN,
--          sections = OPTIONAL(ARRAYOF(STRING)),
--          uploadInterval = STRING,
--      }
-- }
--
function _M.getDiagnosticsSettings(sc)
    sc:readlock()

    return {
        autoUploadEnabled = sc:get_diagnostics_auto_upload_enabled(),
        dataTypes = sc:get_diagnostics_data_types(),
        sysinfoSettings = sc:get_sysinfo_settings()
    }
end

--
-- Set the system diagnostics settings.
--
-- input = CONTEXT, {
--      autoUploadEnabled = BOOLEAN,
--      DataTypes = ARRAYOF(STRING),
--      SysinfoSettings = {
--          periodicUploadEnabled = BOOLEAN,
--          sections = OPTIONAL(ARRAYOF(STRING)),
--          uploadInterval = STRING,
--      }
-- }
--
--
function _M.setDiagnosticsSettings(sc, input)
    sc:writelock()
    local userConsent = sc:get_data_upload_user_consent()
    if input.autoUploadEnabled and not userConsent then
        return 'ErrorNoUserConsent'
    end
    sc:set_diagnostics_auto_upload_enabled(input.autoUploadEnabled)
    if (input.dataTypes) then
        sc:set_diagnostics_data_types(input.dataTypes)
    end
    if input.sysinfoSettings then
        sc:set_sysinfo_settings(input.sysinfoSettings)
    end
end

--
-- Helper function to check if  a section is supported
--
local function isRequestedSectionSupported(requestedSection, supportedSections)
    for i, supportedSection in ipairs(supportedSections) do
        if requestedSection == supportedSection then
            return true
        end
    end
    return false
end

--
-- Request Sysinfo diagnostics data by section.
--
-- input = {
--     sections = ARRAY(STRING)
-- }
--
function _M.requestSysinfoData2(sc, input)
    assert(not sc:isreadlocked() and not sc:iswritelocked(), 'must not hold the sysctx lock when calling requestSysinfoData2')

    local ownedsc
    -- Create an owned sysctx to read from so that we can rollback
    -- to release the lock before running the sysinfo process
    if _M.unittest.ctx then
        ownedsc = _M.unittest.ctx:sysctx()
    else
        ownedsc = require('libsysctxlua').new()
    end
    ownedsc:readlock()

    local existingRequestId = ownedsc:get_sysinfo_process_id()

    -- We're done reading the sysctx context, so release the lock
    ownedsc:rollback()

    if existingRequestId and existingRequestId ~= '' then
        return 'ErrorRequestSysinfoAlreadyRunning'
    end

    local args = nil
    local requestedSections = nil
    local supportedSysinfoSections = _M.getSupportedSysinfoSections()

    if not input.sections then
        requestedSections = supportedSysinfoSections
    else
        requestedSections = input.sections
        -- Validate the requested sections
        for i, requestedSection in ipairs(requestedSections) do
            if not isRequestedSectionSupported(requestedSection, supportedSysinfoSections) then
                return 'ErrorInvalidSection'
            end
        end
    end

    -- Build the arguments out of the requested Sysinfo sections
    for i, requestedSection in ipairs(requestedSections) do
        if not args then
            args = requestedSection
        else
            args = args..','..requestedSection
        end
    end

    -- Run the Sysinfo request
    -- sysinfo_process_id and sysinfo_last_triggered sysevents will be set as part of this execution, therefore
    -- we must be careful with the locks.
    file = io.popen(_M.SYSINFO_REQUEST_CMD:format(args, input.uploadWhenFinished and '1' or '0'))
end

--
-- Get the status of a request for Sysinfo data.
--
-- input = STRING
--
-- output = STRING
--
function _M.getSysinfoRequestStatus2(sc)
    local status = 'Invalid'
    local isRunning = false
    local lastTriggered = nil

    sc:readlock()

    local timestamp = sc:get_sysinfo_last_triggered()
    if timestamp and timestamp ~= '' then
        lastTriggered = hdk.datetime(tonumber(timestamp))
    end

    local pid = sc:get_sysinfo_process_id()
    if pid and pid ~= '' then
        isRunning = true
    end

    local uploadId = sc:get_sysinfo_upload_id()
    local uploadErrorCode, uploadErrorDesc = sc:get_sysinfo_upload_error()

    return {
        lastTriggered = lastTriggered,
        isRunning = isRunning,
        uploadId = (uploadId and uploadId ~= '') and uploadId or nil,
        uploadErrorCode = (uploadErrorCode and uploadErrorCode ~= '') and uploadErrorCode or nil
    }
end

--
-- Get the data generated by a Sysinfo request.
--
-- input = CONTEXT
--
-- output = STRING
--
function _M.getSysinfoData4(sc)
    sc:readlock()
    -- If sysinfo hasn't been requested or if process is still running, return an empty section array
    local timestamp = sc:get_sysinfo_last_triggered()
    local pid = sc:get_sysinfo_process_id()
    if timestamp and timestamp ~= '' and (not pid or pid == '') then
        if lfs.attributes(_M.SYSINFO_FILE_PATH, 'mode') == 'file' then
            local fh = io.open(_M.SYSINFO_FILE_PATH)
            if fh then
                local data = fh:read('*a')
                fh:close()
                return data
            end
        end
    end

    return ''
end

return _M -- return the module
