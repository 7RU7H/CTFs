--
-- 2019 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author: hsulliva $
-- $DateTime: 2020/02/06 14:32:09 $
-- $Id: //depot-irv/lego/branches/wrt_platform_sp1/lego_overlay/proprietary/jnap/lualib/storage.lua#3 $
--

-- storage.lua - library to access local storage and filesharing settings.

local util = require('util')
local platform = require('platform')
local lfs = require('lfs')

local _M = {} -- create the module


-- Constant for the maximum number items shared via the media server
_M.MEDIA_SERVER_MAXITEMS = 80000

-- Constants for calling making media server RPC requests
_M.MEDIA_SERVER_PORT = 9999
_M.MEDIA_SERVER_RPC_TIMEOUT = 5


local SMB_SHARE_INFO = {
    maxFoldersProp = 'MAX_SMB_FOLDERS',
    configFileNameProp = 'SMB_SHARE_FILE'
}

local UPNP_MEDIA_SHARE_INFO = {
    maxFoldersProp = 'MAX_UPNP_MEDIA_FOLDERS',
    configFileNameProp = 'UPNP_MEDIA_SHARE_FILE'
}

local FTP_SHARE_INFO = {
    maxFoldersProp = 'MAX_FTP_FOLDERS',
    configFileNameProp = 'FTP_SHARE_FILE'
}

local function isValidWorkgroup(workgroup)
    return #workgroup >= 1 and #workgroup <= 15 and workgroup:find('^[a-zA-Z0-9- ]*$') ~= nil
end

local function isValidUPnPMediaServerName(name)
    return isValidWorkgroup(name)
end

local function isValidFTPServerName(name)
    return isValidWorkgroup(name)
end

local function isValidSharedFolderName(name)
    return #name >= 1 and #name <= 22 and name:find('^[a-zA-Z0-9- ]*$') ~= nil
end

function _M.isValidUserName(name)
    return #name >= 1 and #name <= 20 and name:find('^[a-zA-Z0-9-_]*$') ~= nil
end

local function isValidUserFullName(fullName)
    return #fullName >= 0 and #fullName <= 63 and fullName:find('^[a-zA-Z0-9- ]*$') ~= nil
end

local function isValidUserDescription(description)
    return isValidUserFullName(description)
end

function _M.isValidUserPassword(password)
    return #password >= 4 and #password <= 64 and password:find('^[a-zA-Z0-9]*$') ~= nil
end

local function isValidGroupName(name)
    return #name >= 1 and #name <= 12 and name:find('^[a-zA-Z0-9-_]*$') ~= nil
end

local function isValidGroupDescription(description)
    return isValidUserDescription(description)
end

function _M.parseFileSystem(fileSystem)
    if fileSystem == 'fat32' then
        return 'FAT32'
    elseif fileSystem == 'fat16' then
        return 'FAT16'
    elseif fileSystem == 'vfat' then
        return 'VFAT'
    elseif fileSystem == 'ext2' then
        return 'EXT2'
    elseif fileSystem == 'ext3' then
        return 'EXT3'
    elseif fileSystem == 'ext4' then
        return 'EXT4'
    elseif fileSystem == 'ntfs' then
        return 'NTFS'
    elseif fileSystem == 'hfs' then
        return 'HFS'
    elseif fileSystem == 'hfs+' then
        return 'HFS+'
    elseif fileSystem == 'hfsx' then
        return 'HFSX'
    elseif fileSystem == 'hfsplus' then
        return 'HFSPLUS'
    elseif fileSystem == 'apfs' then
        return 'APFS'
    else
        return 'Unsupported'
    end
end

function _M.parsePartitionTableFormat(partTableFormat)
    if partTableFormat == 'gpt' then
        return 'GPT'
    elseif partTableFormat == 'msdos' then
        return 'MSDOS'
    else
        return 'Unsupported'
    end
end

function _M.parseStorageType(storageType)
    if storageType == 'usb' then
        return 'USB'
    elseif storageType == 'esata' then
        return 'ESATA'
    else
        return 'Unsupported'
    end
end

--
-- Remove a storage drive.
--
-- input = CONTEXT, STRING ex. '/dev/sda'
--
-- output = NIL_OR_ONE_OF(
--     'ErrorUnknownPartition'
-- )
--
function _M.RemoveStorageDevice(sc, deviceName)
    sc:writelock()
    local isStorageDeviceKnown = false
    for partitionName,partition in pairs(platform.getUSBInfos(sc)) do
        if deviceName == '/dev/'..partition.dname then
            isStorageDeviceKnown = true
            break
        end
    end
    if not isStorageDeviceKnown then
        return 'ErrorUnknownStorageDevice'
    end

    -- Return not ready if another drive is currently being removed.
    if not platform.ejectUSBDrive(sc, deviceName) then
        return '_ErrorNotReady'
    end
end

--
-- Get the SMB server settings of the local device.
--
-- input = CONTEXT
--
-- output = {
--     workgroup = STRING,
--     isAnonymousWriteEnabled = BOOLEAN
-- }
--
function _M.getSMBServerSettings(sc)
    sc:readlock()
    return {
        workgroup = sc:get_shared_folder_workgroup(),
        isAnonymousWriteEnabled = sc:get_shared_folder_anon_access_enabled();
        maxSMBFolders = platform[SMB_SHARE_INFO.maxFoldersProp]
    }
end

--
-- Set the SMB server settings of the local device.
--
-- input = CONTEXT, {
--     workgroup = STRING,
--     isAnonymousWriteEnabled = BOOLEAN
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidWorkgroup'
-- )
--
function _M.setSMBServerSettings(sc, settings)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if not isValidWorkgroup(settings.workgroup) then
        return 'ErrorInvalidWorkgroup'
    end

    sc:set_shared_folder_workgroup(settings.workgroup)
    sc:set_shared_folder_anon_access_enabled(settings.isAnonymousWriteEnabled)
end

local function parseSharedFolderGroupList(s)
    local groups = {}
    if s then
        for group in s:gmatch('([^,]+),') do
            table.insert(groups, group)
        end
        table.insert(groups, s:match(',([^,]+)$'))
        table.sort(groups)
    end
    return groups
end

local function serializeSharedFolderGroupList(l)
    return table.concat(l, ',')..','
end

--
-- Get the UPnP media server settings of the local device.
--
-- input = CONTEXT
--
-- output = {
--     serverName = STRING,
--     isEnabled = BOOLEAN,
--     autoScanIntervalMinutes = NUMBER
-- }
--
function _M.getUPnPMediaServerSettings(sc)
    sc:readlock()
    return {
        serverName = sc:get_media_server_name(),
        isEnabled = sc:get_media_server_enabled(),
        autoScanIntervalMinutes = sc:get_media_server_scan_interval(),
        maxUPnPMediaFolders = platform[UPNP_MEDIA_SHARE_INFO.maxFoldersProp]
    }
end

--
-- Set the UPnP media server settings of the local device.
--
-- input = CONTEXT, {
--     serverName = STRING,
--     isEnabled = BOOLEAN,
--     autoScanIntervalMinutes = NUMBER
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidServerName',
--     'ErrorInvalidAutoScanIntervalMinutes'
-- )
--
function _M.setUPnPMediaServerSettings(sc, settings)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if not isValidUPnPMediaServerName(settings.serverName) then
        return 'ErrorInvalidServerName'
    end
    if settings.autoScanIntervalMinutes < 0 then
        return 'ErrorInvalidAutoScanIntervalMinutes'
    end

    sc:set_media_server_name(settings.serverName)
    sc:set_media_server_enabled(settings.isEnabled)
    sc:set_media_server_scan_interval(settings.autoScanIntervalMinutes)
end

--
-- Get the UPnP media server last scan time, if any.
--
-- input = CONTEXT
--
-- output = OPTIONAL(NUMBER)
--
function _M.getUPnPMediaServerLastScanTime(sc)
    sc:readlock()
    local t = sc:get_media_server_last_scan_time()
    if t then
        local h, m, s = t:match('^(%d+):(%d+):(%d+)$')
        if h and m and s then
            -- Get the current local time for the year, month, day and timezone offset
            local y, mo, d, lh, lm, ls, off, offh, offm = platform.getCurrentLocalTime(sc):match('^(%d+)[-](%d+)[-](%d+)[T](%d+)[:](%d+)[:](%d+)([+|-])(%d%d)(%d%d)')
            if y and mo and d and lh and lm and ls and off and offh and offm then
                local time = {}
                time.year = tonumber(y)
                time.month = tonumber(mo)
                time.day = tonumber(d)
                time.hour = tonumber(h)
                time.min = tonumber(m)
                time.sec = tonumber(s)

                -- If the last_scan_time is greater than the current time, then substract one day
                -- NOTE: This assumes that the last scan time was no more than 24 hours ago.
                local scanTime = os.time(time)
                if (h * 60 * 60 + m * 60 + s) > (lh * 60 * 60 + lm * 60 + ls) then
                    scanTime = scanTime - 24 * 60 * 60
                end

                -- Compute the offset seconds
                local offs = tonumber(offh) * 60 * 60 + tonumber(offm) * 60
                offs = (off == '-') and offs * -1 or offs

                -- Convert local to GMT and return
                return (scanTime - offs)
            end
        end
    end
end

--
-- Get the FTP server settings of the local device.
--
-- input = CONTEXT
--
-- output = {
--     serverName = STRING,
--     isEnabled = BOOLEAN,
--     isAnonymousReadAccessEnabled = BOOLEAN,
--     port = NUMBER,
--     encoding = STRING
-- }
--
function _M.getFTPServerSettings(sc)
    sc:readlock()
    local enc = sc:get_ftp_server_encoding()
    local encoding
    for name, value in pairs(platform.SUPPORTED_FTP_ENCODINGS) do
        if value == enc then
            encoding = name
            break
        end
    end
    return {
        serverName = sc:get_ftp_server_name(),
        isEnabled = sc:get_ftp_server_enabled(),
        isAnonymousReadAccessEnabled = sc:get_ftp_anon_access_enabled(),
        port = sc:get_ftp_server_port(),
        encoding = encoding,
        maxFTPFolders = platform[FTP_SHARE_INFO.maxFoldersProp]
    }
end

--
-- Set the FTP server settings of the local device.
--
-- input = CONTEXT, {
--     serverName = STRING,
--     isEnabled = BOOLEAN,
--     isAnonymousReadAccessEnabled = BOOLEAN,
--     port = NUMBER,
--     encoding = STRING
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidServerName',
--     'ErrorInvalidPort',
--     'ErrorUnsupportedEncoding'
-- )
--
function _M.setFTPServerSettings(sc, settings)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if not isValidFTPServerName(settings.serverName) then
        return 'ErrorInvalidServerName'
    end
    if not util.isValidPort(settings.port) then
        return 'ErrorInvalidPort'
    end
    local enc = platform.SUPPORTED_FTP_ENCODINGS[settings.encoding]
    if not enc then
        return 'ErrorUnsupportedEncoding'
    end

    -- When enabling/disabling the FTP server, the firewall must be restarted to add/remove the WAN to self rule
    -- allowing FTP traffic through the WAN interface to the router.
    sc:set_ftp_server_enabled(settings.isEnabled)
    sc:set_ftp_server_name(settings.serverName)
    sc:set_ftp_anon_access_enabled(settings.isAnonymousReadAccessEnabled)
    sc:set_ftp_server_port(settings.port)
    sc:set_ftp_server_encoding(enc)
end

--
-- Sorting function for shares.
--
local function sortShares(lhs, rhs)
    if lhs.name ~= rhs.name then
        return lhs.name < rhs.name
    else
        return lhs.partitionName < rhs.partitionName
    end
end
--
-- Read a shared configuration file from disk.
--
-- input = STRING
--
-- output = ARRAY_OF({
--     name = STRING,
--     path = STRING,
--     isReadOnly = BOOLEAN,
--     groupsWithPermission = ARRAY_OF(STRING)
-- })
--
local function readShareConfigFile(fileName)
    local shares = {}
    local file = io.open(fileName, 'r')
    if file then
        for line in file:lines() do
            local index, name, drive, label, folder, readonly, groups = line:match('^{index:(%d+)|name:"([^"]+)"|drive:"([^"]+)"|label:"([^"]+)"|folder:"([^"]+)"|readonly:(%d)|groups:"([^"]+)"}')
            if index then
                local groupsWithPermission = {}
                for _, group in ipairs(util.splitOnDelimiter(groups, ',', true)) do
                    if #group > 0 then
                        table.insert(groupsWithPermission, group)
                    end
                end
                table.insert(shares, {
                    name = name,
                    path = folder,
                    isReadOnly = (0 ~= tonumber(readonly)),
                    groupsWithPermission = groupsWithPermission
                })
            end
        end
        file:close()
    end
    return shares
end

--
-- Write all share configuration files to disk.
--
-- input = CONTEXT, MAP_OF(STRING, MAP_OF(STRING, {
--     path = STRING,
--     pathExists = BOOLEAN,
--     isReadOnly = BOOLEAN,
--     groupsWithPermission = ARRAY_OF(STRING)
-- })),
-- STRING
--
local function writeShareConfigFiles(sc, shares, configFileName)
    -- Map of config file path to file contents.
    local configs = {}

    local partitions = platform.getMountedPartitions(sc)

    local couldWriteFiles, message = pcall(function()
        for partition, partitionShares in pairs(shares) do
            local mountPoint = partitions[partition].mountPoints[1]
            local configFilePath = platform.MOUNT_POINT_FMT:format(mountPoint)..'/'..configFileName
            configs[configFilePath] = {
                shares = {}
            }
            for name, share in pairs(partitionShares) do
                local folder = share.path:match('(.+)/$') or share.path -- remove trailing / if ~= '/'
                table.insert(configs[configFilePath].shares, {
                    name = name,
                    partitionName = partition,
                    mountPoint = mountPoint,
                    folder = folder,
                    readonly = share.isReadOnly and 1 or 0,
                    groups = serializeSharedFolderGroupList(share.groupsWithPermission),
                })
                if not configs[configFilePath].file then
                    local configFile, message = io.open(configFilePath, 'w')
                    assert(configFile, message)
                    configs[configFilePath].file = configFile
                end
            end
        end
    end)

    if couldWriteFiles then
        for _, config in pairs(configs) do
            table.sort(config.shares, sortShares)
            for i, share in ipairs(config.shares) do
                local configLine = string.format(
                    '{index:%d|name:"%s"|drive:"+DRIVE+"|label:"%s"|folder:"%s"|readonly:%d|groups:"%s"},\n',
                    i, share.name, share.mountPoint, share.folder, share.readonly, share.groups)
                config.file:write(configLine)
            end
        end
    end

    for configFilePath, config in pairs(configs) do
        if config.file then
            config.file:close()
        end
        -- Delete any empty config files.
        if 0 == #config.shares then
            os.remove(configFilePath)
        end
    end

    assert(couldWriteFiles, message)
end

--
-- Read all configured shares on all given partitions.
--
-- Returns a map of partitions -> share array.
--
-- input = {
--     maxFoldersProp = STRING,
--     configFileNameProp = STRING
-- },
-- MAP_OF(STRING, {
--     mountPoints = ARRAY_OF(STRING),
--     disk = STRING,
--     fileSystem = STRING
-- })
--
-- output = MAP_OF(STRING, MAP_OF(STRING, {
--     path = STRING,
--     pathExists = BOOLEAN,
--     isReadOnly = BOOLEAN,
--     groupsWithPermission = ARRAY_OF(STRING)
-- })),
-- INTEGER
--
local function getSharedFolderMapFromPartitions(shareInfo, partitions)
    local count = 0
    local sharedFolders = {}
    for deviceNode, partition in pairs(partitions) do
        local partitionShares = {}

        local fileName = platform.MOUNT_POINT_FMT:format(partition.mountPoints[1])..'/'..platform[shareInfo.configFileNameProp]
        for _, share in ipairs(readShareConfigFile(fileName)) do
            partitionShares[share.name] = {
                path = share.path,
                pathExists = (lfs.attributes(platform.MOUNT_POINT_FMT:format(partition.mountPoints[1])..'/'..share.path, 'mode') == 'directory'),
                isReadOnly = share.isReadOnly,
                groupsWithPermission = share.groupsWithPermission
            }
            count = count + 1
        end
        sharedFolders[deviceNode] = partitionShares
    end
    return sharedFolders, count
end

--
-- Read all configured shares on all attached partitions.
--
-- Returns a map of partitions -> share array.
--
-- input = {
--     maxFoldersProp = STRING,
--     configFileNameProp = STRING
-- }
--
-- output = MAP_OF(STRING, MAP_OF(STRING, {
--     path = STRING,
--     pathExists = BOOLEAN,
--     isReadOnly = BOOLEAN,
--     groupsWithPermission = ARRAY_OF(STRING)
-- })),
-- INTEGER
--
local function getSharedFolderMap(sc, shareInfo)
    return getSharedFolderMapFromPartitions(shareInfo, platform.getMountedPartitions(sc))
end

--
-- Construct an array of all shares in a share map.
--
-- input = MAP_OF(STRING, MAP_OF(STRING, {
--     path = STRING,
--     pathExists = BOOLEAN,
--     isReadOnly = BOOLEAN,
--     groupsWithPermission = ARRAY_OF(STRING)
-- }))
--
-- output = ARRAY_OF({
--     name = STRING,
--     partitionName = STRING,
--     path = STRING,
--     pathExists = BOOLEAN,
--     isReadOnly = BOOLEAN,
--     groupsWithPermission = ARRAY_OF(STRING)
-- })
--
local function makeSharedFolderArray(shareMap)
    local sharedFolders = {}
    for partition, shares in pairs(shareMap) do
        for name, share in pairs(shares) do
             share.partitionName = partition
             share.name = name

            table.insert(sharedFolders, share)
        end
    end
    table.sort(sharedFolders, sortShares)
    return sharedFolders
end

--
-- Find a share by name in an array of shares.
--
-- input = ARRAY_OF({
--     name = STRING,
--     partitionName = STRING,
--     path = STRING,
--     pathExists = BOOLEAN,
--     isReadOnly = BOOLEAN,
--     groupsWithPermission = ARRAY_OF(STRING)
-- }),
-- STRING
--
-- output = NIL_OR(NUMBER)
--
local function findShare(shareArray, name)
    for i, share in ipairs(shareArray) do
        if share.name == name then
            return i
        end
    end
end

--
-- Create a shared folder on the local device.
--
-- input = CONTEXT, {
--     name = STRING,
--     partitionName = STRING,
--     path = STRING,
--     isReadOnly = BOOLEAN,
--     groupsWithPermission = ARRAY_OF(STRING)
-- }, {
--     maxFoldersProp = STRING,
--     configFileNameProp = STRING
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorFolderExists',
--     'ErrorInvalidName',
--     'ErrorUnknownPartition',
--     'ErrorPathDoesNotExist',
--     'ErrorUnknownGroup',
--     'ErrorAdminGroupMustHavePermission',
--     'ErrorTooManyFolders'
-- )
--
local function createSharedFolder(sc, settings, shareInfo)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local shares, count = getSharedFolderMap(sc, shareInfo)

    -- Make sure the share name won't collide across all partitions.
    for _, partitionShares in pairs(shares) do
        if partitionShares[settings.name] then
            return 'ErrorFolderExists'
        end
    end

    local partition = platform.getMountedPartitions(sc)[settings.partitionName]
    if not partition then
        return 'ErrorUnknownPartition'
    end

    if not isValidSharedFolderName(settings.name) then
        return 'ErrorInvalidName'
    end
    if #settings.path == 0 then
        return 'ErrorPathDoesNotExist'
    end
    local path = platform.MOUNT_POINT_FMT:format(partition.mountPoints[1])..'/'..settings.path
    if lfs.attributes(path, 'mode') ~= 'directory' then
        return 'ErrorPathDoesNotExist'
    end
    local adminHasPermission = false
    local groups = _M.getStorageGroupMap(sc)
    local groupsWithPermission = {}
    for groupName in pairs(util.arrayToSet(settings.groupsWithPermission)) do
        if not groups[groupName] then
            return 'ErrorUnknownGroup'
        end
        if groupName == 'admin' then
            adminHasPermission = true
        end
        table.insert(groupsWithPermission, groupName)
    end
    if not adminHasPermission then
        return 'ErrorAdminGroupMustHavePermission'
    end
    table.sort(groupsWithPermission)

    if platform[shareInfo.maxFoldersProp] and count >= platform[shareInfo.maxFoldersProp] then
        return 'ErrorTooManyFolders'
    end

    if not shares[settings.partitionName] then
        shares[settings.partitionName] = {}
    end
    shares[settings.partitionName][settings.name] = {
        path = settings.path,
        pathExists = true,
        isReadOnly = settings.isReadOnly,
        groupsWithPermission = settings.groupsWithPermission
    }
    writeShareConfigFiles(sc, shares, platform[shareInfo.configFileNameProp])

    sc:setevent('file_sharing-restart')
end

--
-- Edit a shared folder on the local device.
--
-- input = CONTEXT, {
--     name = STRING,
--     partitionName = STRING,
--     path = STRING,
--     isReadOnly = BOOLEAN,
--     groupsWithPermission = ARRAY_OF(STRING)
-- }, {
--     maxFoldersProp = STRING,
--     configFileNameProp = STRING
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorUnknownFolder',
--     'ErrorUnknownPartition',
--     'ErrorPathDoesNotExist',
--     'ErrorUnknownGroup',
--     'ErrorAdminGroupMustHavePermission'
-- )
--
local function editSharedFolder(sc, settings, shareInfo)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local partition = platform.getMountedPartitions(sc)[settings.partitionName]
    if not partition then
        return 'ErrorUnknownPartition'
    end
    if #settings.path == 0 then
        return 'ErrorPathDoesNotExist'
    end
    local path = platform.MOUNT_POINT_FMT:format(partition.mountPoints[1])..'/'..settings.path
    if lfs.attributes(path, 'mode') ~= 'directory' then
        return 'ErrorPathDoesNotExist'
    end
    local shares = getSharedFolderMap(sc, shareInfo)
    if not shares[settings.partitionName][settings.name] then
        return 'ErrorUnknownFolder'
    end
    local share = shares[settings.partitionName][settings.name]

    local adminHasPermission = false
    local groups = _M.getStorageGroupMap(sc)
    local groupsWithPermission = {}
    for groupName in pairs(util.arrayToSet(settings.groupsWithPermission)) do
        if not groups[groupName] then
            return 'ErrorUnknownGroup'
        end
        if groupName == 'admin' then
            adminHasPermission = true
        end
        table.insert(groupsWithPermission, groupName)
    end
    if not adminHasPermission then
        return 'ErrorAdminGroupMustHavePermission'
    end
    table.sort(groupsWithPermission)

    share.path = settings.path
    share.isReadOnly = settings.isReadOnly
    share.groupsWithPermission = groupsWithPermission
    writeShareConfigFiles(sc, shares, platform[shareInfo.configFileNameProp])

    sc:setevent('file_sharing-restart')
end

--
-- Delete a shared folder on the local device.
--
-- input = CONTEXT, STRING, {
--     maxFoldersProp = STRING,
--     configFileNameProp = STRING
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorUnknownFolder'
-- )
--
local function deleteSharedFolder(sc, name, shareInfo)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local shares = getSharedFolderMap(sc, shareInfo)

    -- Delete all shares on all partitions with the given name.
    local deleted = false
    for _, partitionShares in pairs(shares) do
        for currName, _ in pairs(partitionShares) do
            if currName == name then
                deleted = true
                partitionShares[currName] = nil
            end
        end
    end

    if not deleted then
        return 'ErrorUnknownFolder'
    end

    writeShareConfigFiles(sc, shares, platform[shareInfo.configFileNameProp])

    sc:setevent('file_sharing-restart')
end

--
-- Get an array of SMB folders on the local device.
--
-- input = CONTEXT
--
-- output = ARRAY_OF({
--     name = SRING,
--     partitionName = STRING,
--     path = STRING,
--     pathExists = BOOLEAN,
--     isReadOnly = BOOLEAN,
--     groupsWithPermission = ARRAY_OF(STRING)
-- })
--
function _M.getSMBFolderArray(sc)
    return makeSharedFolderArray(getSharedFolderMap(sc, SMB_SHARE_INFO))
end

--
-- Create an SMB folder on the local device.
--
-- input = CONTEXT, {
--     name = STRING,
--     partitionName = STRING,
--     path = STRING,
--     isReadOnly = BOOLEAN,
--     groupsWithPermission = ARRAY_OF(STRING)
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorFolderExists',
--     'ErrorInvalidName',
--     'ErrorUnknownPartition',
--     'ErrorPathDoesNotExist',
--     'ErrorUnknownGroup',
--     'ErrorAdminGroupMustHavePermission',
--     'ErrorTooManyFolders'
-- )
--
function _M.createSMBFolder(sc, settings)
    return createSharedFolder(sc, settings, SMB_SHARE_INFO)
end

--
-- Edit an SMB folder on the local device.
--
-- input = CONTEXT, {
--     name = STRING,
--     partitionName = STRING,
--     path = STRING,
--     isReadOnly = BOOLEAN,
--     groupsWithPermission = ARRAY_OF(STRING)
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorUnknownFolder',
--     'ErrorUnknownPartition',
--     'ErrorPathDoesNotExist',
--     'ErrorUnknownGroup',
--     'ErrorAdminGroupMustHavePermission'
-- )
--
function _M.editSMBFolder(sc, settings)
    return editSharedFolder(sc, settings, SMB_SHARE_INFO)
end

--
-- Delete an SMB folder on the local device.
--
-- input = CONTEXT, STRING
--
-- output = NIL_OR_ONE_OF(
--     'ErrorUnknownFolder'
-- )
--
function _M.deleteSMBFolder(sc, name)
    return deleteSharedFolder(sc, name, SMB_SHARE_INFO)
end

--
-- Get an array of UPnP media folders on the local device.
--
-- input = CONTEXT
--
-- output = ARRAY_OF({
--     name = STRING,
--     partitionName = STRING,
--     path = STRING,
--     pathExists = BOOLEAN,
--     isReadOnly = BOOLEAN,
--     groupsWithPermission = ARRAY_OF(STRING)
-- })
--
function _M.getUPnPMediaFolderArray(sc)
    return makeSharedFolderArray(getSharedFolderMap(sc, UPNP_MEDIA_SHARE_INFO))
end

--
-- Trigger a UPnP media folder scan on the local device.
--
-- input = CONTEXT
--
function _M.triggerUPnPMediaFolderScan(sc)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local h, m, s = platform.getCurrentLocalTime(sc):match('^%d+[-]%d+[-]%d+[T](%d+)[:](%d+)[:](%d+)[+|-]%d+')
    sc:start_media_server_rescan()
    sc:set_media_server_last_scan_time(h..':'..m..':'..s)
end

--
-- Make a media server RPC call
--
-- input = CONTEXT, STRING
--
-- output = STRING
--
function _M.doMediaServerRPC(sc, rpc)
    sc:readlock()

    local ipAddr = sc:get('lan_ipaddr')
    if ipAddr then
        local http = require('socket.http')

        -- Set the timeout to 5 seconds
        local oldTimeout = http.TIMEOUT
        http.TIMEOUT = _M.MEDIA_SERVER_RPC_TIMEOUT

        local output, status = http.request('http://'..ipAddr..':'..tostring(_M.MEDIA_SERVER_PORT)..'/rpc/'..rpc)
        if status == 200 then
            return output
        end
        http.TIMEOUT = oldTimeout
    end
end

--
-- Return whether the media shared item threshold is reached.
--
-- intput = CONTEXT
--
-- output = BOOLEAN
--
function _M.isMediaThresholdReached(sc)
    sc:readlock()

    -- Return false if the media server is not enabled
    if not sc:getboolean('media_server_enabled', false) then
        return false
    end

    local sharedItems = 0
    local maxSharedItems = sc:getinteger('media_server_maxitems', _M.MEDIA_SERVER_MAXITEMS)
    local response = _M.doMediaServerRPC(sc, 'info_status')
    if response then
        local responses = util.splitOnDelimiter(response, '\n')
        for _, v in pairs(responses) do
            -- Add up the total music/photo/video file currently being shared
            local tokens = util.splitOnDelimiter(v, '|')
            if tokens[1] == 'musictracks' or tokens[1] == 'pictures' or tokens[1] == 'videos' then
                local num = tonumber(tokens[2])
                if num then
                    sharedItems = sharedItems + num
                end
            end
        end
    end

    -- If we're over the 90% threshold, then return true
    if (sharedItems / maxSharedItems) > .9 then
        return true
    else
        return false
    end
end

--
-- Create a UPnP media folder on the local device.
--
-- input = CONTEXT, {
--     name = STRING,
--     partitionName = STRING,
--     path = STRING,
--     isReadOnly = BOOLEAN,
--     groupsWithPermission = ARRAY_OF(STRING)
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorFolderExists',
--     'ErrorInvalidName',
--     'ErrorUnknownPartition',
--     'ErrorPathDoesNotExist',
--     'ErrorUnknownGroup',
--     'ErrorAdminGroupMustHavePermission',
--     'ErrorTooManyFolders'
-- )
--
function _M.createUPnPMediaFolder(sc, settings)
    local error = createSharedFolder(sc, settings, UPNP_MEDIA_SHARE_INFO)
    if error then
        return error
    end
    return _M.triggerUPnPMediaFolderScan(sc)
end

--
-- Edit a UPnP media folder on the local device.
--
-- input = CONTEXT, {
--     name = STRING,
--     partitionName = STRING,
--     path = STRING,
--     isReadOnly = BOOLEAN,
--     groupsWithPermission = ARRAY_OF(STRING)
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorUnknownFolder',
--     'ErrorUnknownPartition',
--     'ErrorPathDoesNotExist',
--     'ErrorUnknownGroup',
--     'ErrorAdminGroupMustHavePermission'
-- )
--
function _M.editUPnPMediaFolder(sc, settings)
    local error = editSharedFolder(sc, settings, UPNP_MEDIA_SHARE_INFO)
    if error then
        return error
    end
    return _M.triggerUPnPMediaFolderScan(sc)
end

--
-- Delete a UPnP media folder on the local device.
--
-- input = CONTEXT, STRING
--
-- output = NIL_OR_ONE_OF(
--     'ErrorUnknownFolder'
-- )
--
function _M.deleteUPnPMediaFolder(sc, name)
    local error = deleteSharedFolder(sc, name, UPNP_MEDIA_SHARE_INFO)
    if error then
        return error
    end
    return _M.triggerUPnPMediaFolderScan(sc)
end

--
-- Get an array of FTP folders on the local device.
--
-- input = CONTEXT
--
-- output = ARRAY_OF({
--     name = STRING,
--     partitionName = STRING,
--     path = STRING,
--     pathExists = BOOLEAN,
--     isReadOnly = BOOLEAN,
--     groupsWithPermission = ARRAY_OF(STRING)
-- })
--
function _M.getFTPFolderArray(sc)
   return makeSharedFolderArray(getSharedFolderMap(sc, FTP_SHARE_INFO))
end

--
-- Create a FTP folder on the local device.
--
-- input = CONTEXT, {
--     name = STRING,
--     partitionName = STRING,
--     path = STRING,
--     isReadOnly = BOOLEAN,
--     groupsWithPermission = ARRAY_OF(STRING)
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorFolderExists',
--     'ErrorInvalidName',
--     'ErrorUnknownPartition',
--     'ErrorPathDoesNotExist',
--     'ErrorUnknownGroup',
--     'ErrorAdminGroupMustHavePermission',
--     'ErrorTooManyFolders'
-- )
--
function _M.createFTPFolder(sc, settings)
    return createSharedFolder(sc, settings, FTP_SHARE_INFO)
end

--
-- Edit an FTP folder on the local device.
--
-- input = CONTEXT, {
--     name = STRING,
--     partitionName = STRING,
--     path = STRING,
--     isReadOnly = BOOLEAN,
--     groupsWithPermission = ARRAY_OF(STRING)
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorUnknownFolder',
--     'ErrorUnknownPartition',
--     'ErrorPathDoesNotExist',
--     'ErrorUnknownGroup',
--     'ErrorAdminGroupMustHavePermission'
-- )
--
function _M.editFTPFolder(sc, settings)
    return editSharedFolder(sc, settings, FTP_SHARE_INFO)
end

--
-- Delete an FTP folder on the local device.
--
-- input = CONTEXT, STRING
--
-- output = NIL_OR_ONE_OF(
--     'ErrorUnknownFolder'
-- )
--
function _M.deleteFTPFolder(sc, name)
    return deleteSharedFolder(sc, name, FTP_SHARE_INFO)
end

--
-- Get a map of storage users on the local device. The keys in the map are
-- the user names.
--
-- input = CONTEXT
--
-- output = MAP_OF(STRING, {
--     number = NUMBER,
--     fullName = STRING,
--     description = STRING,
--     memberOfGroup = STRING,
--     isEnabled = BOOLEAN,
--     isEditable = BOOLEAN,
--     isDeletable = BOOLEAN,
-- })
--
function _M.getStorageUserMap(sc)
    sc:readlock()
    local map = {}
    for i = 1, sc:get_user_count() do
        local name = sc:get_user_name(i)
        map[name] = {
            number = i,
            fullName = sc:get_user_full_name(i),
            description = sc:get_user_description(i),
            memberOfGroup = sc:get_user_group(i),
            isEnabled = sc:get_user_enabled(i),
            isEditable = (name ~= 'admin'),
            isDeletable = not sc:get_user_reserved(i)
        }
    end
    return map
end

--
-- Create a storage user on the local device.
--
-- input = CONTEXT, {
--     name = STRING,
--     fullName = STRING,
--     description = STRING,
--     memberOfGroup = STRING,
--     isEnabled = BOOLEAN,
--     password = STRING,
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorUserExists',
--     'ErrorInvalidName',
--     'ErrorInvalidFullName',
--     'ErrorInvalidDescription',
--     'ErrorUnknownMemberOfGroup',
--     'ErrorInvalidPassword',
--     'ErrorTooManyUsers'
-- )
--
function _M.createStorageUser(sc, settings)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local users = _M.getStorageUserMap(sc)
    if users[settings.name] then
        return 'ErrorUserExists'
    end
    if not _M.isValidUserName(settings.name) then
        return 'ErrorInvalidName'
    end
    if not isValidUserFullName(settings.fullName) then
        return 'ErrorInvalidFullName'
    end
    if not isValidUserDescription(settings.description) then
        return 'ErrorInvalidDescription'
    end
    if not _M.getStorageGroupMap(sc)[settings.memberOfGroup] then
        return 'ErrorUnknownMemberOfGroup'
    end
    if not _M.isValidUserPassword(settings.password) then
        return 'ErrorInvalidPassword'
    end
    local userCount = sc:get_user_count() + 1
    if userCount > platform.MAX_STORAGE_USERS then
        return 'ErrorTooManyUsers'
    end

    sc:set_user_name(userCount, settings.name)
    sc:set_user_full_name(userCount, settings.fullName)
    sc:set_user_description(userCount, settings.description)
    sc:set_user_group(userCount, settings.memberOfGroup)
    sc:set_user_enabled(userCount, settings.isEnabled)
    sc:set_user_reserved(userCount, false)

    local passwordMap = platform.readStorageUserPasswordMap(sc)
    local ids = util.setToArray(passwordMap)
    table.sort(ids)
    local nextID = (#ids > 0 and (ids[#ids] + 1)) or (1000 + userCount)
    sc:set_user_id(userCount, nextID)
    passwordMap[nextID] = settings.password
    platform.writeStorageUserPasswordMap(sc, passwordMap)

    sc:set_user_count(userCount)
end

--
-- Edit a storage user on the local device.
--
-- input = CONTEXT, {
--     name = STRING,
--     fullName = STRING,
--     description = STRING,
--     memberOfGroup = STRING,
--     isEnabled = BOOLEAN,
--     password = STRING,
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorUnknownUser',
--     'ErrorCannotEditUser',
--     'ErrorInvalidFullName',
--     'ErrorInvalidDescription',
--     'ErrorUnknownMemberOfGroup',
--     'ErrorInvalidPassword'
-- )
--
function _M.editStorageUser(sc, settings)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local user = _M.getStorageUserMap(sc)[settings.name]
    if not user then
        return 'ErrorUnknownUser'
    end
    if not user.isEditable then
        return 'ErrorCannotEditUser'
    end
    if not isValidUserFullName(settings.fullName) then
        return 'ErrorInvalidFullName'
    end
    if not isValidUserDescription(settings.description) then
        return 'ErrorInvalidDescription'
    end
    if not _M.getStorageGroupMap(sc)[settings.memberOfGroup] then
        return 'ErrorUnknownMemberOfGroup'
    end
    if not _M.isValidUserPassword(settings.password) then
        return 'ErrorInvalidPassword'
    end

    sc:set_user_full_name(user.number, settings.fullName)
    sc:set_user_description(user.number, settings.description)
    local restartFileSharing = sc:set_user_group(user.number, settings.memberOfGroup)
    restartFileSharing = sc:set_user_enabled(user.number, settings.isEnabled) or restartFileSharing

    local passwordMap = platform.readStorageUserPasswordMap(sc)
    local id = sc:get_user_id(user.number)
    if passwordMap[id] ~= settings.password then
        restartFileSharing = true
        passwordMap[id] = settings.password
        platform.writeStorageUserPasswordMap(sc, passwordMap)
    end

end

-- Delete a storage user on the local device.
--
-- input = CONTEXT, STRING
--
-- output = NIL_OR_ONE_OF(
--     'ErrorUnknownUser',
--     'ErrorCannotDeleteUser'
-- )
--
function _M.deleteStorageUser(sc, name)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local users = _M.getStorageUserMap(sc)
    local user = users[name]
    if not user then
        return 'ErrorUnknownUser'
    end
    if not user.isDeletable then
        return 'ErrorCannotDeleteUser'
    end

    local deletedID = sc:get_user_id(user.number)
    sc:delete_user(user.number)

    -- Remove user from passwords file
    local passwordMap = platform.readStorageUserPasswordMap(sc)
    passwordMap[deletedID] = nil
    platform.writeStorageUserPasswordMap(sc, passwordMap)
end

--
-- Get a map of storage groups on the local device. The keys in the map are
-- the group names.
--
-- input = CONTEXT
--
-- output = MAP_OF(STRING, {
--     number = NUMBER,
--     description = STRING,
--     hasWritePermissions = BOOLEAN,
--     isEditable = BOOLEAN,
--     isDeletable = BOOLEAN,
-- })
--
function _M.getStorageGroupMap(sc)
    sc:readlock()
    local map = {}
    for i = 1, sc:get_user_group_count() do
        local name = sc:get_user_group_name(i)
        local unreserved = not sc:get_user_group_reserved(i)
        map[name] = {
            number = i,
            description = sc:get_user_group_description(i),
            hasWritePermissions = (sc:get_user_group_file_permissions(i) == 'file_admin'),
            isEditable = unreserved,
            isDeletable = unreserved
        }
    end
    return map
end

--
-- Create a storage group on the local device.
--
-- input = CONTEXT, {
--     name = STRING,
--     description = STRING,
--     hasWritePermissions = BOOLEAN,
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorGroupExists',
--     'ErrorInvalidName',
--     'ErrorInvalidDescription',
--     'ErrorTooManyGroups'
-- )
--
function _M.createStorageGroup(sc, settings)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local groups = _M.getStorageGroupMap(sc)
    if groups[settings.name] then
        return 'ErrorGroupExists'
    end
    if not isValidGroupName(settings.name) then
        return 'ErrorInvalidName'
    end
    if not isValidGroupDescription(settings.description) then
        return 'ErrorInvalidDescription'
    end
    local groupCount = sc:get_user_group_count() + 1
    if groupCount > platform.MAX_STORAGE_GROUPS then
        return 'ErrorTooManyGroups'
    end

    sc:set_user_group_name(groupCount, settings.name)
    sc:set_user_group_description(groupCount, settings.description)
    sc:set_user_group_file_permissions(groupCount, settings.hasWritePermissions and 'file_admin' or 'file_guest')
    sc:set_user_group_reserved(groupCount, false)

    sc:set_user_group_count(groupCount)
end

--
-- Edit a storage group on the local device.
--
-- input = CONTEXT, {
--     name = STRING,
--     description = STRING,
--     hasWritePermissions = BOOLEAN
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorUnknownGroup',
--     'ErrorCannotEditGroup',
--     'ErrorInvalidDescription'
-- )
--
function _M.editStorageGroup(sc, settings)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local group = _M.getStorageGroupMap(sc)[settings.name]
    if not group then
        return 'ErrorUnknownGroup'
    end
    if not group.isEditable then
        return 'ErrorCannotEditGroup'
    end
    if not isValidGroupDescription(settings.description) then
        return 'ErrorInvalidDescription'
    end

    sc:set_user_group_description(group.number, settings.description)
    sc:set_user_group_file_permissions(group.number, settings.hasWritePermissions and 'file_admin' or 'file_guest')
end

--
-- Delete a storage group on the local device.
--
-- input = CONTEXT, STRING
--
-- output = NIL_OR_ONE_OF(
--     'ErrorUnknownGroup',
--     'ErrorCannotDeleteGroup'
-- )
--
function _M.deleteStorageGroup(sc, name)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local group = _M.getStorageGroupMap(sc)[name]
    if not group then
        return 'ErrorUnknownGroup'
    end
    if not group.isDeletable then
        return 'ErrorCannotDeleteGroup'
    end

    sc:delete_user_group(group.number)

    -- Move any users in the deleted group to the guest group.
    for i = 1, sc:get_user_count() do
        if sc:get_user_group(i) == name then
            sc:set_user_group(i, 'guest')
        end
    end

    -- Remove the group from shared folder group permission lists.
    local partitions = platform.getMountedPartitions(sc)
    local function removeGroupFromSharedFoldersPermissionList(sc, removedGroupName, shareInfo)
        local configChanged = false
        local shares = getSharedFolderMapFromPartitions(shareInfo, partitions)
        for _, share in ipairs(makeSharedFolderArray(shares)) do
            local groupsWithPermission = {}
            local groupsWithPermissionChanged = false
            for i, groupName in ipairs(share.groupsWithPermission) do
                if groupName ~= removedGroupName then
                    table.insert(groupsWithPermission, groupName)
                else
                    groupsWithPermissionChanged = true
                end
            end
            if groupsWithPermissionChanged then
                share.groupsWithPermission = groupsWithPermission
                configChanged = true
            end
        end
        if configChanged then
            writeShareConfigFiles(sc, shares, platform[shareInfo.configFileNameProp])
        end
    end
    removeGroupFromSharedFoldersPermissionList(sc, name, SMB_SHARE_INFO)
    removeGroupFromSharedFoldersPermissionList(sc, name, UPNP_MEDIA_SHARE_INFO)
    removeGroupFromSharedFoldersPermissionList(sc, name, FTP_SHARE_INFO)
end

function _M.getPartitions(sc)
    sc:readlock()
    local partitions = {}
    for usbDevId, partition in pairs(platform.getUSBInfos(sc)) do
        local deviceNode = '/dev/'..usbDevId
        local usedKB, availableKB = platform.getDeviceNodeUsage(deviceNode)
        table.insert(partitions, {
            partitionName = deviceNode,
            storageDeviceName = '/dev/'..partition.dname,
            label = partition.label,
            fileSystem = _M.parseFileSystem(partition.format),
            usedKB = usedKB or 0,
            availableKB = availableKB or 0,
            storageType = _M.parseStorageType(partition.type),
            portId = partition.portId,
            partitionTableFormat = _M.parsePartitionTableFormat(partition.partTableFormat)
        })
    end
    table.sort(partitions, function(a, b) return a.partitionName < b.partitionName end)
    return partitions
end

return _M -- return the module
