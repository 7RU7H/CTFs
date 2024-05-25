--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local function GetSMBServerSettings(ctx)
    local storage = require('storage')

    local sc = ctx:sysctx()
    local output = storage.getSMBServerSettings(sc)
    return 'OK', output
end

local function SetSMBServerSettings(ctx, input)
    local storage = require('storage')

    local sc = ctx:sysctx()
    local error = storage.setSMBServerSettings(sc, input)
    return error or 'OK'
end

local function GetSMBFolders(ctx)
    local storage = require('storage')

    local sc = ctx:sysctx()
    return 'OK', {
        smbFolders = storage.getSMBFolderArray(sc)
    }
end

local function CreateSMBFolder(ctx, input)
    local storage = require('storage')

    local sc = ctx:sysctx()
    local error = storage.createSMBFolder(sc, input)
    return error or 'OK'
end

local function EditSMBFolder(ctx, input)
    local storage = require('storage')

    local sc = ctx:sysctx()
    local error = storage.editSMBFolder(sc, input)
    return error or 'OK'
end

local function DeleteSMBFolder(ctx, input)
    local storage = require('storage')

    local sc = ctx:sysctx()
    local error = storage.deleteSMBFolder(sc, input.name)
    return error or 'OK'
end

return require('libhdklua').loadmodule('jnap_smbserver'), {
    ['http://linksys.com/jnap/storage/GetSMBServerSettings'] = GetSMBServerSettings,
    ['http://linksys.com/jnap/storage/SetSMBServerSettings'] = SetSMBServerSettings,
    ['http://linksys.com/jnap/storage/GetSMBFolders'] = GetSMBFolders,
    ['http://linksys.com/jnap/storage/CreateSMBFolder'] = CreateSMBFolder,
    ['http://linksys.com/jnap/storage/EditSMBFolder'] = EditSMBFolder,
    ['http://linksys.com/jnap/storage/DeleteSMBFolder'] = DeleteSMBFolder,
}
