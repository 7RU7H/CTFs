// Service stack
var stack = {};
var throbber = '<div class="childcontentthrobber"><div class="sk-spinner sk-spinner-pulse"></div></div>';

// Set type sets up the graph view!
function setType(t) {
    type = t;
    window.location.hash = type;
    if (type == 'servicepie' || type == 'hostpie') {
        type = 'pie';
    }
    $("#visContainer" + rand).html(throbber);
    if (type == 'timeline' || type == 'stack' || type == 'multistack') {
        $('#leftNav').show();
        $('#selectService').trigger('change');

        if (type == 'multistack') {
            update_service_stack();
        } else {
            $('#filterButton').attr('disabled', false);
        }
    }
}

function fetch_bar() {
    var url = 'visApi.php?type=' + type + '&div=visContainer' + rand + '&opt=topalerts';
    //alert(url); 
    $('#hiddenUrl').val(url);
    $("#visContainer" + rand).load(url);
}

function fetch_pie(arg) {
    var url = 'visApi.php?type=' + type + '&div=visContainer' + rand + '&opt=' + arg;
    //alert(url);   
    $('#hiddenUrl').val(url);
    //alert($('#hiddenUrl').val());
    $("#visContainer" + rand).load(url);
}

// Fetch timeline

function fetch_timeline(inhost, inservice, f) {
    if (f) filtering = 'true';
    else filtering = false; //clear data type upon fresh graph loading 
    if (type == '') {
        alert('No Graph Type Specified');
        return false;
    }
    host = inhost;
    service = inservice;

    if (type == 'timeline') {
        if (filtering == false) {
            var url = 'visApi.php?type=' + type + '&host=' + host + '&service=' + service + '&div=visContainer' + rand;
        }
        else {
            var url = 'visApi.php?type=' + type + '&host=' + host + '&service=' + service + '&start=' + start + '&end=' + end + '&div=visContainer' + rand + '&filter=' + filter;
        }
    }
    if (type == 'stack') {
        if (filtering == false) {
            var url = 'visApi.php?type=' + type + '&host=' + host + '&service=' + service + '&div=visContainer' + rand + '&opt=' + opt;
        }
        else {
            var url = 'visApi.php?type=' + type + '&host=' + host + '&service=' + service + '&start=' + start + '&end=' + end + '&div=visContainer' + rand + '&opt=' + opt + '&filter=' + filter;
        }
    }

    if (!url) return false;
    //alert(url); 
    $('#hiddenUrl').val(url);
    $("#visContainer" + rand).load(url);
    return false;
}

// Fetch multistack timeline

function fetch_multistack_timeline() {
    var stack_str = '';

    // Get the line type (only for multistacked)
    var linetype = $('#linetype').val();
    //console.log(linetype);

    // Create string from stack
    var i = 0;
    $.each(stack, function (k, v) {
        host = v.host.replace(/ /g, "_");
        service = v.service.replace(/ /g, "_");
        stack_str += '&hslist[' + i + '][host]=' + host + '&hslist[' + i + '][service]=' + service + '&hslist[' + i + '][datatype]=' + v.datatype;
        i++;
    });

    // Send string to visAPI.php to create javascript
    var url = 'visApi.php?type=' + type + stack_str + '&start=' + start + '&end=' + end + '&div=visContainer' + rand + '&linetype=' + linetype;

    $('#hiddenUrl').val(url);
    $("#visContainer" + rand).load(url);
    $('#graphText').hide();
    $('#graphDisplay').show();
    return false;
}

$(document).ready(function () {

    // Load all hosts
    load_hosts();

    $('#selectService').change(function () {
        load_data_types();
    });
    $('#selectDataType').searchable({maxMultiMatch: 999});
    $('#selectService').searchable({maxMultiMatch: 999});

    $('#selectDataType').change(function () {
        if (type != 'multistack') {
            $('#filterButton').trigger('click');
        }
    });

    $('#linetype').change(function () {
        $('#filterButton').trigger('click');
    });

    // Bind on change of select hosts to grab services for that host
    $('#selectHost').change(function () {
        if ($(this).val() == "") {
            $('#selectService').html("<option value=''>Select a host...</option>");
            $('#selectService').attr('disabled', 'disabled');
            $('#addToGraph').attr('disabled', 'disabled');
        } else {
            $('#selectService').html("<option value=''>Loading...</option>");
            $('#selectService').attr('disabled', 'disabled');
            $('#addToGraph').attr('disabled', 'disabled');
            $('#selectService').load('ajax/services.php', {host: $('#selectHost').val()}, function () {
                load_data_types();
            });   
        }
    });

    // Add to the service stack
    $('#addToGraph').click(function () {
        var stack_obj = {
            service: $('#selectService').val(),
            host: $('#selectHost').val(),
            datatype: $('#selectDataType option:selected').val(),
            datatype_readable: $('#selectDataType option:selected').text()
        };
        instack = false;

        // Check if the service is already in the stack
        $.each(stack, function (k, v) {
            if (v.host == stack_obj.host && v.service == stack_obj.service && v.datatype == stack_obj.datatype) {
                instack = true;
            }
        });

        // If it's not in the stack add it and then display
        if (!instack) {
            if (stack_obj.service != "" && stack_obj.host != "") {
                var v = ge_get_new_id();
                stack[v] = stack_obj;
                update_service_stack();
            }
        }

        // Trigger the clicking of update graph when someone adds a new object to the graph
        $('#filterButton').trigger('click');
    });

    $('#reportperiodDropdown').change(function () {
        if ($(this).val() != 'custom') {
            $('#startdateBox').val('');
            $('#enddateBox').val('');
            $('#customdates').hide();
        } else {
            $('#customdates').show();
        }
    });

    $('#timeStackOpt').change(function () {
        opt = $(this).val();
    });

    ///////////// Filter Fields and Functions (Timeline) //////////////////////

    //bind filter button to events
    $('#filterButton').click(function () {
        host = $('#selectHost').val().replace(/ /g, "_");
        service = $('#selectService').val().replace(/ /g, "_");

        filtering = true;

        //retrieve values from form fields and set global vars to match
        //validate form fields      
        if ($('#reportperiodDropdown option:selected').val() != 'custom' && minus == true) {
            start = $('#reportperiodDropdown option:selected').val();
            end = '';
        }
        else {
            start = ge_get_timestamp($('#startdateBox').val());
            end = ge_get_timestamp($('#enddateBox').val());
        }

        if (type == 'multistack') {
            if (!ge_has_items_in_stack()) {
                // remove alert due to trigger on initial page load
                // alert("You must select services to stack.");
            } else {
                fetch_multistack_timeline();
            }
        } else {
            filter = $('#selectDataType option:selected').val();
            fetch_timeline(host, service, 'true');
        }
    });

    /////////////////// Dashify Graph //////////////////////////////
    $('#dashify2').click(function () {

        show_throbber();
        
        // Start loading the boards
        get_ajax_data_innerHTML("getdashboardselectmenuhtml", "", true, '#addToDashboardBoardSelect');

        var content = "<div id='popup_header'><b>" + _("Add to Dashboard") + "</b></div><div id='popup_data'><p>" + _("Add this powerful little dashlet to one of your dashboards for visual goodness.") + "</p></div>";
        content += "<label for='addToDashboardTitleBox'>" + _("Dashlet Title") + "</label><br class='nobr' />";
        content += "<input type='text' size='30' name='title' id='addtoDashboardTitleBox' value='"+_('My Graph')+"' class='form-control' />";
        content += "<br class='nobr' /><label for='addToDashboardBoardSelect'>" + _("Select a Dashboard to Add To") + "</label><br class='nobr' />";
        content += "<select class='form-control' id='addToDashboardBoardSelect'></select><br class='nobr' />";
        content += "<div id='addToDashboardFormButtons' style='margin-top:5px;'><button class='btn btn-sm btn-primary' id='AddToDashButton' onclick='ge_add_formdata()'>" + _('Add It') + "</button></div><div></div>";

        hide_throbber();
        set_child_popup_content(content);
        display_child_popup();
    });

    $('#dashify2').hover(
        function () {
            $("#graphDisplay").addClass("graphdashlethover").fadeTo(0, 0.40);
        },
        function () {
            $("#graphDisplay").removeClass("graphdashlethover").fadeTo(0, 1.00);
        }
    );
    
    $('.ui-tabs-anchor').click(function () {
        $('.ui-tabs-anchor').parent().each(function () {
            $(this).removeClass('ui-tabs-active').removeClass('ui-state-active');
        });
        $(this).parent().toggleClass('ui-tabs-active').toggleClass('ui-state-active');
    });

});

function ge_add_formdata() {
    hide_throbber();
    $('#boardName').val($('#addToDashboardBoardSelect').val()); //assign select value to hidden input
    $('#dashletName').val($('#addtoDashboardTitleBox').val()); //assign dashboard title to hidden input
    if ($('#hiddenUrl').val != '' && $('#boardName').val() != '') {
        ge_add_graph_to_dashlet();
    }
    else {
        alert(_('You must fill out the entire form.'));
    }
}

function toggle_filter(arg) {
    var leftbox = $('#leftContainer');
    var rt = $('#rightContainer');
    var dft = $('#dateFilterTimeline');
    var dfs = $('#dateFilterStack');
    var sfs = $('#serviceFilterStack');
    var dataf = $('#dataFilter');
    var go = $('#graphOptions');

    $('#graphDisplay').show();
    $('#graphText').hide();
    $('#multionly').hide();
    go.hide();

    if (arg == 'timeline') {
        rt.css('float', 'left');
        leftbox.show();
        dft.show();
        dataf.show();
        dfs.hide();
        sfs.hide();

        if ($('#reportperiodDropdown').val() == 'custom') {
            $('#customdates').show();
        } else {
            $('#customdates').hide();
        }

        $('#service_stack_div').hide();
    }
    else if (arg == 'stack') {
        rt.css('float', 'left');
        leftbox.show();
        dft.hide();
        dataf.show();
        dfs.show();
        sfs.hide();

        $('#customdates').hide();
        $('#service_stack_div').hide();
    }
    else if (arg == 'multistack') {
        rt.css('float', 'left');
        leftbox.show();
        dft.show();
        dataf.show();
        dfs.hide();
        sfs.show();
        go.show();

        // Hide left nav and filter
        $('#service_stack_div').show();

        if ($('#reportperiodDropdown').val() == 'custom') {
            $('#customdates').show();
        } else {
            $('#customdates').hide();
        }

        // Display no graph (they must select stuff on the left nav bar)
        $('#graphDisplay').hide();
        $('#graphText').show();
    }
    else {
        leftbox.hide();
        $('#service_stack_div').hide();
        rt.css('float', 'none');
    }
}

// Load a basic list of hosts into #selectHost for selecting services to "stack"
function load_hosts() {
    $('#selectHost').load('ajax/hosts.php', function () {
        $(this).trigger('change');
        $('#selectHost').searchable({maxMultiMatch: 999});
    });
    $('#addToGraph').attr('disabled', 'disabled');
}

function update_service_stack() {
    var stack_html = "";
    if (!ge_has_items_in_stack()) {
        $('#graphDisplay').hide();
        $('#graphText').show();
        $('#filterButton').attr('disabled', true);
    } else {
        $.each(stack, function (k, v) {
            stack_html += "<div data-stackid='" + k + "'><img src='images/cross.png' title='Remove from graph' />" + v.service.replace(/_/g, " ") + " (" + v.host + ") [" + v.datatype_readable + "]</div>";
        });
        $('#filterButton').attr('disabled', false);
    }

    $('#service_stack').html(stack_html);
    update_service_stack_bindings();
}

function update_service_stack_bindings() {
    $('#service_stack div img').unbind('click');
    $('#service_stack div img').click(function () {
        var div = $(this).parent();

        // Remove from stacked list
        var id = div.data('stackid');
        delete stack[id];

        // Update the stack area
        update_service_stack();

        // Trigger the clicking of update graph when someone removes an object from the stack
        if (ge_has_items_in_stack()) {
            $('#filterButton').trigger('click');
        }
    });
}

function load_data_types() {
    $('#selectDataType').empty();

    var all = 0;
    if (type == 'timeline') {
        all = 1;
    }

    $('#selectDataType').load('ajax/datatypes.php', {
        host: $('#selectHost').val(),
        service: $('#selectService').val(),
        all: all
    }, function () {
        $('#selectService').removeAttr('disabled');
        $('#addToGraph').removeAttr('disabled');

        if (type != 'multistack') {
            $('#filterButton').trigger('click');
        } else {

            // Lets grab the first two and create a graph...
            if ($('#selectDataType option').size() >= 2 && $.isEmptyObject(stack)) {
                $('#addToGraph').trigger('click');
                $('#selectDataType option:selected').next().attr("selected", true);
                $('#addToGraph').trigger('click');
            } else {
                $('#filterButton').trigger('click');
            }

        }
    });
}

// Function that breaks apart a date and creates a (normal) timestamp
function ge_get_timestamp(date) {
    var d = date.match(/\d+/g);
    if (d.length == 3) {
        var timestamp = new Date(d[0], d[1] - 1, d[2]);
    } else if (d.length == 5) {
        var timestamp = new Date(d[0], d[1] - 1, d[2], d[3], d[4]);
    } else {
        var timestamp = new Date(d[0], d[1] - 1, d[2], d[3], d[4], d[5]);
    }
    return timestamp / 1000;
}

// Get a new id
function ge_get_new_id() {
    // Unix Epoch Time for randomness
    var newid = new Date().getTime();

    return newid;
}

// Check if there are items in the stack
function ge_has_items_in_stack() {
    var len = $.map(stack, function (n, i) {
        return i;
    }).length;
    if (len == 0) {
        return false;
    } else {
        return true;
    }
}