--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

-- parentalcontrol.lua - library to configure parental control state.

local hdk = require('libhdklua')
local platform = require('platform')
local wirelessap = require('wirelessap')
local guestnetwork = require('guestnetwork')

local _M = {} -- create the module

local configurableRadios

local function initializeRadios(sc)
    sc:readlock()
    if configurableRadios == nil then
        configurableRadios = {}
        for k, v in pairs(wirelessap.RADIO_PROFILES) do
            if wirelessap.RADIO_PROFILES[k].apName ~= 'wl2' then
                local radio = {
                    radioID = k,
                    isGuestRadio = false,
                    apName = wirelessap.RADIO_PROFILES[k].apName
                }
                table.insert(configurableRadios, radio)
            end
        end
        for k, v in pairs(guestnetwork.GUEST_RADIO_PROFILES) do
            local radio = {
                radioID = k,
                isGuestRadio = true,
                apName = guestnetwork.GUEST_RADIO_PROFILES[k].apName..'_guest'
            }
            table.insert(configurableRadios, radio)
        end
    end

    for i, radio in ipairs(configurableRadios) do
        radio.isConfigured = sc:get_wifischeduler_if_enabled(radio.apName)
    end
    return configurableRadios
end

--
-- Get whether parental control is enabled.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.getIsEnabled(sc)
    sc:readlock()
    return sc:get_wifischeduler_enabled()
end

--
-- Set whether parental control is enabled.
--
-- input = CONTEXT, BOOLEAN
--
function _M.setIsEnabled(sc, isEnabled)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    sc:set_wifischeduler_enabled(isEnabled)
end

--
-- Get the wireless schedule.
--
-- input = CONTEXT
--
-- output = {
--     sunday = STRING,
--     monday = STRING,
--     tuesday = STRING,
--     wednesday = STRING,
--     thursday = STRING,
--     friday = STRING,
--     saturday = STRING
-- })
--
function _M.getWirelessSchedule(sc)
    sc:readlock()
    return {
        sunday = sc:get_wifischeduler_time_blocks('sunday'),
        monday = sc:get_wifischeduler_time_blocks('monday'),
        tuesday = sc:get_wifischeduler_time_blocks('tuesday'),
        wednesday = sc:get_wifischeduler_time_blocks('wednesday'),
        thursday = sc:get_wifischeduler_time_blocks('thursday'),
        friday = sc:get_wifischeduler_time_blocks('friday'),
        saturday = sc:get_wifischeduler_time_blocks('saturday')
    }
end

--
-- Set the wireless schedule.
--
-- input = {
--     sunday = STRING,
--     monday = STRING,
--     tuesday = STRING,
--     wednesday = STRING,
--     thursday = STRING,
--     friday = STRING,
--     saturday = STRING
-- })
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidWirelesschedule',
-- )
--
function _M.setWirelessSchedule(sc, schedule)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    for j, day in ipairs({'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'}) do
        local schedule = schedule[day]
        if #schedule ~= 48 or schedule:find('^[01]*$') == nil then
            return 'ErrorInvalidWirelessSchedule'
        end
        sc:set_wifischeduler_time_blocks(day, schedule)
    end
end

--
-- Get the wireless schedule.
--
-- input = CONTEXT
--
-- output = {
--     sunday = STRING,
--     monday = STRING,
--     tuesday = STRING,
--     wednesday = STRING,
--     thursday = STRING,
--     friday = STRING,
--     saturday = STRING
-- })
--
function _M.getConfigurableRadios(sc)
    sc:readlock()
    local radios = {}
    for i, radio in ipairs(initializeRadios(sc)) do
        table.insert(radios, {radioID = radio.radioID, isGuestRadio = radio.isGuestRadio})
    end
    return radios
end

--
-- Get the configured radios.
--
-- input = CONTEXT
--
-- output = ARRAY_OF({
--     radioID = STRING,
--     isConfigured = BOOLEAN
-- })
--
function _M.getConfiguredRadios(sc)
    sc:readlock()

    local radios = {}

    for i, radio in ipairs(initializeRadios(sc)) do
        if radio.isConfigured then
            table.insert(radios, {radioID = radio.radioID, isGuestRadio = radio.isGuestRadio})
        end
    end

    return radios
end

--
-- Set the configured radios.
--
-- input = ARRAY_OF({
--     radioID = STRING,
--     isConfigured = BOOLEAN
-- })
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidConfiguredRadio'
-- )
--
function _M.setConfiguredRadios(sc, configuredRadios)
    sc:writelock()

    local function invalidConfiguredRadios(sc, radios)
        local function isRadioConfigurable(radio)
            for k, v in ipairs(initializeRadios(sc)) do
                if radio.radioID == v.radioID and radio.isGuestRadio == v.isGuestRadio then
                    return true
                end
            end
            return false
        end
        for k, v in pairs(radios) do
            if isRadioConfigurable(v) == false then
                return true
            end
        end
    end

    local function isRadioInConfiguredRadios(radio, configuredRadios)
        for i, r in ipairs(configuredRadios) do
            if radio.radioID == r.radioID and radio.isGuestRadio == r.isGuestRadio then
                return true
            end
        end
        return false
    end

    if invalidConfiguredRadios(sc, configuredRadios) then
        return 'ErrorInvalidConfiguredRadio'
    end

    for i, radio in ipairs(initializeRadios(sc)) do
        if isRadioInConfiguredRadios(radio, configuredRadios) then
            if not radio.isConfigured then
                sc:set_wifischeduler_if_enabled(radio.apName, true)
            end
        else
            if radio.isConfigured then
                sc:set_wifischeduler_if_enabled(radio.apName, false)
            end
        end
    end
end

return _M -- return the module
