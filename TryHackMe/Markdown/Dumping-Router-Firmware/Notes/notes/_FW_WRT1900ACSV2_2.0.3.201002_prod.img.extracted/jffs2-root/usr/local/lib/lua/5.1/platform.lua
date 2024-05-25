--
-- 2019 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author: hsulliva $
-- $DateTime: 2020/03/11 14:08:51 $
-- $Id: //depot-irv/lego/branches/wrt_platform_sp1/lego_overlay/proprietary/jnap/lualib/platform.lua#11 $
--

-- platform.lua - library containing platform-specific functionality.

local util = require('util')
local hdk = require('libhdklua')
local lfs = require('lfs')


local _M = {} -- create the module

_M.unittest = {} -- unit test override data store


----------------------------------------------------------------------
-- Platform-specific constants.
----------------------------------------------------------------------


_M.ADMIN_USERNAME = 'admin'
_M.MIN_ADMIN_PASSWORD_LENGTH = 1
_M.MAX_ADMIN_PASSWORD_LENGTH = 64
_M.MAX_PASSWORD_HINT_LENGTH = 512
_M.DEFAULT_ADMIN_PASSWORD = 'admin'
_M.SYSINFO_PASSWORD_FILE_PATH = '/var/config/.sysinfo_pswd'

-- Limits for the LAN prefix length.
-- This is dependent on underlying firmware support, which may not be present on all platforms.
_M.MIN_LAN_PREFIX_LENGTH = 16 -- support class B and smaller
_M.MAX_LAN_PREFIX_LENGTH = 30

-- Limits for the network prefix length of other network prefix length inputs (e.g. Static WAN)
-- The network prefix itself can be between 0 and 32 (by definition), however
-- for practicality it is restricted further on our platform
_M.MIN_NETWORK_PREFIX_LENGTH = 8
_M.MAX_NETWORK_PREFIX_LENGTH = 30

_M.MIN_DHCP_LEASE_MINUTES = 1 -- dnsmasq documentation specifies 2 minute minimum, however our version has been patched to support 1 minute
_M.MAX_DHCP_LEASE_MINUTES = (60 * 24 * 365) -- ~ 1 year

_M.MAX_DHCP_RESERVATION_DESCRIPTION_LENGTH = util.MAX_DNS_LABEL_LENGTH

_M.MAX_MACFILTER_ADDRESSES = 32

_M.MAX_PORT_RULE_DESCRIPTION_LENGTH = 32
_M.MAX_SINGLEPORT_RULES = 50
_M.MAX_PORTRANGE_RULES = 25
_M.MAX_PORTTRIGGER_RULES = 25

_M.MAX_IPV6FIREWALL_RULE_PORT_RANGES = 2
_M.MAX_IPV6FIREWALL_RULE_DESCRIPTION_LENGTH = 64
_M.MAX_IPV6FIREWALL_RULES = 15

-- dynamic port forwarding
_M.MAX_DPF_SESSION_TIMEOUT_SECONDS = 1200

_M.MAX_SMB_FOLDERS = 10
_M.MAX_UPNP_MEDIA_FOLDERS = 10
_M.MAX_FTP_FOLDERS = 10
_M.MAX_STORAGE_USERS = 10
_M.MAX_STORAGE_GROUPS = 10

_M.SMB_SHARE_FILE = '.smb_share.nfo'
_M.UPNP_MEDIA_SHARE_FILE = '.med_share.nfo'
_M.FTP_SHARE_FILE = '.ftp_share.nfo'

_M.SUPPORTED_FTP_ENCODINGS = {
    ['UTF-8'] = 0,
    ['GB18030'] = 1,
    ['CP1258'] = 2,
    ['ISO8859-1'] = 3
}

_M.GET_LOCAL_TIME_COMMAND = 'date +"%Y-%m-%dT%X%z"'

_M.GET_EPOCH_TIME_COMMAND = 'date +%s'

_M.GET_UUID_COMMAND = 'uuidgen -t'

_M.MAX_STATIC_ROUTE_ENTRIES = 20

_M.GET_ROUTING_TABLE_COMMAND = 'route -en | grep -E -v "^([^0-9]|127.0.0)"'

-- Get the IPv6 route table for a specified interface
_M.GET_IPV6_DEFAULT_ROUTE_COMMAND_FMT = 'ip -6 route show dev %s | grep default'

-- Get the IPv6 link local address for a specified interface
_M.GET_IPV6_LINK_LOCAL_COMMAND_FMT = 'ip -6 addr show dev %s | grep "scope link"'

_M.MESSAGES_FILEPATH = '/var/log/messages'
_M.GET_LOG_COMMAND = 'grep "%s" %s | sed -n %d,%dp'

_M.GET_UPTIME_CMD = 'cat /proc/uptime | cut -d"." -f1'

_M.SERVICES_FILEPATH = '/etc/services'

_M.SERVICES_READY = {
    ['lan'] = 'started'
}

_M.TIMESETTINGS_FILEPATH = '/etc/timesettings'

_M.ARPTABLE_FILEPATH = '/proc/net/arp'

_M.DHCP_CLIENT_LEASE_FILEPATH = '/etc/dnsmasq.leases'

_M.USBPORTVENDOR_FILEPATH = '/sys/bus/usb/devices/usb%d/manufacturer'
_M.USBPORTPRODUCT_FILEPATH = '/sys/bus/usb/devices/usb%d/product'
_M.USBPORTSPEED_FILEPATH = '/sys/bus/usb/devices/usb%d/speed'

_M.PROC_MODULES_FILEPATH = '/proc/modules'

_M.CONFIG_FILES = {
    '/var/config/'
}

_M.CGI_DIR = '/www'

_M.GETSTALIST_COMMAND = 'iwpriv %s getstalistext'
_M.GETSTALIST_BRCM_COMMAND = 'wlancfg %s getstalistext'
_M.GETSTALIST_MRVL_COMMAND = 'iwpriv %s getstalistext'
_M.GETSTALIST_MTK_COMMAND = 'wlan_query -i %s -p stainfo'
_M.GETSTALIST_QCA_COMMAND = 'wlanconfig %s list -H'

-- Retrieve the Ethernet switch port speed(s) for a given interface (or port).
_M.LINKSTATE_FMT = 'linkstate %s' -- first argument is the interface (or port). E.g. eth0, port2

-- Retrieve the peers connected to the LAN Ethernet port(s)
_M.LINKSTATE_L = 'linkstate -l'

-- The port number indexes of the Ethernet switch
_M.LAN_SWITCH_PORTS = { 0, 1, 2, 3 }
_M.WAN_SWITCH_PORT = 4

--
-- The 'usbget' utility gets static information about mounted USB drives. This is returning cached information so
-- it will not take long at all to return, compared with parted, df, usblabel, etc.
--
-- Lists the mounted drives, each of which can be used to query individual information using the following commands
_M.USBGET_LIST_COMMAND = '/sbin/usbget list'
-- The first parameter is one of the drives returned by the list command, the second is one of the following cached fields (comments are from wiki 'Pinnacle USB service enhancement'):
-- 'pname'          partition name detected by the system, i.e. 'sda1'
-- 'dname'          drive name as detected by the system, i.e. 'sda' (please note, in some cases for disks with GPT partition table,
--                  the partition name may match the drive name due to how the paragon driver works)
-- 'label'          disk label as reported by the script '/usr/sbin/usblabel' (please note that if no label is set the partition name should be used)
-- 'format'         file system format of the partition
-- 'size'           total size of the disk partition as detected by the OS
-- 'manufacturer'
-- 'product'
-- 'speed'
_M.USBGET_COMMAND_FMT = '/sbin/usbget %s %s'
--

-- The mount utility is preferred to reading from /proc/mounts directly, which
-- escapes ' ' as \040.
_M.MOUNT_COMMAND = '/bin/mount'

-- The mount point format is used to specify a relative path in unit testing.
-- This format must be applied to the mount point when accessing the file system itself.
_M.MOUNT_POINT_FMT = '%s' -- first argument is the file system mount point

_M.DF_COMMAND_FMT = '/bin/df %s' -- first argument is the device node

_M.DEFAULT_WAN_DHCP_LEASE = 24 * 60

_M.PING_LOG_FILEPATH = '/tmp/ping_output'
_M.START_PING_FILEPATH = '/tmp/startping.sh'
_M.START_PING_SCRIPT_FMT = [[
#!/bin/sh
# params: OUTPUTFILE HOST PACKETSIZE [COUNT]
if [ $# -gt 3 ]
then
    %s -s $3 -c $4 $2 > $1 2>&1 &
else
    %s -s $3 $2 > $1 2>&1 &
fi
echo $!
]]

_M.START_PING_SCRIPT = _M.START_PING_SCRIPT_FMT:format('ping', 'ping')
_M.START_PING6_SCRIPT = _M.START_PING_SCRIPT_FMT:format('ping6', 'ping6')

_M.TRACEROUTE_LOG_FILEPATH = '/tmp/traceroute_output'
_M.START_TRACEROUTE_FILEPATH = '/tmp/starttraceroute.sh'
_M.START_TRACEROUTE_SCRIPT = [[
#!/bin/sh
# params: OUTPUTFILE HOST
traceroute $2 > $1 2>&1 &
echo $!
]]

-- Command to restore previous firmware image
_M.RESTORE_PREVIOUS_FIRMWARE_CMD = 'switch_boot_image > /dev/null 2>&1'
_M.FACTORY_RESET_CMD = 'utcmd factory_reset > /dev/null 2>&1'
_M.RESET_NODES_CMD = 'reset_slave_nodes -a > /dev/null 2>&1'

_M.CONNTRACK_L_IPV4 = 'conntrack -L -f ipv4'
_M.CONNTRACK_L_IPV6 = 'conntrack -L -f ipv6'

_M.CONNECTION_ACCT_FILE = '/etc/cron/cron.everyminute/connection_accounting.sh'
_M.CONNECTION_ACCT_FILE_FMT = [[
# Auto-generated by jnap/lualib/platform.lua
SYSCFG_FAILED='false'
FOO=`utctx_cmd get conntrack_timestamp`
eval $FOO
if [ $SYSCFG_FAILED = 'true' ] ; then
    ulog conn_acct status "$PID utctx_cmd failed to get conntrack_timestamp"
else
    CURRENT_TIMESTAMP=%s
    if [ -z "$SYSCFG_conntrack_timestamp" ] || [ $SYSCFG_conntrack_timestamp -lt $CURRENT_TIMESTAMP ] ; then
        ulog conn_acct status "stopping connection accounting"
        rm -rf %s
        syscfg unset conntrack_timestamp
        syscfg commit
    fi
fi
]]

_M.EMAIL_CONTENTS_FILE = '/var/config/email.contents'

----------------------------------------------------------------------
-- Platform-logging functions.
----------------------------------------------------------------------

-- Logging levels
_M.LOG_ERROR = 'error'
_M.LOG_WARNING = 'warning'
_M.LOG_INFO = 'info'
_M.LOG_VERBOSE = 'verbose'

local registeredLoggingFn = nil

--
-- Register a logging handler.
--
-- input = FUNCTION(STRING, STRING)
--
function _M.registerLoggingCallback(callback)
    registeredLoggingFn = callback
end

--
-- Log a message to the platform.
--
-- input = NUMBER, STRING
--
function _M.logMessage(level, message)
    if registeredLoggingFn then
        registeredLoggingFn(level, message)
    end
end


----------------------------------------------------------------------
-- Platform-specific utility functions.
----------------------------------------------------------------------


--
-- Get the current local UTC time.
--
-- output = NUMBER
--
function _M.getCurrentUTCTime()
    return os.time()
end

--
-- Get the current local time.
--
-- NOTE: The date is formatted as an ISO-8601 date in the device's local time.
--
-- output = STRING
--
function _M.getCurrentLocalTime(sc)
    sc:readlock()
    local output = ''
    -- Set the TZ environment variable, so the spawned processes environment has the correct TZ
    local file = io.popen(string.format('TZ="%s" ', sc:get('TZ', '')) .. _M.GET_LOCAL_TIME_COMMAND)
    if file then
        for line in file:lines() do
            if line:match('^%d%d%d%d[-]%d%d[-]%d%d[T]%d%d[:]%d%d[:]%d%d[+|-]%d%d%d%d') then
                return line
            else
                output = output .. line .. '\\n'
            end
        end
        file:close()
    end

    error(('unable to parse output of "%s": %s'):format(_M.GET_LOCAL_TIME_COMMAND, output))
end

--
-- return the currrent epoch time
-- Input = none
-- Output = NUMBER
--
function _M.getEpochTime()
    local file = io.popen(_M.GET_EPOCH_TIME_COMMAND)
    if file then
        for line in file:lines() do
            if line:match('%d') then
                return line
            else
                return 0
            end
        end
        file:close()
    end
end

--
-- return an UUID
-- Input = none
-- Output = STRING
--
function _M.getUUID()
    local file = io.popen(_M.GET_UUID_COMMAND)
    if file then
        for line in file:lines() do
            if line then
                return line
            else
                return 0
            end
        end
        file:close()
    end
end

--
-- Determine whether a specified process is running on the local device.
--
-- input = NUMBER
--
function _M.isProcessRunning(pid)
    local isRunning = false
    local file = io.popen('ps')
    if file then
        for line in file:lines() do
            local curPID = line:match('^%s*(%d+)')
            if curPID and tonumber(curPID) == pid then
                isRunning = true
                break
            end
        end
        file:close()
    end
    return isRunning
end

--
-- Kill a process on the local device.
--
-- input = NUMBER, OPTIONAL(STRING)
--
function _M.killProcess(pid, signal)
    os.execute(string.format('kill -%s %d >/dev/null 2>&1', signal or 'KILL', pid))
end

--
-- Get the contents of a file as a string.
--
-- input = STRING
--
-- output = OPTIONAL(STRING)
--
function _M.getFileContents(filepath)
    local text
    local file = io.open(filepath)
    if file then
        text = file:read('*a')
    end
    return text
end

--
-- Determine whether an admin password is valid.
--
-- input = STRING
--
-- output = BOOLEAN
--
function _M.isValidAdminPassword(password)
    if type(password) ~= 'string' then
        return false
    end

    if #password < _M.MIN_ADMIN_PASSWORD_LENGTH or #password > _M.MAX_ADMIN_PASSWORD_LENGTH then
        return false
    end

    -- the password can only include characters in the ASCII 32-126 range
    return password:find('^[\032-\126]*$') ~= nil
end

--
-- Set the current admin password.
-- This method sets the password synchronously
--
-- input = CONTEXT, STRING
--
function _M.setAdminPassword(sc, password)
    sc:writelock()

    local passwordChanged = sc:set_admin_password(password)
    if not passwordChanged then
        return
    end

    sc:set_admin_password_is_default(password == _M.DEFAULT_ADMIN_PASSWORD)

    -- Update the admin password in the storage user password map
    local passwordMap = _M.readStorageUserPasswordMap(sc) or {}
    passwordMap[1000] = password -- admin is always user 1000
    _M.writeStorageUserPasswordMap(sc, passwordMap)

    -- Update the sysinfo password file
    file = io.open(_M.SYSINFO_PASSWORD_FILE_PATH, "w")
    if file then
        file:write(string.format('admin:%s', password))
        file:flush()
        file:close()
    end
end

--
-- Determine whether a friendly name is valid.
--
-- input = STRING
--
-- output = BOOLEAN
--
function _M.isValidFriendlyName(friendlyName)
    return type(friendlyName) == 'string'
end

--
-- Determine whether a LAN IP address is valid.
--
-- input = IPADDRESS
--
-- output = BOOLEAN
--
function _M.isValidLANIPAddress(ip)
    return (ip[1] >= 1 and ip[1] <= 223 and ip[4] >= 1 and ip[4] <= 254)
end

--
-- Determine whether a LAN prefix length is supported.
--
-- INPUT = NUMBER
--
-- OUTPUT = BOOLEAN
--
function _M.isSupportedLANPrefixLength(length)
    return util.isValidNetworkPrefixLength(length) and ((_M.MIN_LAN_PREFIX_LENGTH <= length) and (length <= _M.MAX_LAN_PREFIX_LENGTH))
end

--
-- Determine whether a network prefix length is allowed.
--
-- INPUT = NUMBER
--
-- OUTPUT = BOOLEAN
--
function _M.isAllowedNetworkPrefixLength(length)
    return util.isValidNetworkPrefixLength(length) and ((_M.MIN_NETWORK_PREFIX_LENGTH <= length) and (length <= _M.MAX_NETWORK_PREFIX_LENGTH))
end

--
-- Determine whether an IP address is on the guest network.
--
-- input = CONTEXT, IPADDRESS
--
-- output = BOOLEAN
--
function _M.conflictsWithGuestNetwork(sc, ip, networkPrefixLength)
    sc:readlock()
    local g = sc:get_guest_access_lan_subnet_mask() -- guest subnet is the network id of the guest LAN
    if g then
        return hdk.ipaddress(g) == ip:networkid(networkPrefixLength)
    end
    return false
end

--
-- Get the set of locales supported by the device.
--
-- output = SET_OF(STRING)
--
local localeSet -- lazily-generated private cache
function _M.getLocaleSet()
    if not localeSet then
        -- Legacy HNAP implementation is hardcoded.
        -- TODO: read this list from a file somewhere.
        localeSet = {
            ['en-US'] = true,
            ['ar-SA'] = true,
            ['fr-FR'] = true,
            ['fr-CA'] = true,
            ['de-DE'] = true,
            ['es-ES'] = true,
            ['it-IT'] = true,
            ['pl-PL'] = true,
            ['sv-SE'] = true,
            ['tr-TR'] = true,
            ['pt-PT'] = true,
            ['nl-NL'] = true,
            ['ru-RU'] = true,
        }
    end
    return localeSet
end

--
-- Gets the time zone map. The keys in the map are the time zone IDs of the
-- supported time zones.
--
-- output = MAP_OF(STRING, {
--     utcOffsetMinutes = NUMBER,
--     description = STRING,
--     dstOffValue = STRING,
--     dstOnValue = OPTIONAL(STRING)
-- })
--
local timeZoneMap -- lazily-generated private cache
function _M.getTimeZoneMap()
    if not timeZoneMap then
        timeZoneMap = {}
        local file = io.open(_M.TIMESETTINGS_FILEPATH, 'r')
        if file then
            for line in file:lines() do
                local hours, dst, dstOnValue, dstOffValue, description = line:match('^([^|]*)|([^|]*)|([^|]*)|([^|]*)|(.*)$')
                if hours and dst and dstOnValue and dstOffValue and description then
                    local timeZoneID
                    if dst ~= 'TRUE' then
                        timeZoneID = dstOffValue..'-NO-DST'
                        dstOnValue = nil
                    else
                        timeZoneID = dstOffValue
                    end
                    timeZoneMap[timeZoneID] = {
                        utcOffsetMinutes = tonumber(hours) * -60,
                        description = description,
                        dstOffValue = dstOffValue,
                        dstOnValue = dstOnValue,
                    }
                end
            end
            file:close()
        end
    end
    return timeZoneMap
end


--
-- Get the port link speeds for the Ethernet switch.
--
-- input = CONTEXT
--
-- output = {
--     lan = ARRAY_OF({
--         portNumber = NUMBER,
--         speedMbps = NUMBER
--     }),
--     wan = {
--         speedMbps = OPTIONAL(NUMBER)
--     }
-- }
--
function _M.getSwitchPortSpeeds(sc)
    local ports = {
        lan = {},
        wan = {}
    }

    sc:readlock()

    --
    -- Example output from linkstate:
    -- port0 0
    -- port1 1000
    -- port2 0
    -- port3 0
    -- wan 1000
    --

    -- Specify both the LAN and WAN physical interfaces as inputs to the linkstate command.
    -- On some platforms (e.g. Broadcom) don't include duplicate names when the LAN and WAN
    -- share the same physical interface.
    local lanIfaces = sc:get_lan_ethernet_physical_ifnames()
    local wanIface = sc:get_wan_interface_name()
    local ifaces = lanIfaces
    if not ifaces:find(wanIface, 1, true) then
        ifaces = ifaces..' '..wanIface
    end
    local process = io.popen(_M.LINKSTATE_FMT:format(ifaces))
    if process then
        for line in process:lines() do
            local lanPort, lanSpeed = line:match('^port(%d)%s+(%d+)$')
            if lanPort then
                table.insert(ports.lan, {
                    portNumber = tonumber(lanPort), -- LAN port index is 0-based
                    speedMbps = tonumber(lanSpeed)
                })
            else
                local wanSpeed = line:match('^wan%s+(%d+)$')
                if wanSpeed then
                    ports.wan.speedMbps = tonumber(wanSpeed)
                end
            end
        end
        process:close()
    end

    return ports
end

--
-- Get the connection speeds of each switch port on the LAN interface
--
-- input = CONTEXT
--
-- output = ARRAY_OF({
--     portNumber = NUMBER,
--     speedMbps = NUMBER
-- })
--
function _M.getLANPortLinkSpeeds(sc)
    -- Sort the LAN ports by port number.
    local lanPorts = _M.getSwitchPortSpeeds(sc).lan
    table.sort(lanPorts, function(lhs, rhs) return lhs.portNumber < rhs.portNumber end)
    return lanPorts
end

--
-- Get the connection speed of port on the WAN interface.
--
-- input = CONTEXT
--
-- output = NUMBER
--
function _M.getWANPortLinkSpeed(sc)
    return _M.getSwitchPortSpeeds(sc).wan.speedMbps or 0
end

--
-- Gets the MAC address of a network interface from the net name.
--
-- input = STRING
--
-- output = MACADDRESS
--
function _M.getMACAddressFromNetName(name)
    local macAddress
    local file = io.open('/sys/class/net/'..name..'/address', 'r')
    if file then
        macAddress = hdk.macaddress(file:read())
        file:close()
    end
    return macAddress or hdk.macaddress('00:00:00:00:00:00')
end

--
-- Read the map of storage user ID -> password from the specified file.
-- The keys in the map are the user IDs. The values are the passwords.
--
-- output = MAP_OF(NUMBER, STRING)
--
function _M.readStorageUserPasswordMap(sc)
    return sc:read_user_password_map()
end

--
-- Write the map of storage user ID -> password to the specified file.
-- The keys in the map are the user IDs. The values are the passwords.
--
-- input = MAP_OF(NUMBER, STRING)
--
function _M.writeStorageUserPasswordMap(sc, map)
    sc:write_user_password_map(map)
end

--
-- Get the vendor of the device connected to a USB port.
--
-- input = NUMBER
--
-- output = STRING
--
function _M.getConnectedUSBVendor(index)
    local vendor
    local file = io.open(_M.USBPORTVENDOR_FILEPATH:format(index), 'r')
    if file then
        vendor = file:read()
        file:close()
    end
    return vendor or ''
end

--
-- Get the product connected to a USB port.
--
-- input = NUMBER
--
-- output = STRING
--
function _M.getConnectedUSBProduct(index)
    local product
    local file = io.open(_M.USBPORTPRODUCT_FILEPATH:format(index), 'r')
    if file then
        product = file:read()
        file:close()
    end
    return product or ''
end

--
-- Get the speed of a USB port.
--
-- input = NUMBER
--
-- output = NUMBER
--
function _M.getConnectedUSBSpeedMbps(index)
    local speed
    local file = io.open(_M.USBPORTSPEED_FILEPATH:format(index), 'r')
    if file then
        speed = tonumber(file:read())
        file:close()
    end
    return speed or 0
end

--
-- Get whether the storage and Virtual USB drivers are loaded.
--
-- output = BOOLEAN, BOOLEAN
--
function _M.getUSBDriversLoaded()
    local file, storage, virtualUSB
    local lines = _M.unittest.proc_modules_lines
    if not lines then
        file = io.open(_M.PROC_MODULES_FILEPATH)
        if file then
            lines = file:lines()
        end
    end
    for line in lines do
        if not storage and line:find('usb_storage') then
            storage = true
        elseif not virtualUSB and line:find('sxuptp_driver') then
            virtualUSB = true
        end
        if storage and virtualUSB then
            break
        end
    end
    if file then
        file:close()
    end
    return storage, virtualUSB
end

--
-- Get the partition usage data for a specific device node.
--
-- input = STRING
--
-- output = NUMBER,
--          NUMBER
--
function _M.getDeviceNodeUsage(deviceNode)
    local usedKB, availableKB
    local df = io.popen(_M.DF_COMMAND_FMT:format(util.shellEscape(deviceNode)))
    if df then
        for line in df:lines() do
            --
            -- Example output from df
            --
            -- ~ # df /dev/sdb1
            -- Filesystem           1K-blocks      Used Available Use% Mounted on
            -- /dev/sdb1               984288         4    984284   0% /tmp/sdb1
            --
            usedKB, availableKB = line:match('^'..deviceNode..'%s+%d+%s+(%d+)%s+(%d+)%s+%d+%%%s+(.+)$')
            if usedKB and availableKB then
                break
            end
        end
        df:close()
    end
    return tonumber(usedKB), tonumber(availableKB)
end

--
-- Get an attribute of a specific partition.
--
-- partition    name of partition as returned by 'usbget list'
-- attrib       one of the supported attributes of 'usbget' (see definition for USBGET_LIST_COMMAND)
--
local function getUSBInfo(partition, attrib)
    local info = ''
    local usbget = io.popen(_M.USBGET_COMMAND_FMT:format(util.shellEscape(partition), util.shellEscape(attrib)), 'r')
    if usbget then
        info = usbget:read()
        usbget:close()
    end
    return info
end

--
-- Get a list of USB device IDs with their static (cached) info.
--
-- output = MAP_OF(STRING, {
--     dname = STRING,
--     label = STRING,
--     format = STRING,
--     size = NUMBER,
--     manufacturer = STRING,
--     product = STRING,
--     speed = NUMBER
--     type = STRING
--     portId = STRING
--     partTableFormat = STRING
-- })
--

function _M.getUSBInfos(sc)
    sc:readlock()
    local usbInfos = {}
    for i = 1, sc:get_usb_num_partitions() do
        local usb_info = sc:get_usb_partition_info(i)
        local pname = usb_info.pname
        usbInfos[pname] = {
            dname = usb_info.dname,
            label = usb_info.label,
            format = usb_info.format,
            size = usb_info.size,
            manufacturer = usb_info.manufacturer,
            product = usb_info.product,
            speed = usb_info.speed,
            type = usb_info.type,
            portId = usb_info.portId,
            partTableFormat = usb_info.partTableFormat
        }
        if '' == usbInfos[pname].label then
            usbInfos[pname].label = pname
        end
    end

    return usbInfos
end

--
-- Get the system's currently mounted partitioned file systems and
-- its mount point.
--
-- Partitioned file systems are user-accessible, e.g. a USB drive's
-- partitions.
--
-- output = MAP_OF(STRING, {
--     label = STRING,
--     mountPoints = ARRAY_OF(STRING),
--     disk = STRING,
--     fileSystem = STRING
-- })
--
function _M.getMountedPartitions(sc)
    local partitions = {}
    local fileSystems = _M.getMountedFileSystems()
    local usbDrives = _M.getUSBInfos(sc)

    -- For each usb drive, find the matching mount points in the mounted file systems.
    for usbDevId, usb in pairs(usbDrives) do
        local devNode = '/dev/'..usbDevId
        for _, fileSystem in ipairs(fileSystems) do
            if devNode == fileSystem.deviceNode then
                local diskName = '/dev/'..usb.dname
                if not partitions[devNode] then
                    partitions[devNode] = {
                        label = usb.label,
                        mountPoints = {},
                        disk = diskName,
                        fileSystem = usb.format,
                        type = usb.type,
                        portId = usb.portId,
                        partTableFormat = usb.partTableFormat
                    }
                end
                assert(partitions[devNode].fileSystem == usb.format, ('partition file system differs (expected: "%s", actual: "%s")'):format(partitions[devNode].fileSystem, usb.format))
                assert(partitions[devNode].disk == diskName, ('partition disk differs (expected: "%s", actual: "%s")'):format(partitions[devNode].disk, diskName))
                table.insert(partitions[devNode].mountPoints, fileSystem.mountPoint)
            end
        end
    end
    return partitions
end

--
-- Get the system's currently mounted file systems.
--
-- output = ARRAY_OF({
--     deviceNode = STRING,
--     mountPoint = STRING,
--     type = STRING
-- })
--
function _M.getMountedFileSystems()

    local fileSystems = {}
    local mount = io.popen(_M.MOUNT_COMMAND)
    if mount then
        for line in mount:lines() do
            --
            -- Description of fields is defined in man 5 fstab
            --
            -- Example output from mount:
            --
            -- rootfs on / type rootfs (rw)
            -- /dev/root on / type jffs2 (ro,relatime)
            -- none on /proc type proc (rw,relatime)
            -- none on /sys type sysfs (rw,relatime)
            -- mdev on /dev type tmpfs (rw,relatime,size=100k)
            -- none on /tmp type tmpfs (rw,relatime,size=4096k)
            -- none on /dev/pts type devpts (rw,relatime,mode=600)
            -- /dev/mtdblock8 on /tmp/var/config type jffs2 (rw,noatime)
            -- /dev/mtdblock7 on /tmp/var/downloads type jffs2 (rw,noatime)
            -- none on /www/backup type tmpfs (rw,relatime,size=4096k)
            -- none on /proc/bus/usb type usbfs (rw,relatime)
            -- /dev/sda2 on /tmp/sda2 type ufsd (rw,relatime,nls=utf8,uid=0,gid=0,fmask=2,dmask=2)
            -- /dev/sda2 on /tmp/mnt/sda2 type ufsd (rw,relatime,nls=utf8,uid=0,gid=0,fmask=2,dmask=2)
            -- /dev/sda1 on /tmp/sda1 type vfat (rw,relatime,fmask=0002,dmask=0002,allow_utime=0020,codepage=cp437,iocharset=iso8859-1,shortname=mixed,errors=remount-ro)
            -- /dev/sda1 on /tmp/mnt/sda1 type vfat (rw,relatime,fmask=0002,dmask=0002,allow_utime=0020,codepage=cp437,iocharset=iso8859-1,shortname=mixed,errors=remount-ro)
            -- /dev/sda1 on /tmp/anon_smb/FAT Partition (abbr=FAT PARTITI) type vfat (rw,relatime,fmask=0002,dmask=0002,allow_utime=0020,codepage=cp437,iocharset=iso8859-1,shortname=mixed,errors=remount-ro)
            -- /dev/sda2 on /tmp/anon_smb/NTFS Partition type ufsd (rw,relatime,nls=utf8,uid=0,gid=0,fmask=2,dmask=2)
            -- /dev/sda1 on /tmp/ftp/admin_mnt/FAT Partition (abbr=FAT PARTITI) type vfat (rw,relatime,fmask=0002,dmask=0002,allow_utime=0020,codepage=cp437,iocharset=iso8859-1,shortname=mixed,errors=remount-ro)
            -- /dev/sda2 on /tmp/ftp/admin_mnt/NTFS Partition type ufsd (rw,relatime,nls=utf8,uid=0,gid=0,fmask=2,dmask=2)
            --

            local fs_spec, fs_file, fs_vfstype, fs_mntops = line:match('^(%S+) on (.-) type (%S+) (%([^%)]+%))$')
            if fs_spec then
                table.insert(fileSystems, {
                    mountPoint = fs_file,
                    deviceNode = fs_spec,
                    type = fs_vfstype
                })
            end
        end
        mount:close()
    end
    return fileSystems
end

--
-- Eject a USB drive (not synchronized)
--
-- Note: External synchronization methods should be employed
-- by callers of this function to ensure only one instance of it
-- is ever executing at any point. The scripts it is calling are
-- likely to behave in unexpected ways otherwise.
--
-- This method should not be called externally.
-- It is only exposed for unit testing purposes.
--
-- input = STRING
--
function _M.ejectUSBDriveInternal(sc, device)
    return sc:eject_usb_drive_asyn(util.basename(device))
end

--
-- Eject a USB drive.
--
-- Returns false if the drive could not be ejected because another drive is being removed.
--
-- input = CONTEXT, STRING
--
-- output = BOOLEAN
--
function _M.ejectUSBDrive(sc, device)

    -- Use a write lock to ensure only one ejectUSBDriveInternal() call occurs at any point.
    sc:writelock()

    return _M.ejectUSBDriveInternal(sc, device)
end

--
-- Get the ARP table.
--
-- output = ARRAY_OF({
--     ipAddress = IPADDRESS,
--     hwType = NUMBER,
--     flags = NUMBER,
--     macAddress = MACADDRESS,
--     mask = STRING,
--     device = STRING
-- })
--
function _M.getARPTable()
    local arpTable = {}
    local arpTableFile = io.open(_M.ARPTABLE_FILEPATH, 'r')
    if arpTableFile then
        -- The ARP table file has the following format:
        --
        --  IP address       HW type     Flags       HW address            Mask     Device
        --  10.93.148.1      0x1         0x2         00:1e:4a:2f:59:71     *        eth1
        --  192.168.1.144    0x1         0x2         00:24:81:c1:1c:02     *        br0
        --  192.168.1.143    0x1         0x2         00:18:fe:6c:8f:f1     *        br0
        --
        -- Skip the column headers
        arpTableFile:read('*line')
        for line in arpTableFile:lines() do
            for ip, hwType, flags, mac, mask, device in string.gfind(line, '(%d+%.%d+%.%d+%.%d+)%s+0x(%x)%s+0x(%x)%s+(%x%x:%x%x:%x%x:%x%x:%x%x:%x%x)%s+(%S+)%s+(%S+)') do
                table.insert(arpTable, {
                    ipAddress = hdk.ipaddress(ip),
                    hwType = tonumber(hwType, 16),
                    flags = tonumber(flags, 16),
                    macAddress = hdk.macaddress(mac),
                    mask = mask,
                    device = device
                })
            end
        end
        arpTableFile:close()
    end
    return arpTable
end

--
-- Get the connected wireless station list for a given interface.
--
-- input = STRING
--
-- output = ARRAY_OF({
--     macAddress = MACADDRESS,
--     mode = OPTIONAL(STRING),
--     rate = NUMBER,
--     rssi = NUMBER
-- )
--
function _M.getWirelessStationList(sc, iface, isGuest)
    local stations = {}
    local cmd = _M.GETSTALIST_COMMAND

    sc:readlock()
    local isBrcm = (sc:get_hardware_vendor_name() == 'Broadcom')
    local isMrvl = (sc:get_hardware_vendor_name() == 'Marvell')
    local isMtk = (sc:get_hardware_vendor_name() == 'MediaTek')
    local isQCA = (sc:get_hardware_vendor_name() == 'QCA')

    -- for MediaTek chip (Focus), we use wlan_query
    if isMtk then
        cmd = _M.GETSTALIST_MTK_COMMAND
    end

    -- for Broadcom chip (Bentley), we use wlancfg
    if isBrcm then
        cmd = _M.GETSTALIST_BRCM_COMMAND
    end

    -- for Marvell chip (Viper), we use iwpriv
    if isMrvl then
        cmd = _M.GETSTALIST_MRVL_COMMAND
    end

    -- for Qualcomm chip, we use wlanconfig
    if isQCA then
        cmd = _M.GETSTALIST_QCA_COMMAND
    end

    local getSTAList = io.popen(cmd:format(iface))
    if getSTAList then
        if isMtk then
            --
            -- Values taken from linux-2.6.36/drivers/net/wireless/mt_wifi/embedded/ap/ap_cfg.c
            --
            -- The output has the following format:
            --
            -- parameter stainfo
            -- inf_name = 'ra0'
            -- param_name = 'stainfo'
            --
            -- 2.4GHz   : MAC                AID BSS PSM WMM MIMOPS  RSSI0  RSSI1  RSSI2  RSSI3  PhMd      BW    MCS   SGI   STBC  Idle   Rate   TIME
            --            DC:2B:2A:8F:DA:9F  1   0   0   1   3       -47    -45    -45    -49    HTMIX     20M   15    1     0     480    144    11     0         , 0, 0%
            -- 5GHz     : MAC                AID BSS PSM WMM MIMOPS  RSSI0  RSSI1  RSSI2  RSSI3  PhMd      BW    MCS   SGI   STBC  Idle   Rate   TIME
            --            DC:2B:2A:8F:DA:9F  1   0   0   1   3       -50    -47    -49    -50    VHT       80M   2S-M9 0     0     479    540    4      0         , 0, 0%
            local str = getSTAList:read('*line')
            if str and string.match(str, 'parameter stainfo') then
                for line in getSTAList:lines() do
                    for macAddress, rssi, rate in string.gmatch(line, '(%x%x:%x%x:%x%x:%x%x:%x%x:%x%x)%s+%d+%s+'..(isGuest and 1 or 0)..'%s+%d+%s+%d+%s+%d+%s+([%-]?%d+)%s+[%-]*%d+%s+[%-]*[%d+%s+]*[%-]*[%d+%s+]*%u+%s+%d+M%s+%d+[S%-M%d+]*%s+%d+%s+%d+%s+%d+%s+(%d+).*') do
                    --                                                  MAC                              AID       BSS                      PSM   WMM   MIMOPS RSSI0       RSSI1   RSSI2     RSSI3    PhMd  BW     MCS            SGI   STBC  Idle   Rate   TIME
                    --                                                  E4:98:D6:1B:5B:CB                1         0                        1     1     0      -50         -43     -0        -50      VHT   80M    2S-M9          0     0     479    540    4      0         , 0, 0%
                        table.insert(stations, {
                            macAddress = hdk.macaddress(macAddress),
                            mode = nil,
                            rate = tonumber(rate),
                            rssi = tonumber(rssi)
                        })
                    end
                end
            end
        elseif isQCA then
            -- The output has the following format:
            --
            -- 2.4GHz :       ADDR :50:ea:d6:b0:e1:9f AID:   1 CHAN:   6 TXRATE: 19M RXRATE:   129M RSSI:  52 IDLE:4320 TXSEQ:     0 RXSEQ:  65535 CAPS: EPSs ACAPS:      ERP:  0 STATE:         f MAXRATE(DOT11):             0 HTCAPS:            PM ASSOCTIME:00:00:04 RSN WME MODE: IEEE80211_MODE_11NG_HT20 PSMODE: 0
            -- 5GHz :         ADDR :10:1c:0c:4d:60:fe AID:   1 CHAN:  36 TXRATE:120M RXRATE:   230M RSSI:  47 IDLE:   0 TXSEQ:     0 RXSEQ:  65535 CAPS:   EP ACAPS:      ERP:  0 STATE:         b MAXRATE(DOT11):             0 HTCAPS:           WQS ASSOCTIME:00:20:05 RSN WME MODE: IEEE80211_MODE_11NA_HT40 PSMODE: 0
            -- 2.4GHz Guest : ADDR :50:ea:d6:b0:e1:9f AID:   1 CHAN:   6 TXRATE: 38M RXRATE:    59M RSSI:  56 IDLE:4320 TXSEQ:     0 RXSEQ:  65535 CAPS:  ESs ACAPS:      ERP:  0 STATE:         f MAXRATE(DOT11):             0 HTCAPS:            PM ASSOCTIME:00:02:40 WME MODE: IEEE80211_MODE_11NG_HT20 PSMODE: 0
            -- 5GHz Guest :   ADDR :10:1c:0c:4d:60:fe AID:   1 CHAN:  36 TXRATE:120M RXRATE:    21M RSSI:  40 IDLE:   0 TXSEQ:     0 RXSEQ:  65535 CAPS:    E ACAPS:      ERP:  0 STATE:         b MAXRATE(DOT11):             0 HTCAPS:           WQS ASSOCTIME:00:00:49 WME MODE: IEEE80211_MODE_11NA_HT40 PSMODE: 0

            for line in getSTAList:lines() do
                for macAddress, rate, rssi, mode in string.gmatch(line, 'ADDR%s+:(%x%x:%x%x:%x%x:%x%x:%x%x:%x%x).+RXRATE:%s*(%d+)M%s+RSSI:%s*(%d+).+IEEE80211_MODE_11(%u+).*') do
                    table.insert(stations, {
                            macAddress = hdk.macaddress(macAddress),
                            mode = string.lower(mode), -- According to QCA, Impala (EA4500v3 supports the following modes: 11a, 11b, 11g, 11na HT20, 11na HT40, 11ng HT20, 11na HT40)
                            rate = tonumber(rate),
                            rssi = tonumber(rssi)
                        })
                end
            end
        else
            -- Depending on the WLAN driver version, the output has the following format:
            --
            -- Legacy devices:
            --   wdev0ap1  getstalistext:
            --   1: 00:22:5f:86:c9:13 n ASSOCIATED  Rate 300 Mbps, RSSI 52
            --   2: 00:d3:14:d6:59:03 n ASSOCIATED  Rate 270 Mbps, RSSI 16
            --
            -- Possible values are taken from: rango/output/debug/mrvl_wlan_v9drv/build/wlan-v9_drv_9.0.7.3_fw_9.1.9.2/wlan-v9/driver/linux/ap8xLnxApi.c
            --   wdev0ap0  getstalistext:
            --   1: StnId 1 Aid 1 a4:f1:e8:9e:dc:f2 n KEY_CONFIGURED  Rate 6 Mbps, RSSI:A -39  B -41  C -43  D -44
            --   wdev1ap0  getstalistext:
            --   1: StnId 2 Aid 1 dc:2b:2a:8f:da:9f n KEY_CONFIGURED  Rate 6 Mbps, RSSI:A -41  B -42  C -40  D -35
            --
            -- And from shelby/output/debug/mrvl_wlan_v7drv/build/wlan-v7_drv_7.2.9.2.p4_fw_7.2.9.23/wlan-v7/driver/linux/ap8xLnxApi.c
            --   wdev0ap1  getstalistext:
            --   1: StnId 1 Aid 1 dc:2b:2a:8f:da:9f n ASSOCIATED  Rate 866 Mbps, RSSI 36   A -28  B -27  C -31  D -36
            --
            -- mode: [b, g, n, a, NA]
            -- state: [UNAUTHENTICATED, AUTHENTICATING, AUTHENTICATED, DEAUTHENTICATING, ASSOCIATING, PSK-PASSED, KEY_CONFIGURED, ASSOCIATED, REASSOCIATING, DEASSOCIATING]
            --
            -- Check the header line
            local str = getSTAList:read('*line')
            if str and string.match(str, iface..'%s+getstalistext') then
                for line in getSTAList:lines() do
                    for index, macAddress, mode, state, rate, rssi in string.gmatch(line, '(%d+):%s+[StnId%s+%d%s+Aid%s+%d%s+]*(%x%x:%x%x:%x%x:%x%x:%x%x:%x%x)%s+(%a+)%s+(%S+)%s+Rate%s+(%d+)%s+Mbps,%s+RSSI[:A]*%s+[-]*(%d+).*') do
                        table.insert(stations, {
                            macAddress = hdk.macaddress(macAddress),
                            mode = 'NA' ~= mode and mode or nil,
                            rate = tonumber(rate),
                            rssi = tonumber(rssi)
                        })
                    end
                end
            end
        end
        getSTAList:close()
    end
    return stations
end

--
-- Get the link speed, in Mbps (Megabits per second) of the switched
-- connections on the LAN interface.
--
-- input = CONTEXT
--
-- output = ARRAY_OF({
--     macAddress = MACADDRESS,
--     speedMbps = NUMBER
-- })
--
function _M.getSwitchLinkSpeedMap(sc)
    local switchLinkSpeedTable = {}

    --
    -- Sample output from 'linkstate -l' command
    --
    -- mac=00:24:81:a2:73:24 port1 1000
    --
    local links = io.popen(_M.LINKSTATE_L, 'r')
    if links then
        -- Parse the linkstate output
        for line in links:lines() do
            local macAddress, portNumber, speedMbps
            for word in line:gmatch('%S+') do
                if not macAddress then
                    macAddress = word:match('^mac=(%x%x:%x%x:%x%x:%x%x:%x%x:%x%x)$')
                elseif not portNumber then
                    portNumber = word:match('^port(%d+)$')
                else
                    speedMbps = word
                end
            end
            if macAddress then
                table.insert(switchLinkSpeedTable, {
                    macAddress = hdk.macaddress(macAddress),
                    speedMbps = tonumber(speedMbps)
                })
            end
        end
        links:close()
    else
        _M.logMessage(_M.LOG_ERROR, ('failed to open process %s'):format(_M.LINKSTATE_L))
    end
    return switchLinkSpeedTable
end

--
-- Get the static routing table.
--
-- input = CONTEXT
--
-- output = ARRAY_OF({
--     interface = STRING,
--     destinationLAN = IPADDRESS,
--     networkPrefixLength = NUMBER,
--     gateway = IPADDRESS
-- })
--
function _M.getStaticRoutingTable(wanName)
    local entries = {}
    local file = io.popen(_M.GET_ROUTING_TABLE_COMMAND)
    if file then
        for line in file:lines() do
            local words = util.split(line)
            table.insert(entries,
                {
                    interface = (words[8] == wanName) and 'Internet' or 'LAN',
                    destinationLAN = hdk.ipaddress(words[1]),
                    networkPrefixLength = util.subnetMaskToNetworkPrefixLength(hdk.ipaddress(words[3])),
                    gateway = hdk.ipaddress(words[2])
                })
        end
        file:close()
    end
    return entries
end

--
-- Get the IPv6 default route for a given interface
--
-- input = CONTEXT, STRING
--
-- output = IPV6ADDRESS
--
function _M.getIPv6DefaultRoute(sc, ifName)
    local route
    local file = io.popen(_M.GET_IPV6_DEFAULT_ROUTE_COMMAND_FMT:format(util.shellEscape(ifName)))
    if file then
        for line in file:lines() do
            local ipv6 = line:match('default via (%S+)')
            if ipv6 then
                route = hdk.ipv6address(ipv6)
                break
            end
        end
        file:close()
    end
    return route
end

--
-- Get the IPv6 link local address for a given interface.
--
-- input = CONTEXT, STRING
--
-- output = IPV6ADDRESS
--
function _M.getIPv6LinkLocalAddress(sc, wanIfName)
    local linkAddress
    local file = io.popen(_M.GET_IPV6_LINK_LOCAL_COMMAND_FMT:format(util.shellEscape(wanIfName)))
    if file then
        for line in file:lines() do
            local ipv6 = line:match('inet6 ([^/]+)/64 scope link')
            if ipv6 then
                linkAddress = hdk.ipv6address(ipv6)
                break
            end
        end
        file:close()
    end
    return linkAddress
end

--
-- Get the DHCP client lease table.
--
-- input = CONTEXT
--
-- output = ARRAY_OF({
--     macAddress = MACADDRESS,
--     ipAddress = IPADDRESS,
--     expiration = DATETIME,
--     hostName = OPTIONAL(STRING),
--     clientID = OPTIONAL(STRING)
-- })
--
function _M.getDHCPClientLeases(sc)
    local leases = {}
    -- Format is: LEASE MAC IP HOST|* ID|*
    --
    --  '*' indicates that the value is not provided.
    --
    -- Example:
    --      1331413178 00:22:5f:86:c9:13 192.168.1.129 joslang-win7x64 01:00:22:5f:86:c9:13
    --
    local function isValid(str)
        return str ~= '*'
    end
    for line in io.lines(_M.DHCP_CLIENT_LEASE_FILEPATH) do
        local expire, mac, ip, host, id = line:match('(%S+)%s+(%x%x:%x%x:%x%x:%x%x:%x%x:%x%x)%s+(%d+%.%d+%.%d+%.%d+)%s+(%S+)%s+(%S+)')
        if expire then
            table.insert(leases, {
                macAddress = hdk.macaddress(mac),
                ipAddress = hdk.ipaddress(ip),
                expiration = hdk.datetime(tonumber(expire)),
                hostName = isValid(host) and host or nil,
                clientID = isValid(id) and id or nil
            })
        end
    end

    return leases
end

local services = {
    tcp = {},
    udp = {}
}

local function getServiceName(proto, port)
    local service
    proto = proto:lower()
    if #services['tcp'] == 0 then
        for line in io.lines(_M.SERVICES_FILEPATH) do
            local name, port, proto = line:match('^([%w-_]+)%s+(%d+)/(%l+)')
            if proto == 'tcp' or proto == 'udp' then
                services[proto][tonumber(port)] = name
            end
        end
    end
    return services[proto][port]
end

local function datetimeFromMonthDayTime(month, day, hour, minute, second)
    local months = {
        ['Jan'] = 1,
        ['Feb'] = 2,
        ['Mar'] = 3,
        ['Apr'] = 4,
        ['May'] = 5,
        ['Jun'] = 6,
        ['Jul'] = 7,
        ['Aug'] = 8,
        ['Sep'] = 9,
        ['Oct'] = 10,
        ['Nov'] = 11,
        ['Dec'] = 12
    }
    local time = {}
    local currtime = os.date('!*t')
    local mon = months[month]
    -- Year isn't specified so we'll need to figure it out
    -- Handle case where year has rolled over
    time.year = mon > currtime.month and (currtime.year - 1) or currtime.year
    time.month = mon
    time.day = tonumber(day)
    time.hour = tonumber(hour)
    time.min = tonumber(minute)
    time.sec = tonumber(second)
    return os.time(time)
end

--
-- Return oldest count of entries from specified log on the local device.
--
-- input = CONTEXT, STRING, NUMBER, NUMBER
--
-- output = ARRAY_OF({ })
--
function _M.getLogEntries(sc, logType, index, count)
    assert('number' == type(index))
    assert('number' == type(count))

    local equal = function(e1, e2)
        for k, v in pairs(e1) do
            if e2[k] ~= v then
                return false
            end
        end
        return true
    end

    -- local cache of ARP table
    local arpTable

    local logs = {
        ['IncomingTraffic'] =
            {
                regex = 'UTOPIA: FW.WAN2\\(LAN\\|SELF\\) ACCEPT .* PROTO=\\(TCP\\|UDP\\)',
                parse = function(line)
                    local src, dpt = line:match('SRC=(%S+).*DPT=(%S+)')
                    if src then
                        return {
                            source = hdk.ipaddress(src),
                            destinationPort = tonumber(dpt)
                        }
                    end
                end
            },
        ['OutgoingTraffic'] =
            {
                regex = 'UTOPIA: FW.LAN2WAN ACCEPT .* PROTO=\\(TCP\\|UDP\\)',
                parse = function(line)
                    local src, dst, proto, dpt = line:match('SRC=(%S+).*DST=(%S+).*PROTO=(%S+).*DPT=(%S+)')
                    if src then
                        local destinationPort = tonumber(dpt)
                        return {
                            source = hdk.ipaddress(src),
                            destination = hdk.ipaddress(dst),
                            destinationPort = destinationPort,
                            service = getServiceName(proto, destinationPort)
                        }
                    end
                end
            },
        ['Security'] =
            {
                regex = 'jnapcgi.*\\?authorization',
                parse = function(line)
                    --
                    -- Example log entry:
                    -- IPv4:
                    -- Jul 13 18:25:01 joslang-ea4500 daemon.warn jnapcgi[5237]: Succeeded authorization for client: 192.168.1.104 action: http://linksys.com/jnap/core/CheckAdminPassword
                    --
                    -- IPv6:
                    -- Feb  4 01:16:22 blk-mamba daemon.warn jnapcgi[10289]: Succeeded authorization for client: fe80::41d4:91c9:61d4:9ccf action: http://linksys.com/jnap/core/CheckAdminPassword
                    --
                    local month, day, hr, min, sec, result, ip = line:match('^(%S+)%s+(%d+)%s+(%d+):(%d+):(%d+).*jnapcgi.*: (%S+) authorization for client: (%d+%.%d+%.%d+%.%d+)')
                    if month and day and hr and min and sec and result and ip then
                        local ipAddress = hdk.ipaddress(ip)
                        local macAddress
                        --
                        -- Retrieve the MAC address for the given IP.
                        --
                        -- Note: It is possible that the IP has been associated with
                        -- a different IP between the time the log entry was create.
                        -- However, this is likely rare given that DHCP-assigned addresses
                        -- are generated based on the MAC address. Furthermore MAC addresses
                        -- can easily be spoofed making the log entries merely informative,
                        -- and not guaranteed to be accurate anyway.
                        --
                        if not arpTable then
                            arpTable = _M.getARPTable()
                        end
                        for _, entry in ipairs(arpTable) do
                            if entry.ipAddress == ipAddress then
                                macAddress = entry.macAddress
                                break
                            end
                        end

                        return {
                            timestamp = datetimeFromMonthDayTime(month, day, hr, min, sec),
                            ipAddress = ipAddress,
                            macAddress = macAddress or hdk.macaddress('00:00:00:00:00:00'),
                            authSucceeded = result == 'Succeeded'
                        }
                    else
                        _M.logMessage(_M.LOG_WARNING, string.format('Match failed for this line: "%s"...', line))
                    end
                end
            },
        ['DHCPClient'] =
            {
                regex = 'DHCP\\(DISCOVER\\|OFFER\\|REQUEST\\|ACK\\|NAK\\|DECLINE\\|RELEASE\\|INFORM\\)',
                parse = function(line)
                    local month, day, hr, min, sec, action, index = line:match('^(%S+)%s+(%d+)%s+(%d+):(%d+):(%d+).*DHCP(%u+)%S*%s+()')
                    if month then
                        local ip, mac
                        local data = line:sub(index)
                        ip, mac = data:match('^([%d.]+)%s+([%x:]+)')
                        if not ip then
                            mac = data:match('^([%x:]+)')
                        end
                        if mac then
                            return {
                                timestamp = datetimeFromMonthDayTime(month, day, hr, min, sec),
                                messageType = action:sub(1,1)..action:sub(2):lower(),
                                macAddress = hdk.macaddress(mac),
                                ipAddress = ip and hdk.ipaddress(ip)
                            }
                        end
                    end
                end
            }
    }
    local log = logs[logType]
    assert(log, 'Unsupported log type: '..logType)
    local entries = {}
    local file = io.popen(_M.GET_LOG_COMMAND:format(log.regex, _M.MESSAGES_FILEPATH, index, index + count - 1))
    if file then
        local prevEntry
        for line in file:lines() do
            local entry = log.parse(line)
            if entry and (not prevEntry or not equal(prevEntry, entry)) then
                table.insert(entries, entry)
                prevEntry = entry
            end
        end
        file:close()
    end

    return entries
end

--
-- Deletes all the log messages.
--
function _M.deleteLog()
    os.remove(_M.MESSAGES_FILEPATH)
end

--
-- Restores the device to factory default settings.
--
-- input = CONTEXT
--
function _M.restoreFactoryDefaults(sc)
    sc:writelock()
    if util.isNodeUtilModuleAvailable() then
        os.execute(_M.RESET_NODES_CMD)
    end
    sc:setevent('router_status', 'FACTORY_RESET')
    os.execute(_M.FACTORY_RESET_CMD)
    sc:setevent('jnap-restorefactorydefaults')
end

--
-- Restore device's previous firmware image.
--
function _M.restorePreviousFirmware()
    os.execute(_M.RESTORE_PREVIOUS_FIRMWARE_CMD)
end

function _M.isDeviceReady(sc, services)
    return sc:is_device_ready()
end


function _M.isReady(sc, services)
    local ready = _M.isDeviceReady(sc, services)
    return ready
end


--
-- Get connections from conntrack
--
local function getConnections(command, fnAddressConversion)

    local connections = {}

    local file = io.popen(command)
    if file then
        for line in file:lines() do
            --
            -- Sample:
            -- tcp      6 1770 ESTABLISHED src=192.168.1.111 dst=140.174.24.194 sport=54287 dport=443 packets=15 bytes=3007 src=140.174.24.194 dst=10.93.148.183 sport=443 dport=54287 packets=25 bytes=19843 [ASSURED] mark=02
            -- udp      17 15 src=10.93.148.183 dst=171.70.168.183 sport=46615 dport=53 packets=1 bytes=62 src=171.70.168.183 dst=10.93.148.183 sport=53 dport=46615 packets=1 bytes=533 mark=0 use=2
            -- unknown  2 1 src=192.168.1.1 dst=224.0.0.251 packets=1 bytes=32 [UNREPLIED] src=224.0.0.251 dst=192.168.1.1 packets=0 bytes=0 mark=0 use=2
            --
            -- Note: This regex will not match the 'unknown' protocol lines. These are typically mutlicast messages, and probably fine to exclude.
            local protocol, ttl, state, localSource, localDest, localSourcePort, localDestPort, sentPackets, sentBytes, remoteSource, remoteDest, remoteSourcePort, remoteDestPort, recvPackets, recvBytes =
                line:match('^(%w+)%s+%d+ (%d+) (%S-) ?src=(%S+) dst=(%S+) sport=(%d+) dport=(%d+) packets=(%d+) bytes=(%d+) src=(%S+) dst=(%S+) sport=(%d+) dport=(%d+) packets=(%d+) bytes=(%d+)')
            if protocol then
                table.insert(connections, {
                    protocol = protocol,
                    ttl = tonumber(ttl),
                    state = state, -- will be nil for non-TCP connections
                    localSource = fnAddressConversion(localSource),
                    localDest = fnAddressConversion(localDest),
                    localSourcePort = tonumber(localSourcePort),
                    localDestPort = tonumber(localDestPort),
                    sentPackets = tonumber(sentPackets),
                    sentBytes = tonumber(sentBytes),
                    remoteSource = fnAddressConversion(remoteSource),
                    remoteDest = fnAddressConversion(remoteDest),
                    remoteSourcePort = tonumber(remoteSourcePort),
                    remoteDestPort = tonumber(remoteDestPort),
                    recvPackets = tonumber(recvPackets),
                    recvBytes = tonumber(recvBytes)
                })
            end
        end
        file:close()
    end

    return connections
end

function _M.getIPv4Connections(sc)
    return getConnections(_M.CONNTRACK_L_IPV4, hdk.ipaddress)
end

function _M.getIPv6Connections(sc)
    return getConnections(_M.CONNTRACK_L_IPV6, hdk.ipv6address)
end

local fileExists = function(path)
    return ( io.open(path or '', 'r') ~= nil and true or false )
end

function _M.startConnectionAccounting(sc)
    sc:writelock()

    -- Need to check to make sure that tracking is not already enabled
    if fileExists(_M.CONNECTION_ACCT_FILE) then
        return 'ErrorStatisticsTrackingStarted'
    end

    -- Execute the conntrack command to zero out the counters
    os.execute(_M.CONNTRACK_L_IPV4.. ' -z > /dev/null 2>&1')
    os.execute(_M.CONNTRACK_L_IPV6.. ' -z > /dev/null 2>&1')

    -- Write out connection timeout cron job
    local fh = io.open(_M.CONNECTION_ACCT_FILE, 'w+')
    fh:write((_M.CONNECTION_ACCT_FILE_FMT):format('`date +%s`', _M.CONNECTION_ACCT_FILE))
    fh:close()
    os.execute('chmod +x '.._M.CONNECTION_ACCT_FILE)

    -- Set the connection timeout
    sc:setnumber('conntrack_timestamp', _M.getCurrentUTCTime(sc) + 60)
end

function _M.stopConnectionAccounting(sc)
    sc:readlock()

    -- Need to check to make sure that tracking is enabled
    if not fileExists(_M.CONNECTION_ACCT_FILE) then
        return 'ErrorStatisticsTrackingStopped'
    end

    -- Remove cron job
    os.remove(_M.CONNECTION_ACCT_FILE)
end

--
-- Determine whether an email address is valid format or not.
--
-- input = STRING
--
-- output = BOOLEAN
--
function _M.isValidEmailFormat(email)
    if type(email) ~= 'string' then
        return false
    end

    -- the email address should be consisted of '@' and '.'
    return email:match('[A-Za-z0-9%.%%%+%-]+@[A-Za-z0-9%.%%%+%-]+%.%w%w%w?%w?') ~= nil
end

function _M.getUptimeSeconds()
    return tonumber(io.popen(_M.GET_UPTIME_CMD):read())
end


return _M -- return the module
