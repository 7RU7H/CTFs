--
-- 2019 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author: hesia $
-- $DateTime: 2019/07/23 14:00:53 $
-- $Id: //depot-irv/user/hesia/pinnacle_f70_auth_jnap/lego_overlay/proprietary/jnap/lualib/util.lua#5 $
--

-- util.lua - library of stateless utility functions.

local _M = {} -- create the module

local hdk = require('libhdklua')

_M.unittest = {} -- unit test override data store

_M.TIME_PERIOD_REGEX = '^(%d%d):(%d%d)'


--
-- Convert a string to an IP address.
--
-- input = STRING
--
-- output = NIL_OR(IPADDRESS)
--
function _M.addressify(str)
    if str then
        local ipaddr = hdk.ipaddress(str)
        if not ipaddr:iszero() then
            return ipaddr
        end
    end
    return nil
end

--
-- Create a set from an array. For each unique value V in the array,
-- set[V] will be set to true.
--
-- input = ARRAY_OF(ANY)
--
-- output = MAP_OF(ANY, BOOLEAN)
--
function _M.arrayToSet(a)
    local s = {}
    for i, v in pairs(a) do
        s[v] = true
    end
    return s
end

--
-- Create an array from an set. The array will contain every key K
-- that satisfies the condition (not not set[K])
--
-- input = MAP_OF(ANY, BOOLEAN)
--
-- output = ARRAY_OF(ANY)
--
function _M.setToArray(s)
    local a = {}
    for k, v in pairs(s) do
        if s[k] then
            table.insert(a, k)
        end
    end
    return a
end

--
-- Escape a string so that it will be parsed as a single word with no
-- metacharacters or expansions by Bourne shell.
--
-- input = STRING
--
-- output = STRING
--
function _M.shellEscape(s)
    s = s:gsub('[$`"\\]', function(c) return '\\'..c end)
    return '"'..s..'"'
end

--
-- Determine whether a port number is valid.
--
-- input = NUMBER
--
-- output = BOOLEAN
--
function _M.isValidPort(port)
    return port >= 0 and port <= 65535
end

--
-- Parse abbreviated dot-decimal notation.
-- Any octets of 0 are not included. E.g. 0.0.128.100 would appear as 128.100
--
-- input = STRING
--
-- output = IPADDRESS
--
function _M.parseAbbreviatedDotDecimal(value)
    local octets = _M.splitOnDelimiter(value, '.')
    for i = 1, (4 - #octets) do
        table.insert(octets, i, '0')
    end
    return hdk.ipaddress(tonumber(octets[1]), tonumber(octets[2]), tonumber(octets[3]), tonumber(octets[4]))
end

--
-- Serialize an IP address to abbreviated dot-decimal notation
-- Any octets of 0 are not included. E.g. 0.0.128.100 would appear as 128.100
--
-- input = IPADDRESS
--
-- output = STRING
--
function _M.serializeAbbreviatedDotDecimal(ip)
    local output = ''
    for i = 1, 4 do
        -- Discard ONLY leading 0 octets
        if 0 ~= ip[i] or #output > 0 then
            if #output > 0 then
                output = output..'.'
            end
            output = output..tostring(ip[i])
        end
    end
    return output
end

--
-- Convert an IP address to a (32-bit) integer
--
-- input = IPADDRESS
--
-- output = NUMBER
--
function _M.ipAddressToInteger(ip)
    return ((ip[1] ~= 0) and (ip[1] * 2 ^ 24) or 0) +
            ((ip[2] ~= 0) and (ip[2] * 2 ^ 16) or 0) +
            ((ip[3] ~= 0) and (ip[3] * 2 ^ 8) or 0) +
            ((ip[4] ~= 0) and (ip[4]) or 0)
end

--
-- Determine whether the given number is a valid CIDR subnet prefix length.
--
-- input = NUMBER
--
-- OUTPUT = BOOLEAN
--
function _M.isValidNetworkPrefixLength(prefixLength)
    return (0 <= prefixLength and prefixLength <= 32)
end

--
-- Determine the maximum number of hosts for a given CIDR block (network prefix length)
--
-- Note: For subnetworks larger than /31, this does NOT include the two reserved addresses format
-- the network id and subnet broadcast address. See RFC 3021 in regards to the /31 exception.
--
-- INPUT = NUMBER
--
-- OUTPUT = NUMBER
--
function _M.possibleSubnetHosts(prefixLength)
    return ((2 ^ (32 - prefixLength)) - ((prefixLength < 31) and 2 or 0))
end

--
-- Convert a network prefix length to a subnet mask.
--
-- INPUT = NUMBER
--
-- OUTPUT = IPADDRESS
--
function _M.networkPrefixLengthToSubnetMask(prefixLength)
    assert(_M.isValidNetworkPrefixLength(prefixLength))

    local mask = { 0, 0, 0, 0 }
    for i = 1, math.floor(prefixLength / 8) do
        mask[i] = 255
    end
    local bits = prefixLength % 8
    if bits ~= 0 then
        local i = math.floor(prefixLength / 8) + 1
        while bits > 0 do
            mask[i] = mask[i] + (2 ^ (8 - bits))
            bits = bits - 1
        end
    end

    return hdk.ipaddress(mask[1], mask[2], mask[3], mask[4])
end

--
-- Convert a subnet mask to a network prefix length.
--
-- INPUT = IPADDRESS
--
-- OUTPUT = NUMBER
--
function _M.subnetMaskToNetworkPrefixLength(mask)
    local prefixLength = 0
    local function isBitSet(x, bit)
        return (x % (bit + bit) >= bit)
    end

    for bit = 0, 31 do
        local octet = mask[math.floor(bit / 8) + 1]
        local bitMask = (2 ^ (7 - (bit % 8)))
        if isBitSet(octet, bitMask) then
            if prefixLength == bit then
                prefixLength = prefixLength + 1
            else
                -- Non-contiguous bit is set.
                error(('invalid subnet mask %s bit %d'):format(tostring(mask), bit))
            end
        end
    end

    return prefixLength
end

--
-- Check if two IP addresses are in the same subnet
--
-- INPUT = IPADDRESS, IPADDRESS, NUMBER
--
-- OUTPUT = BOOLEAN
--
function _M.inSameSubnet(ip1, ip2, networkPrefixLength)
    return ip1:networkid(networkPrefixLength) == ip2:networkid(networkPrefixLength)
end


--
-- Check if an IP address is in the "this" network range, as defined in RFC 1112, Section 3.2.1.3
--
--  * 0.0.0.0/8
--
-- INPUT = IPADDRESS
--
-- OUTPUT = BOOLEAN
--
function _M.isThisNetworkAddress(ip)
    return ip:networkid(8) == hdk.ipaddress(0,0,0,0)
end

--
-- Check if an IP address is in the private network range, as defined in RFC 1918.
--
--  * 10.0.0.0/8
--  * 172.16.0.0/12
--  * 192.168.0.0/16
--
-- INPUT = IPADDRESS
--
-- OUTPUT = BOOLEAN
--
function _M.isPrivateAddress(ip)
    return ip:networkid(8) == hdk.ipaddress(10,0,0,0) or
            ip:networkid(12) == hdk.ipaddress(172,16,0,0) or
            ip:networkid(16) == hdk.ipaddress(192,168,0,0)
end

--
-- Check if an IP address is in the loopback range, as defined in RFC 5735.
--
--  * 127.0.0.0/8
--
-- INPUT = IPADDRESS
--
-- OUTPUT = BOOLEAN
--
function _M.isLoopbackAddress(ip)
    return ip:networkid(8) == hdk.ipaddress(127,0,0,0)
end

--
-- Check if an IP address is in the link local reserved range, as defined in RFC 3927.
--
-- * 169.254.0.0/16
--
-- INPUT = IPADDRESS
--
-- OUTPUT = BOOLEAN
--
function _M.isLinkLocalAddress(ip)
    return ip:networkid(16) == hdk.ipaddress(169,254,0,0)
end

--
-- Check if an IP address is in the multicast range, as defined in RFC 5771.
--
--  * 224.0.0.0/8 - 239.0.0.0/8
--
-- INPUT = IPADDRESS
--
-- OUTPUT = BOOLEAN
--
function _M.isMulticastAddress(ip)
    local octet = ip:networkid(8)[1]
    return ((224 <= octet) and (octet <= 239))
end

--
-- Check if an IP address is in the reserved range, as defined in RFC 1112.
--
--  * 240.0.0.0/8 - 255.0.0.0/8
--
-- INPUT = IPADDRESS
--
-- OUTPUT = BOOLEAN
--
function _M.isReservedForFutureUseAddress(ip)
    return (240 <= ip:networkid(8)[1])
end

--
-- Check if an IP address is the limited broadcast address, as defined in RFC 919.
--
--  * 255.255.255.255/32
--
-- INPUT = IPADDRESS
--
-- OUTPUT = BOOLEAN
--
function _M.isLimitedBroadcastAddress(ip)
    return ip == hdk.ipaddress(255,255,255,255)
end

--
-- Check if an IP address is a reserved subnetwork address, as define in RFC 922 and RFC 3021.
--
-- This checks if an IP is the network ID or broadcast address for a given subnet.
--
-- input = IPADDRESS, NUMBER
--
-- OUTPUT = BOOLEAN
--
function _M.isReservedSubnetAddress(ip, networkPrefixLength)
    -- RFC 3021 excludes /31 and /32 subnetworks from this restriction.
    return (networkPrefixLength < 31) and ((ip:broadcast(networkPrefixLength) == ip) or
                                           (ip:networkid(networkPrefixLength) == ip))
end

--
-- Check if an IP address is a reserved address.
--
-- This verifies that an IP does not:
--
--  * Fall in the "this" network range [RFC 1122]
--  * Fall in the loopback range [RFC 5735]
--  * Fall in the link local range [RFC 3927]
--  * Fall in the multicast range [RFC 5771]
--  * Fall in the "reserved for future use" range [RFC 1112]
--  * Equal the "limited broadcast" address [RFC 919]
--  * Is a reserved subnet address, if the network prefix length is supplied [RFC 922 RFC 3021]
--
-- INPUT = IPADDRESS
--
-- OUTPUT = BOOLEAN
--
function _M.isReservedAddress(ip, networkPrefixLength)
    return _M.isThisNetworkAddress(ip) or
            _M.isLoopbackAddress(ip) or
            _M.isLinkLocalAddress(ip) or
            _M.isMulticastAddress(ip) or
            _M.isReservedForFutureUseAddress(ip) or
            _M.isLimitedBroadcastAddress(ip) or
            (networkPrefixLength and _M.isReservedSubnetAddress(ip, networkPrefixLength))
end


--
-- Check if a MAC address is a reserved address.
--
-- See http://www.iana.org/assignments/ethernet-numbers for more information.
--
-- This verifies that a MAC address does not:
--
-- * Fall in the reserved IPv4 multicast range [RFC 1112]
-- * Fall in the reserved MPLS multicast range [RFC 5332]
-- * Fall in the IANA reserved range [RFC 5342]
--
-- input = MACADDRESS
--
-- output = BOOLEAN
--
function _M.isReservedMACAddress(mac)
    return (mac[1] == 0x00 and mac[2] == 0x00 and mac[3] == 0x00 and mac[4] == 0x00 and mac[5] == 0x00) or -- RFC 5342
            (mac[1] == 0x00 and mac[2] == 0x00 and mac[3] == 0x00 and mac[4] == 0x00 and mac[5] == 0x01) or -- RFC 5342
            (mac[1] == 0x00 and mac[2] == 0x00 and mac[3] == 0x00 and mac[4] == 0x00 and mac[5] == 0x02) or -- RFC 5342
            (mac[1] == 0x00 and mac[2] == 0x00 and mac[3] == 0x00 and mac[4] == 0x00 and mac[5] == 0x03 and mac[6] == 0x00) or -- RFC 5342
            (mac[1] == 0x01 and mac[2] == 0x00 and mac[3] == 0x5e and mac[4] < 0x80) or -- RFC 1112
            (mac[1] == 0x01 and mac[2] == 0x00 and mac[3] == 0x5e and mac[4] < 0x90) or -- RFC 5332
            (mac[1] == 0x01 and mac[2] == 0x00 and mac[3] == 0x5e and mac[4] == 0x90 and mac[5] == 0x00 and mac[6] == 0x00) or -- draft-ietf-mpls-tp-ethernet-addressing
            (mac[1] == 0xFF and mac[2] == 0xFF and mac[3] == 0xFF and mac[4] == 0xFF and mac[5] == 0xFF and mac[6] == 0xFF) -- RFC 5342
end

--
-- Determine whether an IPv6 prefix is valid.
--
-- input = IPV6ADDRESS, NUMBER
--
-- output = BOOLEAN
--
function _M.isValidIPv6Prefix(prefix, length)
    -- the prefix must be all 0 bits after the specified length
    local remainingPrefixBits = length
    for i = 1, 16 do
        if remainingPrefixBits == 0 then
            -- all 8 bits must be 0
            if prefix[i] ~= 0 then
                return false
            end
        elseif remainingPrefixBits < 8 then
            -- last (8 - remainingPrefixBits) bits of prefix must be 0
            if prefix[i] % (2 ^ (8 - remainingPrefixBits)) ~= 0 then
                return false
            end
            remainingPrefixBits = 0
        else
            -- all 8 bits can be 0 or 1
            remainingPrefixBits = remainingPrefixBits - 8
        end
    end
    return true
end

--
-- Determine whether a string is a valid PPP Authentication peer ID (username),
-- as defined in RFC 1334, section 2.2.1
--
-- input = STRING
--
-- output = BOOLEAN
--
function _M.isValidPPPAuthenticationPeerID(peerID)
    return ((0 <= #peerID) and (#peerID <= 255))
end

--
-- Determine whether a string is a valid PPP Authentication password,
-- as defined in RFC 1334, section 2.2.1
--
-- input = STRING
--
-- output = BOOLEAN
--
function _M.isValidPPPAuthenticationPassword(password)
    return ((0 <= #password) and (#password <= 255))
end

--
-- Determine whether a string is a valid PPPoE Service-Name.
-- RFC 2516 defines this as a TLV tag, which could have a length
-- of up to 65535 (16 bit). In practice this will not be used, given
-- MTU and other protocol restrictions, but there isn't a definitive
-- value otherwise. Hence the returned value is rather arbitrary.
--
-- input = STRING
--
-- output = BOOLEAN
--
function _M.isValidPPPoEServiceName(serviceName)
    return ((0 <= #serviceName) and (#serviceName <= 255))
end

_M.MAX_DNS_LABEL_LENGTH = 63

--
-- Determine whether a string is a valid DNS host name label.
--
-- input = STRING, OPTIONAL(BOOLEAN)
--
-- output = BOOLEAN
--
function _M.isValidHostNameLabel(label, rfc1123Revision)
    -- Label must be between 1 and 63 characters
    if #label < 1 or #label > _M.MAX_DNS_LABEL_LENGTH then
        return false
    end
    -- Valid characters are: [a-z],[A-Z],[0-9],'-'
    -- First character must be a letter or number (if rfc1123Revision).
    -- The last character must not be a '-'
    return label:match(('^%s%s$'):format(rfc1123Revision and '%w' or '%a', (#label > 1) and '[%-%w]-%w' or '')) ~= nil
end

_M.MAX_DNS_HOST_NAME_LENGTH = 255

--
-- Determine whether a string is a valid host name.
--
-- A valid hostname is defined in RFC 952 and RFC 1123, section 2.1
--
-- input = STRING
--
-- output = BOOLEAN
--
function _M.isValidHostName(name)
    if type(name) ~= 'string' then
        return false
    end

    assert(nil ~= name)
    -- Overall length must be between 1 and 255 characters.
    if #name < 1 or #name > _M.MAX_DNS_HOST_NAME_LENGTH then
        return false
    end

    -- Split the name into labels
    local labels = _M.splitOnDelimiter(name, '.')

    for _, label in ipairs(labels) do
        -- Make sure the label is valid
        if not _M.isValidHostNameLabel(label, true) then
            return false
        end
    end

    return true
end

--
-- Determine if a string is a valid WPS PIN.
--
-- Currently accepted WPS PINs include:
--
-- * 4-digit string
-- * 8-digit string, which passes WPS PIN checksum
--
-- input = STRING
--
-- output = BOOLEAN
--
function _M.isValidWPSPIN(pin)
    if type(pin) ~= 'string' then
        return false
    end
    if #pin ~= 4 and #pin ~= 8 then
        return false
    end
    if pin:match('[^%d]') then
        return false
    end

    if 8 == #pin then
        local pinNumber = tonumber(pin)
        -- Adapted from WPS specification
        local accum = 0
        accum = accum + (3 * math.floor(pinNumber / 10000000) % 10)
        accum = accum + (1 * math.floor(pinNumber / 1000000) % 10)
        accum = accum + (3 * math.floor(pinNumber / 100000) % 10)
        accum = accum + (1 * math.floor(pinNumber / 10000) % 10)
        accum = accum + (3 * math.floor(pinNumber / 1000) % 10)
        accum = accum + (1 * math.floor(pinNumber / 100) % 10)
        accum = accum + (3 * math.floor(pinNumber / 10) % 10)
        accum = accum + (1 * math.floor(pinNumber / 1) % 10)

        return (0 == (accum % 10))
    end

    return true
end

--
-- Get the base name of a path.
--
-- input = STRING
--
-- output = STRING
--
function _M.basename(path)
    return path:match('([^/]+)$')
end

--
-- Determine whether a directory name is valid.
--
-- input = STRING
--
-- output = BOOLEAN
--
function _M.isValidDirectoryName(name)
    return (#name >= 1 and
            #name <= 22 and
            name ~= '.' and
            name ~= '..' and
            not name:find('^ ') and
            not name:find(' $') and
            not name:find('[%c/\\?*:|<>"]'))
end

--
-- Creates the set of subdirectories of a directory.
--
-- input = CONTEXT, STRING
--
-- output = OPTIONAL(SET_OF(STRING))
--
function _M.getSubdirectorySet(path)
    local subdirectories
    if not path:match('/$') then
        path = path..'/'
    end
    local lfs = require('lfs')
    if lfs.attributes(path, 'mode') == 'directory' then
        subdirectories = {}
        for name in lfs.dir(path) do
            if name ~= '.' and name ~= '..' and lfs.attributes(path..name, 'mode') == 'directory' then
                subdirectories[name] = true
            end
        end
    end
    return subdirectories
end

function _M.isPathADirectory(path)
    local lfs = require('lfs')
    return lfs.attributes(path, 'mode')  == 'directory'
end

function _M.isPathAFile(path)
    local lfs = require('lfs')
    return lfs.attributes(path, 'mode')  == 'file'
end

--
-- Combine given paths
--
-- input = ARRAY_OF(STRING)
--
function _M.concatPaths(path)
    return table.concat(path, '/')
end

--
-- Deletes the file or directory. If directory is not empty, it is recursively deleted.
--
-- input = STRING
--
function _M.removePath(path)
    local lfs = require('lfs')
    local mode = lfs.attributes(path, 'mode')
    if mode == 'file' then
        os.remove(path)
    elseif mode == 'directory' then
        for file in lfs.dir(path) do
            if file ~= '.' and file ~= '..' then
                _M.removePath(path..'/'..file)
            end
        end
        lfs.rmdir(path)
    end
end

--
-- Returns content of the file.
-- Returns nil if it's not a file or there's a failure in reading the file.
--
-- input = STRING
--
function _M.readFile(path)
    local lfs = require('lfs')
    local retVal = nil
    if lfs.attributes(path, 'mode') == 'file' then
        local fh = io.open(path)
        if fh then
            retVal = fh:read('*a')
            fh:close()
        end
    end
    return retVal
end

--
-- Split whitespace-delimited string into array of words.
--
-- input = STRING
--
-- output = ARRAY_OF(STRING)
--
function _M.split(str)
    local words = {}
    for word in string.gmatch(str, "%S+") do
        table.insert(words, word)
    end
    return words
end

--
-- Slit a string on a given string delimiter.
--
-- input = STRING, STRING
--
-- output = ARRAY_OF(STRING)
--
function _M.splitOnDelimiter(str, delimiter)
    local strings = {}

    local start = 1
    local delimiterLocation = str:find(delimiter, start, true)
    while delimiterLocation do
        table.insert(strings, str:sub(start, delimiterLocation - 1))
        start = delimiterLocation + 1
        delimiterLocation = str:find(delimiter, start, true)
    end
    table.insert(strings, str:sub(start))

    return strings
end

--
-- Adds unique leading and trailing, non-space characters to a string.
--
-- input = STRING
--
-- output = STRING
--
function _M.wrap(str)
    local x = _M.unittest.wrap_chars or string.format('%.8x', os.time())
    return x:sub(1, 4)..str..x:sub(5)
end

--
-- Removes wrap characters from a string.
--
-- input = STRING
--
-- output = STRING
--
function _M.unwrap(str)
    return str:sub(5, -5)
end

--
-- Determines whether an input string is a valid email address.
--
-- input = STRING
--
-- output = BOOLEAN
--
function _M.isValidEmailAddress(str)
    if (str == nil) or (type(str) ~= 'string') then
        return false
    end
    local lastAt = str:find("[^%@]+$")
    local localPart = str:sub(1, (lastAt - 2))    -- Returns the substring before '@' symbol
    local domainPart = str:sub(lastAt, #str)      -- Returns the substring after '@' symbol

    -- Validate the local name part of the address
    if (localPart == nil) or (#localPart > 64) or (lastAt >= 65) then
        return false
    end

    -- quotes are only allowed at the beginning of a the local name
    local quotes = localPart:find("[\"]")
    if type(quotes) == 'number' and quotes > 1 then
        return false
    end
    -- no @ symbols allowed outside quotes
    if localPart:find("%@+") and quotes == nil then
        return false
    end
    -- Validate the domain name part of the address
    if not _M.isValidHostName(domainPart) then
        return false
    end
    -- just a general match
    if not str:match('[%w]*[%p]*%@+[%w]*[%.]?[%w]*') then
        return false
    end
    -- all tests passed, so the address is valid
    return true
end

--
-- print table
--
-- input = STRING , STRING
--
-- output = STRING
--
local function printTable(t, prefix)
    if not prefix then
        prefix = ''
    end

    for k, v in pairs(t) do
        if type(v) == 'table' then
            print(prefix .. k .. ' :')
            printTable(v, prefix .. '  ')
        else
            print(prefix .. k .. ' : ' .. tostring(v))
        end
    end
end

-- Lua version of Perl chomp function.
-- Removes trailing newline.  Inspired by Perl chomp.
--
-- input = STRING
--
-- output = STRING
--
function _M.chomp(s)
   return s:gsub( "\n$", "" )
end

--
-- Helper function to print the time period string.
--
-- input = NUMBER
--
-- output = STRING
--
local function formatTimePeriodHelper(index)
    return string.format('%02d:%02d', index / 2, index % 2 == 1 and 30 or 0)
end

--
-- Converts binary schedule to an array of time period
--
-- input = STRING
--
-- output = BOOLEAN, ARRAY_OF({
--     startTime = STRING,
--     endTime = STRING
-- })
--
function _M.convertBinaryScheduleToTimePeriodArray(binarySchedule)
    if binarySchedule and #binarySchedule == 48 and not string.find(binarySchedule,'[^01]') then
        local timePeriods = {}
        local timePeriod = {}
        for i = 1, #binarySchedule do
            local value = binarySchedule:sub(i,i)
            if tonumber(value) == 0 and not timePeriod.startTime then
                timePeriod.startTime = formatTimePeriodHelper(i - 1)
            elseif tonumber(value) == 1 and timePeriod.startTime then
                timePeriod.endTime = formatTimePeriodHelper(i - 1)
                table.insert(timePeriods, timePeriod)
                timePeriod = {}
            end
        end
        -- This would take care of the case where the schedule is all 1s
        if timePeriod.startTime and not timePeriod.endTime then
            timePeriod.endTime = formatTimePeriodHelper(#binarySchedule)
            table.insert(timePeriods, timePeriod)
        end
        return true, timePeriods
    end
    return false
end


--
-- Helper function to validate a time period string.
-- It needs to match the following format HH:MM
--
-- input = STRING
--
-- output = BOOLEAN, NUMBER, NUMBER
--
local function isTimePeriodValid(timePeriod)
    if timePeriod.startTime and timePeriod.endTime then
        local startHour, startMinute = string.match(timePeriod.startTime, _M.TIME_PERIOD_REGEX)
        if startHour and startMinute and tonumber(startHour) <= 24 and tonumber(startMinute) <= 60 then
            local endHour, endMinute = string.match(timePeriod.endTime, _M.TIME_PERIOD_REGEX)
            if endHour and endMinute and tonumber(endHour) <= 24 and tonumber(endMinute) <= 60 then
                local startTimeInMins = tonumber(startHour) * 60 + tonumber(startMinute)
                local endTimeInMins = tonumber(endHour) * 60 + tonumber(endMinute)
                if startTimeInMins % 30 == 0 and endTimeInMins %30 == 0 and -- Make sure time slice is every 30 minutes.
                        startTimeInMins <= 1440 and endTimeInMins <= 1440 and
                        endTimeInMins > startTimeInMins then
                    return true, startTimeInMins, endTimeInMins
                end
            end
        end
    end
    return false
end

--
-- Converts an array of time period to a binary schedule
--
-- input = ARRAY_OF({
--     startTime = STRING,
--     endTime = STRING
-- })
--
-- output = BOOLEAN, STRING
--
function _M.convertTimePeriodArrayToBinarySchedule(timePeriods)
    -- Create a hashmap for the schedule
    local scheduleDict = {}
    for i = 1, 48 do
        scheduleDict[i] = 1
    end
    if timePeriods then
        for i, timePeriod in ipairs(timePeriods) do
            local isValid, startTimeInMins, endTimeInMins = isTimePeriodValid(timePeriod)
            if isValid then
                local startIndex = startTimeInMins / 30 + 1
                local endIndex = endTimeInMins / 30
                for i = startIndex, endIndex do
                    scheduleDict[i] = 0
                end
            else
                return false
            end
        end
    end
    -- convert hashmap to binary string
    local binarySchedule = ''
    for i = 1, 48 do
        binarySchedule = binarySchedule..scheduleDict[i]
    end
    return true, binarySchedule
end

--
-- Returns the parent directory of a file
--
-- input = STRING
--
-- output = STRING
--
function _M.getParentDiretory(filePath)
    local index = filePath:match('^.*()/')
    if index then
        return string.sub(filePath, 1, filePath:match('^.*()/') - 1)
    end
end

--
-- Recursively create parent directories
--
-- input = STRING
--
function _M.createParentDirectoryHelper(dir)
    local parentDir = _M.getParentDiretory(dir)
    if parentDir and #parentDir > 0 then
        return _M.createParentDirectoryHelper(parentDir)
    end
    return lfs.mkdir(dir)
end

--
-- Checks whether table is equal
--
function _M.isTableEqual(t1, t2)
    local keys = {}

    for k, v1 in pairs(t1) do
        local v2 = t2[k]
        if v2 == nil then
            return false
        elseif type(v1) ~= type(v2) then
            return false
        elseif type(v1) == 'table' then
            local result, error = _M.isTableEqual(v1, v2)
            if not result then
                return false
            end
        else
            if type(v1) == 'userdata' and getmetatable(v1) == getmetatable(v2) and getmetatable(v1)['__eq'] == nil then
                v1 = tostring(v1)
                v2 = tostring(v2)
            end
            if v1 ~= v2 then
                return false
            end
        end
        keys[k] = true
    end
    for k in pairs(t2) do
        if not keys[k] then
            return false
        end
    end
    return true
end

--
-- Checks whether a module is available
--
function _M.isModuleAvailable(name)
    if package.loaded[name] then
        return true
    else
        for _, searcher in ipairs(package.searchers or package.loaders) do
            local loader = searcher(name)
            if type(loader) == 'function' then
                package.preload[name] = loader
                return true
            end
        end
        return false
    end
end

--
-- Checks whether a node_util module is available
-- This module should exist on Velop devices (JNAP_NODES_PRODUCT=1)
--
function _M.isNodeUtilModuleAvailable()
    return _M.isModuleAvailable('nodes_util')
end

--
-- Converts array of values coming from OLAPI to conform to HSL definitions.
--
function _M.parseTableData(values, parserFn)
    local result = {}
    for i, value in ipairs(values) do
        table.insert(result, parserFn(value))
    end
    table.sort(result)
    return result
end

return _M -- return the module
