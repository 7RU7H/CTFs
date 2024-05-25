--
-- 2018 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

-- cloud.lua - library of functions that wrap calls to cloud APIs.


local _M = {} -- create the module


----------------------------------------------------------------------
-- Platform-specific constants.
----------------------------------------------------------------------

_M.ROUTER_CLIENT_TYPE_ID = 'AA296AC6-61D1-4D3F-83FA-96D7486A09CF'

local SESSION_HEADER_FORMAT = 'CiscoHNUserAuth session_token=%s'

local CA_PATH = '/etc/certs/root'


----------------------------------------------------------------------
-- Helper functions.
----------------------------------------------------------------------


--
-- Call a cloud webservice API synchronously with a preformatted
-- request body, and return the response body and status.
--
-- The only parameter must be a table with the following members:
--
--   * host (string) : the host for the request
--
--   * path (string, optional) : the path on the host; if not
--     specified, the default is '/'
--
--   * method (string) : one of 'GET', 'POST', 'PUT', or 'DELETE'
--
--   * request (string, optional) : the request body
--
--   * sessionToken (string, optional) : the session token
--
--   * headers (map of string, optional) : a map of HTTP header -> value values
--
--   * timeout (number, optional) : the timeout, in seconds; if not
--     specified, the default timeout will be used
--
--   * verifyPath (string, optional) : a path to a directory
--     containing CAs to use to verify the host's certificate; if not
--     specified, the host will not be verified
--
-- If the call succeeds, it returns the response body as a string
-- and the status code as a number.
--
-- If the call fails, it returns nil and an error string.
--
function _M.callCloudLowLevel(params)
    local http = require('socket.http')
    local ltn12 = require('ltn12')
    local platform = require('platform')
    local https

    -- Handle discrepancies between different versions of SSL/HTTPS library.
    if not pcall(function() https = require('ssl.https') end) then
        require('https')
        https = ssl.https
    end

    local request = params.request
    local sessionToken = params.sessionToken
    local timeout = params.timeout or 10

    -- Set global timeout, if a timeout was specified
    local oldTimeout = http.TIMEOUT
    http.TIMEOUT = timeout

    local headers = {
        ['X-Cisco-HN-Client-Type-Id'] = _M.ROUTER_CLIENT_TYPE_ID,
        ['Authorization'] = sessionToken and SESSION_HEADER_FORMAT:format(sessionToken),
        ['Content-Type'] = 'application/json; charset=UTF-8',
        ['Accept'] = 'application/json',
        ['Content-Length'] = request and #request or 0
    }
    -- Add custom header values
    if params.headers then
        for name, value in pairs(params.headers) do
            if not headers[name] then
                headers[name] = value
            end
        end
    end

    platform.logMessage(platform.LOG_INFO, ('sending cloud request \'%s\''):format(params.path))

    -- Make the request
    local response = {}
    local result, status = https.request({
        url = 'https://'..params.host..(params.path or '/'),
        source = request and ltn12.source.string(request),
        sink = ltn12.sink.table(response),
        method = params.method,
        headers = headers,
        capath = params.verifyPath,
        verify = params.verifyPath and 'peer' or 'none'
    })

    platform.logMessage(platform.LOG_INFO, ('cloud response HTTP status: '..tostring(status)))

    -- Restore global timeout to previous value
    http.TIMEOUT = oldTimeout

    return (1 == result) and table.concat(response) or nil, status
end

--
-- Call a cloud webservice API synchronously with a request
-- payload object to be sent as JSON, and return the response
-- payload object parsed from JSON and the status.
--
-- The only parameter must be a table with the following members:
--
--   * host (string) : the host for the request
--
--   * path (string, optional) : the path on the host; if not
--     specified, the default is '/'
--
--   * method (string) : one of 'GET', 'POST', 'PUT', or 'DELETE'
--
--   * request (table, optional) : the request payload object
--
--   * sessionToken (string, optional) : the session token
--
--   * headers (map of string, optional) : a map of HTTP header -> value values
--
--   * timeout (number, optional) : the timeout, in seconds; if not
--     specified, the default timeout will be used
--
--   * verifyPath (string, optional) : a path to a directory
--     containing CAs to use to verify the host's certificate; if not
--     specified, the host will not be verified
--
-- The call returns the result lua object or nil on failure, the HTTP
-- status code, if returned, and an error message on failure.
-- If a non-200 HTTP status is returned, the error message is the
-- HTTP response content
--
function _M.callCloud(params)
    local json = require('libhdkjsonlua')
    local response, status = _M.callCloudLowLevel({
        host = params.host,
        path = params.path,
        method = params.method,
        request = params.request and json.stringify(params.request),
        sessionToken = params.sessionToken,
        headers = params.headers,
        timeout = params.timeout,
        verifyPath = params.verifyPath
    })
    local result = nil
    local httpStatus = nil
    local errorMessage = nil
    if response then
        -- Received a response from the server. 'status' is an HTTP status code
        httpStatus = tonumber(status)
        if httpStatus == 200 then
            local success, message = pcall(function() result = json.parse(response) end)
            if not success then
                -- Failed to parse the HTTP response content as JSON
                assert(nil == result)
                errorMessage = message
            end
        else
            -- Non 200 HTTP status; return the HTTP response content as the error
            errorMessage = response
        end
    else
        -- No response; 'status' contains the error
        errorMessage = status
    end
    assert(((nil ~= result) and (nil == errorMessage)) or ((nil == result) and (nil ~= errorMessage)))
    return result, httpStatus, errorMessage
end


----------------------------------------------------------------------
-- Cloud API wrapper functions.
----------------------------------------------------------------------

--
-- Create a cloud event. Returns 2 values:
--
--     1. nil if the call succeeded, error (string) otherwise
--     2. event ID (string) if the call succeeded, nil otherwise
--
function _M.createEvent(cloudHost, networkId, networkPassword, deviceId, eventType, eventTime, verify, payload)
    local response, status = _M.callCloud({
        host = cloudHost,
        path = '/event-service/rest/events/',
        method = 'POST',
        request = {
            event = {
                network = {
                    networkId = networkId,
                    password = networkPassword
                },
                device = {
                    deviceId = deviceId
                },
                eventType = eventType,
                happenedAt = eventTime,
                payload = payload
            }
        },
        verifyPath = verify and CA_PATH or nil
    })

    if status == 401 then
        return 'ErrorUnknownOwnerSession'
    end

    local eventId
    if not pcall(function() eventId = response.event.eventId end) then
        return 'ErrorCloudUnavailable'
    end

    return nil, eventId
end

--
-- Register network ownership with the cloud. Returns 3 values:
--
--     1. nil if the call succeeded, error (string) otherwise
--     2. owned network ID (string) if the call succeeded, nil otherwise
--     3. owned network password (string) if the call succeeded, nil otherwise
--
function _M.setNetworkOwner(cloudHost, routerUUID, serialNumber, modelNumber, friendlyName, ownerSessionToken, networkId, password, verify)
    local response, status = _M.callCloud({
        host = cloudHost,
        path = '/device-service/rest/networks/',
        method = 'POST',
        request = {
            network = {
                routerId = routerUUID,
                routerSerialNumber = serialNumber,
                routerModelNumber = modelNumber,
                friendlyName = friendlyName,
                networkId = networkId,
                password = password
            }
        },
        sessionToken = ownerSessionToken,
        verifyPath = verify and CA_PATH or nil
    })

    if status == 401 then
        return 'ErrorUnknownOwnerSession'
    end

    local networkID, password
    if not pcall(function() networkID, password = response.network.networkId, response.network.password end) then
        return 'ErrorCloudUnavailable'
    end

    return nil, networkID, password
end

--
-- Get the role associated with a session token for a particular network.
--
-- Returns 'ADMIN', 'BASIC', or nil (no role), HTTP status (or nil on network error)
--
function _M.getUserRole(cloudHost, ownedNetworkID, sessionToken, verify)
    local response, status

    if not ownedNetworkID then
        response, status = nil, 404
    else
        response, status = _M.callCloud(
            {
                host = cloudHost,
                path = '/device-service/rest/networks/'..ownedNetworkID..'/accounts/self',
                method = 'GET',
                sessionToken = sessionToken,
                verifyPath = verify and CA_PATH or nil
            })
    end

    return (status == 200) and response.networkAccountAssociation.role or nil, status
end


--
-- Validates the session associated with a session token
--
-- Returns true or false, status
--
function _M.isValidSession(cloudHost, ownedNetworkID, sessionToken, verify)
    local response, status = _M.callCloud(
            {
                host = cloudHost,
                path = '/device-service/rest/networks/'..ownedNetworkID,
                method = 'GET',
                sessionToken = sessionToken,
                verifyPath = verify and CA_PATH or nil
            })
    -- Only if everything is fine and the session token matches ownedNetworkID is a 200 status returned
    return status == 200, status
end

--
-- Retrieve the selected set of properties for a given set of devices.
--
-- The devices table must contain objects in the following structure:
--  {
--      deviceId = STRING,
--      macAddresses = OPTIONAL(ARRAY_OF(MACADDRESS)),
--      manufacturer = OPTIONAL(STRING),
--      modelNumber = OPTIONAL(STRING),
--      serialNumber = OPTIONAL(STRING),
--      hardwareVersion = OPTIONAL(STRING),
--      firmwareVersion = OPTIONAL(STRING),
--      type = OPTIONAL(STRING),
--      description = OPTIONAL(STRING),
--      operatingSystem = OPTIONAL(STRING)
--  }
--
-- The output contains one or more optional properties, depending on the 'view' parameter.
-- Valid values for 'view' are: [ 'QOS', 'BASIC_INFO', 'FULL' ]
--
-- On error, nil is returned as the first parameter, and the error is returned as the
-- second parameter.
--
-- input = STRING,
--      ARRAY_OF({
--          deviceID = UUID,
--          macAddresses = OPTIONAL(ARRAY_OF(MACADDRESS)),
--          manufacturer = OPTIONAL(STRING),
--          modelNumber = OPTIONAL(STRING),
--          serialNumber = OPTIONAL(STRING),
--          hardwareVersion = OPTIONAL(STRING),
--          firmwareVersion = OPTIONAL(STRING),
--          type = OPTIONAL(STRING),
--          description = OPTIONAL(STRING),
--          operatingSystem = OPTIONAL(STRING)
--  }),
--      STRING,
--      STRING,
--      OPTIONAL(STRING),
--      OPTIONAL(STRING),
--      OPTIONAL(BOOLEAN)
--
-- output = NIL_OR_ARRAY_OF({
--      deviceID = UUID,
--      defaultQosPriority = OPTIONAL(STRING),
--      defaultQosTrafficType = OPTIONAL(STRING),
--      moreInfoUrl = OPTIONAL(STRING),
--      iconUrl = OPTIONAL(STRING),
--      defaultFriendlyName = OPTIONAL(STRING)
--  }),
--  NIL_OR_STRING
--
function _M.describeDevices(cloudHost, devices, firmwareVersion, modelNumber, networkID, view, verify, log)
    local platform = require('platform')
    local path = '/device-service/rest/devicedescriber'
    if view then
        path = path..'?view='..view
    end

    -- Construct the expected web-service API structure.
    -- See http://wikiccp.cisco.com/display/fven/Device+Service#DeviceService-Describedevices
    local deviceDescriber = {
    }
    -- pairs is used to iterate to table of devices to allow for named table keys.
    for _, device in pairs(devices) do
        local macAddresses
        for _, macAddress in ipairs(device.macAddresses) do
            if not macAddresses then
                macAddresses = {}
            end
            table.insert(macAddresses, {
                [ 'macAddress' ] = tostring(macAddress)
            })
        end

        if not deviceDescriber.devices then
            deviceDescriber.devices = {}
        end
        table.insert(deviceDescriber.devices, {
            [ 'device' ] = {
                deviceId = tostring(device.deviceID),
                macAddresses = macAddresses,
                manufacturer = device.manufacturer,
                modelNumber = device.modelNumber,
                serialNumber = device.serialNumber,
                hardwareVersion = device.hardwareVersion,
                firmwareVersion = device.firmwareVersion,
                type = device.type,
                description = device.description,
                operatingSystem = device.operatingSystem
            }
        })
    end

    -- Log request if needed
    if log then
        platform.logMessage(platform.LOG_VERBOSE, ('DeviceDescriber request:\n%s\n'):format(require('libhdkjsonlua').stringify(deviceDescriber)))
    end

    local output, status, message = _M.callCloud(
            {
                host = cloudHost,
                path = path,
                method = 'POST',
                request = {
                    [ 'deviceDescriber' ] = deviceDescriber
                },
                headers = {
                    ['X-Cisco-HN-Network-Id'] = networkID,
                    ['X-Cisco-HN-Router-FW-Version'] = firmwareVersion,
                    ['X-Cisco-HN-Router-Model-Number'] = modelNumber
                },
                verifyPath = verify and CA_PATH or nil
            })

    if output then
        local hdk = require('libhdklua')
        assert(status == 200)

        -- Log response if needed
        if log then
            platform.logMessage(platform.LOG_VERBOSE, ('DeviceDescriber response:\n%s\n'):format(require('libhdkjsonlua').stringify(output)))
        end

        local devices = {}
        for _, device in ipairs(output.devices) do
            -- Move deviceId to deviceID
            device.device.deviceID = hdk.uuid(device.device.deviceId)
            device.device.deviceId = nil
            table.insert(devices, device.device)
        end
        return devices, nil
    else
        return nil, message
    end
end

function _M.getServerStatus(sc)
    local ltn12 = require('ltn12')
    local json = require('libhdkjsonlua')
    local https

    sc:readlock()
    local cloud_host = sc:get_cloud_host()

    -- Handle discrepancies between different versions of SSL/HTTPS library.
    if not pcall(function() https = require('ssl.https') end) then
        require('https')
        https = ssl.https
    end

    local headers = {
        ['Content-Type'] = 'application/json; charset=UTF-8',
        ['Accept'] = 'application/json',
        ['X-Linksys-Client-Type-Id'] = _M.ROUTER_CLIENT_TYPE_ID
    }

    local resp = {}

    local retCode, status, header = https.request {
        method = "GET",
        url = "https://"..cloud_host.."/device-service/rest/mypublicipaddress",
        headers = headers,
        sink = ltn12.sink.table(resp)
    }

    local response = json.stringify(table.concat(resp))
    local ipAddress = response:match("ipAddress")

    --[[
        (e.g.)
        response = "{\"ipAddress\":{\"ipv4\":\"61.37.179.146\"}}"
    ]]

    -- local retCode, status, header = https.request {
    --     method = "HEAD",
    --     url = "https://"..cloud_host
    -- }

    -- If WAN interface does not connect to the internet, retCod and header are nil,
    -- status is 'host not found' or 'Network is unreachable'.
    -- If Cloud server is alive, status code is 200 and response data such as the above example.
    return (status == 200) and (ipAddress ~= nil)
end

--
-- Provision the device with the Linksys cloud.  Returns 3 values:
--
--     1. nil if the call succeeded, error (string) otherwise
--     2. owned network ID (string) if the call succeeded, nil otherwise
--     3. owned network password (string) if the call succeeded, nil otherwise
--
function _M.provisionDevice(cloudHost, provisionId, routerUUID, serialNumber, modelNumber, friendlyName, networkId, password, token, verify)
    local platform = require('platform')

    local response, status, message = _M.callCloud({
        host = cloudHost,
        path = string.format('/device-service/rest/deviceprovisions/%s', provisionId),
        method = 'PUT',
        request = {
            clientDevice = {
                network = {
                    routerId = routerUUID,
                    routerSerialNumber = serialNumber,
                    routerModelNumber = modelNumber,
                    friendlyName = friendlyName,
                    networkId = networkId,
                    password = password
                }
            }
        },
        headers = {
            ['X-Linksys-Token'] = token
        },
        verifyPath = verify and CA_PATH or nil
    })

    if status == 400 then
        return 'ErrorInvalidRequest'
    elseif status == 403 then
        return 'ErrorAccessDenied'
    elseif status == 404 then
        return 'ErrorProvisionNotFound'
    elseif status == 410 then
        return 'ErrorProvisionExpired'
    end

    local networkID, password
    if not pcall(function() networkID, password = response.provision.network.networkId, response.provision.network.password end) then
        return 'ErrorCloudUnavailable'
    end
    platform.logMessage(platform.LOG_INFO, ('provisioned networkID=%s, password=%s'):format(networkID, password))

    return nil, { networkID = networkID, password = password }
end

--
-- Self provision the device in the Linksys cloud, for remote access. Returns 3 values:
--
--     1. nil if the call succeeded, error (string) otherwise
--     2. owned network ID (string) if the call succeeded, nil otherwise
--     3. owned network password (string) if the call succeeded, nil otherwise
--
function _M.provisionSelf(cloudHost, serialNumber, routerUUID, friendlyName, networkId, password, token, verify)
    local platform = require('platform')

    local response, status, message = _M.callCloud({
        host = cloudHost,
        path = '/device-service/rest/deviceprovisions/self',
        method = 'POST',
        request = {
            clientDevice = {
                network = {
                    routerSerialNumber = serialNumber,
                    routerId = routerUUID,
                    friendlyName = friendlyName,
                    networkId = networkId,
                    password = password
                }
            }
        },
        headers = {
            ['X-Linksys-Token'] = token
        },
        verifyPath = verify and CA_PATH or nil
    })

    if status == 400 then
        return 'ErrorInvalidRequest'
    elseif status == 403 then
        return 'ErrorAccessDenied'
    end

    local networkID, password
    if not pcall(function() networkID, password = response.provision.network.networkId, response.provision.network.password end) then
        return 'ErrorCloudUnavailable'
    end
    platform.logMessage(platform.LOG_INFO, ('self provisioned networkID=%s, password=%s'):format(networkID, password))

    return nil, { networkID = networkID, password = password }
end

--
-- Registers the device with the cloud. Returns 2 values:
--
--     1. nil if the call succeeded, error (string) otherwise
--     2. device token (string) if the call succeeded, nil otherwise
--
function _M.registerDevice(cloudHost, serialNumber, macAddress, modelNumber, hwVersion, fwVersion, uuid, mfgDate, verify)
    local ok, response, status = pcall(_M.callCloud, {
        host = cloudHost,
        path = '/device-service/rest/devices/registrations',
        method = 'POST',
        request = {
            clientDevice = {
                serialNumber = serialNumber,
                macAddress = macAddress,
                modelNumber = modelNumber,
                hardwareVersion = hwVersion,
                firmwareVersion = fwVersion,
                uuid = uuid,
                manufacturingDate = mfgDate
            }
        },
        verifyPath = verify and CA_PATH or nil
    })
    if status == 400 then
        return 'ErrorInvalidParameter'
    end

    local token
    if not pcall(function() token = response.clientDevice.linksysToken end) then
        return 'ErrorCloudUnavailable'
    end

    return nil, token
end

--
-- Gets a new device token from the cloud. Returns 2 values:
--
--     1. nil if the call succeeded, error (string) otherwise
--     2. device token (string) if the call succeeded, nil otherwise
--
function _M.getNewToken(cloudHost, serialNumber, macAddress, currentToken, verify)
    local ok, response, status = pcall(_M.callCloud, {
        host = cloudHost,
        path = '/device-service/rest/devices/tokens',
        method = 'POST',
        request = {
            clientDevice = {
                serialNumber = serialNumber,
                macAddress = macAddress,
                linksysToken = currentToken
            }
        },
        verifyPath = verify and CA_PATH or nil
    })
    if status == 400 then
        return 'ErrorInvalidParameter'
    elseif status == 401 then
        return 'ErrorInvalidLinksysToken'
    elseif status == 403 then
        return 'ErrorParameterMismatch'
    end

    local token
    if not pcall(function() token = response.clientDevice.linksysToken end) then
        return 'ErrorCloudUnavailable'
    end

    return nil, token
end

--
-- Gets information about a configuration stored for this device in the cloud.
--
-- input = STRING, STRING, STRING, BOOLEAN
-- output = NIL_OR_ONE_OF(
--     ErrorInvalidLinksysToken
--     ErrorInvalidClientType
--     ErrorConfigurationNotFound
--     ErrorCloudUnavailable
-- ),
-- NIL_OR_ARRAY_OF({
--     configurationId = STRING,
--     type = STRING,
--     friendlyName = STRING,
--     configKey = ARRAYOF({
--         name = STRING,
--         value = STRING
--     }),
--     _links = {
--         self = {
--             href = STRING
--         }
--     }
-- })
--
function _M.getConfigurationInfo(cloudHost, token, params, verify)
    local configurations
    local path = '/lswf-service/rest/configurations'
    if params then
        local i = 1
        for key, val in pairs(params) do
            path = string.format('%s%s%s=%s', path, (i == 1) and '?' or '&', key, val)
            i = i + 1
        end
    end

    local response, status, message = _M.callCloud({
        host = cloudHost,
        path = path,
        method = 'GET',
        headers = {
            ['X-Linksys-Token'] = token,
            ['X-Linksys-Client-Type-Id'] = _M.ROUTER_CLIENT_TYPE_ID
        },
        verifyPath = verify and CA_PATH or nil
    })

    if status == 401 then
        return 'ErrorInvalidLinksysToken'
    elseif status == 403 then
        return 'ErrorInvalidClientType'
    elseif status == 404 then
        return 'ErrorConfigurationNotFound'
    elseif status == 200 then
        local totalElements
        if not pcall(function() totalElements = response.page.totalElements end) then
            return 'ErrorCloudUnavailable'
        elseif totalElements == 0 then
            return 'ErrorConfigurationNotFound'
        elseif not pcall(function() configurations = response._embedded.configurations end) then
            return 'ErrorCloudUnavailable'
        end
    else
        return 'ErrorCloudUnavailable'
    end

    return nil, configurations
end

--
-- Stores a device configuration to the cloud. Returns 1 value.
--     1. nil if the call succeeded, error (string) otherwise
--     2. configuration Id if a new configuration was created, nil otherwise
--
-- input = STRING, STRING, STRING, STRING, STRING, STRING, STRING, BOOLEAN
--
-- output = NIL_OR_ONE_OF(
--     ErrorInvalidLinksysToken
--     ErrorInvalidParameter
--     ErrorInvalidClientType
--     ErrorConfigurationNotFound
--     ErrorCloudUnavailable
-- ),
-- NIL_OR_STRING
--
function _M.storeConfiguration(cloudHost, token, configId, friendlyName, configType, configKey, configData, verify)
    local path = '/lswf-service/rest/configurations'

    local response, status = _M.callCloud({
        host = cloudHost,
        -- If input configId is not nil, then update the existing configuration (PUT)
        path = configId and path..'/'..configId or path,
        method = configId and 'PUT' or 'POST',
        headers = {
            ['X-Linksys-Token'] = token,
            ['X-Linksys-Client-Type-Id'] = _M.ROUTER_CLIENT_TYPE_ID
        },
        request = {
            friendlyName = friendlyName,
            type = configType,
            configKey = configKey,
            configValue = configData
        },
        verifyPath = verify and CA_PATH or nil
    })

    if status == 200 then -- Configuration was created
        local configurationId
        if not pcall(function() configurationId = response.configurationId end) then
            return 'ErrorCloudUnavailable'
        end
        return nil, configurationId
    elseif status == 204 then -- Configuration was updated
        return
    elseif status == 400 then
        return 'ErrorInvalidParameter'
    elseif status == 401 then
        return 'ErrorInvalidLinksysToken'
    elseif status == 403 then
        return 'ErrorInvalidClientType'
    elseif status == 404 then
        return 'ErrorConfigurationNotFound'
    else
        return 'ErrorCloudUnavailable'
    end
end

--
-- Gets information about a configuration stored for this device in the cloud.
--
-- input = STRING, STRING, STRING, BOOLEAN
-- output = NIL_OR_ONE_OF(
--     ErrorInvalidLinksysToken
--     ErrorInvalidClientType
--     ErrorConfigurationNotFound
--     ErrorCloudUnavailable
-- ),
-- NIL_OR_STRING
--
function _M.getConfiguration(cloudHost, token, configId, verify)
    local response, status, message = _M.callCloud({
        host = cloudHost,
        path = '/lswf-service/rest/configurations/'..configId,
        method = 'GET',
        headers = {
            ['X-Linksys-Token'] = token,
            ['X-Linksys-Client-Type-Id'] = _M.ROUTER_CLIENT_TYPE_ID
        },
        verifyPath = verify and CA_PATH or nil
    })

    if status == 401 then
        return 'ErrorInvalidLinksysToken'
    elseif status == 403 then
        return 'ErrorInvalidClientType'
    elseif status == 404 then
        return 'ErrorConfigurationNotFound'
    end

    -- Verify the response
    if not pcall(function() local configurationId = response.configurationId end) then
        return 'ErrorCloudUnavailable'
    end

    return nil, response
end

--
-- Uploads historical data to the cloud.
--
-- input = STRING, STRING, TABLE, BOOLEAN
-- output = NIL_OR_ONE_OF(
--     ErrorInvalidRequest
--     ErrorAccessDenied
--     ErrorCloudUnavailable
-- )
--
function _M.uploadHistData(cloudHost, token, serialNumber, data, verify)
    local response, status, message = _M.callCloud({
        host = cloudHost,
        path = '/data-collection-service/rest/historicaldata',
        method = 'POST',
        headers = {
            ['X-Linksys-Token'] = token,
            ['X-Linksys-Client-Type-Id'] = _M.ROUTER_CLIENT_TYPE_ID,
            ['X-Linksys-SN'] = serialNumber
        },
        request = data,
        verifyPath = verify and CA_PATH or nil
    })
    if status == 400 then
        return 'ErrorInvalidRequest'
    elseif status == 403 then
        return 'ErrorAccessDenied'
    elseif status ~= 200 then
        return 'ErrorCloudUnavailable'
    end
end


return _M -- return the module
