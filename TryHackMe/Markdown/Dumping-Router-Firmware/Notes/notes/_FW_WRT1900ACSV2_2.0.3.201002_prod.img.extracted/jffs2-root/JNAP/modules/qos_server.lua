--
-- 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
--
-- $Author$
-- $DateTime$
-- $Id$
--

local function GetWLANQoSSettings(ctx)
    local qos = require('qos')

    local sc = ctx:sysctx()
    return 'OK', {
        isWMMEnabled = qos.getIsWMMEnabled(sc),
        isWirelessAcknowledgementEnabled = qos.getIsWirelessAcknowledgementEnabled(sc)
    }
end

local function SetWLANQoSSettings(ctx, input)
    local qos = require('qos')

    local sc = ctx:sysctx()
    local error = (qos.setIsWMMEnabled(sc, input.isWMMEnabled) or
                   qos.setIsWirelessAcknowledgementEnabled(sc, input.isWirelessAcknowledgementEnabled))
    return error or 'OK'
end

local function GetLANQoSSettings(ctx)
    local qos = require('qos')

    local sc = ctx:sysctx()
    return 'OK', {
        ethernetPortPriorities = qos.getSwitchPortPriorities(sc)
    }
end

local function SetLANQoSSettings(ctx, input)
    local qos = require('qos')

    local sc = ctx:sysctx()
    local error = qos.setSwitchPortPriorities(sc, input.ethernetPortPriorities)
    return error or 'OK'
end

local function GetQoSSettings(ctx)
    local qos = require('qos')

    local sc = ctx:sysctx()
    return 'OK', {
        isQoSEnabled = qos.getIsEnabled(sc),
        isQoSAutoPrioritizingEnabled = qos.getIsQoSAutoPrioritizingEnabled(sc),
        upstreamBandwidthbps = qos.getUpstreamBandwidthbps(sc),
        downstreamBandwidthbps = qos.getDownstreamBandwidthbps(sc),
        deviceRules = qos.getDeviceRules(sc),
        applicationRules = qos.getApplicationRules(sc),
        maxDescriptionLength = qos.MAX_RULE_DESCRIPTION_LENGTH,
        maxApplicationRules = qos.MAX_APPLICATION_RULES,
        maxPortRanges = qos.MAX_PORT_RANGES,
        maxDeviceRules = qos.MAX_DEVICE_RULES
    }
end

local function SetQoSSettings(ctx, input)
    local qos = require('qos')

    local sc = ctx:sysctx()
    local error =
        qos.setIsEnabled(sc, input.isQoSEnabled) or
        qos.setIsQoSAutoPrioritizingEnabled(sc, input.isQoSAutoPrioritizingEnabled) or
        qos.setUpstreamBandwidthbps(sc, input.upstreamBandwidthbps) or
        qos.setDownstreamBandwidthbps(sc, input.downstreamBandwidthbps) or
        qos.setDeviceRules(sc, input.deviceRules) or
        qos.setApplicationRules(sc, input.applicationRules)
    return error or 'OK'
end

local function GetQoSSettings2(ctx)
    local qos = require('qos')

    local sc = ctx:sysctx()
    return 'OK', {
        maxAutoAssignedDeviceRules = qos.MAX_AUTO_ASSIGNED_DEVICE_RULES,
        maxAutoAssignedApplicationRules = qos.MAX_AUTO_ASSIGNED_APPLICATION_RULES,
        autoAssignedDeviceRules = qos.getAutoAssignedDeviceRules(sc),
        autoAssignedApplicationRules = qos.getAutoAssignedApplicationRules(sc)
    }
end

-- DEPRECATED: This action does nothing and will always return an 'OK' result.
local function UpdateAutoAssignedRules(ctx)
    return 'OK'
end

local function BeginDownloadCalibration(ctx)
    local sc = ctx:sysctx()
    sc:writelock()
    sc:setevent('speedtest-download-start')

    return 'OK'
end

local function EndDownloadCalibration(ctx)
    local sc = ctx:sysctx()
    sc:writelock()
    sc:setevent('speedtest-download-stop')

    return 'OK'
end

local function BeginUploadCalibration(ctx)
    local sc = ctx:sysctx()
    sc:writelock()
    sc:setevent('speedtest-upload-start')

    return 'OK'
end

local function EndUploadCalibration(ctx)
    local sc = ctx:sysctx()
    sc:writelock()
    sc:setevent('speedtest-upload-stop')

    return 'OK'
end

return require('libhdklua').loadmodule('jnap_qos'), {
    ['http://linksys.com/jnap/qos/GetWLANQoSSettings'] = GetWLANQoSSettings,
    ['http://linksys.com/jnap/qos/SetWLANQoSSettings'] = SetWLANQoSSettings,
    ['http://linksys.com/jnap/qos/GetLANQoSSettings'] = GetLANQoSSettings,
    ['http://linksys.com/jnap/qos/SetLANQoSSettings'] = SetLANQoSSettings,
    ['http://linksys.com/jnap/qos/GetQoSSettings'] = GetQoSSettings,
    ['http://linksys.com/jnap/qos/SetQoSSettings'] = SetQoSSettings,
    ['http://linksys.com/jnap/qos/GetQoSSettings2'] = GetQoSSettings2,
    ['http://linksys.com/jnap/qos/UpdateAutoAssignedRules'] = UpdateAutoAssignedRules,

    ['http://linksys.com/jnap/qos/calibration/BeginDownloadCalibration'] = BeginDownloadCalibration,
    ['http://linksys.com/jnap/qos/calibration/EndDownloadCalibration'] = EndDownloadCalibration,
    ['http://linksys.com/jnap/qos/calibration/BeginUploadCalibration'] = BeginUploadCalibration,
    ['http://linksys.com/jnap/qos/calibration/EndUploadCalibration'] = EndUploadCalibration
}
