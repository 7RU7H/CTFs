--
-- 2019 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author: hsulliva $
-- $DateTime: 2020/02/06 14:32:09 $
-- $Id: //depot-irv/lego/branches/wrt_platform_sp1/lego_overlay/proprietary/jnap/lualib/networkconnections.lua#2 $
--

-- networkconnections.lua - library to get network connections state.

local platform = require('platform')
local wirelessap = require('wirelessap')

local _M = {} -- create the module


--
-- Get information about the active layer 2 network connections.
--
-- input = CONTEXT, OPTIONAL(ARRAY_OF(MACADDRESS))
--
-- output = ARRAY_OF({
--     macAddress = MACADDRESS,
--     negotiatedMbps = NUMBER,
--     wireless = OPTIONAL({
--         bssid = MACADDRESS,
--         isGuest = BOOLEAN,
--         band = STRING,
--         signalDecibels = NUMBER,
--         mode = OPTIONAL(STRING)
--     })
-- })
--
function _M.getNetworkConnections(sc, includeList, includeRadioID)
    sc:readlock()
    local connections = {}

    local filterSet
    if includeList then
        filterSet = {}
        for i, mac in ipairs(includeList) do
            -- User types can't be used as keys in a table
            filterSet[tostring(mac)] = mac
        end
    end

    -- Iterate over all the user VAPs and guest VAPs (if enabled) and get the connected clients for each.
    for radioID, profile in pairs(wirelessap.getSupportedRadios(sc)) do
        local vapTable = {
            { name = sc:get_wifi_virtual_ap_name(profile.apName), isGuest = false }
        }

        -- Only check guest VAPs if they're enabled
        local guestVapName = sc:get_wifi_virtual_ap_name(profile.apName, true)
        if 0 ~= tonumber(sc:get_guest_access_enabled() or '0') and guestVapName ~= '' then
            table.insert(vapTable, {
                name = guestVapName, isGuest = true
            })
        end

        for i, vap in ipairs(vapTable) do
            if vap.name and string.len(vap.name) > 0 then
                local stations = platform.getWirelessStationList(sc, vap.name, vap.isGuest)
                if stations then
                    local vapBssid = platform.getMACAddressFromNetName(vap.name)

                    for j, sta in ipairs(stations) do
                        if not filterSet or filterSet[tostring(sta.macAddress)] then
                            local connection = {
                                macAddress = sta.macAddress,
                                        negotiatedMbps = sta.rate,
                                        wireless = {
                                            bssid = vapBssid,
                                            isGuest = vap.isGuest,
                                            band = profile.band,
                                            signalDecibels = sta.rssi,
                                            mode = sta.mode
                                        }
                            }
                            if includeRadioID then
                                connection.wireless.radioID = radioID
                            end
                            table.insert(connections, connection)
                        end
                    end
                end
            end
        end
    end

    -- Get the switch link speed table for wired connections
    local switchTable = platform.getSwitchLinkSpeedMap(sc)

    for i, entry in ipairs(switchTable) do
        if not filterSet or filterSet[tostring(entry.macAddress)] then
            table.insert(connections, {
                macAddress = entry.macAddress,
                negotiatedMbps = entry.speedMbps or 0
            })
        end
    end

    table.sort(connections, function(a, b) return a.macAddress < b.macAddress end)

    return connections
end


return _M -- return the module
