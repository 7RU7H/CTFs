<?php
// Create some random unique ids.
$hc_div_id = "hc-" . uniqid();
$tb_div_id = "tb-" . uniqid();
$rd_div_id = "rd-" . uniqid();
$tab_div_id = "tab-" . uniqid();
$class_id = "tb-box-" . uniqid();
?>

<div id='capacity-report-<?php echo $targetlist[$host]['uid']; ?>' class='capacity-report'>
    <div class="cp-tab-container" style="height: <?php echo $height; ?>px;">
        <div class="<?php echo $class_id; ?>" id="<?php echo $hc_div_id; ?>">
            <div id='highcharts-target-<?php echo $targetlist[$host]['uid']; ?>'></div>
        </div>
        <div class="<?php echo $class_id; ?>" id="<?php echo $tb_div_id; ?>" style="display: none;">
            <table id='capacityplanning-execsum-<?php echo $targetlist[$host]['uid']; ?>' class="table table-condensed table-no-margin">
                <thead>
                    <tr><th><?php echo _("Executive Summary"); ?></th></tr>
                </thead>
                <tbody id='capacityreport-execsum-tbody-<?php echo $targetlist[$host]['uid']; ?>'>
                    <tr><td id='capacityreport-execsum-td-<?php echo $targetlist[$host]['uid']; ?>'>
                      <?php echo _("track ").$targetlist[$host]['track']." ";
if($targetlist[$host]['service'] != "_HOST_" ) echo "for service ".$targetlist[$host]['service'];
echo _(" on ").$host." "; ?>
                        </td>
                    </tr>
                </tbody>
              </table>
              <table id='capacityplanning-table-<?php echo $targetlist[$host]['uid']; ?>' class="table table-condensed table-hover table-no-margin">
                  <thead>
                    <tr>
                        <th><?php echo _("Summary Data"); ?></th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody id='capacityreport-tbody-<?php echo $targetlist[$host]['uid']; ?>'>
                    <tr>
                        <td><?php echo _("Host"); ?></td>
                        <td><?php echo $host; ?></td>
                    </tr>
                    <tr>
                        <td><?php echo _("Service"); ?></td>
                        <td><?php echo $targetlist[$host]['service']; ?></td>
                    </tr>
                    <tr>
                        <td><?php echo _("Track"); ?></td>
                        <td><?php echo $targetlist[$host]['track']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="<?php echo $class_id; ?>" id="<?php echo $rd_div_id; ?>" style="display: none; width: <?php echo $width; ?>px; height: <?php echo $height; ?>px;">
            <table id='rawdata-table-<?php echo $targetlist[$host]['uid']; ?>' class="cp-rawdata table table-condensed table-striped table-hover table-no-margin">
                <thead>
                    <tr>
                      <th style="width: 140px"><?php echo _("Date"); ?></th>
                      <th><?php echo _("Value"); ?></th>
                      <th><?php echo _("Warning"); ?></th>
                      <th><?php echo _("Critial"); ?></th>
                      <th><?php echo _("Fit"); ?></th>
                    </tr>
                </thead>
                <tbody id='rawdata-tbody-<?php echo $targetlist[$host]['uid']; ?>' style="width: <?php echo $width-3; ?>px; height: <?php echo ($height - 30)?>px;" >
                </tbody>
            </table>
        </div>
    </div>
    <ul class="cp-tabs" id="<?php echo $tab_div_id; ?>">
        <li class="active tt-bind" data-tab="<?php echo $hc_div_id; ?>" data-type="graph" data-placement="left" title="<?php echo _("Graph"); ?>"><i class="fa fa-area-chart fa-fw fa-14"></i></li>
        <li data-tab="<?php echo $tb_div_id; ?>" data-type="table" class="tt-bind" data-placement="left" title="<?php echo _("Summary"); ?>"><i class="fa fa-file-text fa-fw fa-14"></i></li>
        <li data-tab="<?php echo $rd_div_id; ?>" data-type="static-headers-table" class="tt-bind" data-placement="left" title="<?php echo _("Data"); ?>"><i class="fa fa-list fa-fw fa-14"></i></li>
    </ul>
    <div style="clear:both;"></div>
</div>

<script>
$("#<?php echo $tab_div_id; ?> li").click(function () {
    // Clear all
    $("#<?php echo $tab_div_id; ?> li").removeClass('active');
    $(".<?php echo $class_id; ?>").hide();

    var display_tab = $(this).data("tab");
    var display_type = $(this).data("type");

    if (display_type == "graph") {
        $("#" + display_tab).parents('.capacityplanning_map_outboard').find('.dashifybutton').show();
    } else { 
        $("#" + display_tab).parents('.capacityplanning_map_outboard').find('.dashifybutton').hide();
    }

    if (display_type == "table") {
        $("#" + display_tab).parent().addClass('cp-scroll');
    } else {
        $("#" + display_tab).parent().removeClass('cp-scroll');
    }

    $("#" + display_tab).show();
    $(this).addClass('active');

    // after it has gone visible, make headers align with data in 
    if (display_type == "static-headers-table") {
        adjust_table_hdr_cols($("#rawdata-table-<?php echo $targetlist[$host]['uid']; ?>"));
    }
});
</script>
