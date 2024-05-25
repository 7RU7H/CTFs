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

--
-- Script to retrieve default device QoS settings from a cloud service and configure them.
--
-- Arguments:
--  $1: (optional) The device identifier to configure settings for. If not supplied, QoS settings
--      will be retrieved and configured for all devices.
--

local sysctx = require('libsysctxlua')
local qos = require('qos')
local platform = require('platform')

local fhDevConsole = io.open('/dev/console', 'w')
platform.registerLoggingCallback(function(level, message)
    -- Fall back to io.stderr if /dev/console is not available
    local fhLogging = fhDevConsole or io.stderr
    fhLogging:write(('QoS auto-prioritize [%s]: %s'):format(level, message))
end)

local sc = sysctx.new()
sc:readlock()

if not qos.getIsQoSAutoPrioritizingEnabled(sc) then
    -- Abort if auto prioritizing is disabled.
    platform.logMessage(platform.LOG_INFO, 'QoS auto prioritizing is disabled\n')
    os.exit()
end

if #qos.getAutoAssignedDeviceRules(sc) >= qos.MAX_AUTO_ASSIGNED_DEVICE_RULES then
    -- Abort if the rule limit has been reached.
    platform.logMessage(platform.LOG_INFO, 'QoS auto assigned rule limit reached; skipping auto prioritizing\n')
    os.exit()
end

sc:rollback() -- release the lock

local result = qos.configureDefaultDevicePriorities(sc, arg[1]) -- must NOT hold any sysctx lock when calling this method
if result then
    platform.logMessage(platform.LOG_ERROR, ('QoS auto prioritizing failed with result "%s"\n'):format(result))
end

sc:commit()

if fhDevConsole then
    fhDevConsole:close()
end
