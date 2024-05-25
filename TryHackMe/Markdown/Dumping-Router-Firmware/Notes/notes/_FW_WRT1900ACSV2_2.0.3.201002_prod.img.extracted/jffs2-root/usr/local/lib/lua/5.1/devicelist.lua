--
-- 2019 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author: hsulliva $
-- $DateTime: 2020/04/21 20:46:19 $
-- $Id: //depot-irv/lego/branches/wrt_platform_sp1/lego_overlay/proprietary/jnap/lualib/devicelist.lua#6 $
--

local topodb = require('libtopodblua')

local _M = {} -- create the module

---------------------------------------------------------------------------------
-- Constants.
---------------------------------------------------------------------------------

local MAX_PROPERTIES = 16
local MAX_PROPERTY_NAME_LENGTH = 31
local MAX_PROPERTY_VALUE_LENGTH = 255

---------------------------------------------------------------------------------
-- Utility functions.
---------------------------------------------------------------------------------

-- Create a table of where the entries are of the form: tostring(uuid) = uuid.
local function makeDeviceLookup(deviceIDs)
    local lookup = nil
    if deviceIDs then
        lookup = {}
        for i, deviceID in ipairs(deviceIDs) do
            lookup[tostring(deviceID)] = deviceID
        end
    end
    return lookup
end

-- Create a list of device IDs from a device list
local function deviceListToDeviceIDs(deviceList)
    local deviceIDs = {}
    local devices = deviceList.devices
    if devices then
        for i, device in ipairs(devices) do
            deviceIDs[i] = device.deviceID
        end
    end
    return deviceIDs
end

local function isValidPropertyName(name)
    return #name <= MAX_PROPERTY_NAME_LENGTH and string.find(name, '^[A-Za-z][A-Za-z0-9%-_:]*$') ~= nil
end

local function isValidPropertyValue(value)
    return #value <= MAX_PROPERTY_VALUE_LENGTH
end

local function toOutputDevice(sc, d, apiVersion)
    local model = {
        deviceType = d.model and d.model.deviceType or '',
        manufacturer = d.model and d.model.manufacturer, -- may be nil
        modelNumber = d.model and d.model.modelNumber, -- may be nil
        hardwareVersion = d.model and d.model.hardwareVersion, -- may be nil
        description = d.model and d.model.description -- may be nil
    }
    local unit = {
        serialNumber = d.unit and d.unit.serialNumber, -- may be nil
        firmwareVersion = d.unit and d.unit.firmwareVersion, -- may be nil
        firmwareDate = d.unit and d.unit.firmwareDate, -- may be nil
        operatingSystem = d.unit and d.unit.operatingSystem -- may be nil
    }
    local outputDevice = {
        deviceID = d.deviceID,
        lastChangeRevision = d.lastChangeRevision or 0,
        model = model,
        unit = unit,
        friendlyName = d.friendlyName or d.hostName, -- may be nil (fallback to host name if friendly name is not set)
        isAuthority = d.isAuthority or false,
        connections = d.connections or {},
        properties = d.properties or {},
        maxAllowedProperties = MAX_PROPERTIES
    }

    if apiVersion == 2 then
        local netConns = require('networkconnections').getNetworkConnections(sc, d.knownMACAddresses)
        local function findNetworkConnection(mac)
            for _, conn in ipairs(netConns) do
                if (tostring(conn.macAddress) == mac) then
                    return conn
                end
            end
        end
        local function removeConnection(connections, mac)
            for i, conn in ipairs(connections) do
                if (tostring(conn.macAddress) == mac) then
                    table.remove(connections, i)
                    return
                end
            end
        end

        outputDevice.knownInterfaces = {}
        -- convert knownMACAddresses to knownInterfaces
        for _, mac in ipairs(d.knownMACAddresses) do
            local ifaceType = 'Unknown'
            local conn = findNetworkConnection(tostring(mac))
            local band
            if conn then
                if conn.wireless then
                    ifaceType = 'Wireless'
                    band = conn.wireless.band
                    -- Now set the isGuest parameter on the respective connection of the output device.
                    for _, devConn in ipairs(outputDevice.connections) do
                        if (tostring(devConn.macAddress) == tostring(mac)) then
                            devConn.isGuest = conn.wireless.isGuest
                        end
                    end
                end
            elseif d.connections then
                -- No network connection was found for the MAC address,
                -- so remove the connection from the device
                removeConnection(d.connections, tostring(mac))
            end
            local iface = {
                macAddress = mac,
                interfaceType = ifaceType,
                band = band
            }
            table.insert(outputDevice.knownInterfaces, iface)
        end
    else
        outputDevice.knownMACAddresses = d.knownMACAddresses or {}
    end

    return outputDevice
end

---------------------------------------------------------------------------------
-- Inner functions implementing the service's business logic.
---------------------------------------------------------------------------------

local function innerGetDevices(sc, tdb, input, apiVersion)
    local includeDeviceWithID = makeDeviceLookup(input.deviceIDs)
    local devices, revision, deleted = tdb:getChangedDevices(input.sinceRevision or 0)
    local output = { revision = revision, devices = {} }
    for _, device in ipairs(devices.devices) do
        if includeDeviceWithID == nil or includeDeviceWithID[tostring(device.deviceID)] then
            table.insert(output.devices, toOutputDevice(sc, device, apiVersion))
        end
    end

    if input.sinceRevision then
        output.deletedDeviceIDs = {}
        local deletedDevicesByID = deviceListToDeviceIDs(deleted)
        deletedDevicesByID = makeDeviceLookup(deletedDevicesByID)
        for deletedIDString, deletedID in pairs(deletedDevicesByID) do
            if includeDeviceWithID == nil or includeDeviceWithID[deletedIDString] then
                table.insert(output.deletedDeviceIDs, deletedID)
            end
        end
    end

    return 'OK', output
end

local function innerSetDeviceProperties(tdb, input)
    local device = tdb:getDevice(input.deviceID)
    if not device then
        return 'ErrorUnknownDevice'
    else
        -- Convert the existing property array into a dictionary.
        local dict, removed = {}, {}
        if device.properties then
            for i, prop in ipairs(device.properties) do
                dict[prop.name] = prop.value
            end
        end

        -- Remove properties from the dictionary.
        if input.propertiesToRemove then
            for i, name in ipairs(input.propertiesToRemove) do
                if not isValidPropertyName(name) then
                    return 'ErrorInvalidPropertyName'
                end
                -- Make sure property to be removed exists
                if dict[name] then
                    dict[name] = nil
                    removed[name] = true
                end
            end
        end

        -- Add/modify properties to/in the dictionary.
        if input.propertiesToModify then
            for i, prop in ipairs(input.propertiesToModify) do
                if not isValidPropertyName(prop.name) then
                    return 'ErrorInvalidPropertyName'
                end
                if not isValidPropertyValue(prop.value) then
                    return 'ErrorPropertyValueTooLong'
                end
                dict[prop.name] = prop.value
                removed[prop.name] = nil
            end
        end

        -- Convert the dictionary back to array form.
        device.properties = {}
        for name, value in pairs(dict) do
            table.insert(device.properties, { name = name, value = value })
        end

        -- Check if there are too many properties.
        if device.properties and #device.properties > MAX_PROPERTIES then
            return 'ErrorTooManyProperties'
        end

        -- Add 'no value' entries to the array for all the
        -- properties we want to delete; setDevice interprets
        -- these entries as requests to remove the properties.
        for name, wasRemoved in pairs(removed) do
            table.insert(device.properties, { name = name })
        end
    end

    tdb:setDevice(device, 0)
    return 'OK'
end

local function innerDeleteDevice(tdb, deviceID)
    local device = tdb:getDevice(deviceID)
    if not device then
        return 'ErrorUnknownDevice'
    elseif device.isAuthority or (device.connections and #device.connections > 0) then
        return 'ErrorCannotDeleteDevice'
    else
        tdb:deleteDevice(deviceID) --@ TODO: handle return value from deleteDevice()
    end
    return 'OK'
end

-- RAINIER-9267: Need the ability for a user to rebuild the device list
-- TODO: This function is not unit testable.
local PROP_CACHE_PATH = '/tmp/var/config/ipa/props/'
local function innerClearDeviceList(sc, saveProps)
    -- If flagged, save the device properties so that devidentd can preserve them the next time
    -- each device is discovered.
    if saveProps == true then
        local devices = topodb.db(sc):getAllDevices()
        for _, device in ipairs(devices.devices) do
            -- Don't save the properties if the device has more than 2 known MAC addresses as this
            -- is indicative that this device is corrupted.
            if device.knownMACAddresses and
               #device.knownMACAddresses > 0 and
               #device.knownMACAddresses < 3 and
               device.properties then

                -- Create the directory, which may not exist
                os.execute('mkdir -p '..PROP_CACHE_PATH..' > /dev/null 2>&1')

                -- Iterate over the known mac addresses, saving the property file
                for _, macAddress in ipairs(device.knownMACAddresses) do

                    -- Create the property cache file, naming it w/ the device's mac address
                    local f = io.open(PROP_CACHE_PATH..tostring(macAddress), 'w+')

                    -- Save out the name/value property
                    for _, property in ipairs(device.properties) do
                        f:write(property.name..'='..property.value..'\n')
                    end
                end
            end
        end
    else
        -- Get rid of any old cached properties
        os.execute('rm -rf '..PROP_CACHE_PATH..' > /dev/null 2>&1')
    end

    -- Lock out the database
    os.execute('touch /tmp/ipa/.lockedout > /dev/null 2>&1')

    -- Delete the database
    os.execute('rm -rf /tmp/var/config/ipa/topodb* > /dev/null 2>&1')

    -- Set the reboot event, which is required because the topology database
    -- uses shared memory that must be cleared out.
    sc:reboot()

    return 'OK'
end

---------------------------------------------------------------------------------
-- Outer functions that lock the DB before calling the inner functions.
---------------------------------------------------------------------------------

function _M.getDevices(sc, input, apiVersion)
    local tdb = topodb.db(sc)
    if not tdb:readLock() then
        return 'Error'
    end
    return innerGetDevices(sc, tdb, input, apiVersion)
end

function _M.setDeviceProperties(sc, input)
    local tdb = topodb.db(sc)
    if not tdb:writeLock() then
        return 'Error'
    end
    return innerSetDeviceProperties(tdb, input)
end

function _M.deleteDevice(sc, deviceID)
    local tdb = topodb.db(sc)
    if not tdb:writeLock() then
        return 'Error'
    end
    return innerDeleteDevice(tdb, deviceID)
end

function _M.clearDeviceList(sc, saveProps)
    local tdb = topodb.db(sc)
    if not tdb:writeLock() then
        return 'Error'
    end
    return innerClearDeviceList(sc, saveProps)
end

return _M -- return the module
