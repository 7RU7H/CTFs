--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

-- routerlog.lua - library to configure router log state.

local _M = {} -- create the module


--
-- Get whether logging is currently enabled.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.getIsLoggingEnabled(sc)
    sc:readlock()
    return sc:get_logging_enabled()
end

--
-- Set whether logging should be enabled.
--
-- input = CONTEXT, BOOLEAN
--
function _M.setIsLoggingEnabled(sc, isLoggingEnabled)
    sc:writelock()
    local platform = require('platform')
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    sc:set_logging_enabled(isLoggingEnabled)
end


return _M -- return the module
