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

local sysctx = require('libsysctxlua')
local topodb = require('libtopodblua')

local sc = sysctx.new()
sc:writelock()

local utc = os.date('!*t')
local year = utc.year
local month = utc.month - 2
if month < 1 then
    year = year - 1
    month = month + 12
end
local prune = string.format('%d-%02d', year, month)

local tdb = topodb.db(sc)
local devices = tdb:getAllDevices()
for _, device in ipairs(devices.devices) do
    if not device.isAuthority and
        (not device.connections or #device.connections == 0) and
        device.lastOnline and
        device.lastOnline <= prune then
        tdb:deleteDevice(device.deviceID)
    end
end
sc:commit()
