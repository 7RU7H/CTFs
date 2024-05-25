--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

-- qos.lua - library to configure QoS state.

local hdk = require('libhdklua')
local device = require('device')
local platform = require('platform')
local util = require('util')

local _M = {} -- create the module

_M.MAX_RULE_DESCRIPTION_LENGTH = 64 -- arbitrary limit

_M.MAX_PORT_RANGES = 100 -- syscfg-imposed limit
_M.MAX_APPLICATION_RULES = 100 -- arbitrary limit

_M.MAX_DEVICE_RULES = 100 -- arbitrary limit

_M.MAX_AUTO_ASSIGNED_DEVICE_RULES = 3
_M.MAX_AUTO_ASSIGNED_APPLICATION_RULES = 3

_M.MBITS = 1000000 -- 10^6
_M.KBITS = 1000 -- 10^3

_M.MARKED_DEVICES_FILE = '/var/config/qos_marked_devices' -- location of the marked devices file

_M.MAX_MARKED_DEVICES_TIMESTAMP_SECS = 60*60*24*7*2 -- 2 weeks
_M.MIN_MARKED_DEVICES_TIMESTAMP_SECS = 60*60*24*7   -- 1 week

_M.GEN_MARKED_DEVICES_TIMESTAMP_FUN = function() return os.time() + math.random(_M.MIN_MARKED_DEVICES_TIMESTAMP_SECS, _M.MAX_MARKED_DEVICES_TIMESTAMP_SECS) end

local function toEnabledDisabledString(condition)
    return condition and 'enabled' or 'disabled'
end

local function parseQoSPriority(priority)
    if priority == '$HIGH' then
        return 'High'
    elseif priority == '$NORMAL' then
        return 'Normal'
    elseif priority == '$LOW' then
        return 'Low'
    elseif priority == '$MEDIUM' then
        return 'Medium'
    end

    error(('failed to parse invalid QoS priority "%s"'):format(tostring(priority)))
end

local function serializeQoSPriority(priority)
    if priority == 'High' then
        return '$HIGH'
    elseif priority == 'Normal' then
        return '$NORMAL'
    elseif priority == 'Low' then
        return '$LOW'
    elseif priority == 'Medium' then
        return '$MEDIUM'
    end

    error(('failed to serialize invalid QoS priority "%s"'):format(tostring(priority)))
end

local function parseQoSTrafficType(trafficType)
    if trafficType == 'voice' then
        return 'Voice'
    elseif trafficType == 'video' then
        return 'Video'
    elseif trafficType == 'bk' then
        return 'Background'
    elseif trafficType == 'be' then
        return 'Generic'
    end

    error(('failed to parse invalid QoS traffic type "%s"'):format(tostring(trafficType)))
end

local function serializeQoSTrafficType(trafficType)
    if trafficType == 'Voice' then
        return 'voice'
    elseif trafficType == 'Video' then
        return 'video'
    elseif trafficType == 'Background' then
        return 'bk'
    elseif trafficType == 'Generic' then
        return 'be'
    end

    error(('failed to serialize invalid QoS traffic type "%s"'):format(tostring(trafficType)))
end

local function parseQoSProtocol(protocol)
    if protocol == 'udp' then
        return 'UDP'
    elseif protocol == 'tcp' then
        return 'TCP'
    elseif protocol == 'both' then
        return 'Both'
    end
end

local function serializeQoSProtocol(protocol)
    if protocol == 'UDP' then
        return 'udp'
    elseif protocol == 'TCP' then
        return 'tcp'
    elseif protocol == 'Both' then
        return 'both'
    end
end

--
-- Get whether QoS is enabled.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.getIsEnabled(sc)
    sc:readlock()
    return sc:get_qos_is_enabled();
end

--
-- Set whether QoS is enabled.
--
-- input = CONTEXT, BOOLEAN
--
function _M.setIsEnabled(sc, isEnabled)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    sc:set_qos_is_enabled(isEnabled)
end

--
-- Get whether QoS auto prioritizing is enabled.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.getIsQoSAutoPrioritizingEnabled(sc)
    sc:readlock()
    return sc:get_qos_auto_prioritizing_enabled()
end

--
-- Set whether QoS auto prioritizing is enabled.
--
-- input = CONTEXT, BOOLEAN
--
function _M.setIsQoSAutoPrioritizingEnabled(sc, isEnabled)
    sc:writelock()
    sc:set_qos_auto_prioritizing_enabled(isEnabled)
end

--
-- Get QoS upstream bandwidth, in bps.
--
-- input = CONTEXT
--
-- output = NUMBER
--
function _M.getUpstreamBandwidthbps(sc)
    sc:readlock()
    local upstreamBandwidth = sc:get_qos_wan_upload_speed()
    -- Check if the units are set to Kbps (value 1). Default is Mbps (value 2)
    if sc:get_qos_wan_upload_speed_unit() == 2 then
        upstreamBandwidth = upstreamBandwidth * _M.MBITS
    else
        upstreamBandwidth = upstreamBandwidth * _M.KBITS
    end
    return math.modf(upstreamBandwidth)
end

--
-- Set QoS upstream bandwidth.
--
-- input = CONTEXT, NUMBER
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidUpstreamBandwidth'
-- )
--
function _M.setUpstreamBandwidthbps(sc, upstreamBandwidthbps)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if upstreamBandwidthbps < 0 or math.modf(upstreamBandwidthbps) ~= upstreamBandwidthbps then
        return 'ErrorInvalidUpstreamBandwidth'
    end
    -- Convert units to Kbps and format as a string
    local upstreamBandwidthKbps = upstreamBandwidthbps == 0 and '0' or string.format('%f', upstreamBandwidthbps / _M.KBITS)
    sc:set_qos_wan_upload_speed(upstreamBandwidthKbps)
    sc:set_qos_wan_upload_speed_unit(1)
end

--
-- Get QoS downstream bandwidth, in bps.
--
-- input = CONTEXT
--
-- output = NUMBER
--
function _M.getDownstreamBandwidthbps(sc)
    sc:readlock()
    -- Download speed is stored in Mbps in syscfg.
    return math.modf(sc:get_qos_wan_download_speed() * _M.MBITS)
end

--
-- Set QoS upstream bandwidth.
--
-- input = CONTEXT, NUMBER
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidUpstreamBandwidth'
-- )
--
function _M.setDownstreamBandwidthbps(sc, downstreamBandwidthbps)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if downstreamBandwidthbps < 0 or math.modf(downstreamBandwidthbps) ~= downstreamBandwidthbps then
        return 'ErrorInvalidDownstreamBandwidth'
    end
    -- Convert units to Mbps and format as a string
    local downstreamBandwidthMbps = downstreamBandwidthbps == 0 and '0' or string.format('%f', downstreamBandwidthbps / _M.MBITS)
    sc:set_qos_wan_download_speed(downstreamBandwidthMbps)
end

--
-- Get whether Wireless Multimedia (WMM) is enabled.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.getIsWMMEnabled(sc)
    sc:readlock()
    return sc:get_qos_wmm_enabled()
end

--
-- Set whether Wireless Multimedia (WMM) is enabled.
--
-- input = CONTEXT, BOOLEAN
--
function _M.setIsWMMEnabled(sc, isEnabled)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    sc:set_qos_wmm_enabled(isEnabled)
end

--
-- Get whether wireless acknowledgement is enabled.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.getIsWirelessAcknowledgementEnabled(sc)
    sc:readlock()
    return sc:get_qos_wireless_ack_enabled()
end

--
-- Set whether wireless acknowledgement is enabled.
--
-- input = CONTEXT, BOOLEAN
--
function _M.setIsWirelessAcknowledgementEnabled(sc, isEnabled)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    sc:set_qos_wireless_ack_enabled(isEnabled)
end

--
-- Get the LAN QoS settings
--
-- input = CONTEXT
--
-- output = ARRAY_OF(STRING)
--
function _M.getSwitchPortPriorities(sc)
    sc:readlock()
    local priorities = {}
    for i = 1, #platform.LAN_SWITCH_PORTS do
        local priority = parseQoSPriority(sc:get_qos_switch_port_priority(i))
        if not priority then
            error(('invalid QoS priority "%s" for switch port %d'):format(priority, i))
        end
        table.insert(priorities, i, priority)
    end

    return priorities
end

--
-- Set the LAN QoS settings
--
-- input = CONTEXT, ARRAY_OF(STRING)
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidPortCount'
-- )
--
function _M.setSwitchPortPriorities(sc, priorities)
    if #priorities > #platform.LAN_SWITCH_PORTS then
        return 'ErrorInvalidPortCount'
    end

    sc:writelock()
    for i, priority in ipairs(priorities) do
        sc:set_qos_switch_port_priority(i, serializeQoSPriority(priority))
    end

end

-- Deprecated
local function selectAutoAssignedRules(sc, ns)
    sc:readlock()
    return sc:getboolean(ns..'::'..'auto_assigned', false)
end

local function selectDeviceAutoAssignedRules(sc, deviceIndex)
    sc:readlock()
    return sc:get_qos_device_auto_assigned(deviceIndex)
end

local function selectApplicationAutoAssignedRules(sc, appIndex)
    sc:readlock()
    return sc:get_qos_app_auto_assigned(appIndex)
end

local function getApplicationRules(sc, fnFilter)
    sc:readlock()

    local rules = {}
    for i = 1, sc:get_qos_application_count() do
        local ranges = {}

        local type = sc:get_qos_application_type(i)
        if not type or type == "" then
           type = 'be'
        end
        local trafficType = parseQoSTrafficType(type)
        if nil == trafficType then
            error(('invalid QoS type "%s" in user-defined rule %d'):format(type, i))
        end

        for j = 1, sc:get_qos_port_range_count(i) do
            local protocol = sc:get_qos_application_protocol(i, j)
            local firstPort, lastPort = sc:get_qos_application_port_range(i, j)
            local class = sc:get_qos_application_class(i, j)
            local range = {
                protocol = parseQoSProtocol(protocol),
                firstPort = firstPort,
                lastPort = lastPort,
                priority = parseQoSPriority(class)
            }

            if not range.protocol then
                error(('invalid QoS protocol "%s" in user-defined rule %d:%d'):format(protocol, i, j))
            end
            if not range.firstPort then
                error(('invalid QoS port range "%s" in user-defined rule %d:%d'):format(portRange, i, j))
            end
            if not range.priority then
                error(('invalid QoS class "%s" in user-defined rule %d:%d'):format(class, i, j))
            end
            table.insert(ranges, range)
        end

        if fnFilter(sc, i) then
            table.insert(rules, {
                description = sc:get_qos_application_name(i),
                portRanges = ranges,
                trafficType = trafficType
            })
        end
    end

    return rules
end

--
-- Get all QoS application rules.
--
-- input = CONTEXT
--
-- output = ARRAY_OF({
--     description = STRING,
--     portRanges = ARRAY_OF({
--         protocol = STRING,
--         firstPort = INTEGER,
--         lastPort = OPTIONAL(INTEGER),
--         priority = STRING
--     })
--     trafficType = STRING
-- })
--
function _M.getApplicationRules(sc, fnFilter)
    return getApplicationRules(sc, function() return true end)
end

--
-- Get only auto-assigned QoS application rules.
--
-- input = CONTEXT
--
-- output = ARRAY_OF({
--     description = STRING,
--     portRanges = ARRAY_OF({
--         protocol = STRING,
--         firstPort = INTEGER,
--         lastPort = OPTIONAL(INTEGER),
--         priority = STRING
--     })
--     trafficType = STRING
-- })
--
function _M.getAutoAssignedApplicationRules(sc)
    return getApplicationRules(sc, selectApplicationAutoAssignedRules)
end


local function getDeviceRules(sc, fnFilter)
    sc:readlock()

    local rules = {}
    for i = 1, sc:get_qos_device_count() do

        local class = sc:get_qos_device_class(i)
        local type = sc:get_qos_device_type(i)
        local rule = {
            macAddress = hdk.macaddress(sc:get_qos_device_mac(i)),
            priority = parseQoSPriority(class),
            trafficType = parseQoSTrafficType(type),
            description = sc:get_qos_device_name(i)
        }
        if not rule.priority then
            error(('invalid QoS class "%s" in device rule %d'):format(class, i))
        end
        if not rule.trafficType then
            error(('invalid QoS type "%s" in user-defined rule %d'):format(type, i))
        end
        if fnFilter(sc, i) then
            table.insert(rules, rule)
        end
    end

    return rules
end

--
-- Get all QoS device rules.
--
-- input = CONTEXT
--
-- output = ARRAY_OF({
--     macAddress = MACADDRESS,
--     priority = STRING,
--     trafficType = STRING,
--     description = STRING
-- })
--
function _M.getDeviceRules(sc)
    return getDeviceRules(sc, function() return true end)
end

--
-- Get only auto-assigned QoS device rules.
--
-- input = CONTEXT
--
-- output = ARRAY_OF({
--     macAddress = MACADDRESS,
--     priority = STRING,
--     trafficType = STRING,
--     description = STRING
-- })
--
function _M.getAutoAssignedDeviceRules(sc)
    return getDeviceRules(sc, selectDeviceAutoAssignedRules)
end

--
-- Set the QoS application rules.
--
-- input = CONTEXT, ARRAY_OF({
--     description = STRING,
--     portRanges = ARRAY_OF({
--         protocol = STRING,
--         firstPort = INTEGER,
--         lastPort = OPTIONAL(INTEGER),
--         priority = STRING
--     })
--     trafficType = STRING
-- })
--
-- output = NIL_OR_ONE_OF(
--     'ErrorDescriptionTooLong',
--     'ErrorConflictingPortRanges',
--     'ErrorTooManyApplicationRules',
--     'ErrorConflictingApplicationRules'
-- )
--
function _M.setApplicationRules(sc, rules)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    if #rules > _M.MAX_APPLICATION_RULES then
        return 'ErrorTooManyApplicationRules'
    end

    local udpRanges = {}
    local tcpRanges = {}
    local function checkForPortRangeOverlap(ranges, first, last)
        for _, range in ipairs(ranges) do
            if (first >= range.first and first <= range.last) or
                (last >= range.first and last <= range.last) then
                return true
            end
        end
        return false
    end


    for i, rule in ipairs(rules) do
        if #rule.description > _M.MAX_RULE_DESCRIPTION_LENGTH then
            return 'ErrorDescriptionTooLong'
        end

        if #rule.portRanges > _M.MAX_PORT_RANGES then
            return 'ErrorTooManyPortRanges'
        end

        sc:set_qos_application_type(i, serializeQoSTrafficType(rule.trafficType))

        -- Setting the QoS application name does not require any service restart (it is only used by this layer)
        sc:set_qos_application_name(i, rule.description)

        -- Add port ranges, ensuring there are no conflicts
        for j, range in ipairs(rule.portRanges) do
            local firstPort = range.firstPort
            local lastPort = range.lastPort or range.firstPort
            if not util.isValidPort(firstPort) or not util.isValidPort(lastPort) or (firstPort > lastPort) then
                return 'ErrorInvalidPortRange'
            end

            if range.protocol == 'TCP' or range.protocol == 'Both' then
                if checkForPortRangeOverlap(tcpRanges, firstPort, lastPort) then
                    return 'ErrorConflictingApplicationRules'
                end
                table.insert(tcpRanges, { first = firstPort, last = lastPort })
            end
            if range.protocol == 'UDP' or range.protocol == 'Both' then
                if checkForPortRangeOverlap(udpRanges, firstPort, lastPort) then
                    return 'ErrorConflictingApplicationRules'
                end
                table.insert(udpRanges, { first = firstPort, last = lastPort })
            end

            sc:set_qos_application_protocol(i, j, serializeQoSProtocol(range.protocol))
            sc:set_qos_application_port_range(i, j, firstPort, lastPort)
            sc:set_qos_application_class(i, j, serializeQoSPriority(range.priority))
        end
        sc:set_qos_port_range_count(i, #rule.portRanges)
    end

    sc:set_qos_application_count(#rules)
end

--
-- Set the QoS device rules.
--
-- input = CONTEXT, ARRAY_OF({
--     macAddress = MACADDRESS,
--     priority = STRING,
--     trafficType = STRING,
--     description = STRING,
--     autoAssigned = OPTIONAL(BOOLEAN)
-- })
--
-- output = NIL_OR_ONE_OF(
--     'ErrorDescriptionTooLong',
--     'ErrorConflictingDeviceRules',
--     'ErrorTooManyDeviceRules',
--     'ErrorDescriptionTooLong'
-- )
--
function _M.setDeviceRules(sc, rules)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    if #rules > _M.MAX_DEVICE_RULES then
        return 'ErrorTooManyDeviceRules'
    end

    local devices = {}

    for i, rule in ipairs(rules) do
        if #rule.description > _M.MAX_RULE_DESCRIPTION_LENGTH then
            return 'ErrorDescriptionTooLong'
        end

        -- Does a rule already exist for this device?
        if devices[tostring(rule.macAddress)] then
            return 'ErrorConflictingDeviceRules'
        end

        devices[tostring(rule.macAddress)] = true

        sc:set_qos_device_name(i, rule.description)
        sc:set_qos_device_mac(i, tostring(rule.macAddress))
        sc:set_qos_device_class(i, serializeQoSPriority(rule.priority))
        sc:set_qos_device_type(i, serializeQoSTrafficType(rule.trafficType))

        -- The auto-assigned value is merely for tracking the number of auto-assigned rules and does not
        -- affect the underlying platform QoS services.
        if rule.autoAssigned then
            sc:set_qos_device_auto_assigned(i, rule.autoAssigned)
        end
    end

    sc:set_qos_device_count(#rules)
end

--
-- Retrieve and configure the 'default' device priorities from the cloud
-- for all devices in the device list, or the device matching a specified
-- device ID
--
-- input = CONTEXT, OPTIONAL(STRING)
--
-- output = NIL_OR_ONE_OF(
--     'ErrorCloudUnavailable',
--     'ErrorTooManyDeviceRules'
-- )
--
function _M.configureDefaultDevicePriorities(sc, deviceIdToUpdate)

    -- Ensure no lock is held when making this call as it is blocking
    assert(not sc:isreadlocked() and not sc:iswritelocked(), 'must not hold the sysctx lock when calling configureDefaultDevicePriorities')

    local device = require('device')
    local topodb = require('libtopodblua')
    local ownednetwork = require('ownednetwork')
    local sysctx = require('libsysctxlua')

    -- Create an owned sysctx to read from so that we can rollback
    -- to release the lock before doing the cloud call
    local ownedsc = sysctx.new()
    ownedsc:readlock()

    local existingDeviceRules = _M.getDeviceRules(ownedsc)

    -- Compile a map of devices with previously existing rules.
    local existingDevices = {}
    for _, existingRule in ipairs(existingDeviceRules) do
        existingDevices[tostring(existingRule.macAddress)] = true
    end

    -- Compile a map of "marked" devices (meaning a describe device call has been made for them)
    local markedDevices = {}
    local markedDevicesFile = io.open(_M.MARKED_DEVICES_FILE, 'r')
    if markedDevicesFile then
        local markedDeviceIds = require('libhdkjsonlua').parse(markedDevicesFile:read('*a'))
        if markedDeviceIds then
            for _, markedDeviceId in ipairs(markedDeviceIds) do
                markedDevices[markedDeviceId] = true
            end
        end
    end

    local devicesToPrioritize = {}
    local count = 0

    local tdb = topodb.db(ownedsc)
    local deviceList, revision = tdb:getAllDevices()
    for _, deviceIter in ipairs(deviceList.devices) do
        local deviceId = tostring(deviceIter.deviceID)
        if not deviceIter.isAuthority and (nil == deviceIdToUpdate or deviceIdToUpdate == deviceId) then

            -- Don't retrieve priorities for devices already marked.
            if not markedDevices[deviceId] then

                markedDevices[deviceId] = true

                -- Don't retrieve priorities for devices with existing rules.
                local ruleExists = true
                for _, macAddress in ipairs(deviceIter.knownMACAddresses) do
                    -- All of the devices known MAC addresses must have an associated rule to avoid the query.
                    ruleExists = existingDevices[tostring(macAddress)] and ruleExists
                end
                if not ruleExists then
                    devicesToPrioritize[deviceId] = {
                        deviceID = deviceIter.deviceID,
                        macAddresses = deviceIter.knownMACAddresses,
                        manufacturer = deviceIter.model and deviceIter.model.manufacturer,
                        modelNumber = deviceIter.model and deviceIter.model.modelNumber,
                        serialNumber = deviceIter.unit and deviceIter.unit.serialNumber,
                        hardwareVersion = deviceIter.model and deviceIter.model.hardwareVersion,
                        firmwareVersion = deviceIter.unit and deviceIter.unit.firmwareVersion,
                        type = deviceIter.model and deviceIter.model.deviceType,
                        description = deviceIter.model and deviceIter.model.description or deviceIter.friendlyName,
                        operatingSystem = deviceIter.unit and deviceIter.unit.operatingSystem
                    }
                    count = count + 1
                else
                    -- Device already has a rule, don't request a rule from the cloud for it.
                    platform.logMessage(platform.LOG_INFO, ('Rule for device %s already exists; omitting from auto-prioritized rule query\n'):format(tostring(deviceId)))
                end
            else
                -- Device already has been marked, don't request a rule from the cloud for it.
                platform.logMessage(platform.LOG_INFO, ('Device %s has already been marked; omitting from auto-prioritized rule query\n'):format(tostring(deviceId)))
            end
        end
    end

    local cloudHost = device.getCloudHost(ownedsc)
    local verifyHost = device.getVerifyCloudHost(ownedsc)
    local networkID = ownednetwork.getOwnedNetworkID(ownedsc)
    local firmwareVersion = device.getFirmwareVersion(ownedsc)
    local modelNumber = device.getModelNumber(ownedsc)

    local logVerbose = ownedsc:getnumber('log_level', '1') > 1

    ownedsc:rollback() -- release the lock

    -- Skip describe devices call if we don't have any devices to prioritize or we don't have a networkID
    if count > 0 then
        if networkID then
        local cloud = require('cloud')

        local describedDevices, response = cloud.describeDevices(cloudHost, devicesToPrioritize, firmwareVersion, modelNumber, networkID, 'QOS',  verifyHost, logVerbose)
        if describedDevices ~= nil then

            -- Push the priorities into the system using the lualib APIs.

            sc:writelock()
            local existingDeviceRules = _M.getDeviceRules(sc)

            -- Determine how many more auto-assigned rules are allowed.
            local allowed = _M.MAX_AUTO_ASSIGNED_DEVICE_RULES - #_M.getAutoAssignedDeviceRules(sc)

            -- Compile a map of devices with previously existing rules.
            local existingDevices = {}
            for _, existingRule in ipairs(existingDeviceRules) do
                existingDevices[tostring(existingRule.macAddress)] = true
            end

            for _, describedDevice in pairs(describedDevices) do
                -- Require both a priority and traffic type for a QoS rule.
                -- Use the serialize methods to verify the values are valid.
                local invalidParameters = {}
                if not pcall(serializeQoSPriority, describedDevice.defaultQosPriority) then
                    table.insert(invalidParameters, ('defaultQosPriority: %s'):format(tostring(describedDevice.defaultQosPriority)))
                end
                if not pcall(serializeQoSTrafficType, describedDevice.defaultQosTrafficType) then
                    table.insert(invalidParameters, ('defaultQosTrafficType: %s'):format(tostring(describedDevice.defaultQosTrafficType)))
                end
                if #invalidParameters == 0 then
                    -- Add a rule for each of the device's MAC addresses, if one does not already exist.
                    local deviceToPrioritize = devicesToPrioritize[tostring(describedDevice.deviceID)]
                    if deviceToPrioritize then
                        for _, macAddress in ipairs(deviceToPrioritize.macAddresses) do
                            local mac = tostring(macAddress)
                            if not existingDevices[mac] then
                                if allowed > 0 then
                                    table.insert(existingDeviceRules, {
                                        macAddress = macAddress,
                                        description = ('auto-assigned rule for %s'):format(tostring(describedDevice.deviceID)),
                                        priority = describedDevice.defaultQosPriority,
                                        trafficType = describedDevice.defaultQosTrafficType,
                                        autoAssigned = true
                                    })

                                    platform.logMessage(platform.LOG_VERBOSE, ('Adding auto-prioritized rule for %s\n'):format(mac))
                                    local result = _M.setDeviceRules(sc, existingDeviceRules)
                                    if result then
                                        return result
                                    end

                                    allowed = allowed - 1
                                else
                                    platform.logMessage(platform.LOG_INFO, ('Maximum allowed auto-assigned rules applied; ignoring rule for %s\n'):format(tostring(describedDevice.deviceID)))
                                end
                            end
                        end
                    else
                        platform.logMessage(platform.LOG_WARNING, ('Web service returned unknown device ID "%s"\n'):format(tostring(describedDevice.deviceID)))
                    end
                end
            end
        else
            platform.logMessage(platform.LOG_ERROR, ('Failed to retrieve QoS priorities for devices with error: "%s"\n'):format(response or ''))
            return 'ErrorCloudUnavailable'
        end

        -- If there's currently no qos_auto_prioritize_timestamp, generate a new one
        sc:writelock()
        if not sc:get_qos_auto_prioritize_timestamp() then
            math.randomseed(os.time())
            local newTimestamp = _M.GEN_MARKED_DEVICES_TIMESTAMP_FUN()
            platform.logMessage(platform.LOG_INFO, ('Set next marked devices delete timestamp to %d\n'):format(newTimestamp))
            sc:set_qos_auto_prioritize_timestamp(newTimestamp)
        end

        -- Convert marked devices map to an array
        local markedDevicesArray = {}
        for k, _ in pairs(markedDevices) do
            table.insert(markedDevicesArray, k)
        end

        -- Save marked devices out if we have any
        if #markedDevicesArray > 0 then
            local markedDevicesFile = io.open(_M.MARKED_DEVICES_FILE, 'w+')
            if markedDevicesFile then
                markedDevicesFile:write(require('libhdkjsonlua').stringify(markedDevicesArray))
            end
        end
    else
        platform.logMessage(platform.LOG_INFO, ('No networkID; skipping auto-priority update\n'))
    end
    else
        platform.logMessage(platform.LOG_INFO, ('No unpriortized devices found; skipping auto-priority update\n'))
    end
end

return _M -- return the module
