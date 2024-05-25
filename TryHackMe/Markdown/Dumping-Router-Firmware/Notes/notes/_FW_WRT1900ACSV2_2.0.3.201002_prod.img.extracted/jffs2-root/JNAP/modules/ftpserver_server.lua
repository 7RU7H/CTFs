--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local function GetFTPServerSettings(ctx)
    local platform = require('platform')
    local storage = require('storage')
    local util = require('util')

    local sc = ctx:sysctx()
    local output = storage.getFTPServerSettings(sc)
    output.supportedEncodings = util.setToArray(platform.SUPPORTED_FTP_ENCODINGS)
    table.sort(output.supportedEncodings)
    return 'OK', output
end

local function SetFTPServerSettings(ctx, input)
    local storage = require('storage')

    local sc = ctx:sysctx()
    local error = storage.setFTPServerSettings(sc, input)
    return error or 'OK'
end

local function GetFTPFolders(ctx)
    local storage = require('storage')

    local sc = ctx:sysctx()
    return 'OK', {
       ftpFolders = storage.getFTPFolderArray(sc)
    }
end

local function CreateFTPFolder(ctx, input)
    local storage = require('storage')

    local sc = ctx:sysctx()
    local error = storage.createFTPFolder(sc, input)
    return error or 'OK'
end

local function EditFTPFolder(ctx, input)
    local storage = require('storage')

    local sc = ctx:sysctx()
    local error = storage.editFTPFolder(sc, input)
    return error or 'OK'
end

local function DeleteFTPFolder(ctx, input)
    local storage = require('storage')

    local sc = ctx:sysctx()
    local error = storage.deleteFTPFolder(sc, input.name)
    return error or 'OK'
end

return require('libhdklua').loadmodule('jnap_ftpserver'), {
    ['http://linksys.com/jnap/storage/GetFTPServerSettings'] = GetFTPServerSettings,
    ['http://linksys.com/jnap/storage/SetFTPServerSettings'] = SetFTPServerSettings,
    ['http://linksys.com/jnap/storage/GetFTPFolders'] = GetFTPFolders,
    ['http://linksys.com/jnap/storage/CreateFTPFolder'] = CreateFTPFolder,
    ['http://linksys.com/jnap/storage/EditFTPFolder'] = EditFTPFolder,
    ['http://linksys.com/jnap/storage/DeleteFTPFolder'] = DeleteFTPFolder,
}
