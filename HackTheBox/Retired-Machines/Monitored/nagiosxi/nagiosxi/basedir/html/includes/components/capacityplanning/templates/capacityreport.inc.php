<script>
    var targetlist = <?php echo json_encode($targetlist); ?>;
    var dataoptions = <?php echo json_encode($dataoptions); ?>;

    var API_URL = base_url + '/includes/components/capacityplanning/cp-extrap.php';

    // For each target in the target list, make a query for the data in order
    // to fill the extrapolation table and graph it.
    $.each(targetlist, function (host, info) {
        query = get_extrapolation_query(host, info.service, info.track);
        $.getJSON(API_URL, query, function (data) {
            make_highcharts(data, host, info.service, info.track, info.uid, <?php echo $width-2; ?>, <?php echo $height-2; ?>);
            summary(data, info.uid);
            exec_summary( data, info.uid);
            raw_data(data, info.uid);
        }).fail(function (d, textStatus, error) {
            console.error('getJSON failed, status: ' + textStatus + ', error: ' + error)
        })
    });
</script>

<?php include('capacityreporttemplate.inc.php'); ?>
