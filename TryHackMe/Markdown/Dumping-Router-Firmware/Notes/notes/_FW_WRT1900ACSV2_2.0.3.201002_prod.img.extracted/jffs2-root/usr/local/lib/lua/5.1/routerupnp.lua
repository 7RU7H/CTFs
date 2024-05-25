--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

-- routerupnp.lua - library to configure router UPnP state.

local platform = require('platform')

local _M = {} -- create the module


--
-- Get whether UPnP is enabled.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.getIsEnabled(sc)
    sc:readlock()
    return sc:get_upnp_enabled()
end

--
-- Set whether UPnP is enabled.
--
-- input = CONTEXT, BOOLEAN
--
function _M.setIsEnabled(sc, isEnabled)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    sc:set_upnp_enabled(isEnabled)
end

--
-- Get whether users can configure UPnP.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.getCanUsersConfigure(sc)
    sc:readlock()
    return sc:get_user_can_configure_upnp()
end

--
-- Set whether users can configure UPnP.
--
-- input = CONTEXT, BOOLEAN
--
function _M.setCanUsersConfigure(sc, canUsersConfigure)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    sc:set_user_can_configure_upnp(canUsersConfigure)
end

--
-- Get whether users can disable WAN access via UPnP.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.getCanUsersDisableWANAccess(sc)
    sc:readlock()
    return sc:get_upnp_user_can_disable_wan()
end

--
-- Set whether users can disable WAN access via UPnP.
--
-- input = CONTEXT, BOOLEAN
--
function _M.setCanUsersDisableWANAccess(sc, canUsersDisableWANAccess)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    sc:set_upnp_user_can_disable_wan(canUsersDisableWANAccess)
end


return _M -- return the module
