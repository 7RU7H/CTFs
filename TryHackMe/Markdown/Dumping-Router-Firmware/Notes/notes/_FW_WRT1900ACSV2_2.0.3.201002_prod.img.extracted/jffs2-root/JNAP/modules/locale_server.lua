--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local function GetLocale(ctx)
    local locale = require('locale')
    local platform = require('platform')

    local sc = ctx:sysctx()
    local supportedLocales = {}
    for locale in pairs(platform.getLocaleSet(sc)) do
        table.insert(supportedLocales, locale)
    end
    table.sort(supportedLocales)
    return 'OK', {
        locale = locale.getLocale(sc),
        supportedLocales = supportedLocales
    }
end

local function SetLocale(ctx, input)
    local locale = require('locale')

    local sc = ctx:sysctx()
    local error = locale.setLocale(sc, input.locale)
    return error or 'OK'
end

local function GetTimeSettings(ctx)
    local locale = require('locale')
    local platform = require('platform')

    local sc = ctx:sysctx()
    local timeZoneMap = platform.getTimeZoneMap()
    local supportedTimeZones = {}
    for timeZoneID, timeZone in pairs(timeZoneMap) do
        table.insert(supportedTimeZones, {
                         timeZoneID = timeZoneID,
                         utcOffsetMinutes = timeZone.utcOffsetMinutes,
                         observesDST = (timeZone.dstOnValue ~= nil),
                         description = timeZone.description
                     })
    end
    local settings = locale.getTimeSettings(sc)
    settings.supportedTimeZones = supportedTimeZones
    settings.currentTime = platform.getCurrentUTCTime(sc)
    return 'OK', settings
end

local function SetTimeSettings(ctx, input)
    local locale = require('locale')

    local sc = ctx:sysctx()
    local error = locale.setTimeSettings(sc, input)
    return error or 'OK'
end

local function GetLocalTime(ctx)
    local platform = require('platform')
    local sc = ctx:sysctx()

    return 'OK', { currentTime = platform.getCurrentLocalTime(sc) }
end

return require('libhdklua').loadmodule('jnap_locale'), {
    ['http://linksys.com/jnap/locale/GetLocale'] = GetLocale,
    ['http://linksys.com/jnap/locale/SetLocale'] = SetLocale,
    ['http://linksys.com/jnap/locale/GetTimeSettings'] = GetTimeSettings,
    ['http://linksys.com/jnap/locale/SetTimeSettings'] = SetTimeSettings,

    ['http://linksys.com/jnap/locale/GetLocalTime'] = GetLocalTime,
}
