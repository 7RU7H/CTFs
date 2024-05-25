--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

-- routerleds.lua - library to configure router LED state.

local _M = {} -- create the module


--
-- Get whether the switchport LED is enabled.
--
function _M.getIsSwitchportLEDEnabled(sc)
    sc:readlock()
    return sc:get_leds_enabled()
end

--
-- Set whether the switchport LED is enabled.
--
function _M.setIsSwitchportLEDEnabled(sc, isEnabled)
    sc:writelock()
    local platform = require('platform')
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    sc:set_leds_enabled(isEnabled)
end


return _M -- return the module
