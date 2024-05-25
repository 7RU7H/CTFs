--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local function Authenticate(ctx, input)
    local guestnetwork = require('guestnetwork')

    local sc = ctx:sysctx()
    return guestnetwork.authenticate(sc, input.macAddress, input.ipAddress, input.password) or 'OK'
end

return require('libhdklua').loadmodule('jnap_guestnetworkauth'), {
    ['http://linksys.com/jnap/guestnetwork/Authenticate'] = Authenticate
}
