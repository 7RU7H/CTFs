#!/usr/bin/lua

--
-- Generate a Rainier lighttpd configuration file.
--
-- All functionality is encapsulated in one file to minimize file system footprint.
--

local sysctx = require('libsysctxlua')
local sc = sysctx:new()
sc:readlock()

local function UseFastCGI()
    local fastcgi = require('lfs').attributes('/www/JNAP/index.fcgi', 'mode')
    return (nil ~= fastcgi) and (('file' == fastcgi) or ('link' == fastcgi))
end

local useFastCGI = UseFastCGI()
local remoteDisabled = sc:getboolean('ui::remote_disabled', false)
local httpsEnabled = sc:getboolean('mgmt_https_enable', false)
local isPlatformMtk = (sc:get_hardware_vendor_name() == 'MediaTek')

-- Write rules required for UI localization.
local function WriteLocalizationRules()
    io.stdout:write([[
    #
    # Localization
    #
    cgi.assign += ( ".html" => server.document-root + "/ui/cgi/localize.cgi",
                    ".json" => server.document-root + "/ui/cgi/localize.cgi",
                    ".localized" => server.document-root + "/ui/cgi/localize.cgi")

]])
end

-- Write rules required for allowing/disallowing access to CGI pages.
local function WriteCGIPagesRules(allowAccess)
    io.stdout:write([[
    #
    # CGI pages configuration
    #
    $HTTP["url"] =~ "(?:\.cgi)" {
]])

    if allowAccess then
    io.stdout:write([[
        # Require authentication to access cgi pages.
        auth.require = ( "" =>
            (
                "method" => "basic",
                "realm" => "admin",
                "require" => "valid-user"
            )
        )
]])
    else
        io.stdout:write([[
        # Disallow access to cgi pages.
        url.access-deny = ("")
]])
    end
        io.stdout:write([[
    }
]])
end

-- Write rules required for UI/cloud proxy.
local function WriteProxyRules()

    -- Rewrite rules for local/remote UI, based on a cookie value.
    -- If the remote UI is not disabled, rewrite conditionally based on the cookie value.
    -- If the cookie is not set, rewrite back to / which will set the cookie (and request the page supplied as the query string)
    if not remoteDisabled then
        io.stdout:write([[

    # Remote UI is enabled.
    url.rewrite += ( "^/ui(/.*)?$" => "/ui/remote$1" )

]])
    else
        io.stdout:write([[

    # Remote UI is disabled.
    url.rewrite += ( "^/ui(/[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)?(/.*)?$" => "/ui/local$2" )

]])
    end

    local proxyMap = {
        ['/cloud'] = { name = 'cloud-proxy', sni = 'cloud::host', hostKey = 'cloud::host', portKey = 'cloud::port', stunnelKey = 'cloud::stunnel', stunnelPortKey = 'cloud::stunnel_port' }
    }
    if not remoteDisabled then
        proxyMap['/ui/remote'] = { name = 'remote-ui-proxy', sni = 'cloud::host', hostKey = 'ui::remote_host', portKey = 'ui::remote_port', stunnelKey = 'ui::remote_stunnel', stunnelPortKey = 'ui::remote_stunnel_port' }
    end

    for path, params in pairs(proxyMap) do
        -- Proxy directly to host:port if not SSL tunneling, otherwise proxy to the tunnel server at 127.0.0.1:tunnel_port
        local stunnel = sc:getboolean(params.stunnelKey, false)
        local host = stunnel and '127.0.0.1' or sc:get(params.hostKey)
        local port = stunnel and sc:getinteger(params.stunnelPortKey) or sc:getinteger(params.portKey)
        io.stdout:write(([[
    proxy.server += (
        "%s" => ( "%s" => ( "host" => "%s", "port" => %u ) )
    )

]]):format(path, sc:get(params.sni), host, port))
    end
end

local function WriteJNAPHandler(modulePath, useFCGI)
    io.stdout:write(([[
    #
    # JNAP %sCGI configuration
    #
]]):format(useFCGI and 'fast ' or ''))

    if useFCGI then
        io.stdout:write(([[
    fastcgi.server += (
        "/JNAP/" =>
            ( "jnap" => (
                "bin-path" => "/www/JNAP/index.fcgi",
                "bin-environment" => ( "JNAP_CGI_MODULES_PATH" => "%s" ),
                "socket" => "/tmp/jnap-fcgi-" + PID + ".sock",
                "check-local" => "disable",
                "max-procs" => 1
            ))
    )
]]):format(modulePath))
    else
        io.stdout:write(([[
    cgi.assign += ( "/JNAP/" => "" )
    $HTTP["url"] =~ "^/JNAP/?$" {
        setenv.add-environment = ( "JNAP_CGI_MODULES_PATH" => "%s" )
    }

]]):format(modulePath))
    end
end

local function WriteHNAPHandler(modulePath)
    io.stdout:write(([[
    #
    # HNAP CGI configuration
    #
    cgi.assign += ( "/HNAP1/" => "" )
    $HTTP["url"] =~ "^/HNAP1/?$" {
        setenv.add-environment = ( "JNAP_CGI_MODULES_PATH" => "%s" )
    }

]]):format(modulePath))
end

-- Write the guest LAN configuration.
local function WriteGuestLANConfig()
    io.stdout:write([[
    # Never list directories on the guest LAN.
    dir-listing.activate   = "disable"

    # Only serve local UI from the guest LAN.
    url.rewrite += ( "^/ui(/[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)?(/.*)?$" => "/ui/local$2" )

]])

    WriteLocalizationRules()

    -- Use the CGI version of JNAP because guest LAN requests occur very infrequently.
    WriteJNAPHandler("/JNAP/modules/guest_lan", false)

    -- Disallow access to CGI pages on guest subnet
    WriteCGIPagesRules(false)
end

-- Write the LAN configuration.
local function WriteLANConfig()

    --
    -- Dynamically generate the lighttpd LAN port configuration rules. This may contain conditional rewrite rules and therefore
    -- this statement MUST follow all other rewrite rules, for the reason described below.
    --
    -- Note: The conditionally appended rewrite rules must be last. Rules following the conditional don't seem to work,
    -- as noted here: http://redmine.lighttpd.net/boards/2/topics/3029.
    --

    WriteProxyRules()

    WriteLocalizationRules()

    WriteJNAPHandler("/JNAP/modules/lan", useFastCGI)

    WriteHNAPHandler("/JNAP/modules/hnap")

    -- Allow access to CGI pages on main subnet
    WriteCGIPagesRules(true)
end

-- Write the SmartConnect client configuration.
local function WriteSmartConnectClientConfig()
    -- Use the CGI version of JNAP because SmartConnect client requests occur very infrequently.
    WriteJNAPHandler("/JNAP/modules/smartconnect_client", false)

    -- Disallow access to CGI pages on the SmartConnect client port
    WriteCGIPagesRules(false)

    io.stdout:write([[

    # Disallow access to the UI on SmartConnect client port
    $HTTP["url"] =~ "^/ui/" {
        url.access-deny = ("")
    }
]])
end

io.stdout:write([[
#
# Lighttpd server configuration (generated from lighttpd-raininer-conf.lua)
#
server.use-ipv6         = "enable"
server.bind             = "[::]"
server.document-root    = "/www"
server.pid-file         = "/var/run/lighttpd.pid"
server.max-connections  = 42
server.max-request-size = 50000             # max file size in kbytes
]])
if isPlatformMtk then
    io.stdout:write([[
server.event-handler    = "select"
]])
end
io.stdout:write([[
server.upload-dirs      = ( "/tmp" ) # location to place uploaded files
server.network-backend  = "writev"          # needed to make file upload work?

server.reject-expect-100-with-417 = "disable"

server.indexfiles = ( "index.html",
                      "index.cgi" )

auth.backend = "plain"
auth.backend.plain.userfile = "/var/config/.sysinfo_pswd"

# Note: adding additional modules may require updating opensource/lighttpd/Makefile
server.modules    = ( "mod_rewrite",
                      "mod_setenv",
                      "mod_access",
                      "mod_cgi",
                      "mod_auth",
]])
if useFastCGI then
    io.stdout:write([[
                      "mod_fastcgi",
]])
end
io.stdout:write([[
                      "mod_proxy",
                      "mod_accesslog"
)

dir-listing.activate = "disable"

# Don't allow access to files with the following extensions
static-file.exclude-extensions = ( ".fcgi", ".properties", ".htpasswd" )

mimetype.assign = (
    ".html" => "text/html",
    ".txt" => "text/plain",
    ".jpg" => "image/jpeg",
    ".png" => "image/png",
    ".gif" => "image/gif",
    ".js" => "text/javascript; charset=utf-8",
    ".json" => "text/javascript; charset=utf-8",
    ".js.localized" => "text/javascript; charset=utf-8",
    ".css" => "text/css",
    ".pdf" => "application/pdf",
    ".ico" => "image/x-icon"
)

server.modules += ( "mod_fastcgi" )
fastcgi.server = ( "/cgi-bin/ozker/api" =>
(( "host" => "127.0.0.1",
        "port" => 9000,
        "check-local" => "disable",
))
)

cgi.assign = ( ".cgi"   => "",
                "ozker" => "",
                "cgi-bin/luci" => ""
)

# rewrites to keep luci code untouched
url.rewrite = ( "^/luci$" => "/luci/", # helper only
                "^/cgi-bin/luci.*" => "/luci$0",
                "^/luci-static/.*" => "/luci$0" )

]])

if remoteDisabled then
    io.stdout:write([[
#
# Rewrite index request to the local index.html
#

url.rewrite += ( "^/$" => "/ui/local/dynamic/index.html" )

]])
else
    io.stdout:write([[
#
# Rewrite index request to the remote index.html
#

url.rewrite += ( "^/$" => "/ui/remote/dynamic/index.html" )

]])
end

io.stdout:write([[
#
# Rewrite the legacy upgrade page
#

url.rewrite += ( "^/Upgrade.asp" => "/" )

#
# Rewrite request for /favicon.ico to support IE8 + 9.
#

url.rewrite += ( "^/favicon\.ico$" => "/ui/local/static/favicon.ico" )

#
# 404 error handling
#

server.error-handler-404 = "/ui/local/dynamic/404.html"

# Make sure the 404 page URL is never rewritten
url.rewrite += ( server.error-handler-404 => "$0" )

# Make sure the 502 page URL is never rewritten
url.rewrite += ( "/ui/local/dynamic/502.html" => "$0" )

# Don't rewrite the URL if local/remote has already been specified
url.rewrite += ( "^/ui/local(/.*)?$" => "$0" )
url.rewrite += ( "^/ui/remote(/.*)?$" => "$0" )

#
# Logging configuration
#

]])

--
-- Possible log levels:
-- 0 - disabled, 1 - normal logging (default) , 2 - debug logging
--
local logLevel = sc:getinteger('httpd_log_level', 1)
local verboseLogging = (logLevel > 1)
local defaultLogging = (logLevel > 0)
local function enableDisable(value)
    return value and 'enable' or 'disable'
end

-- debug.log-condition-handling and debug.log-state-handling are always disabled since
-- they are very noisy and not typically relevant
io.stdout:write(([[
server.errorlog-use-syslog = "enable"
accesslog.use-syslog       = "enable"

debug.log-request-header            = "%s"
debug.log-request-header-on-error   = "%s"
debug.log-response-header           = "%s"
debug.log-file-not-found            = "%s"
debug.log-request-handling          = "%s"
debug.log-condition-handling        = "disable"
debug.log-state-handling            = "disable"
debug.log-ssl-noise                 = "%s"
debug.log-timeouts                  = "%s"

proxy.debug                         = %d

]]):format(enableDisable(verboseLogging), -- debug.log-request-header
           enableDisable(defaultLogging), -- debug.log-request-header-on-error
           enableDisable(verboseLogging), -- debug.log-response-header
           enableDisable(defaultLogging), -- debug.log-file-not-found
           enableDisable(verboseLogging), -- debug.log-request-handling
           enableDisable(verboseLogging), -- debug.log-ssl-noise
           enableDisable(defaultLogging), -- debug.log-timeouts
           (verboseLogging) and 1 or 0)) -- proxy.debug

if useFastCGI then
    io.stdout:write(([[
fastcgi.debug                       = %d

]]):format((verboseLogging) and 1 or 0)) -- fastcgi.debug
end

io.stdout:write([[
#
# Server socket configurations
#

#
# Allow limited HTTP access from the guest network.  Restrict to
# guest-access control services.  Note: We are hijacking the port
# assigned to the amanda backup service for this purpose.
# Also Note: The ignore port comment below is needed.  Do not remove
# it; firewall manipulation code needs that (see block-ports)
$SERVER["socket"] == ":10080" { # IGNORE-PORT

]])

    WriteGuestLANConfig()

    io.stdout:write([[
}

$SERVER["socket"] == "[::]:10080" { # IGNORE-PORT

]])

    WriteGuestLANConfig()

    io.stdout:write([[
}

# HTTP port config
$SERVER["socket"] == ":80" {

]])

    WriteLANConfig()

    io.stdout:write([[
}

# HTTP port config (IPv6)
$SERVER["socket"] == "[::]:80" {

]])

    WriteLANConfig()


if httpsEnabled then

    io.stdout:write([[
    }

    # HTTPS port config
    $SERVER["socket"] == ":443" {
        ssl.engine = "enable"
        ssl.pemfile = "/etc/certs/server.pem"
        ssl.use-compression = "disable"
        ssl.use-sslv2 = "disable"
        ssl.use-sslv3 = "disable"
        ssl.cipher-list = "EECDH+AESGCM:EDH+AESGCM:ECDHE-RSA-AES128-GCM-SHA256:AES256+EECDH:AES256+EDH:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-SHA384:ECDHE-RSA-AES128-SHA256:ECDHE-RSA-AES256-SHA:ECDHE-RSA-AES128-SHA:DHE-RSA-AES256-SHA256:DHE-RSA-AES128-SHA256:DHE-RSA-AES256-SHA:DHE-RSA-AES128-SHA:ECDHE-RSA-DES-CBC3-SHA:EDH-RSA-DES-CBC3-SHA:AES256-GCM-SHA384:AES128-GCM-SHA256:AES256-SHA256:AES128-SHA256:AES256-SHA:AES128-SHA:DES-CBC3-SHA:HIGH:!aNULL:!eNULL:!EXPORT:!DES:!MD5:!PSK:!RC4"

    ]])

    WriteLANConfig()

    io.stdout:write([[
    }

    # HTTPS port config (IPv6)
    $SERVER["socket"] == "[::]:443" {
        ssl.engine = "enable"
        ssl.pemfile = "/etc/certs/server.pem"
        ssl.use-compression = "disable"
        ssl.use-sslv2 = "disable"
        ssl.use-sslv3 = "disable"
        ssl.cipher-list = "EECDH+AESGCM:EDH+AESGCM:ECDHE-RSA-AES128-GCM-SHA256:AES256+EECDH:AES256+EDH:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-SHA384:ECDHE-RSA-AES128-SHA256:ECDHE-RSA-AES256-SHA:ECDHE-RSA-AES128-SHA:DHE-RSA-AES256-SHA256:DHE-RSA-AES128-SHA256:DHE-RSA-AES256-SHA:DHE-RSA-AES128-SHA:ECDHE-RSA-DES-CBC3-SHA:EDH-RSA-DES-CBC3-SHA:AES256-GCM-SHA384:AES128-GCM-SHA256:AES256-SHA256:AES128-SHA256:AES256-SHA:AES128-SHA:DES-CBC3-SHA:HIGH:!aNULL:!eNULL:!EXPORT:!DES:!MD5:!PSK:!RC4"

    ]])

    WriteLANConfig()

end

    io.stdout:write([[
}

# SmartConnect client port config
$SERVER["socket"] == ":8080" {

]])

    WriteSmartConnectClientConfig()

    io.stdout:write([[
}

# SmartConnect client port config (IPv6)
$SERVER["socket"] == "[::]:8080" {

]])

    WriteSmartConnectClientConfig()

    io.stdout:write([[
}

# "Unsecured wireless" redirect port.
$SERVER["socket"] == ":52000" {
    proxy.server += (
        "/ui"    => ( "blocking-proxy" => ( "host" => "127.0.0.1", "port" => 80 ) ),
        "/JNAP/" => ( "blocking-proxy" => ( "host" => "127.0.0.1", "port" => 80 ) )
    )
}

# "Unsecured wireless" redirect port (IPv6).
$SERVER["socket"] == "[::]:52000" {
    proxy.server += (
        "/ui"    => ( "blocking-proxy" => ( "host" => "127.0.0.1", "port" => 80 ) ),
        "/JNAP/" => ( "blocking-proxy" => ( "host" => "127.0.0.1", "port" => 80 ) )
    )
}

# eurl proxy for twonky remote access
$SERVER["socket"] == ":10000" {
    $HTTP["url"] !~ "^/eurl/" {
        url.access-deny = ("")
    }
    $HTTP["url"] =~ "^/eurl/" {
        #proxy.debug = 1
        #proxy.buffer_limit = 1048576
        #proxy.buffer_delay = 50000
        proxy.cisco_eurl = "enable"
    }
}

# eurl proxy for twonky remote access
$SERVER["socket"] == "[::]:10000" {
    $HTTP["url"] !~ "^/eurl/" {
        url.access-deny = ("")
    }
    $HTTP["url"] =~ "^/eurl/" {
        #proxy.debug = 1
        #proxy.buffer_limit = 1048576
        #proxy.buffer_delay = 50000
        proxy.cisco_eurl = "enable"
    }
}
]])

sc:rollback() -- explicitly release the lock
