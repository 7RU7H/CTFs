<?php
require_once(dirname(__FILE__) . '/../componenthelper.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs 
grab_request_vars();
check_prereqs();
check_authentication(false);
?>
<form role="form">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" ng-click="cancel()">
            <span aria-hidden="true">&times;</span>
            <span class="sr-only"><?php echo _('Close'); ?></span>
        </button>
        <h4 class="modal-title" id="nagiosTrendsLabel">
            <?php echo _('Network Status Map Settings'); ?>
        </h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label><?php echo _('Layout'); ?></label>
            <select class="form-control" ng-model="params.layout">
                <option value="3"><?php echo _('Balanced Tree (Horizontal)'); ?></option>
                <option value="7"><?php echo _('Balanced Tree (Vertical)'); ?></option>
                <option value="6"><?php echo _('Circular Balloon'); ?></option>
                <option value="5"><?php echo _('Circular Markup'); ?></option>
                <option value="2"><?php echo _('Collapsed Tree (Horizontal)'); ?></option>
                <option value="8"><?php echo _('Collapsed Tree (Vertical)'); ?></option>
                <option value="1"><?php echo _('Depth Layers (Horizontal)'); ?></option>
                <option value="9"><?php echo _('Depth Layers (Vertical)'); ?></option>
                <option value="10"><?php echo _('Force Map'); ?></option>
                <option value="0"><?php echo _('User Supplied'); ?></option>
            </select>
        </div>
        <div class="form-group" ng-show="showDimensions()">
            <label><?php echo _('Dimensions (User Supplied Layout Only)'); ?></label>
            <select class="form-control" ng-model="params.dimensions">
                <option value="fixed"><?php echo _('Fixed'); ?></option>
                <option value="auto"><?php echo _('Automatic'); ?></option>
                <option value="user"><?php echo _('User Supplied'); ?></option>
            </select>
        </div>
        <div class="form-group" ng-show="showCoordinates()">
            <label><?php echo _('Upper Left X Coordinate'); ?></label>
            <input type="number" class="form-control" placeholder="0"
                    ng-model="params.ulx">
        </div>
        <div class="form-group" ng-show="showCoordinates()">
            <label><?php echo _('Upper Left Y Coordinate'); ?></label>
            <input type="number" class="form-control" placeholder="0"
                    ng-model="params.uly">
        </div>
        <div class="form-group" ng-show="showCoordinates()">
            <label><?php echo _('Lower Right X Coordinate'); ?></label>
            <input type="number" class="form-control" placeholder="0"
                    ng-model="params.lrx">
        </div>
        <div class="form-group" ng-show="showCoordinates()">
            <label><?php echo _('Lower Right Y Coordinate'); ?></label>
            <input type="number" class="form-control" placeholder="0"
                    ng-model="params.lry">
        </div>
        <div class="form-group">
            <label><?php echo _('Root Node'); ?></label>
            <select class="form-control" ng-model="params.root"
                    ng-options="node for node in nodelist">
            </select>
        </div>
        <div class="form-group">
            <label><?php echo _('Maximum Zoom Ratio'); ?></label>
            <input type="text" class="form-control"
                    ng-model="params.maxzoom">
        </div>
        <!--
        <div class="checkbox">
            <label>
                <input type="checkbox" ng-model="params.nolinks">
                Disable Display of Links
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" ng-model="params.notext">
                Disable Display of Text
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" ng-model="params.nopopups">
                Disable Display of Popups
            </label>
        </div>
        -->
    </div>
    <div class="modal-footer">
        <button id="submit" type="submit" class="btn btn-default"
                ng-disabled="disableApply()" ng-click="apply()">
            <?php echo _('Apply'); ?>
        </button>
    </div>
</form>
