--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

-- macfilter.lua - library to configure MAC filter state.

local hdk = require('libhdklua')
local platform = require('platform')

local _M = {} -- create the module


--
-- Get the MAC filter mode.
--
-- input = CONTEXT
--
-- output = STRING
--
function _M.getMode(sc)
    sc:readlock()
    return sc:get_wifi_access_restriction()
end

--
-- Sets the MAC filter mode.
--
-- input = CONTEXT, STRING
--
function _M.setMode(sc, mode)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    sc:set_wifi_access_restriction(mode)
end

--
-- Get the MAC filter MAC address list.
--
-- input = CONTEXT
--
-- output = ARRAY_OF(MACADDRESS)
--
function _M.getMACAddresses(sc)
    sc:readlock()
    local macAddresses = {}
    local filter = sc:get_mac_filter_addresses()
    for macAddress in string.gmatch(filter, '%x%x:%x%x:%x%x:%x%x:%x%x:%x%x') do
        table.insert(macAddresses, hdk.macaddress(macAddress))
    end
    return macAddresses
end

--
-- Sets the MAC filter MAC address list.
--
-- input = CONTEXT, ARRAY_OF(MACADDRESS)
--
-- output = NIL_OR_ONE_OF(
--     'ErrorTooManyMACAddresses'
-- )
--
function _M.setMACAddresses(sc, macAddresses)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if #macAddresses > platform.MAX_MACFILTER_ADDRESSES then
        return 'ErrorTooManyMACAddresses'
    end
    local macAddressStrings = {}
    for i, macAddress in ipairs(macAddresses) do
        table.insert(macAddressStrings, tostring(macAddress))
    end
    local filter = table.concat(macAddressStrings, ' ')

    -- Yes, it would be much more efficient to do this validation
    -- in the above loop, but TestDevice gets upset when the MAC Address
    -- order changes, so we'll do it after generating the filter string.
    table.sort(macAddresses)
    local prev
    for i, macAddress in ipairs(macAddresses) do
        if macAddress:iszero() then
            return 'ErrorInvalidMACAddress'
        end
        if prev and prev == macAddress then
            return 'ErrorDuplicateMACAddresses'
        end
        prev = macAddress
    end

    sc:set_mac_filter_addresses(filter)
end


return _M -- return the module
