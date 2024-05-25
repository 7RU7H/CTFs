--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

-- locale.lua - library to configure locale state.

local platform = require('platform')

local _M = {} -- create the module


--
-- Get the current locale of the device.
--
-- input = CONTEXT
--
-- output = STRING
--
function _M.getLocale(sc)
    sc:readlock()
    return sc:get_locale()
end

--
-- Set the current locale of the device.
--
-- input = CONTEXT, STRING
--
-- output = NIL_OR_ONE_OF(
--     'ErrorUnsupportedLocale'
-- )
--
function _M.setLocale(sc, locale)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if not platform.getLocaleSet()[locale] then
        return 'ErrorUnsupportedLocale'
    end

    sc:set_locale(locale)

    -- Curiously, the firmware doesn't trigger any event
    -- when the locale changes.
end

--
-- Get the current time settings.
--
-- input = CONTEXT
--
-- output = {
--     timeZoneID = STRING,
--     autoAdjustForDST = BOOLEAN
-- }
--
function _M.getTimeSettings(sc)
    sc:readlock()
    -- The auto_dst setting has three values:
    --   0 - DST is observed in the time zone, and auto-adjust is off
    --   1 - DST is observed in the time zone, and auto-adjust is on
    --   2 - DST is not observed in the time zone
    local autoDST = sc:get_auto_adjust_dst()
    local value = sc:get_time_zone()
    local observesDST = (autoDST ~= 2)
    local activeTimeZoneID = ''
    local activeAutoAdjustForDST = false
    if value then
        for timeZoneID, timeZone in pairs(platform.getTimeZoneMap()) do
            local timeZoneObservesDST = (timeZone.dstOnValue ~= nil)
            if ((value == timeZone.dstOnValue) or
                (value == timeZone.dstOffValue and (timeZoneObservesDST == observesDST))) then
                activeTimeZoneID = timeZoneID
                activeAutoAdjustForDST = (timeZone.dstOffValue and autoDST == 1)
                break
            end
        end
    end
    return {
        timeZoneID = activeTimeZoneID,
        autoAdjustForDST = activeAutoAdjustForDST
    }
end

--
-- Set the current time settings.
--
-- input = CONTEXT, {
--     timeZoneID = STRING,
--     autoAdjustForDST = BOOLEAN
-- }
--
-- output = NIL_OR_ONE_OF(
--     'ErrorUnknownTimeZone',
--     'ErrorTimeZoneDoesNotObserveDST'
-- )
--
function _M.setTimeSettings(sc, settings)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    local timeZoneMap = platform.getTimeZoneMap()
    local timeZone = timeZoneMap[settings.timeZoneID]
    if not timeZone then
        return 'ErrorUnknownTimeZone'
    end

    if settings.autoAdjustForDST and not timeZone.dstOnValue then
        return 'ErrorTimeZoneDoesNotObserveDST'
    end

    -- The auto_dst setting has three values:
    --   0 - DST is observed in the time zone, and auto-adjust is off
    --   1 - DST is observed in the time zone, and auto-adjust is on
    --   2 - DST is not observed in the time zone
    local newAutoDST
    local newValue
    if timeZone.dstOnValue then
        if settings.autoAdjustForDST then
            newAutoDST = 1
            newValue = timeZone.dstOnValue
        else
            newAutoDST = 0
            newValue = timeZone.dstOffValue
        end
    else
        newAutoDST = 2
        newValue = timeZone.dstOffValue
    end

    sc:set_time_zone(newValue)
    sc:set_auto_adjust_dst(newAutoDST)
end


return _M -- return the module
