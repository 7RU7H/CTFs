#!/usr/bin/lua

--
-- Copyright (c) 2010-2011 Cisco Systems, Inc. and/or its affiliates. All rights
-- reserved.
--
-- Cisco Systems, Inc. retains all right, title and interest (including all
-- intellectual property rights) in and to this computer program, which is
-- protected by applicable intellectual property laws. Unless you have obtained
-- a separate written license from Cisco Systems, Inc., you are not authorized
-- to utilize all or a part of this computer program for any purpose (including
-- reproduction, distribution, modification, and compilation into object code),
-- and you must immediately destroy or return to Cisco Systems, Inc. all copies
-- of this computer program. If you are licensed by Cisco Systems, Inc., your
-- rights to utilize this computer program are limited by the terms of that
-- license. To obtain a license, please contact Cisco Systems, Inc.
--
-- This computer program contains trade secrets owned by Cisco Systems, Inc.
-- and, unless unauthorized by Cisco Systems, Inc. in writing, you agree to
-- maintain the confidentiality of this computer program and related information
-- and to not disclose this computer program and related information to any
-- other person or entity.
--
-- THIS COMPUTER PROGRAM IS PROVIDED AS IS WITHOUT ANY WARRANTIES, AND CISCO
-- SYSTEMS, INC. EXPRESSLY DISCLAIMS ALL WARRANTIES, EXPRESS OR IMPLIED,
-- INCLUDING THE WARRANTIES OF MERCHANTIBILITY, FITNESS FOR A PARTICULAR
-- PURPOSE, TITLE, AND NONINFRINGEMENT.
--

local hdk = require('libhdklua')
local sysctx = require('libsysctxlua')
local device = require('device')
local topodb = require('libtopodblua')
local platform = require('platform')

local sc = sysctx.new()
local tdb = topodb.db(sc)

-- An implicit read lock is acquired by device calls, which cannot be upgraded. Therefore a write lock must be
-- acquired before any calls to device are made.
tdb:writeLock()

local lanMACAddress = platform.getMACAddressFromNetName(sc:get('lan_ifname'))

local function getIPv4Address(sc)
    if sc:getinteger('bridge_mode', 0) > 0 then
        return sc:getevent('ipv4_wan_ipaddr')
    else
        return sc:getevent('lan_ipaddr')
    end
end

local localDev = {
    deviceID = hdk.uuid(device.getUUID(sc)),
    model = {
        deviceType = 'Infrastructure',
        manufacturer = device.getManufacturer(sc),
        modelNumber = device.getModelNumber(sc),
        hardwareVersion = device.getHardwareRevision(sc),
        description = device.getModelDescription(sc)
    },
    unit = {
        serialNumber = device.getSerialNumber(sc),
        firmwareVersion = device.getFirmwareVersion(sc),
        firmwareDate = device.getFirmwareDate(sc)
    },
    isAuthority = true,
    friendlyName = device.getHostName(sc),
    knownMACAddresses = {
        lanMACAddress
    },
    connections = {
        {
            macAddress = lanMACAddress,
            ipAddress = hdk.ipaddress(getIPv4Address(sc))
        }
    }
}

------------------------------------------------------------------------------------------------
-- RAINIER-9267: Check to see if there's any cached custom properties that need to be set
pcall(function(device)
    local PROP_CACHE_FILE = '/tmp/var/config/ipa/props/'..tostring(lanMACAddress)
    local properties = {}
    for line in io.lines(PROP_CACHE_FILE) do
        local token = line:find('=')
        if token then
            table.insert(properties, { name = line:sub(1, token - 1), value = line:sub(token + 1) })
        end
    end
    if #properties > 0 then
        device.properties = properties
        os.remove(PROP_CACHE_FILE)
    end
end, localDev)
------------------------------------------------------------------------------------------------

-- Use a very high confidence value
tdb:setDevice(localDev, 1000000)

-- Use the serial number as an alias
if localDev.unit.serialNumber ~= nil and #localDev.unit.serialNumber > 0 then
    tdb:addDeviceAlias(localDev.deviceID, localDev.unit.serialNumber)
end

-- Commit
sc:commit()