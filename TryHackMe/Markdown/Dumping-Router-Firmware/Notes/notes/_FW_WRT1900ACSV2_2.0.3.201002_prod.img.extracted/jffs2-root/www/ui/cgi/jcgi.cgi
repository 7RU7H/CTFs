#!/usr/bin/lua

--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

-- jcgi.lua - CGI script to handle multipart form POST requests such as
-- updatefirmware, restoreconfiguration, backupconfiguration, and downloadvpnprofile
--
-- Assumes it is being called by lighttpd as a CGI.

local sysctx = require('libsysctxlua')


-- Constants
local cfgVersion     = '0x0002'
local ipaCachePath   = '/var/config/ipa'
local tmpSyscfgPath  = '/tmp/syscfg.tmp'
local tmpFilePath    = os.getenv('JCGI_DEBUG') and 'jcgi.tmp' or '/tmp/jcgi.tmp'

local backupPaths   = {ipaCachePath, tmpSyscfgPath}
local responseFilename


-- Helper method to send the response
local function printResponse(message, content)
    local responseTemplate = [[
{
"result": "%s"
}]]

    local contentType = content and 'application/octet-stream' or 'text/plain'
    local response = content or string.format(responseTemplate, message)
    io.write('Status: 200 OK\r\n')
    io.write('Content-Type: ' .. contentType .. '\r\n')
    if content then
        io.write('Content-Disposition: attachment; filename='..responseFilename..'\r\n')
    end
    io.write('Connection: close\r\n')
    io.write(string.format('X-JCGI-Result: %s\r\n', message))
    io.write(string.format('Content-Length: %d\r\n\r\n', #response));
    io.write(response)
    io.flush()
end

--
-- Exception throwing functions
--

local error = 'Error'
local function fail(e)
    error = e
    assert(false)
end

local function verify(condition, e)
    if not condition then
        fail(e)
    end
end

--
-- This function filters out device-specific settings from the syscfg data buffer
--
local function filterSyscfg(buf)
    local newBuf = ''
    for cfgStr in string.gmatch(buf, '([^%z]+)') do
        local cfgName = string.sub(cfgStr, 1, string.find(cfgStr, '=', 1, true) - 1)
        -- Filter out MAC addresses
        if not string.find(cfgName, '^wl%d.*_mac_addr') and
           not string.find(cfgName, '^[wl]an_mac_addr') and
           not string.find(cfgName, '^device::.+') then
            newBuf = newBuf..cfgStr..'\0'
        end
    end
    return newBuf
end

--
-- Action handlers
--

local actionHandlers = {
    ['updatefirmware'] = require('firmware-update'),

    ['backupconfiguration'] =
        function(sc)
            -- First off, save the syscfg values to a temp file
            if not os.getenv('JCGI_DEBUG') then
                local syscfgFile = io.open(tmpSyscfgPath, 'w+')
                verify(syscfgFile, 'ErrorCreatingTempSyscfgFile')

                local buf = sc:getall()
                verify(buf and #buf ~= 0, 'ErrorReadingSyscfgValues')

                -- Filter any device-specific settings from the syscfg buffer
                local bkBuf = filterSyscfg(buf)

                syscfgFile:write(bkBuf)
                syscfgFile:close()
            end

            -- Second, create a tar.gz file
            local cmd = 'tar -zcf ' .. tmpFilePath
            for i = 1, #backupPaths do
                cmd = cmd .. ' ' .. backupPaths[i]
            end
            cmd = cmd .. ' > /dev/null 2>&1'
            -- Print the command and create a fake archive file if we're debugging
            if os.getenv('JCGI_DEBUG') then
                print(cmd)
                local archiveFile = io.open(tmpFilePath, 'w')
                archiveFile:write('A fake tar.gz file')
                archiveFile:close()
            else
                verify(os.execute(cmd) == 0, 'ErrorCreatingArchiveFile')
            end

            -- Open the tar.gz file
            local archiveFile = io.open(tmpFilePath, 'r')
            verify(archiveFile, 'ErrorNoArchiveFile')

            -- Read the archiveFile into a buffer
            local archiveBuffer = archiveFile:read('*a')
            archiveFile:close()

            --  Create the configuration backup buffer
            local backupBuffer = cfgVersion .. '\n'
            backupBuffer = backupBuffer .. #archiveBuffer .. '\n'
            backupBuffer = backupBuffer .. archiveBuffer

            responseFilename = 'backup.cfg'

            return nil, backupBuffer
        end,

    ['restoreconfiguration'] =
        function(sc)
            -- Open the restore file
            local restoreFile = io.open(tmpFilePath, 'r')
            verify(restoreFile, 'ErrorNoRestoreFile')

            -- Make sure the version is correct
            verify(restoreFile:read() == cfgVersion, 'ErrorIncompatibleConfigVersion')

            -- Get the archive file length
            local success, archiveLength = pcall(tonumber, restoreFile:read())
            verify(success and archiveLength, 'ErrorNoArchiveFileLength')

            -- Read in the archive binary into buffer
            local archiveBuffer = restoreFile:read('*a')
            restoreFile:close()

            -- Make sure the length matches what's expected
            verify(archiveLength == #archiveBuffer, 'ErrorIncorrectArchiveFileLength')

            -- Save out the archive file
            local archiveFile = io.open(tmpFilePath, 'w')
            verify(archiveFile, 'ErrorWritingArchiveFile')
            verify(archiveFile:write(archiveBuffer), 'ErrorWritingArchiveFile')
            archiveFile:close()

            -- Now delete the old IPA cache, extract the achive, and lock out the
            -- IPA database until the device reboots so that we don't end up with
            -- duplicate entries
            local cmd = 'rm -rf ' .. ipaCachePath .. ' && cd / && tar -xzf ' .. tmpFilePath .. ' tmp/syscfg.tmp var/config/ipa && touch /tmp/ipa/.lockedout > /dev/null 2>&1'

            -- Just print the command if we're debugging
            if os.getenv('JCGI_DEBUG') then
                print(cmd)
            else
                verify(os.execute(cmd) == 0, 'ErrorExtractArchiveFailed')

                -- Now read in the temporary syscfg file and save out to syscfg
                local syscfgFile = io.open(tmpSyscfgPath, 'r')
                verify(syscfgFile, 'ErrorOpeningTempSyscfgFile')

                local buf = syscfgFile:read('*a')
                verify(buf and #buf ~= 0, 'ErrorReadingTempSyscfgFile')

                -- Filter any device-specific settings from the syscfg backup
                local fBuf = filterSyscfg(buf)

                sc:setall(fBuf)
                syscfgFile:close()
            end

            -- Set the reboot event
            sc:setevent('system-restart')
        end,

    ['downloadvpnprofile'] =
        function(sc)
            local profile

            -- Create a fake profile if we're debugging
            if os.getenv('JCGI_DEBUG') then
                profile = 'A fake OpenVPN profile'
            else
                profile = sc:get_openvpn_client_connection_profile() or ''
            end
            assert(#profile > 0, 'ErrorNoVPNClientProfile')

            responseFilename = 'clientconfig.ovpn'

            return nil, profile
        end
}

-- Helper to parse HTTP header values
local function parseHeaderValue(s)
    if s then
        local v, rest = s:match('^([%w/-]+)(.*)$')
        local params = {}
        while rest and #rest > 0 do
            local name, value
            name, rest = rest:match('^;%s*([%w-]+)=(.*)$')
            if name then
                if rest:match('^"') then
                    value, rest = rest:match('^"([^"]*)"(.*)$')
                else
                    value, rest = rest:match('^([%w-]+)(.*)$')
                end
            end
            if name and value then
                params[name] = value
            else
                break
            end
        end
        return v, params
    end
end

--
-- Authorization
--

-- Helper function to decode base 64 strings
local function base64Decode(b64String)
    local base64='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/'
    b64String = string.gsub(b64String, '[^' .. base64 .. '=]', '')
    b64String = b64String:gsub('.',
                     function(x)
                         if (x == '=') then
                             return ''
                         end
                         local r, f = '', (base64:find(x) - 1)
                         for i = 6, 1, -1 do
                             r = r .. (f % 2^i - f % 2^(i - 1) > 0 and '1' or '0')
                         end
                         return r;
                     end)
    return b64String:gsub('%d%d%d?%d?%d?%d?%d?%d?',
                     function(x)
                         if (#x ~= 8) then
                             return ''
                         end
                         local c = 0
                         for i = 1, 8 do
                             c = c + (x:sub(i, i) == '1' and 2^(8 - i) or 0)
                         end
                         return string.char(c)
                     end)
end

-- Helper to validate Basic auth
local function isValidBasicAuth(sc, authorization)
    local result = false
    sc:readlock()
    local authorization = authorization:match('^[%s]*[Bb][Aa][Ss][Ii][Cc][%s]*(.*)')
    if authorization then
        local username, password = base64Decode(authorization):match('^(.*):(.*)')
        if username and password then
            result = sc:isvalidauth(username, password)
        end
    end
    sc:rollback() -- release lock
    return result
end

-- Helper to validate JNAP session
local function isValidJNAPSession(sc, session)
   -- Don't lock before this call - it does a call to the cloud
    return require('ownednetwork').getUserRole(sc, session) == 'ADMIN'
end

--
-- Request handlers
--

local function handleGETRequest(sc)
    local handler, authorization, session
    if os.getenv('HTTP_X_JNAP_ACTION') then
        handler = getActionHandlers[os.getenv('HTTP_X_JNAP_ACTION')]
        verify(handler, 'ErrorUnknownAction')
    end
    if os.getenv('HTTP_X_JNAP_AUTHORIZATION') then
        authorization = os.getenv('HTTP_X_JNAP_AUTHORIZATION')
        verify(isValidBasicAuth(sc, authorization), 'ErrorUnauthorized')
    end
    if os.getenv('HTPP_X_JNAP_Session') then
        session = os.getenv('HTTP_X_JNAP_SESSION')
        verify(isValidJNAPSession(sc, session), 'ErrorUnauthorized')
    end

    verify(handler, 'ErrorMissingAction')
    verify(authorization or session, 'ErrorUnauthorized')

    -- Grab a sysctx read lock
    sc:readlock()

    -- Run the handler on the file we uploaded
    return handler(sc, tmpFilePath, verify)
end

local function handleMultipartPOSTRequest(sc, boundary)
    -- Validate the boundary
    verify(boundary, 'ErrorInvalidBoundary')
    boundary = boundary:match('^(%S+)') -- see RFC 2046
    verify(boundary, 'ErrorInvalidBoundary')
    boundary = '--'..boundary -- see RFC 2046

    local contentLength = tonumber(os.getenv('CONTENT_LENGTH'))
    verify(contentLength, 'ErrorMissingContentLength')
    verify(contentLength > 0, 'ErrorInvalidContentLength')

    --
    -- Input reading functions
    --

    local buffer = ''
    local READ_CHUNK_SIZE = 16 * 1024
    local function ensureBuffered(count)
        if #buffer < count and contentLength > 0 then
            count = math.min(contentLength, math.max(count - #buffer, READ_CHUNK_SIZE))
            local more = io.stdin:read(count)
            if more then
                buffer = buffer..more
                contentLength = contentLength - #more
            else
                contentLength = 0
            end
        end
        return #buffer
    end

    local function read(count)
        if ensureBuffered(count) == 0 then
            return nil
        end
        local s = buffer:sub(1, count)
        buffer = buffer:sub(count + 1, count)
        return s
    end

    local function readUntil(sentinal)
        local start = 1
        repeat
            if ensureBuffered(start - 1 + #sentinal) < #sentinal then
                break -- EOF found before sentinal
            end
            local found = buffer:find(sentinal, start, true)
            if found then
                local s = buffer:sub(1, found - 1)
                buffer = buffer:sub(found + #sentinal)
                return s
            end
            start = #buffer - #sentinal + 2
        until contentLength == 0
    end

    --
    -- Parsing
    --

    local function parseBoundarySuffix()
        local rest = readUntil('\r\n')
        if rest then
            if rest == '--' then
                -- If there is a '--' after the boundary, this must be the final boundary
                verify(contentLength == 0, 'ErrorTrailingGarbage')
            else
                -- Whitespace is allowed after the boundary, before the CRLF
                verify(rest:match('^%s*$'), 'ErrorInvalidBoundary')
            end
        end
    end

    -- Parse optional preamble and first boundary
    local preamble = readUntil(boundary)
    verify(preamble and (#preamble == 0 or preamble:match('\r\n$')), 'ErrorInvalidPreamble')
    parseBoundarySuffix()

    -- Boundaries after the first must be preceded by \r\n
    boundary = '\r\n'..boundary

    local function parsePartHeaders()
        local headers = {}
        while true do
            local header = readUntil('\r\n')
            verify(header, 'ErrorInvalidPartHeader')
            if #header == 0 then
                -- End of headers
                break
            else
                local name, value = header:match('^([%w-]+):%s*(%S.*)$')
                verify(name and value, 'ErrorInvalidPartHeader')
                headers[name] = value
            end
        end
        return headers
    end

    local function parsePartBody()
        local body = readUntil(boundary)
        verify(body, 'ErrorInvalidPart')
        parseBoundarySuffix()
        return body
    end

    local function streamPartBodyToFile()
        local f = io.open(tmpFilePath, 'w')
        if f then
            local found
            repeat
                if ensureBuffered(#boundary) < #boundary then
                    fail('ErrorNoFinalBoundary')
                end
                found = buffer:find(boundary, 1, true)
                if not found then
                    -- Write buffer to buffer - boundary out to file
                    f:write(buffer:sub(1, #buffer - #boundary + 1))
                    buffer = buffer:sub(#buffer - #boundary + 2)
                end
            until found

            -- Write the remaining buffer out to file
            f:write(buffer:sub(1, found - 1))
            f:close()
            buffer = buffer:sub(found + #boundary)
            parseBoundarySuffix()
        end
    end

    --
    -- Parse parts:
    --
    --     X-JNAP-Action (text)
    --     X-JNAP-Authorization (X-JNAP-Authorization string as text)
    --     X-JNAP-Session (session token as text)
    --     upload (binary file content)
    --
    -- Either authorization or session must be specified.
    -- The upload part must come AFTER the other parts.
    --
    local authorization, session, handler
    while true do
        local partHeaders = parsePartHeaders()
        local contentDisposition, contentDispositionParams = parseHeaderValue(partHeaders['Content-Disposition'])
        verify(contentDisposition == 'form-data', 'ErrorInvalidPartContentDisposition')
        local partName = contentDispositionParams.name
        if partName == 'X-JNAP-Action' then
            local action = parsePartBody()
            handler = actionHandlers[action]
            verify(handler, 'ErrorUnknownAction')
        elseif partName == 'X-JNAP-Authorization' then
            authorization = parsePartBody()
            verify(isValidBasicAuth(sc, authorization), 'ErrorUnauthorized')
        elseif partName == 'X-JNAP-Session' then
            session = parsePartBody()
            verify(isValidJNAPSession(sc, session), 'ErrorUnauthorized')
        elseif partName == 'upload' then
            verify(handler, 'ErrorMissingAction')
            verify(authorization or session, 'ErrorUnauthorized')
            streamPartBodyToFile()
            break -- ignore any parts after the file
        else
            fail('ErrorUnexpectedPart')
        end
        if (handler == actionHandlers['downloadvpnprofile'] or handler == actionHandlers['backupconfiguration']) and
           (authorization or session) then
            break -- we have all we need for backupconfiguration/downloadvpnprofile
        end
    end

    -- Grab a sysctx write lock
    sc:writelock()

    -- Run the handler on the file we uploaded
    return handler(sc, tmpFilePath, verify)
end

--
-- CGI entrypoint
--

local success, failure = pcall(function()

    -- Initialize the sysctx
    local sc = sysctx.new()

    local completionHandler
    local responseContent
    -- Get the request method
    local requestMethod = os.getenv('REQUEST_METHOD')
    if requestMethod == 'POST' then
        -- Get the Content-Type header
        local contentType = os.getenv('CONTENT_TYPE')
        verify(contentType, 'ErrorInvalidContentType')

        -- The only Content-Type we handle is multipart
        local value, params = parseHeaderValue(contentType)
        verify(value == 'multipart/form-data', 'ErrorInvalidContentType')
        verify(params, 'ErrorInvalidContentType')

        -- Handle the multipart POST
        completionHandler, responseContent = handleMultipartPOSTRequest(sc, params.boundary)
    else
        fail('ErrorUnsupportedRequestMethod')
    end

    -- Send the response
    printResponse('OK', responseContent)

    -- Now run the completion handler if there is one
    if completionHandler then
        completionHandler()
    end

    -- Commit sysctx
    sc:commit()
end)

-- Delete the temp files (outside of the pcall so it always occurs)
os.remove(tmpFilePath)
os.remove(tmpSyscfgPath)

-- Send the error response if we didn't succeed
if not success then
    printResponse(error)
end

return success and 0 or -1
