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

local upperLimit = ...
math.randomseed(os.time())
print(os.time() + math.random(upperLimit))
