--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

-- ownednetwork.lua - library to configure owned network state

local _M = {} -- create the module
local sysctx = require('libsysctxlua')

--
-- Get the owned network ID of the router, or nil if the network is not owned.
--
-- input = CONTEXT
--
-- output = OPTIONAL(STRING)
--
function _M.getOwnedNetworkID(sc)
    sc:readlock()
    if sc:get_user_set_network_owner() == true then
        return sc:get_xrac_owned_network_id()
    end
end

--
-- Set the ownership of the network.
--
-- Note: This call makes a blocking HTTP request and, thus,
-- will fail if the context is locked.
--
-- input = CONTEXT, STRING, OPTIONAL(STRING)
--
-- output = NIL_OR_ONE_OF(
--     'ErrorCloudUnavailable',
--     'ErrorUnknownOwnerSession'
-- )
--
function _M.setNetworkOwner(sc, ownerSessionToken, userDefinedFriendlyName)
    if #ownerSessionToken == 0 then
        return 'ErrorUnknownOwnerSession'
    end

    local device = require('device')
    local cloud = require('cloud')
    local platform = require('platform')

    -- Ensure no lock is held when making this call as it is blocking
    assert(not sc:isreadlocked() and not sc:iswritelocked(), 'must not hold the sysctx lock when calling setNetworkOwner')

    -- Create an owned sysctx to read from so that we can rollback
    -- to release the lock before doing the cloud call
    local ownedsc = sysctx.new()
    ownedsc:readlock()
    if not platform.isReady(ownedsc) then
        ownedsc:rollback()  -- release the ctx lock
        return '_ErrorNotReady'
    end
    local host = device.getCloudHost(ownedsc)
    local routerUUID = device.getUUID(ownedsc)
    local serialNumber = device.getSerialNumber(ownedsc)
    local modelNumber = device.getModelNumber(ownedsc)
    -- Default to the device's hostname if no friendly name is supplied.
    local friendlyName = userDefinedFriendlyName or device.getHostName(ownedsc)
    local networkId = ownedsc:get_xrac_owned_network_id()
    local password = ownedsc:get_xrac_owned_network_password()
    local verifyHost = device.getVerifyCloudHost(ownedsc)
    ownedsc:rollback() -- release the lock

    local error, id, password = cloud.setNetworkOwner(
        host,
        routerUUID,
        serialNumber,
        modelNumber,
        friendlyName,
        ownerSessionToken,
        networkId,
        password,
        verifyHost)
    if error then
        return error
    end

    sc:writelock()
    sc:set_xrac_owned_network_id(id)
    sc:set_xrac_owned_network_password(password)
    sc:set_user_set_network_owner(true)
end

--
-- Clear the ownership of the network.
--
-- input = CONTEXT
--
function _M.clearNetworkOwner(sc)
    sc:writelock()

    -- Clear the flag indicating that the user provisioned the device.
    sc:set_user_set_network_owner(false)

    -- If the router has been self-provisioned, we need to retain the network
    -- id/password for the internal remote management account
    if sc:get_self_provisioned() == false then
        sc:set_xrac_owned_network_id(nil)
        sc:set_xrac_owned_network_password(nil)
    end
end

--
-- Get the role associated with a session token.
--
-- Note: This call makes a blocking HTTP request and, thus,
-- will fail if the context is locked.
--
-- input = CONTEXT, STRING
--
-- output = OPTIONAL(STRING)
--
function _M.getUserRole(sc, sessionToken)
    local device = require('device')
    local cloud = require('cloud')

    -- Ensure no lock is held when making this call as it is blocking
    assert(not sc:isreadlocked() and not sc:iswritelocked(), 'must not hold the sysctx lock when calling getUserRole')

    -- Create an owned sysctx to read from so that we can rollback
    -- to release the lock before doing the cloud call
    local ownedsc = sysctx.new()
    ownedsc:readlock()
    local host = device.getCloudHost(ownedsc)
    local ownedNetworkID = _M.getOwnedNetworkID(ownedsc)
    local verifyHost = device.getVerifyCloudHost(ownedsc)
    ownedsc:rollback() -- release lock

    return cloud.getUserRole(host, ownedNetworkID, sessionToken, verifyHost)
end

--
-- Provision the router for remote access by associating the device with a user's cloud account.
--
-- Note: This call makes a blocking HTTP request, and therefore,
-- will fail if the context is locked.
--
-- input = CONTEXT, STRING
--
-- output = NIL_OR_ONE_OF(
--     'ErrorCloudUnavailable',
--     'ErrorInvalidRequest'
--     'ErrorAccessDenied'
--     'ErrorProvisionNotFound'
--     'ErrorProvisionExpired'
-- )
--
function _M.provisionDevice(sc, provisionId)
    local cloud = require('cloud')
    local device = require('device')

    -- Ensure no lock is held when making this call as it is blocking
    assert(not sc:isreadlocked() and not sc:iswritelocked(), 'must not hold the sysctx lock when calling setProvisionId')

    -- Create a local sysctx to read from so we can rollback
    -- to release the lock before doing the cloud call
    local ownedsc = sysctx.new()
    ownedsc:readlock()

    local host = device.getCloudHost(ownedsc)
    local routerUUID = device.getUUID(ownedsc)
    local serialNumber = device.getSerialNumber(ownedsc)
    local modelNumber = device.getModelNumber(ownedsc)
    local friendlyName = ownedsc:get_wifi_default_ssid()
    local token = device.getLinksysToken(ownedsc)
    local networkId = ownedsc:get_xrac_owned_network_id()
    local password = ownedsc:get_xrac_owned_network_password()

    ownedsc:rollback() -- release the lock

    local error, output = cloud.provisionDevice(
        host,
        provisionId,
        routerUUID,
        serialNumber,
        modelNumber,
        friendlyName,
        networkId,
        password,
        token,
        true)
    if error then
        return error
    end

    sc:writelock()
    sc:set_xrac_owned_network_id(output.networkID)
    sc:set_xrac_owned_network_password(output.password)
    sc:set_user_set_network_owner(true)
end

--
-- Self provision the router for remote access.
--
-- Note: This call makes a blocking HTTP request, and therefore,
-- will fail if the context is locked.
--
-- input = CONTEXT
--
-- output = NIL_OR_ONE_OF(
--     'ErrorCloudUnavailable',
--     'ErrorInvalidRequest'
--     'ErrorAccessDenied'
-- )
--
function _M.provisionSelf(sc)
    local cloud = require('cloud')
    local device = require('device')

    -- Ensure no lock is held when making this call as it is blocking
    assert(not sc:isreadlocked() and not sc:iswritelocked(), 'must not hold the sysctx lock when calling provisionSelf')

    -- Create a local sysctx to read from so we can rollback
    -- to release the lock before doing the cloud call
    local ownedsc = sysctx.new()
    ownedsc:readlock()

    local host = device.getCloudHost(ownedsc)
    local serialNumber = device.getSerialNumber(ownedsc)
    local routerUUID = device.getUUID(ownedsc)
    local friendlyName = device.getHostName(ownedsc)
    local token = device.getLinksysToken(ownedsc)
    local networkId = ownedsc:get_xrac_owned_network_id()
    local password = ownedsc:get_xrac_owned_network_password()

    ownedsc:rollback() -- release the lock

    local error, output = cloud.provisionSelf(
        host,
        serialNumber,
        routerUUID,
        friendlyName,
        networkId,
        password,
        token,
        true)
    if error then
        return error
    end

    sc:writelock()
    sc:set_xrac_owned_network_id(output.networkID)
    sc:set_xrac_owned_network_password(output.password)
end


return _M -- return the module
