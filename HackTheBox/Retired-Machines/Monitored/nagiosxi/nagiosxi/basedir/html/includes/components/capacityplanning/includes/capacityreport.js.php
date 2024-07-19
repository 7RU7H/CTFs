<?php
//
// Capacity Planning Component
// Copyright (c) 2014-2019 Nagios Enterprises, LLC. All rights reserved.
//  

require_once(dirname(__FILE__) . '/../cp-common.inc.php');
db_connect_all();
header('Content-Type: application/javascript');
?>
$(document).ready(function () {
    // Do we really want to set a no-op ready functions here?
});

function get_extrapolation_query(host, service, track, count) {
    return {
        host: host,
        service: service,
        track: track,
        options: dataoptions[host],
        cmd: 'extrapolate'
    };
};

function exec_summary(d, id) {
    var unit = d.unit ? d.unit : '';
    var tdsum = $('#capacityreport-execsum-td-' + id);
    // pull out the what was there, the track, service, host
    var trackServiceHost= tdsum.text();
    var execSumm = '<?php echo _("The"); ?> '+trackServiceHost+'<?php echo _("has"); ?> ';
    var interval = find_interval( d);

    if(( interval.observed == '') || ( interval.predicted == '' )) {
        execSumm='<?php echo _("Unable to determine the interval for observed or predicted."); ?> ';
    } else {

        // check that we have data...
        if( d.integrity <= 0 ) { // we have a problem no data...
            // no data probably.... do we have a floor for data need to make predictions ?
            execSumm+='<?php echo _("insufficient data to make predictions."); ?>';
        } else {

            // find the date of the emax because we should have it now...
            var foundDate=d.highcharts[1].pointStart;
            for(var datapt in d.highcharts[1].data  ) {
                if( d.highcharts[1].data[datapt] == d.emax ) {
                    break;
                };
                foundDate+=d.highcharts[1].pointInterval;
            };

            // The {host} (if service (if track))

            if( d.integrity >= 0.90 ) execSumm+="<?php echo _("High"); ?> ";
            else if ( d.integrity <= 0.50 ) execSumm+="<?php echo _("Low"); ?> ";
            else execSumm+="<?php echo _("Medium"); ?> ";
            execSumm += "<?php echo _("data integrity over the previous observed"); ?> "+interval.observed+" <?php echo _("period"); ?>. ";

            //$('<td>', {text: 'Recent Maximum Value'}).appendTo(tr);
            execSumm +="<?php echo _("During the observed"); ?> "+interval.observed+" <?php echo _("the data has ranged to a maximum of"); ?> ";
            execSumm +=parseFloat(d.dmax).toFixed(2) + unit + ", ";

            //$('<td>', {text: 'Recent Average Value'}).appendTo(tr);
            execSumm+="<?php echo _("with an average of"); ?> ";
            execSumm+=parseFloat(d.dmean).toFixed(2) + unit + ". ";

            execSumm+="<?php echo _("For the estimated"); ?> "+interval.predicted+" <?php echo _("period the"); ?> ";
            if(d.emax != 0 ) {
                //$('<th>', {text: 'The Extrapolated Future', colspan: 999}).appendTo(tr);
                execSumm+='<?php echo _("maximum of"); ?> '; 
                execSumm+=parseFloat(d.emax).toFixed(2) + unit;
                //$('<td>', {text: 'Average will change'}).appendTo(tr);
                var delta = d.emax - d.dmax;
                if ( delta != 0 ) {
                    var sign = (delta < 0) ? '-' : '+';
                    var textNum = sign + parseFloat(Math.abs(delta)).toFixed(2) + unit;
                    execSumm+= ", a "+textNum+" ";
                    var pct = delta / d.dmax * 100;
                    if (isFinite(pct)) execSumm +='('+sign + parseFloat(Math.abs(pct)).toFixed(2) + '%) ';
                } else {
                    execSumm+=', <?php echo _("no"); ?> ';
                }
                execSumm+='<?php echo _("change from observed maximum, is predicted to occur at"); ?> '+date_YmdHM( foundDate)+'. ';
            } else {
                execSumm+='<?php echo _("maximum is not expected to change."); ?> ';
            }

            //$('<td>', {text: 'Maximum will change'}).appendTo(tr);

            execSumm+="<?php echo _("For the estimated"); ?> "+interval.predicted+" <?php echo _("period the"); ?> ";
            if(d.emean != 0 ) {
                //$('<td>', {text: 'Extrapolated Average Value'}).appendTo(tr);
                execSumm+="<?php echo _("estimated average is"); ?> "+parseFloat(d.emean).toFixed(2) + unit + ", <?php echo _("which is"); ?> ";
                //$('<th>', {text: 'Past vs Future Analysis', colspan: 999}).appendTo(tr);
                var delta = d.emean - d.dmean;
                if( delta != 0 ) {
                    var sign = (delta < 0) ? '-' : '+';
                    var textNum = sign + parseFloat(Math.abs(delta)).toFixed(2) + unit;
                    execSumm+='a '+textNum+" (";
                    var pct = delta / d.dmean * 100;
                    if (isFinite(pct)) execSumm += sign + parseFloat(Math.abs(pct)).toFixed(2) + '%) ';
                } else {
                    execSumm+=' <?php echo _("no"); ?> ';
                }
                execSumm+='<?php echo _("change from observed data."); ?> ';
            } else {
                execSumm+="<?php echo _("average is not expected to change."); ?> ";
            }
        } // if not enough data
    }

    // put the execSumm into where we want it.
    tdsum.html(execSumm);
}

function summary(d, id) {
    var unit = d.unit ? d.unit : '';
    var tbody = $('#capacityreport-tbody-' + id);

    var tr = $('<tr>');
    $('<td>', {text: '<?php echo _("Data Integrity (Not NaN)"); ?>'}).appendTo(tr);
    $('<td>', {text: parseFloat((d.integrity) * 100).toFixed(2) + '%'}).appendTo(tr);
    tr.appendTo(tbody);

    var tr = $('<tr>');
    $('<th>', {text: '<?php echo _("The Recent Past"); ?>', colspan: 999}).appendTo(tr);
    tr.appendTo(tbody);

    var tr = $('<tr>');
    $('<td>', {text: '<?php echo _("Recent Maximum Value"); ?>'}).appendTo(tr);
    $('<td>', {text: parseFloat(d.dmax).toFixed(2) + unit}).appendTo(tr);
    tr.appendTo(tbody);

    var tr = $('<tr>');
    $('<td>', {text: '<?php echo _("Recent Average Value"); ?>'}).appendTo(tr);
    $('<td>', {text: parseFloat(d.dmean).toFixed(2) + unit}).appendTo(tr);
    tr.appendTo(tbody);


    var tr = $('<tr>');
    $('<th>', {text: '<?php echo _("The Extrapolated Future"); ?>', colspan: 999}).appendTo(tr);
    tr.appendTo(tbody);

    var tr = $('<tr>');
    $('<td>', {text: '<?php echo _("Extrapolated Maximum Value"); ?>'}).appendTo(tr);
    $('<td>', {text: parseFloat(d.emax).toFixed(2) + unit}).appendTo(tr);
    tr.appendTo(tbody);

    var tr = $('<tr>');
    $('<td>', {text: '<?php echo _("Extrapolated Average Value"); ?>'}).appendTo(tr);
    $('<td>', {text: parseFloat(d.emean).toFixed(2) + unit}).appendTo(tr);
    tr.appendTo(tbody);


    var tr = $('<tr>');
    $('<th>', {text: '<?php echo _("Past vs Future Analysis"); ?>', colspan: 999}).appendTo(tr);
    tr.appendTo(tbody);

    var delta = d.emax - d.dmax;
    var sign = (delta < 0) ? '-' : '+';
    var text = sign + parseFloat(Math.abs(delta)).toFixed(2) + unit;
    var pct = delta / d.dmax * 100;
    if (isFinite(pct)) text += ' (' + sign + parseFloat(Math.abs(pct)).toFixed(2) + '%)';

    var tr = $('<tr>');
    $('<td>', {text: '<?php echo _("Maximum will change"); ?>'}).appendTo(tr);
    $('<td>', {text: text}).appendTo(tr);
    tr.appendTo(tbody);

    var delta = d.emean - d.dmean;
    var sign = (delta < 0) ? '-' : '+';
    var text = sign + parseFloat(Math.abs(delta)).toFixed(2) + unit;
    var pct = delta / d.dmean * 100;
    if (isFinite(pct)) text += ' (' + sign + parseFloat(Math.abs(pct)).toFixed(2) + '%)';

    var tr = $('<tr>');
    $('<td>', {text: '<?php echo _("Average will change"); ?>'}).appendTo(tr);
    $('<td>', {text: text}).appendTo(tr);
    tr.appendTo(tbody);

}

function make_highcharts_constant_line(name, color, level, start, step, stop) {
    var n = Math.floor(stop - start) / step;
    var d = new Array(n);
    for (var i = 0; i < n; ++i) d[i] = level;

    var visible = false; 

    // warn/crit display tps#7514 -bh
    if (name == 'Warning' || name == 'Critical') {
        visible = <?php echo (get_option('wc_display', 0) == 1 ? "true" : "false"); ?>;
    }

    return {
        type: 'line',
        color: color,
        name: name,
        pointStart: start,
        pointInterval: step,
        visible: visible,
        data: d
    }
}

function make_highcharts_fit_range(fit, name, delta, zIndex, fillOpacity, fillColor) {
    var n = fit.data.length;
    var d = new Array(n);
    for (var i = 0; i < n; ++i) d[i] = [
        fit.pointStart + i * fit.pointInterval,
        fit.data[i] - delta,
        fit.data[i] + delta
    ];

    var series = {
        type: 'arearange',
        name: name,
        pointStart: fit.pointStart,
        pointInterval: fit.pointInterval,
        linked: 'previous',
        zIndex: zIndex,
        fillOpacity: fillOpacity,
        visible: false,
        data: d
    };

    if (fillColor) series.fillColor = fillColor;

    return series;
};

function make_highcharts(data, host, service, track, uid, width, height) {
    var title = track + ' <?php echo _("for"); ?> ' + service + ' <?php echo _("on"); ?> ' + host;

    var hideoptions = (get_url_parameter('hideoptions') == '1');
    var tracking = !hideoptions;

    var series_data = data.highcharts;

    // Add warning and critical level lines if not hiding options, and we
    // have first and last timepoints and critical or warning level values.
    var start = data.t_start * 1000;
    var step = data.t_step * 1000;
    var stop = data.t_stop * 1000;
    if (start && step && stop) {
        if (data.warn_level !== undefined) {
            series_data.push(make_highcharts_constant_line(
                'Warning', '#dd0', data.warn_level, start, step, stop
            ));
        }
        if (data.crit_level !== undefined) {
            series_data.push(make_highcharts_constant_line(
                'Critical', '#d00', data.crit_level, start, step, stop
            ));
        }
    }

    Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });

    var yAxis = {
        title: {
            text: track
        },
        min: 0,
        startOnTick: true,
        endOnTick: true,
        gridLineWidth: 1,
        gridLineColor: '#EEE'
    };
    if (data.unit) yAxis.title.text += ' (' + data.unit + ')';
    if (data.dmax == 0 && data.emax == 0) {
        // Fix the y-axis labels and limits for all zero data.
        // @todo: We should check min values too to support negative values.
        yAxis.max = 1;
        yAxis.allowDecimals = false;
        yAxis.showFirstLabel = true;
        yAxis.showLastLabel = false;
    }

    if (data.dmin < 0 || data.emin < 0) {
        yAxis.min = 1.1 * Math.min(data.dmin, data.emin);
    }

    // Create a nice looking filename
    var filename = title.replace(/ /g, "_");

    var chart = new Highcharts.Chart({
        exporting: {
            url: window.location.protocol + '//' + window.location.hostname + '/nagiosxi/includes/components/highcharts/exporting-server/index.php',
            filename: filename,
            chartOptions: { chart: { spacing: [25, 25, 25, 20], marginRight: 30 } }
        },
        chart: {
            renderTo: 'highcharts-target-' + uid,
            zoomType: 'x',
            height: height,
            width: width,
            animation: false,
            ignoreHiddenSeries: true,
            spacingRight: 25,
            spacingTop: 20,
            marginTop: 65,
            marginLeft: 75,
            plotBorderWidth: 1,
            plotBorderColor: '#EEE',
            resetZoomButton: {
                position: {
                    x: -29,
                    y: -50
                },
                theme: {
                    height: 15,
                    r: 0,
                    states: {
                        hover: {
                            'stroke-width': 1,
                            stroke: '#AAA',
                            fill: '#F0F0F0'
                        },
                        select: {
                            'stroke-width': 1,
                            stroke: '#AAA',
                            fill: '#F0F0F0'
                        }
                    },
                    style: {
                        cursor: 'pointer'
                    }
                }
            }
        },
        credits: {
            enabled: false
        },
        title: {
            text: title,
            style: {
                fontWeight: 'bold',
                "font-family": "'verdana', 'serif'",
                "font-size": 15
            }
        },
        navigation: {
            menuStyle: {
                'box-shadow': '1px 1px 0 #AAA',
                'border-right': 0,
                'border-bottom': 0
            },
            buttonOptions: {
                y: -4,
                theme: {
                    r: 0,
                    states: {
                        hover: {
                            'stroke-width': 1,
                            stroke: '#AAA',
                            fill: '#F0F0F0'
                        },
                        select: {
                            'stroke-width': 1,
                            stroke: '#AAA',
                            fill: '#F0F0F0'
                        }
                    }
                }
            }
        },
        xAxis: {
            type: 'datetime',
            maxZoom: 300000,
            title: {
                text: null
            },
            gridLineWidth: 1,
            gridLineColor: '#DFDFDF',
            gridLineDashStyle: 'dot',
            lineColor: '#EEE',
            tickColor: '#EEE'
        },
        yAxis: yAxis,
        tooltip: {
            enabled: tracking,
            crosshairs: true,
            shared: true,
            shadow: false,
            shape: 'callout',
            valueDecimals: 3,
            borderRadius: 0,
            valueSuffix: data.unit ? ' ' + data.unit : '',
            xDateFormat: '%Y-%m-%d %H:%M'
        },
        legend: {
            enabled: true
        },
        plotOptions: {
            series: {
                fillOpacity: 0.5,
                animation: false,
                shadow: false,
                enableMouseTracking: tracking
            },
            area: {
                lineWidth: 1,
                marker: {
                    enabled: false,
                    states: {
                        hover: {
                            enabled: true,
                            radius: 4
                        }
                    }
                },
                shadow: false,
                states: {
                    hover: {
                        lineWidth: 1.5
                    }
                }
            },
            arearange: {

            },
            line: {
                lineWidth: 1.5,
                marker: {
                    enabled: false,
                    states: {
                        hover: {
                            enabled: true,
                            radius: 4
                        }
                    }
                },
                shadow: false,
                states: {
                    hover: {
                        lineWidth: 2
                    }
                }
            }

        },
        noData: {
            style: {
                color: "#AAAAAA",
                fontWeight: "normal"
            }
        },
        series: series_data
    });

    if (data.error) {
        Highcharts.setOptions({
            lang: { noData: '<?php echo _("No data available"); ?>' }
        });
    }
}

function raw_data(data_rd, id_rd) {
    var rd_body = $('#rawdata-tbody-' + id_rd);
    var total_point = 0;
    // build table data
    function table_data( what_data, thedata) { // that pretty much sums it up..
        var table_blob=""; // the blob of table we are going to pass back.

        if( thedata.dmean !== undefined ) dmean=thedata.dmean;
        else dmean="";
        
        for(var dataset in thedata.highcharts )  // look through highchart data for Fit
            if( thedata.highcharts[dataset].name == 'Fit' )
                fit = thedata.highcharts[dataset]
        
        for(var dataset in thedata.highcharts ) { // look through highcart data for the data we want
            if( thedata.highcharts[dataset].name == what_data ) {
                var datapt_timestamp=thedata.highcharts[dataset].pointStart;
                        //console.log( "Date start:"+datapt_timestamp);
                for( var datapt in thedata.highcharts[dataset].data ) {
                            // the number of decimal places from  make_highchart new highchart { tooltip: valueDecimanls:
                            var decPlaces=3;
                    table_blob +="<TR>"; // Observed color == #7cb5ec Predicted Color == #434348
         
                    var TheTD="<TD Class=\""+what_data+"\" style=\"overflow: hidden\">";
                    // Date | Value | Warn | Crit | Fit
                    table_blob +=TheTD+date_YmdHM(datapt_timestamp)+"</TD>";
                    table_blob +=TheTD+thedata.highcharts[dataset].data[datapt].toFixed(decPlaces)+"</TD>";

                    if (data_rd.warn_level !== undefined) {
                        table_blob +=TheTD+data_rd.warn_level+"</TD>";
                    } else 
                        table_blob +=TheTD+" </TD>";
                    if (data_rd.crit_level !== undefined) {
                        table_blob +=TheTD+data_rd.crit_level;
                    } else 
                        table_blob +=TheTD+" </TD>";

                    table_blob +=TheTD+fit.data[total_point].toFixed(decPlaces)+"</TD>";
                    table_blob +="</TR>";
                    total_point++;
                    datapt_timestamp+=thedata.highcharts[dataset].pointInterval;
                }
            }
        }
        return(table_blob);
    }

    // add data to table body
    $( table_data('Observed', data_rd)).appendTo(rd_body);
    $( table_data('Predicted', data_rd)).appendTo(rd_body);
   
}

function get_url_parameter(name) {
    var vars = window.location.search.substring(1).split('&');
    for (var i = 0; i < vars.length; ++i) {
        var v = vars[i].split('=');
        if (v[0] == name) return unescape(v[1]);
    }
    return null;
}

function format_table_body( tbodyToModify ) {
  // set height for table (parent.div - thead.height)
  // <div
  //   <table
  //    <thead
  //    <tbody
  //   </table
  // </div

  // what the height for the div
  var theadHeight=tbodyToModify.parent().find('thead tr th:eq(0)').height();
  var divHeight=tbodyToModify.parent().parent().height();  // should be the height of the div
  var bodyHeight=divHeight-theadHeight;

  tbodyToModify.height( bodyHeight);
  tbodyToModify.css( "height", bodyHeight);
}

function date_YmdHM (edate){
  // take an epoch date and return a string for the format
  // "%Y-%m-%d %H:%M

  if (edate === undefined) {
      // return null date if date undefined.
      return( "0000-00-00 00:00");
  } else {
      var theDate = new Date(edate);

      var tmpDateThinger = theDate.getDate();
      var theDay=('0' + tmpDateThinger).slice(-2);
      var tmpDateThinger=theDate.getMonth()+1;
      var theMonth=('0' + tmpDateThinger).slice(-2);
      var theHour=('0' + theDate.getHours()).slice(-2);
      var theMinute=('0' + theDate.getMinutes()).slice(-2);

      return( theDate.getFullYear()+"-"+theMonth+"-"+theDay+" "+theHour+":"+theMinute);
  }
}

function adjust_table_hdr_cols( tableToAdjust ){ // adjust the data cells to match the headers
    var $bodyCells = tableToAdjust.find('thead tr:first').children();
    var colWidth;
    // Get the thead columns width array
    colWidth = $bodyCells.map(function() {
        return $(this).width();
    }).get();
    
    // Set the width of tbody columns
    tableToAdjust.find('tbody tr').children().each(function(i, v) {
        if( $(v).is(":last-child")) $(v).width(colWidth[i]-17);
        //else if( $(v).is(":first")) {
          // try to make sure the min width on first column is 100px
          //$(v).width((colWidth[i]<100)?100:colwidth[i]);
          // set the header to 100
          //$bodyCells.width(100);
          //console.log( (colWidth[i]<100)?100:colwidth[i]); }
        else $(v).width(colWidth[i]);
    });
};

function find_interval( theData){
  // we will figure out the interval for the data
  // would have 1 week, 2 weeks, 1 month, 3 months, 6 months, 1 year (for predicted)
  // date       2 weeks, 1 month, 2 months, 6 months, 1 year, 2 years

  var interval= theData.t_stop - theData.t_start;
    //               /60 == min /60 == hours /24 ==days)
  var days=(((interval/60)/60)/24);

  //console.log("interval days="+days );

  // based on the total number of days avail, set observed and predicted
  if( days < 20 ) return( { "observed": "", "predicted": "" });                  // error no interval that short.
  else if ( days < 41 ) return( {"observed": "<?php echo _("2 weeks"); ?>", "predicted": "<?php echo _("1 week"); ?>"});
  else if ( days < 83 ) return( {"observed": "<?php echo _("4 weeks"); ?>", "predicted": "<?php echo _("2 weeks"); ?>"});
  else if ( days < 251 ) return( {"observed": "<?php echo _("2 months"); ?>", "predicted": "<?php echo _("1 month"); ?>"});
  else if ( days < 503 ) return( {"observed": "<?php echo _("6 months"); ?>", "predicted": "<?php echo _("3 months"); ?>"});
  else if ( days < 1092 ) return( {"observed": "<?php echo _("1 year"); ?>", "predicted": "<?php echo _("6 months"); ?>"});
  else return( {"observed": "<?php echo _("2 years"); ?>", "predicted": "<?php echo _("1 year"); ?>"});
  //else return( { null, null});

}
