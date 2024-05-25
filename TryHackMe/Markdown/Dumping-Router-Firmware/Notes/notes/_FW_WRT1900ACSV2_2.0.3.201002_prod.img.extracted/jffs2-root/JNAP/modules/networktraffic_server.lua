--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local function coallesceConnections(connections, addressName)
    local mapped = {}
    for _, entry in ipairs(connections) do
        local key = tostring(entry.localSource)
        if not mapped[key] then
            mapped[key] = {
                bytesSent = 0,
                bytesReceived = 0
            }
            mapped[key][addressName] = entry.localSource
        end

        mapped[key].bytesSent = mapped[key].bytesSent + entry.sentBytes
        mapped[key].bytesReceived = mapped[key].bytesReceived + entry.recvBytes
    end
    return mapped
end

local function setToArray(set, fnFilter)
    local array = {}
    for _, v in pairs(set) do
        if not fnFilter or fnFilter(v) then
            table.insert(array, v)
        end
    end
    local function sortEntry(lhs, rhs)
        if lhs.ipAddress then
            return lhs.ipAddress < rhs.ipAddress
        else
            return lhs.ipv6Address < rhs.ipv6Address
        end
    end
    table.sort(array, sortEntry)
    return array
end

local function BeginStatisticsTracking(ctx, input)
    local platform = require('platform')
    local sc = ctx:sysctx()

    return platform.startConnectionAccounting(sc) or 'OK'
end

local function GetStatisticsByDevice(ctx, input)
    local platform = require('platform')
    local router = require('router')

    local sc = ctx:sysctx()

    local function fnIsInLANSubnet(v)
        return router.isInRouterSubnet(sc, v.ipAddress)
    end

    local error = platform.stopConnectionAccounting(sc)
    if error then
        return error
    else
        return 'OK', {
            ipStats = setToArray(coallesceConnections(platform.getIPv4Connections(sc), 'ipAddress'), fnIsInLANSubnet),
            ipv6Stats = setToArray(coallesceConnections(platform.getIPv6Connections(sc), 'ipv6Address'))
        }
    end
end

return require('libhdklua').loadmodule('jnap_networktraffic'), {
    ['http://linksys.com/jnap/networktraffic/BeginStatisticsTracking'] = BeginStatisticsTracking,
    ['http://linksys.com/jnap/networktraffic/GetStatisticsByDevice'] = GetStatisticsByDevice
}
