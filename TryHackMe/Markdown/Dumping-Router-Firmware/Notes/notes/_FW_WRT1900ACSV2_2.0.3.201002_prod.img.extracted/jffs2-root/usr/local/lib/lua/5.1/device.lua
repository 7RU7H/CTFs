--
-- 2017 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author: hsulliva $
-- $DateTime: 2020/02/06 14:32:09 $
-- $Id: //depot-irv/lego/branches/wrt_platform_sp1/lego_overlay/proprietary/jnap/lualib/device.lua#8 $
--

-- device.lua - library to configure device state.

local util = require('util')
local platform = require('platform')
local hdk = require('libhdklua')

local _M = {} -- create the module

----------------------------------------------------------------------
-- Device-specific constants.
----------------------------------------------------------------------
_M.SECONDS_IN_A_DAY = 86400

_M.PRODUCT_TYPE_FILE_PATH= '/etc/product.type'

----------------------------------------------------------------------
-- Device-specific utility functions.
----------------------------------------------------------------------

--
-- Get the cloud host.
--
-- input = CONTEXT
--
-- output = STRING
--
function _M.getCloudHost(sc)
    sc:readlock()
    return sc:get_cloud_host()
end

--
-- Get whether or not to verify the cloud host.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.getVerifyCloudHost(sc)
    sc:readlock()
    return sc:get_cloud_stunnel_verify()
end

--
-- Get the manufacturer of the local device.
--
function _M.getManufacturer(sc)
    sc:readlock()
    return sc:get_manufacturer()
end

--
-- Get the manufacturer of the local device.
--
function _M.getManufactureDate(sc)
    sc:readlock()
    return sc:get_manufacture_date()
end

--
-- Get the local device's unique identifier.
--
function _M.getUUID(sc)
    sc:readlock()
    return sc:get_device_uuid()
end

--
-- Get the local device's unique mac address.
--
function _M.getMACAddress(sc)
    sc:readlock()
    return sc:get_device_macaddr()
end

--
-- Get the manufacturer's URL.
--
function _M.getManufacturerURL(sc)
    sc:readlock()
    return sc:get_manufacturerURL()
end

--
-- Get the local device's serial number.
--
function _M.getSerialNumber(sc)
    sc:readlock()
    return sc:get_serial_number()
end

--
-- Get the local device's model number.
--
function _M.getModelNumber(sc)
    sc:readlock()
    return sc:get_modelbase()
end

--
-- Get the local device's model description
--
function _M.getModelDescription(sc)
    sc:readlock()
    return sc:get_modeldescription()
end

--
-- Get the hardware revision of the local device.
--
function _M.getHardwareRevision(sc)
    sc:readlock()
    return sc:get_hardware_revision()
end

--
-- Get the type of the local device.
--
function _M.getDeviceType(sc)
    sc:readlock()
    return sc:get_device_type()
end

--
-- Get the version firmware of the local device.
--
function _M.getFirmwareVersion(sc)
    sc:readlock()
    return sc:get_firmware_version()
end

--
-- Get the build date of the local device.
--
function _M.getFirmwareDate(sc)
    sc:readlock()
    return sc:get_firmware_builddate_integer()
end

--
-- Get the host name of the local device.
--
function _M.getHostName(sc)
    sc:readlock()
    return sc:get_hostname()
end

--
-- Get the device cloud registration token.
--
function _M.getLinksysToken(sc)
    sc:readlock()
    return sc:get_linksys_token()
end

--
-- Set the host name of the local device.
--
-- input = CONTEXT, STRING
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidHostName'
-- )
--
function _M.setHostName(sc, hostName)

    -- The device's host name must be a valid Microsoft NetBIOS name AND DNS host name label.
    -- The only more restrictive requirement the NetBIOS name restriction places is on the length ([1,15])
    if #hostName > 15 or not util.isValidHostNameLabel(hostName, true) then
        return 'ErrorInvalidHostName'
    end
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end

    sc:set_hostname(hostName)
end

--
-- Set the admin password of the device.
--
-- This method will set the admin password asynchronously, and
-- in a transaction safe manner, if 'transactionSafe' is true.
-- Otherwise the admin password will be set synchronously, and
-- in a manner not safe for transactions.
--
-- input = CONTEXT, STRING
--
-- output = NIL_OR_ONE_OF(
--     'ErrorInvalidAdminPassword'
-- )
--
function _M.setAdminPassword(sc, password, transactionSafe, hintSupported)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    if not platform.isValidAdminPassword(password) then
        return 'ErrorInvalidAdminPassword'
    end
    if transactionSafe then
        sc:setevent('jnap_side_effects-setpassword', util.wrap(password))
    else
        platform.setAdminPassword(sc, password)
    end
    -- If the password hint feature is not supported, then unset it
    if not hintSupported then
        sc:set_admin_password_hint(nil)
    end

    if util.isNodeUtilModuleAvailable() then
        sc:set_node_user_set_admin_password('true')
    end
end

--
-- setAdminPassword2 Adds a new optional password hint parameter
--
function _M.setAdminPassword2(sc, input, transactionSafe)
    sc:writelock()

    local error = _M.setAdminPassword(sc, input.adminPassword, transactionSafe, true)
    if error then
        return error
    end

    if (input.passwordHint) then
        if #input.passwordHint > platform.MAX_PASSWORD_HINT_LENGTH then
            return 'ErrorInvalidPasswordHint'
        end
        sc:set_admin_password_hint(input.passwordHint)
    end
end

--
-- Get whether the current admin password of the local device is the default value.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.getIsAdminPasswordDefault(sc)
    sc:readlock()
    return sc:get_admin_password_is_default()
end

--
-- Check the admin password of the device.
--
-- This method will check that the admin password is valid.
--
-- input = CONTEXT, STRING
--
-- output = BOOLEAN
--
function _M.checkAdminPassword(sc, password)
    local platform = require('platform')
    sc:readlock()
    return sc:isvalidauth(platform.ADMIN_USERNAME, password)
end

--
-- Get the unsecured wifi warning boolean
--
-- intput = CONTEXT
--
-- output = BOOLEAN
--
function _M.getUnsecuredWiFiWarningEnabled(sc)
    sc:readlock()
    return not sc:get_user_accepts_unsecure_wifi()
end

--
-- Set the unsecured wifi warning boolean
--
-- intput = CONTEXT, BOOLEAN
--
function _M.setUnsecuredWiFiWarningEnabled(sc, enabled)
    sc:writelock()
    if not platform.isReady(sc) then
        return '_ErrorNotReady'
    end
    current = sc:get_user_accepts_unsecure_wifi()
    if current and enabled then
        return 'ErrorCannotEnableUnsecuredWiFiWarning'
    end
    sc:set_user_accepts_unsecure_wifi(not enabled)
end

--
-- Trigger a reboot of the device.
--
-- input = CONTEXT
--
function _M.reboot(sc)
    sc:writelock()
    sc:reboot()
end


--
-- Clean up indexed list and return whether anything was changed.
--
-- input = CONTEXT, NUMBER, NUMBER, STRING, ARRAY_OF(STRING)
--
-- output = BOOLEAN
--
function _M.unsetIndexedList(sc, first, last, ns, keys)
    sc:writelock()
    local changed = false
    for i = first, last do
        local nsKey = ns..i
        local namespace = sc:get(nsKey)
        if namespace then
            changed = sc:unset(nsKey) or changed
            for _, key in ipairs(keys) do
                changed = sc:unset(namespace..'::'..key) or changed
            end
        end
    end
    return changed
end

--
-- Get the remote UI enabled setting for the local device.
--
-- intput = CONTEXT
--
-- output = BOOLEAN
--
function _M.getRemoteUIEnabled(sc)
    sc:readlock()
    return sc:get_remote_ui_enabled()
end

--
-- Set the remote UI enabled setting for the local device.
--
-- input = CONTEXT, STRING
--
-- output = NIL
--
function _M.setRemoteUIEnabled(sc, isEnabled)
    sc:writelock()
    sc:set_remote_ui_enabled(isEnabled)
end

--
-- Get the data upload user consent.
--
-- intput = CONTEXT
--
-- output = BOOLEAN
--
function _M.getDataUploadUserConsent(sc)
    sc:readlock()
    return sc:get_data_upload_user_consent()
end

--
-- Set the data upload user consent.
--
-- intput = CONTEXT, BOOLEAN
--
function _M.setDataUploadUserConsent(sc, consent)
    sc:writelock()
    sc:set_data_upload_user_consent(consent)
end

--
-- Set failure attempts to verify reset code.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
local function setFailedRouterResetCode(sc)
    local varFailedAttempts = sc:get_node_admin_password_failed_attempts() + 1
    local varFailedLimit = sc:get_node_admin_password_failed_limit()
    if varFailedAttempts <= varFailedLimit then
        sc:set_node_admin_password_failed_attempts(varFailedAttempts)
        -- if limit is reached, then enable throttling
        if varFailedAttempts == varFailedLimit then
            sc:set_node_admin_password_failed_expiration(os.time() + _M.SECONDS_IN_A_DAY)
        end
    end
end

--
-- Get whether the throttling is enabled.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
local function isThrottlingEnabled(sc)
    -- throttling is indicated by admin_password::expiration sysevent
    local expiration = sc:get_node_admin_password_failed_expiration()
    -- if the sysevent value exists and it's not 0, then it means throttling is enabled
    if expiration and expiration ~= 0 then
        local currentTime = tonumber(os.time())
        if currentTime < tonumber(expiration) then
            return true, expiration
        else -- throttle expired, need to clear
            sc:set_node_admin_password_failed_expiration(nil) -- Unfortunately we don't have sysevent unset, instead we'll use a nil to clear
            sc:set_node_admin_password_failed_attempts(0)
        end
    end

    return false
end

--
-- Get the attempts remaining
--
-- output = NUMBER
--
local function getAttemptsRemaining(sc)
    local varFailedAttempts = sc:get_node_admin_password_failed_attempts()
    local varFailedLimit = sc:get_node_admin_password_failed_limit()
    return varFailedLimit - varFailedAttempts
end

--
-- Get whether the reset code matches.
--
-- input = CONTEXT, STRING
--
-- output = BOOLEAN
--
function _M.verifyRouterResetCode(sc, resetCode)
    -- Ensure no lock is held when making this call as it is blocking
    assert(not sc:isreadlocked() and not sc:iswritelocked(), 'must not hold the sysctx lock when calling verifyRouterResetCode')
    -- It needs to hold its own lock because it needs to commit syscfg and sysevent values even when an error is returned.
    -- This call is one of a kind. Generally, JNAP will rollback any changes when it encounters an error as part of
    -- IPA_Uninitialize() routine.
    local ownedsc = require('libsysctxlua').new()
    ownedsc:writelock()
    -- First, make sure we're not being throttled
    local isThrottled, expiration = isThrottlingEnabled(ownedsc)
    if isThrottled then
        ownedsc:commit()
        return 'ErrorConsecutiveInvalidResetCodeEntered', {
            lockoutExpirationTime = hdk.datetime(tonumber(expiration))
        }
    end

    -- If not throttled, we verify reset code
    if ownedsc:is_valid_nodes_recovery_key(resetCode) then
        ownedsc:set_node_admin_password_failed_expiration(nil)
        ownedsc:set_node_admin_password_failed_attempts(0)
        ownedsc:commit()
    else -- if reset code is invalid
        setFailedRouterResetCode(ownedsc)
        local attemptsRemaining = getAttemptsRemaining(ownedsc)
        ownedsc:commit()
        return 'ErrorInvalidResetCode', {
            attemptsRemaining = attemptsRemaining
        }
    end
end

--
-- Get whether the current admin password of the local device was changed by a user.
--
-- input = CONTEXT
--
-- output = BOOLEAN
--
function _M.isAdminPasswordSetByUser(sc)
    sc:readlock()
    return sc:get_node_user_set_admin_password() == 'true'
end

--
-- Get the firmware product type.
--
-- output = STRING
--
function _M.getFirmwareProductType()
    local file = io.open(_M.PRODUCT_TYPE_FILE_PATH, 'r')
    if (file) then
        local type = file:read()
        file:close()
        return type
    end
end

return _M -- return the module
