--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local function GetUPnPMediaServerSettings(ctx)
    local storage = require('storage')

    local sc = ctx:sysctx()
    local output = storage.getUPnPMediaServerSettings(sc)
    output.lastScanTime = storage.getUPnPMediaServerLastScanTime(sc)
    return 'OK', output
end

local function SetUPnPMediaServerSettings(ctx, input)
    local storage = require('storage')

    local sc = ctx:sysctx()
    local error = storage.setUPnPMediaServerSettings(sc, input)
    return error or 'OK'
end

local function GetUPnPMediaFolders(ctx)
    local storage = require('storage')

    local sc = ctx:sysctx()
    return 'OK', {
        upnpMediaFolders = storage.getUPnPMediaFolderArray(sc)
    }
end

local function CreateUPnPMediaFolder(ctx, input)
    local storage = require('storage')

    local sc = ctx:sysctx()
    local error = storage.createUPnPMediaFolder(sc, input)
    return error or 'OK'
end

local function EditUPnPMediaFolder(ctx, input)
    local storage = require('storage')

    local sc = ctx:sysctx()
    local error = storage.editUPnPMediaFolder(sc, input)
    return error or 'OK'
end

local function DeleteUPnPMediaFolder(ctx, input)
    local storage = require('storage')

    local sc = ctx:sysctx()
    local error = storage.deleteUPnPMediaFolder(sc, input.name)
    return error or 'OK'
end

local function TriggerUPnPMediaFolderScan(ctx)
    local storage = require('storage')

    local sc = ctx:sysctx()
    storage.triggerUPnPMediaFolderScan(sc)
    return 'OK'
end

local function IsMediaThresholdReached(ctx)
    local storage = require('storage')

    local sc = ctx:sysctx()
    return 'OK', {
        isReached = storage.isMediaThresholdReached(sc)
    }
end

return require('libhdklua').loadmodule('jnap_upnpmediaserver'), {
    ['http://linksys.com/jnap/storage/GetUPnPMediaServerSettings'] = GetUPnPMediaServerSettings,
    ['http://linksys.com/jnap/storage/SetUPnPMediaServerSettings'] = SetUPnPMediaServerSettings,
    ['http://linksys.com/jnap/storage/GetUPnPMediaFolders'] = GetUPnPMediaFolders,
    ['http://linksys.com/jnap/storage/CreateUPnPMediaFolder'] = CreateUPnPMediaFolder,
    ['http://linksys.com/jnap/storage/EditUPnPMediaFolder'] = EditUPnPMediaFolder,
    ['http://linksys.com/jnap/storage/DeleteUPnPMediaFolder'] = DeleteUPnPMediaFolder,
    ['http://linksys.com/jnap/storage/TriggerUPnPMediaFolderScan'] = TriggerUPnPMediaFolderScan,
    ['http://linksys.com/jnap/storage/IsMediaThresholdReached'] = IsMediaThresholdReached,
}
