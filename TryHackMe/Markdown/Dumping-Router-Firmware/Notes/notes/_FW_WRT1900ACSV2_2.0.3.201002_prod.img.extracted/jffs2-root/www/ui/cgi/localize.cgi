#!/usr/bin/lua

--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

--
-- localize.cgi - cgi script to perform localized string replacement
--
-- Inputs (via environment):
--  Required:
--      SCRIPT_FILENAME : The path of the file to perform string replacement on (typically passed by the HTTP server)
--
--  Optional:
--      HTTP_ACCEPT_LANGUAGE : The Accept-Language header included in the request (typically passed by the HTTP server).
--                             If not specified, the default language will be used.
--      LOCALIZE_BUNDLE_DIR : The string resource bundle directory. If not specified, the sibling subdirectory to the
--                            SCRIPT_FILENAME file called 'localized' is used.
--      NO_HTTP_HEADERS : Don't output any HTTP headers, just the file with localized strings replaced.
--

cgiutil = require('cgi-util')

-- Retrieve a resource string from the given file object
local function RetrieveResourceString(file, key)
    file:seek("set") -- reset to the start of the file
    for line in file:lines() do
        local match = string.match(line, '^'..key..'=(.+)$')
        if match then return match end
    end

    return nil
end

-- As defined at http://wikiccp.cisco.com/display/fven/Internationalization+and+localization+spec
local function GenerateResourceFileName(bundleDir, bundleName, language, country)
    return bundleDir..'/'..bundleName..(language and '_'..string.lower(language) or '')..(country and '_'..string.upper(country) or '')..'.properties'
end

local function LocalizeStream(inputStream, outputStream, bundleDir, acceptLanguages, missingString)

    local countryResourceFile
    local languageResourceFile
    local defaultResourceFile

    local currentBundle -- name of the currently loaded resource bundle

    local function closeResourceBundle()
        if countryResourceFile then countryResourceFile:close() end
        if languageResourceFile then languageResourceFile:close() end
        if defaultResourceFile then defaultResourceFile:close() end
    end

    local function openResourceBundle(bundleName)
        closeResourceBundle()

        -- Locate the resource to use
        defaultResourceFile = io.open(GenerateResourceFileName(bundleDir, bundleName), 'r')
        for _, v in ipairs(acceptLanguages) do

            -- Generate the resource file name (resources are already sorted by quality)
            if v.country ~= nil then
                countryResourceFile, error = io.open(GenerateResourceFileName(bundleDir, bundleName, v.language, v.country), 'r')
            end
            languageResourceFile = io.open(GenerateResourceFileName(bundleDir, bundleName, v.language), 'r')

            if countryResourceFile ~= nil or languageResourceFile ~= nil then break end
        end
    end

    local function LookupString(bundleName, key)
        if currentBundle ~= bundleName then
            openResourceBundle(bundleName)
            currentBundle = bundleName
        end

        return countryResourceFile and RetrieveResourceString(countryResourceFile, key) or
               languageResourceFile and RetrieveResourceString(languageResourceFile, key) or
               defaultResourceFile and RetrieveResourceString(defaultResourceFile, key) or
               missingString or ''
    end

    for line in inputStream:lines() do
        outputStream:write(string.gsub(line, '{{([%w%.%-_]*)%.([%w%.%-_]*)}}', LookupString)..'\n')
    end

    closeResourceBundle()
end

local fileToProcess = os.getenv('SCRIPT_FILENAME')

local userLanguage = os.getenv('HTTP_COOKIE') and string.match(os.getenv('HTTP_COOKIE'), 'ui%-language=([^;]*)')

local function RemoveFileName(path)
    local init = 1
    while true do
        local i = string.find(path, '/', init, true)
        if not i then break end
        init = i + 1
    end
    return string.sub(path, 1, init - 1)
end

local inputStream, error = io.open(fileToProcess or '')
if inputStream then
    localize = require('localize')

    local acceptLanguages = localize.ParseAcceptLanguageHeader(userLanguage or os.getenv('HTTP_ACCEPT_LANGUAGE'))

    -- Subdirectory containing the localized resource strings
    local bundleDir = os.getenv('LOCALIZE_BUNDLE_DIR') or RemoveFileName(fileToProcess)..'localized'

    -- Placeholder string
    local placeHolder = 'ERROR: Missing localized string!'

    if not os.getenv('NO_HTTP_HEADERS') then
        local status = tonumber(os.getenv('REDIRECT_STATUS') or '200')

        if 200 ~= status then
            io.stdout:write(('Status: %u %s\r\n'):format(status, cgiutil.StatusCodeMessage(status)))
        end

        -- Set the mime type, if it can be determined.
        local contentType = cgiutil.GetMimeType(fileToProcess)
        if contentType then
            io.stdout:write('Content-Type: '..contentType..'; charset=UTF-8\r\n')
        end

        -- Instruct clients not to cache localized content as there
        -- is currently no system (e.g. ETags) in place for managing it.
        io.stdout:write('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0\r\n')
        io.stdout:write('Pragma: no-cache\r\n')
        io.stdout:write('\r\n')
    end

    LocalizeStream(inputStream, io.stdout, bundleDir, acceptLanguages, placeHolder)
    inputStream:close()
else
    cgiutil.WriteErrorResponse(io.stdout, error)
end
