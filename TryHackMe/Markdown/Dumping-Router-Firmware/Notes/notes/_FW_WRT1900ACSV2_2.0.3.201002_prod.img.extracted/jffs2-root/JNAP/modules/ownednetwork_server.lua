--
-- 2019 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author: hsulliva $
-- $DateTime: 2020/02/06 14:32:09 $
-- $Id: //depot-irv/lego/branches/wrt_platform_sp1/lego_overlay/proprietary/jnap/modules/ownednetwork/ownednetwork_server.lua#3 $
--

local function SetNetworkOwner(ctx, input)
    local ownednetwork = require('ownednetwork')

    local sc = ctx:sysctx()

    -- Ensure no lock is held when making this call as it is blocking
    assert(not sc:isreadlocked() and not sc:iswritelocked(), 'must not hold the sysctx lock when calling SetNetworkOwner')

    local error = ownednetwork.setNetworkOwner(sc, input.ownerSessionToken, input.friendlyName)
    if error then
        return error
    end
    return 'OK', {
        ownedNetworkID = ownednetwork.getOwnedNetworkID(sc)
    }
end

local function ClearNetworkOwner(ctx)
    if ctx:isremotecall() then
        return "ErrorDisallowedRemoteCall"
    end

    local ownednetwork = require('ownednetwork')

    local sc = ctx:sysctx()

    local error = ownednetwork.clearNetworkOwner(sc)
    return error or 'OK'
end

local function GetOwnedNetworkID(ctx)
    local ownednetwork = require('ownednetwork')

    local sc = ctx:sysctx()
    return 'OK', {
        ownedNetworkID = ownednetwork.getOwnedNetworkID(sc)
    }
end

local function IsOwnedNetwork(ctx)
    local ownednetwork = require('ownednetwork')

    local sc = ctx:sysctx()
    return 'OK', {
        isOwnedNetwork = ownednetwork.getOwnedNetworkID(sc) and true or false
    }
end

return require('libhdklua').loadmodule('jnap_ownednetwork'), {
    ['http://linksys.com/jnap/ownednetwork/SetNetworkOwner'] = SetNetworkOwner,
    ['http://linksys.com/jnap/ownednetwork/ClearNetworkOwner'] = ClearNetworkOwner,
    ['http://linksys.com/jnap/ownednetwork/GetOwnedNetworkID'] = GetOwnedNetworkID,
    ['http://linksys.com/jnap/ownednetwork/IsOwnedNetwork'] = IsOwnedNetwork
}
