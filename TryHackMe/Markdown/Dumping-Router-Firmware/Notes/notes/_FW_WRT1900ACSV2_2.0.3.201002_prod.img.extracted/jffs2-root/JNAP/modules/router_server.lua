--
-- 2017 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author: hsulliva $
-- $DateTime: 2020/02/06 14:32:09 $
-- $Id: //depot-irv/lego/branches/wrt_platform_sp1/lego_overlay/proprietary/jnap/modules/router/router_server.lua#9 $
--

local function GetEthernetPortConnections(ctx)
    local router = require('router')

    local sc = ctx:sysctx()
    local connections = router.getEthernetPortConnections(sc)
    return 'OK', connections
end

local function GetLANSettings(ctx)
    local router = require('router')
    local platform = require('platform')

    local sc = ctx:sysctx()
    local settings = router.getLANSettings(sc)
    local dhcpSettings = router.getDHCPSettings(sc)
    settings.minAllowedDHCPLeaseMinutes = platform.MIN_DHCP_LEASE_MINUTES
    settings.maxAllowedDHCPLeaseMinutes = platform.MAX_DHCP_LEASE_MINUTES
    settings.maxDHCPReservationDescriptionLength = platform.MAX_DHCP_RESERVATION_DESCRIPTION_LENGTH
    settings.isDHCPEnabled = dhcpSettings.isDHCPEnabled
    settings.dhcpSettings = dhcpSettings.settings

    return 'OK', settings
end

local function SetLANSettings(ctx, input)
    local router = require('router')
    local platform = require('platform')

    local sc = ctx:sysctx()
    local error = router.setLANSettings(sc, input)
    if not error then
        local dhcpSettings = {
            isDHCPEnabled = input.isDHCPEnabled,
            settings = input.dhcpSettings
        }
        error = router.setDHCPSettings(sc, dhcpSettings)
    end
    return error or 'OK'
end

local function GetDHCPClientLeases(ctx)
    local platform = require('platform')

    local sc = ctx:sysctx()
    local settings = {
        leases = platform.getDHCPClientLeases(sc)
    }
    return 'OK', settings
end

local function GetIPv6Settings(ctx)
    local router = require('router')

    local sc = ctx:sysctx()
    local settings = router.getIPv6Settings(sc)
    settings.duid = router.getDUID(sc)
    return 'OK', settings
end

local function GetIPv6Settings2(ctx)
    local router = require('router')

    local sc = ctx:sysctx()
    local settings = router.getIPv6Settings2(sc)
    settings.duid = router.getDUID(sc)
    return 'OK', settings
end

local function SetIPv6Settings(ctx, input)
    local router = require('router')

    local sc = ctx:sysctx()
    local error = router.setIPv6Settings(sc, input)
    return error or 'OK'
end

local function SetIPv6Settings2(ctx, input)
    local router = require('router')

    local sc = ctx:sysctx()
    local error = router.setIPv6Settings2(sc, input)
    return error or 'OK'
end

local function GetWANSettings(ctx)
    local router = require('router')

    local sc = ctx:sysctx()
    local settings, err = router.getWANSettings(sc, 1)
    return err or 'OK', nil == err and settings or nil
end

local function GetWANSettings3(ctx)
    local router = require('router')

    local sc = ctx:sysctx()
    local settings, err = router.getWANSettings(sc, 2)
    return err or 'OK', nil == err and settings or nil
end

local function GetWANSettings4(ctx)
    local router = require('router')

    local sc = ctx:sysctx()
    local settings, err = router.getWANSettings(sc, 3)
    return err or 'OK', nil == err and settings or nil
end

local function GetWANSettings5(ctx)
    local router = require('router')

    local sc = ctx:sysctx()
    local settings, err = router.getWANSettings(sc, 4)
    return err or 'OK', nil == err and settings or nil
end

local function GetWANStatus(ctx)
    local router = require('router')

    local sc = ctx:sysctx()
    local settings, err = router.getWANStatus(sc)
    return err or 'OK', nil == err and settings or nil
end

local function GetWANStatus3(ctx)
    local router = require('router')

    local sc = ctx:sysctx()
    local settings, err = router.getWANStatus3(sc)
    return err or 'OK', nil == err and settings or nil
end

local function SetWANSettings(ctx, input)
    local router = require('router')

    local sc = ctx:sysctx()
    local output, err = router.setWANSettingsWithRedirection(sc, input)
    return err or 'OK', nil == err and output or nil
end

local function SetWANSettings3(ctx, input)
    local router = require('router')

    local sc = ctx:sysctx()
    local output, err = router.setWANSettings3(sc, input)
    return err or 'OK', nil == err and output or nil
end

local function SetWANSettings4(ctx, input)
    local router = require('router')

    local sc = ctx:sysctx()
    local output, err = router.setWANSettings4(sc, input)
    return err or 'OK', nil == err and output or nil
end

local function GetMACAddressCloneSettings(ctx)
    local router = require('router')

    local sc = ctx:sysctx()
    local settings = router.getMACAddressCloneSettings(sc)
    return 'OK', settings
end

local function SetMACAddressCloneSettings(ctx, input)
    local router = require('router')

    local sc = ctx:sysctx()
    local error = router.setMACAddressCloneSettings(sc, input)
    return error or 'OK'
end

local function GetRoutingSettings(ctx)
    local router = require('router')

    local sc = ctx:sysctx()
    local settings = router.getRoutingSettings(sc)
    return 'OK', settings
end

local function SetRoutingSettings(ctx, input)
    local router = require('router')

    local sc = ctx:sysctx()
    local error = router.setRoutingSettings(sc, input)
    return error or 'OK'
end

local function GetStaticRoutingTable(ctx)
    local platform = require('platform')

    local sc = ctx:sysctx()
    sc:readlock()
    return 'OK',
        {
            table = platform.getStaticRoutingTable(sc:getevent('current_wan_ifname'))
        }
end

local function ConnectPPPWAN(ctx)
    local router = require('router')

    local sc = ctx:sysctx()
    local error = router.connectPPPWAN(sc)
    return error or 'OK'
end

local function DisconnectPPPWAN(ctx)
    local router = require('router')

    local sc = ctx:sysctx()
    local error = router.disconnectPPPWAN(sc)
    return error or 'OK'
end

local function ReleaseDHCPWANLease(ctx)
    local router = require('router')

    local sc = ctx:sysctx()
    local error = router.releaseDHCPWANLease(sc)
    return error or 'OK'
end

local function RenewDHCPWANLease(ctx)
    local router = require('router')

    local sc = ctx:sysctx()
    local error = router.renewDHCPWANLease(sc)
    return error or 'OK'
end

local function ReleaseDHCPIPv6WANLease(ctx)
    local router = require('router')

    local sc = ctx:sysctx()
    local error = router.releaseDHCPIPv6WANLease(sc)
    return error or 'OK'
end

local function RenewDHCPIPv6WANLease(ctx)
    local router = require('router')

    local sc = ctx:sysctx()
    local error = router.renewDHCPIPv6WANLease(sc)
    return error or 'OK'
end

local function Reconnect6rdTunnel(ctx)
    local router = require('router')

    local sc = ctx:sysctx()
    local error = router.reconnect6rdTunnel(sc)
    return error or 'OK'
end

local function GetExpressForwardingSettings(ctx)
    local sc = ctx:sysctx()
    sc:readlock()

    local isEnabled
    local isSupported = sc:is_express_forwarding_supported()
    if isSupported then
        isEnabled = sc:get_express_forwarding_enabled()
    end
    return 'OK', {
        isExpressForwardingSupported = isSupported,
        isExpressForwardingEnabled = isEnabled
    }
end

local function SetExpressForwardingSettings(ctx, input)
    local sc = ctx:sysctx()
    sc:writelock()

    if (sc:is_express_forwarding_supported()) then
        sc:set_express_forwarding_enabled(input.isExpressForwardingEnabled)
        return 'OK'
    else
        return "ErrorExpressForwardingNotSupported"
    end
end

local function GetWirelessNetworks(ctx)
    local router = require('router')

    local sc = ctx:sysctx()
    local settings, err = router.getWirelessNetworks(sc)
    return err or 'OK', nil == err and settings or nil
end

local function RefreshWirelessNetworks(ctx)
    local router = require('router')

    local sc = ctx:sysctx()
    local error = router.refreshWirelessNetworks(sc)
    return error or 'OK'
end

local function GetWirelessConnectionStatus(ctx)
    local router = require('router')

    local sc = ctx:sysctx()
    local settings, err = router.getWirelessConnectionStatus(sc)
    return err or 'OK', nil == err and settings or nil
end

local function InitiateWirelessConnection(ctx, input)
    local router = require('router')

    local sc = ctx:sysctx()
    local error = router.initiateWirelessConnection(sc, input.wirelessModeSettings)
    return error or 'OK'
end


return require('libhdklua').loadmodule('jnap_router'), {
    ['http://linksys.com/jnap/router/GetEthernetPortConnections'] = GetEthernetPortConnections,
    ['http://linksys.com/jnap/router/GetLANSettings'] = GetLANSettings,
    ['http://linksys.com/jnap/router/SetLANSettings'] = SetLANSettings,
    ['http://linksys.com/jnap/router/GetDHCPClientLeases'] = GetDHCPClientLeases,
    ['http://linksys.com/jnap/router/GetIPv6Settings'] = GetIPv6Settings,
    ['http://linksys.com/jnap/router/GetIPv6Settings2'] = GetIPv6Settings2,
    ['http://linksys.com/jnap/router/SetIPv6Settings'] = SetIPv6Settings,
    ['http://linksys.com/jnap/router/SetIPv6Settings2'] = SetIPv6Settings2,
    ['http://linksys.com/jnap/router/GetWANSettings'] = GetWANSettings,
    ['http://linksys.com/jnap/router/GetWANSettings3'] = GetWANSettings3,
    ['http://linksys.com/jnap/router/GetWANSettings4'] = GetWANSettings4,
    ['http://linksys.com/jnap/router/GetWANSettings5'] = GetWANSettings5,
    ['http://linksys.com/jnap/router/SetWANSettings'] = SetWANSettings,
    ['http://linksys.com/jnap/router/SetWANSettings3'] = SetWANSettings3,
    ['http://linksys.com/jnap/router/SetWANSettings4'] = SetWANSettings4,
    ['http://linksys.com/jnap/router/GetRoutingSettings'] = GetRoutingSettings,
    ['http://linksys.com/jnap/router/SetRoutingSettings'] = SetRoutingSettings,
    ['http://linksys.com/jnap/router/GetRoutingSettings2'] = GetRoutingSettings2,
    ['http://linksys.com/jnap/router/SetRoutingSettings2'] = SetRoutingSettings2,
    ['http://linksys.com/jnap/router/GetStaticRoutingTable'] = GetStaticRoutingTable,
    ['http://linksys.com/jnap/router/GetWANStatus'] = GetWANStatus,
    ['http://linksys.com/jnap/router/GetWANStatus3'] = GetWANStatus3,
    ['http://linksys.com/jnap/router/GetMACAddressCloneSettings'] = GetMACAddressCloneSettings,
    ['http://linksys.com/jnap/router/SetMACAddressCloneSettings'] = SetMACAddressCloneSettings,
    ['http://linksys.com/jnap/router/ConnectPPPWAN'] = ConnectPPPWAN,
    ['http://linksys.com/jnap/router/DisconnectPPPWAN'] = DisconnectPPPWAN,
    ['http://linksys.com/jnap/router/ReleaseDHCPWANLease'] = ReleaseDHCPWANLease,
    ['http://linksys.com/jnap/router/RenewDHCPWANLease'] = RenewDHCPWANLease,
    ['http://linksys.com/jnap/router/ReleaseDHCPIPv6WANLease'] = ReleaseDHCPIPv6WANLease,
    ['http://linksys.com/jnap/router/RenewDHCPIPv6WANLease'] = RenewDHCPIPv6WANLease,
    ['http://linksys.com/jnap/router/Reconnect6rdTunnel'] = Reconnect6rdTunnel,
    ['http://linksys.com/jnap/router/GetExpressForwardingSettings'] = GetExpressForwardingSettings,
    ['http://linksys.com/jnap/router/SetExpressForwardingSettings'] = SetExpressForwardingSettings,
    ['http://linksys.com/jnap/router/GetWirelessNetworks'] = GetWirelessNetworks,
    ['http://linksys.com/jnap/router/RefreshWirelessNetworks'] = RefreshWirelessNetworks,
    ['http://linksys.com/jnap/router/GetWirelessConnectionStatus'] = GetWirelessConnectionStatus,
    ['http://linksys.com/jnap/router/InitiateWirelessConnection'] = InitiateWirelessConnection
}
