--
-- 2018 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

-- jnap.lua - library to programatically call a JNAP action.

local util = require('util')


local _M = {} -- create the module


--
-- Programatically call a JNAP action.
--
-- Input:
--
--     action (string) - the JNAP action URI
--     request (string) - the JNAP request body
--     session (string or nil) - the JNAP session token, if any
--     role (string or nil) - the role ('ADMIN' or 'BASIC'), if any
--     remote (boolean or nil) - indicates if the call is remote
--
-- Output:
--
--     response (string) - the JNAP response body
--
function _M.request(action, request, session, role, remote)

    -- Build environment variable list
    local envVars = {
        'REQUEST_METHOD=POST',
        'REQUEST_URI=/JNAP',
        'CONTENT_LENGTH='..tostring(#request),
        'HTTP_X_JNAP_ACTION='..util.shellEscape(action),
        'JNAP_CGI_MODULES_PATH=/JNAP/modules/wan'
    }
    if session then
        table.insert(envVars, 'HTTP_X_JNAP_SESSION='..util.shellEscape(session))
    end
    if role then
        table.insert(envVars, 'ROLE='..util.shellEscape(role))
    end
    if remote then
        table.insert(envVars, 'JNAP_CGI_REMOTE=1')
    end

    -- Build the JNAP CGI application command line
    local cmd = string.format('echo %s | %s jnap',
                              util.shellEscape(request),
                              table.concat(envVars, ' '))

    -- Execute the JNAP action
    local out = io.popen(cmd, 'r')
    local response = out:read('*all')
    out:close()

    return response
end

-- Determines whether a given JNAP service is supported
--
-- Input:
--
--     ctx - the UTCTX context
--     serviceName (string) - the JNAP service name
--
-- Output:
--
--     serviceSupported (boolean) - whether the service is supported
--
function _M.isServiceSupported(ctx, serviceName)
    local hdk = require('libhdklua')

    local serviceSupported = false
    for module in hdk.values(ctx.modules) do
        for service in hdk.values(module.services) do
            if service.uri:match('^http://linksys.com'..serviceName..'$') then
                serviceSupported = true
                for action in hdk.values(service.actions) do
                    if not action.is_implemented or not action.is_jnap then
                        serviceSupported = false
                        break
                    end
                end
            end
            if serviceSupported then
                break
            end
        end
        if serviceSupported then
            break
        end
    end

    return serviceSupported
end

return _M -- return the module
