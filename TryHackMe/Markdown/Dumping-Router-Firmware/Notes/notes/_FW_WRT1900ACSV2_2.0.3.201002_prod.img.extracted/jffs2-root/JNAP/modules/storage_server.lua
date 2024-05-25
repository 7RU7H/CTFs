--
-- 2019 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author: hsulliva $
-- $DateTime: 2020/02/06 14:32:09 $
-- $Id: //depot-irv/lego/branches/wrt_platform_sp1/lego_overlay/proprietary/jnap/modules/storage/storage_server.lua#3 $
--

local function GetMountedPartitions(ctx)
    local platform = require('platform')

    local partitions = {}
    local sc = ctx:sysctx()
    for deviceNode, partition in pairs(platform.getMountedPartitions(sc)) do
        local usedKB, availableKB = platform.getDeviceNodeUsage(deviceNode)
        table.insert(partitions, {
                        partitionName = deviceNode,
                        storageDeviceName = partition.disk,
                        label = partition.label,
                        fileSystem = partition.fileSystem,
                        usedKB = usedKB or 0,
                        availableKB = availableKB or 0
                    })
    end
    table.sort(partitions, function(a, b) return a.partitionName < b.partitionName end)
    return 'OK', {
        partitions = partitions
    }
end

local function GetPartitions(ctx)
    local storage = require('storage')
    local sc = ctx:sysctx()

    return 'OK', {
        partitions = storage.getPartitions(sc)
    }
end

local function RemoveStorageDevice(ctx, input)
    local storage = require('storage')

    local sc = ctx:sysctx()
    local error = storage.RemoveStorageDevice(sc, input.storageDeviceName)
    return error or 'OK'
end

local function ListSubdirectories(ctx, input)
    local platform = require('platform')
    local util = require('util')
    local sc = ctx:sysctx()

    -- does not use context
    local partition = platform.getMountedPartitions(sc)[input.partitionName]
    if not partition then
        return 'ErrorUnknownPartition'
    end
    if #input.path == 0 then
        return 'ErrorPathDoesNotExist'
    end
    if string.find(input.path, '%.%.') then
        return 'ErrorInvalidPath'
    end
    local path = platform.MOUNT_POINT_FMT:format(partition.mountPoints[1])..'/'..input.path
    local subdirectories = util.getSubdirectorySet(path)
    if not subdirectories then
        return 'ErrorPathDoesNotExist'
    end
    subdirectories = util.setToArray(subdirectories)
    table.sort(subdirectories)
    return 'OK', {
        subdirectories = subdirectories
    }
end

local function CreateDirectory(ctx, input)
    local platform = require('platform')
    local util = require('util')
    local sc = ctx:sysctx()

    local partition = platform.getMountedPartitions(sc)[input.partitionName]
    if not partition then
        return 'ErrorUnknownPartition'
    end
    local subdirs = util.splitOnDelimiter(input.path, '/')

    local path = platform.MOUNT_POINT_FMT:format(partition.mountPoints[1])
    for _, subdir in ipairs(subdirs) do
        if not util.isValidDirectoryName(subdir) then
            return 'ErrorInvalidPath'
        end

        -- Make sure any existing filename in the path is a directory
        local dir = path..'/'..subdir
        local mode = lfs.attributes(dir, 'mode')
        if mode ~= nil and mode ~= 'directory' then
            return 'ErrorInvalidPath'
        end
        path = dir
    end

    path = platform.MOUNT_POINT_FMT:format(partition.mountPoints[1])

    -- Recursively create the directories.
    for _, subdir in ipairs(subdirs) do
        local dir = path..'/'..subdir

        -- Only create the directory if it does not exist.
        if not lfs.attributes(dir, 'mode') then
            assert(lfs.mkdir(dir), ('failed to create directory "%s"'):format(dir))
        end
        path = dir
    end
    return 'OK'
end

local function GetUsers(ctx)
    local storage = require('storage')
    local platform = require('platform')

    local sc = ctx:sysctx()
    local users = {}
    for name, user in pairs(storage.getStorageUserMap(sc)) do
        table.insert(users, {
                         name = name,
                         fullName = user.fullName,
                         description = user.description,
                         memberOfGroup = user.memberOfGroup,
                         isEnabled = user.isEnabled,
                         isEditable = user.isEditable,
                         isDeletable = user.isDeletable
                     })
    end
    table.sort(users, function(a, b) return a.name < b.name end)
    return 'OK', {
        users = users,
        maxUsers = platform.MAX_STORAGE_USERS
    }
end

local function CreateUser(ctx, input)
    local storage = require('storage')

    local sc = ctx:sysctx()
    local error = storage.createStorageUser(sc, input)
    return error or 'OK'
end

local function EditUser(ctx, input)
    local storage = require('storage')

    local sc = ctx:sysctx()
    local error = storage.editStorageUser(sc, input)
    return error or 'OK'
end

local function DeleteUser(ctx, input)
    local storage = require('storage')

    local sc = ctx:sysctx()
    local error = storage.deleteStorageUser(sc, input.name)
    return error or 'OK'
end

local function GetGroups(ctx)
    local platform = require('platform')
    local storage = require('storage')

    local sc = ctx:sysctx()
    local groups = {}
    for name, group in pairs(storage.getStorageGroupMap(sc)) do
        table.insert(groups, {
                         name = name,
                         description = group.description,
                         hasWritePermissions = group.hasWritePermissions,
                         isEditable = group.isEditable,
                         isDeletable = group.isDeletable
                     })
    end
    table.sort(groups, function(a, b) return a.name < b.name end)
    return 'OK', {
        groups = groups,
        maxGroups = platform.MAX_STORAGE_GROUPS
    }
end

local function CreateGroup(ctx, input)
    local storage = require('storage')

    local sc = ctx:sysctx()
    local error = storage.createStorageGroup(sc, input)
    return error or 'OK'
end

local function EditGroup(ctx, input)
    local storage = require('storage')

    local sc = ctx:sysctx()
    local error = storage.editStorageGroup(sc, input)
    return error or 'OK'
end

local function DeleteGroup(ctx, input)
    local storage = require('storage')

    local sc = ctx:sysctx()
    local error = storage.deleteStorageGroup(sc, input.name)
    return error or 'OK'
end

return require('libhdklua').loadmodule('jnap_storage'), {
    ['http://linksys.com/jnap/storage/GetMountedPartitions'] = GetMountedPartitions,
    ['http://linksys.com/jnap/storage/RemoveStorageDevice'] = RemoveStorageDevice,
    ['http://linksys.com/jnap/storage/ListSubdirectories'] = ListSubdirectories,
    ['http://linksys.com/jnap/storage/CreateDirectory'] = CreateDirectory,

    ['http://linksys.com/jnap/storage/GetUsers'] = GetUsers,
    ['http://linksys.com/jnap/storage/CreateUser'] = CreateUser,
    ['http://linksys.com/jnap/storage/EditUser'] = EditUser,
    ['http://linksys.com/jnap/storage/DeleteUser'] = DeleteUser,

    ['http://linksys.com/jnap/storage/GetGroups'] = GetGroups,
    ['http://linksys.com/jnap/storage/CreateGroup'] = CreateGroup,
    ['http://linksys.com/jnap/storage/EditGroup'] = EditGroup,
    ['http://linksys.com/jnap/storage/DeleteGroup'] = DeleteGroup,
    ['http://linksys.com/jnap/storage/GetPartitions'] = GetPartitions,
}
