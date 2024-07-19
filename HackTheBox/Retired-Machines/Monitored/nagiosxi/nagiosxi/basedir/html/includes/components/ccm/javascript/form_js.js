//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: form_js.js
//  Desc: Standard set of functions for the "Form" which is the actual object that creates and
//        displays the actual object's tabbed view used to edit the object.
//

// List filter function
// Credit: Andrew Berridge
// http://www.lessanvaezi.com/filter-select-list-options/#comment-796
jQuery.fn.filterByText = function(textbox, selectSingleMatch, minimumTextValue) {
    return this.each(function() {
        var select = $(this);
        var optionsAndOptGroups = [];
        if (typeof selectSingleMatch === "undefined") { selectSingleMatch = false; }
        if (typeof minimumTextValue === "undefined") { minimumTextValue = 0; }

        //Find all options and option groups
        select.children('option, optgroup').each(function(){
            optionsAndOptGroups.push(jQuery(this));
        });

        select.data('optionsAndOptGroups', optionsAndOptGroups);

        jQuery(textbox).bind('keyup', function(e) {
            if (textbox.val().length > minimumTextValue || e.which === 46 || e.which === 8) {

                var optionsAndOptGroups = select.empty().data('optionsAndOptGroups');
                var search = jQuery.trim(textbox.val());
                var regex = new RegExp(search, 'gi');

                jQuery.each(optionsAndOptGroups, function(k, v) {
                    if(jQuery(v).is('option')){
                        if(jQuery(v).text().match(regex) != null){
                            if (typeof select[0].append == 'function') {
                                select[0].append(v[0]);
                            } else {
                                select.append(v);
                            }
                        }
                    }else {
                        var optionGroupClone = v.clone();
                        jQuery.each(optionGroupClone.children('option'), function(){
                            if(v.text().match(regex) === null){
                                v.remove();
                            }
                        });
                        if(optionGroupClone.children().length) { jQuery(select).append(optionGroupClone); }
                    }
                });
            }
            if (jQuery(this).val().length > 0) {
                jQuery(this).parent().find('.clear-filter').show();
            } else {
                jQuery(this).parent().find('.clear-filter').hide();
            }
        });
        if (selectSingleMatch === true && select.children().length === 1) {
            select.children().get(0).selected = true;
        }
    });
};

// Loading pre-selected items
$(document).ready(function() {

    $('#tabs').tabs().show();
    $('.req').tooltip();

    // Select any preloaded items and clear cached selections and restore to DB entries 
    //document.forms.reset();

    // Clear filter
    $('.clear-filter').click(function() {
        var p = $.Event('keyup');
        p.ctrlKey = false;
        p.which = 46;
        $(this).parent().find('input').val('').trigger(p);
    });

    // Hosts
    transferMembers('selHosts', 'tblHosts', 'hosts');

    // Parents
    transferMembers('selParents', 'tblParents', 'parents');

    // Hostgroups
    transferMembers('selHostgroups', 'tblHostgroups', 'hostgroups');

    // Servicegroups
    transferMembers('selServicegroups', 'tblServicegroups', 'servicegroups');

    // Contacts
    transferMembers('selContacts', 'tblContacts', 'contacts');

    // Services
    transferMembers('selHostservices', 'tblHostservices', 'hostservices');

    // Timeperiods
    transferMembers('selExcludes', 'tblExcludes', 'excludes');

    // Contactgroups
    transferMembers('selContactgroups', 'tblContactgroups', 'contactgroups');

    // Notification commands
    transferMembers('selHostcommands', 'tblHostcommands', 'hostcommands');
    transferMembers('selServicecommands', 'tblServicecommands', 'servicecommands');

    // Services (service escalations and dependency page)
    transferMembers('selServices', 'tblServices', 'services');

    // Dependencies
    transferMembers('selHostdependencys', 'tblHostdependencys', 'hostdependencys');
    transferMembers('selHostgroupdependencys', 'tblHostgroupdependencys', 'hostgroupdependencys');
    transferMembers('selServicedependencys', 'tblServicedependencys', 'servicedependencys');
    transferMembers('selServicegroupdependencys', 'tblServicegroupdependencys', 'servicegroupdependencys');

    // Templates
    transferMembers('selTemplates', 'tblTemplates', 'templates');

    // Contact Templates
    transferMembers('selContacttemplates', 'tblContacttemplates', 'contacttemplates');

    // ---------------------------------
    // Validate form submission
    // ---------------------------------
    $('#subForm1').click(function() {
        var objtype = $('#type').val();

        var valid = true;
        var match;
        $('input.required').each(function() {
            if ($(this).val() == '') {
                valid = false;
            }
        });

        if (valid == false) {
            alert('Missing required fields');
            return;
        }

        // Make sure passwords are the same
        if ($('#type').val() == 'user') {
            if ($('#password').val() != $('#confirm').val()) {
                alert('Passwords do not match!');
                return;
            }

            // Send directly to the admin section
            $('#cmd').val('admin');
        }

        // Object name character checks
        if (objtype == 'service') {
            search = $('#tfServiceDescription').val();
            match = search.match(/[{}`~!$%^&*|'"<>?,()=]/);

            if (match) {
                alert('Illegal characters ('+match[0]+') in service description!');
                return false;
            }

            // Last character cannot be backslash
            if (search[search.length-1] === '\\') {
                alert('Cannot have \\ character as the last character in service description!');
                return false;
            }
        }

        // Object names
        if (typeof $('#tfName').val() !== 'undefined') {
            search = $('#tfName').val();
            match  = search.match(/[{}`~!$%^&*|'"<>?,()=]/);

            if (match) {
                alert('Illegal characters ('+match[0]+') in object name!');
                return false;
            }

            // Last character cannot be backslash
            if (search[search.length-1] === '\\') {
                alert('Cannot have \\ character as the last character in object name!');
                return false;
            }
        }

        // Verify host/service escalation items have been filled out
        if (objtype == "hostescalation") {
            var hosts_size = $('input[name="hosts[]"]').size();
            var hostgroups_size = $('input[name="hostgroups[]"]').size();
            if (hosts_size == 0 && hostgroups_size == 0) {
                alert("Must define at least 1 host or hostgroup for a host escalation definition.");
                return false;
            }
        } else if (objtype == "serviceescalation") {
            var hosts_size = $('input[name="hosts[]"]').size();
            var hostgroups_size = $('input[name="hostgroups[]"]').size();
            var services_size = $('input[name="services[]"]').size();
            var servicegroups_size = $('input[name="servicegroups[]"]').size();
            if (hosts_size == 0 && hostgroups_size == 0 && servicegroups_size == 0) {
                alert("Must define at least 1 host, hostgroup or servicegroup for a valid service escalation definition.");
                return false;
            } else if (services_size == 0 && servicegroups_size == 0) {
                alert("Must define at least 1 service or servicegroup for a valid service escalation definition.");
                return false;
            }
        }

        // Verify host/service dependancies... Hosts are currently not required to be checked
        // since even an empty host dependency will pass Core verification
        if (objtype == "servicedependency") {
            var hosts_size = $('input[name="hosts[]"]').size();
            var hostgroups_size = $('input[name="hostgroups[]"]').size();
            var servicegroup_size = $('input[name="servicegroups[]"]').size();
            var services_size = $('input[name="services[]"]').size();
            var dep_hosts_size = $('input[name="hostdependencys[]"]').size();
            var dep_hostgroups_size = $('input[name="hostgroupdependencys[]"]').size();
            var dep_services_size = $('input[name="servicedependencys[]"]').size();

            if (hosts_size == 0 && hostgroups_size == 0 && servicegroup_size == 0) {
                alert("Must define at least 1 host, hostgroup, or servicegroup for a valid service dependency definition.");
                return false;
            } else if (services_size == 0 && servicegroup_size == 0) {
                alert("Must define at least 1 service for a valid service dependency definition.");
                return false;
            } else if (dep_services_size == 0) {
                alert("Must define at least 1 service dependency for a valid service dependency definition.");
                return false;
            }
        }

        // check for ! in arguments
        $('.arg').each(function(k, v) {
            var cmd_arg = $(v).val();
            if (cmd_arg.indexOf('!') != -1) {

                // replace a ! with \!
                cmd_arg = cmd_arg.replace(/!/g, '\\!');

                // replace \\! with \!
                cmd_arg = cmd_arg.replace(/\\\\!/g, '\\!');
                $(v).val(cmd_arg);
            }
        });

        // Sanity checks passed 
        $('#mainCcmForm').submit();
    });

    // Show the command test button (?)
    toggle_command_test();

    ///////////////////command test/////////////////    
    $('#command_test').click(function() {

        // Bail without a check command id 
        var thecid = $('#selHostCommand').val();
        if (thecid == 'null' || thecid == '0' || thecid == undefined) {
            alert('You must select a check command to test');  
            return false;
        }

        // Check if we need a hostaddress for this command
        var thecommand = $('#fullcommand').html();
        var hostmacro = new RegExp(/HOSTADDRESS/);
        var bool = hostmacro.test(thecommand);

        if (bool === false) {
            $('#commandOutputBox #command_input').hide();
        } else {
            $('#commandOutputBox #command_input').show();
        }

        // reset the command output css
        $('#command_output')
            .css('display', 'none')
            .css('text-align', 'center')
            .css('overflow', 'hidden');
           
        // Dump output to overlay div
        $('#commandOutputBox #command_output').html('');
        overlay('commandOutputBox');

        if (bool === true) {

            // readjust the size and position here
            $('#commandOutputBox').height($('#commandOutputBox').height() / 2).center();

            // set the place holder of the check_address textbox of this host address
            $('#commandOutputBox #check_address').attr('placeholder', $('#hostAddress').val());
        }
    });

    $('#run_command').click(function() {
        // set the value of check_address to the placeholder value if none entered
        if ($('#commandOutputBox #check_address').val() === '') {
            $('#commandOutputBox #check_address').val($('#commandOutputBox #check_address').attr('placeholder'));
        }
        var address = encodeURIComponent($('#commandOutputBox #check_address').val());

        //var address = $('#tfAddress').val();
        var cid = $('#selHostCommand').val();
        var arg1 = encodeURIComponent($('#tfArg1').val());
        var arg2 = encodeURIComponent($('#tfArg2').val());
        var arg3 = encodeURIComponent($('#tfArg3').val());
        var arg4 = encodeURIComponent($('#tfArg4').val());
        var arg5 = encodeURIComponent($('#tfArg5').val());
        var arg6 = encodeURIComponent($('#tfArg6').val());
        var arg7 = encodeURIComponent($('#tfArg7').val());
        var arg8 = encodeURIComponent($('#tfArg8').val());
        var arg9 = encodeURIComponent($('#tfArg9').val());
        var arg10 = encodeURIComponent($('#tfArg10').val());
        var arg11 = encodeURIComponent($('#tfArg11').val());
        var arg12 = encodeURIComponent($('#tfArg12').val());
        var arg13 = encodeURIComponent($('#tfArg13').val());
        var arg14 = encodeURIComponent($('#tfArg14').val());
        var arg15 = encodeURIComponent($('#tfArg15').val());
        var arg16 = encodeURIComponent($('#tfArg16').val());
        var arg17 = encodeURIComponent($('#tfArg17').val());
        var arg18 = encodeURIComponent($('#tfArg18').val());
        var arg19 = encodeURIComponent($('#tfArg19').val());
        var arg20 = encodeURIComponent($('#tfArg20').val());
        var arg21 = encodeURIComponent($('#tfArg21').val());
        var arg22 = encodeURIComponent($('#tfArg22').val());
        var arg23 = encodeURIComponent($('#tfArg23').val());
        var arg24 = encodeURIComponent($('#tfArg24').val());
        var arg25 = encodeURIComponent($('#tfArg25').val());
        var arg26 = encodeURIComponent($('#tfArg26').val());
        var arg27 = encodeURIComponent($('#tfArg27').val());
        var arg28 = encodeURIComponent($('#tfArg28').val());
        var arg29 = encodeURIComponent($('#tfArg29').val());
        var arg30 = encodeURIComponent($('#tfArg30').val());
        var arg31 = encodeURIComponent($('#tfArg31').val());
        var arg32 = encodeURIComponent($('#tfArg32').val());

        var fullcommand = $('#fullcommand').html();
        var token = $('#token').val();

        var url = "command_test.php?cmd=test&token="+token+"&mode=test&address="+address+"&cid="+cid+"&arg1="+arg1+"&arg2="+arg2+"&arg3="+arg3;
        url += "&arg4="+arg4+"&arg5="+arg5+"&arg6="+arg6+"&arg7="+arg7+"&arg8="+arg8+"&arg9="+arg9+"&arg10="+arg10+"&arg11="+arg11+"&arg12="+arg12+"&arg13="+arg13+"&arg14="+arg14+"&arg15="+arg15+"&arg16="+arg16+"&arg17="+arg17+"&arg18="+arg18+"&arg19="+arg19+"&arg20="+arg20+"&arg21="+arg21+"&arg22="+arg22+"&arg23="+arg23+"&arg24="+arg24+"&arg25="+arg25+"&arg26="+arg26+"&arg27="+arg27+"&arg28="+arg28+"&arg29="+arg29+"&arg30="+arg30+"&arg31="+arg31+"&arg32="+arg32+"&nsp="+nsp_str;

        $('#command_output').show().html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>');

        $.get(url, { }, function(data) {
            $('#command_output').html(data);

            // Set max height of command output box
            var maxheight = $('#commandOutputBox').height() - $('#command_input').height() - $('#commandOutputBox .overlay-title').height() - 90;
            if (maxheight < 100) { maxheight = 100; }
            $('#command_output').css('max-height', maxheight+'px');

            $('#command_output').css('text-align', 'left').css('overflow', 'auto');
        }, 'text');
    });

    // Activate list filter text input
    $('#selHosts').filterByText($('#filterHosts'), true);
    $('#selHostgroups').filterByText($('#filterHostgroups'), true);
    $('#selServices').filterByText($('#filterServices'), true);
    $('#selServicegroups').filterByText($('#filterServicegroups'), true);
    $('#selContacts').filterByText($('#filterContacts'), true);
    $('#selContactgroups').filterByText($('#filterContactgroups'), true);
    $('#selParents').filterByText($('#filterParents'), true);
    $('#selTemplates').filterByText($('#filterTemplates'), true);
    $('#selHosttemplates').filterByText($('#filterHosttemplates'), true);
    $('#selServicetemplates').filterByText($('#filterServicetemplates'), true);
    $('#selContacttemplates').filterByText($('#filterContacttemplates'), true);
    $('#selHostcommands').filterByText($('#filterHostcommands'), true);
    $('#selServicecommands').filterByText($('#filterServicecommands'), true);
    $('#selHostdependencys').filterByText($('#filterHostdependencys'), true);
    $('#selHostgroupdependencys').filterByText($('#filterHostgroupdependencys'), true);
    $('#selServicedependencys').filterByText($('#filterServicedependencys'), true);
    $('#selServicegroupdependencys').filterByText($('#filterServicegroupdependencys'), true);
    $('#selHostservices').filterByText($('#filterHostservices'), true);
});

// Hide command test if no check command selected
function toggle_command_test()
{
    var cid = $('#selHostCommand').val();
    if (cid == 'null' || cid == '0' || cid == undefined) {
        $('#command_test_box').hide();
    } else {
        $('#command_test_box').show();
    }
}

function encode(arg)
{
    if (arg == '' || arg == undefined || arg == 'undefined') {
        return '';
    }
    return encodeURI(arg);
}

// Tabular display for CCM forms 
function showHideTab(id)
{
    var inpID = "#tab"+id;
    for (i = 1; i < 5; i++) {
        if (i != id) {
            var tab = "#tab"+i;
            $(tab).hide()
        }
    }
    $(inpID).show();
}

$(document).ready(function() {
    // Handle Service Escalation User Configurations
    var objtype = $('#type').val();
    if (objtype == "serviceescalation") {
        configureSelections();
        $('#manage_services_count, #manage_servicegroups_count, #manage_hosts_count, #manage_hostgroups_count').change(function(){
            configureSelections();
        });

        // Calculates the available options based on the currently selected to maintain a valid configuration
        function configureSelections(){

            // Resets all options before recalculating what the new available options will return.
            $('#manage_services_button, #manage_servicegroups_button, #manage_hosts_button, #manage_hostgroups_button').removeAttr('disabled');

            // Checks if any services have been selected and disables/removes incompatible options
            if($('#manage_services_count').html() > 0) {
                $('#manage_servicegroups_button').attr('disabled', true);

                removeAll('tblServicegroups');

                $('#manage_servicegroups_count').html('0');
            }

            // Checks if any servicegroups have been selected and disables/removes incompatible options
            if($('#manage_servicegroups_count').html() > 0) {
                $('#manage_hosts_button').attr('disabled', true);
                $('#manage_hostgroups_button').attr('disabled', true);
                $('#manage_services_button').attr('disabled', true);

                removeAll('tblHosts');
                removeAll('tblHostgroups');
                removeAll('tblServices');

                $('#manage_hosts_count').html('0');
                $('#manage_hostgroups_count').html('0');
                $('#manage_services_count').html('0');
            }

            // Checks if any hosts have been selected and disables/removes incompatible options
            if($('#manage_hosts_count').html() > 0) {
                $('#manage_servicegroups_button').attr('disabled', true);

                removeAll('tblServicegroups');

                $('#manage_servicegroups_count').html('0');
            }

            // Checks if any hostgroups have been selected and disables/removes incompatible options
            if($('#manage_hostgroups_count').html() > 0) {
                $('#manage_servicegroups_button').attr('disabled', true);

                removeAll('tblServicegroups');

                $('#manage_servicegroups_count').html('0');
            }
        }
    }


    $('#commonSettings').toggleClass('selectedTab');

    $('.navLink').click(function() {
        $('.navLink').parent().each(function() {
            $(this).removeClass('selectedTab');
        }); 
        $(this).parent().toggleClass('selectedTab');
    });

    // On window resize, re-draw any visible overlays...
    $(window).resize(function() {
        $('.overlay:visible').each(function(k, v) {
            if ($(v).attr('id') == "commandOutputBox") {
                var maxheight = $('#commandOutputBox').height() - $('#commandOutputBox .overlay-title').height() - 90;
                if (maxheight < 100) { maxheight = 100; }
                $('#command_output').css('max-height', maxheight+'px');
            }
            overlay($(v).attr('id'));
        });
    });

});

function abort(type, redirect)
{
    if (type == 'user') {
        window.location = 'index.php?cmd=admin&type='+type;
    } else {
        window.location = redirect;
    }
}

function removeAll(tbl)
{
    $('#'+tbl+' tr.trOption').each(function() {
        var oldid = $(this).attr('oldid');
        $('#'+oldid).prop('disabled', false);
        $('#'+oldid).prop('selected', 'selected');
        $('#'+oldid).show();
        $(this).remove();
    });
}

// Load and split command args into fields upon page load. DO NOT CHANGE when select list changes 

function reveal_command(id)
{
    $('#fullcommand').empty();
    if (id == 0) {
        $('#fullcommand').append("No command selected");
    } else {
        $('#fullcommand').append(command_list[id]);
    }
    toggle_command_test();
}   

function get_plugin_help(token)
{
    var input_plugin = $('#selPlugins').val();
    if (input_plugin == 'null') {
        $('#pluginhelp').html("<pre>No plugin selected.</pre>");
    } else {
        $('#pluginhelp').load('command_test.php?&cmd=help&mode=help&plugin='+input_plugin+'&token='+token);
    }
}

/* ==================================
        OVERLAY FUNCTIONALITY
===================================== */

function overlay(div)
{
    var ID = '#'+div;

    // Check if overlay actually exists 
    if ($(ID).html() == null) {
        alert('Undefined overlay');
        return;
    }

    // Cover the page with a opaque hidden div...
    var height = $(window).height() + 20;
    var width = $(window).width() + 20;

    whiteout();

    // Make the overlay width much bigger if it's a small screen
    if ($(window).width() < 1024) {
        var overlay_width = width-(width*0.10);
    } else {
        var overlay_width = width-(width*0.16);
    }
    var overlay_height = height-(height*0.22);

    // Set defaults (min and max)
    if (overlay_width < 400) { overlay_width = 400; }

    // Display the selection overlay
    $(ID).css('width', overlay_width)
         .css('position', 'absolute')
         .css('height', overlay_height)
         .center().show();

    $(ID).find('select').val([]);

    var filter_height = $(ID).find('.listDiv .filter').outerHeight();
    var title_height = $(ID).find('.overlay-title').outerHeight();
    var padding_height = 45;
    var bottom_height = $(ID).find('.overlay-left-bottom').outerHeight();
    var close_height = $(ID).find('.closeOverlay').outerHeight();

    // Resize selection box to be most of the height (use as much real estate as possible)
    var select_height = overlay_height - filter_height - title_height - padding_height - bottom_height - close_height;
    $(ID).find('.lists').css("height", Math.round(select_height));

    var assigned_height = overlay_height - title_height - padding_height - 28;

    $(ID).find('.assigned-container').css('max-height', Math.round(assigned_height));

    $(document).keyup(function(e) {
        if (e.keyCode == 27) {
            killOverlay(div);
        }
    });
}

function killOverlay(id)
{
    // Remove the overlay
    clear_whiteout();
    ID = '#'+id;

    // Grab amount of items assigned
    var assigned = $(ID).find('.table-assigned tbody tr').length;
    $('.btn-'+id).find('span').html(assigned);
    $('.btn-'+id).find('span').change();

    $(ID).hide();
}

/* **************Transfer Memberships and form auto population **** */

// This function toggles the grids and configuration tables
function showHide(id, td_id)
{
    // Change background color of 'this' td
    var tdID = "#"+td_id;
    $(tdID).toggleClass('groupexpand');
    var divID = "#"+id;
    $(divID).slideToggle("fast");
}

// This function hides all lists that can be toggled
function hide()
{
    $(".hidden").hide();
}

// Unique identifier for added <tr> rows
var unique = 0;

// Control array for transferMembers()
var uniqueIDs = []; 

///////////////////// transferMembers()  ///////////////////////////////////////// 
//used to select parents, hostgroups, and other items that may have multiple values
//inputDiv - the ID of the select list to pull selected items from 
//outputDiv - the table that the selected items are being added to ->(hidden inputs will be added for the values)
//postArray - the php $_POST array value that these items will be added to 
function transferMembers(inputDiv, outputDiv, postArray, afterLoad)
{
    var objtype = $('#type').val();

    var titles = []; //display titles for select options
    var values = []; //option values 
    var orderids = []; // order ids
    var ids   = []; //array of id's 
    
    var input = '#' + inputDiv;
    $(input+' :selected').each(function(i, selected) { 
      titles[i] = $(selected).text(); 
      values[i] = $(selected).val();
      orderids[i]   = [i,$(selected).attr('orderid')]; //capture order item id  
      ids[i]    = $(selected).attr('id'); //capture item id  
    });
    
    function cmp(a, b) {
        return a[1].localeCompare(b[1]);
    }

    if (typeof orderids[0] !== 'undefined' && typeof orderids[0][1] !== 'undefined')
        orderids.sort(cmp);

    for (i=0;i < ids.length; i++)
    {
        //create data id that ties to option
        unique++; 
        if (orderids.length > 0)
            index_id = orderids[i][0];
        else
            index_id = ids[i]
        var thisID = ids[index_id]; 

        // Input string for group or service selections 
        var string = '<tr class="trOption" oldid="'+ids[index_id]+'" id="tr'+unique+'"><td><div class="outputTableData"></div></td>';
        string += "<input class='hiddenList' type='hidden' name='"+postArray+"[]' value='"+values[index_id]+"'>";
        string += '<td class="actions">';

        // For template ordering
        if (postArray == 'templates' || postArray == 'contacttemplates') {
            string += '<div class="order-buttons"></div>';
        }

         if (objtype == 'service' || objtype == 'serviceescalation' || objtype == 'hostescalation' || objtype == 'servicetemplate' || objtype == 'hostgroup' || objtype == 'hosttemplate') {
            if (values[index_id] != '*') {
                if (postArray == 'hosts' || postArray == 'hostgroups' && (objtype == 'service' || objtype == 'hostescalation' || objtype == 'hosttemplate' || objtype == 'serviceescalation') || (postArray == 'services' && objtype == 'serviceescalation')) {
                    var checked = '';
                    if ($('#'+thisID).data('exclude') == 1) { checked = ' checked="checked"'; }
                    string += '<label style="margin-right: 8px; cursor: pointer;" class="'+i+'-tt-bind" title="Exclude"><i class="fa fa-exclamation fa-14"></i> <input type="checkbox" style="margin: 0; vertical-align: text-top; cursor: pointer;" name="'+postArray+'_exc[]" value="'+values[index_id]+'"'+checked+'></label>';
                }
            }
        }

        string += '<a class="xBox '+i+'-tt-bind" href="javascript:void(0)" title="Remove" onclick="remove_row(\'tr'+unique+'\', \''+postArray+'\',\''+values[index_id]+'\');"></a></td></tr>';
        
        // Write output to new table/form    
        var output = '#'+outputDiv;
        
        // Prevent duplicate entries 
        var itemID = '#'+thisID;
        $(itemID).hide(); 
        $(itemID).prop('disabled', 'disabled');
        $(itemID).prop('selected', false); 
        
        // Display the template
        $(output).append(string);
        $(output).find('#tr'+unique+' .outputTableData').text(titles[index_id]);

        if (postArray == 'hosts' || postArray == 'hostgroups') {
            $('.'+i+'-tt-bind').tooltip();
        }
    }

    // For template ordering
    if (postArray == 'contacttemplates' || postArray == 'templates') {
        var upName = postArray.charAt(0).toUpperCase() + postArray.slice(1);
        if ($('#sel' + upName).has('option').length) {
            var tblName = "tbl" + upName;
            reorder_templates(tblName);
        }
    }
}

function insertDefinition(varName, varDef)
{
    // Grab text fields if nothing has been passed
    if (varName == false && varDef == false) {
        varName = $('#txtVariablename').val();
        varDef = $('#txtVariablevalue').val();
    }

    varName = varName.toLowerCase();

    if (varName =='' || varDef=='') {
        alert('Invalid entry, please enter both a variable name and a variable value.');
        $('#txtVariablename')
        return;
    }

    if (varName.match(/[{}`~!$%^&*|'"<>?,()=\\]/)) {
        alert('Illegal characters in variable name.');
        return;
    }
    
    if (varDef.match(/[{}]/)) {
        alert('Illegal characters in variable definition.');
        return;
    }
    if (varDef.slice(-1) == '\\') {
        alert('Can not have backslash as last character.');
        return;
    }

    varDef = htmlEntities(varDef);

    // Create data id that ties to option
    unique++;

    // Input string for group or service selections 
    var string = '<tr class="trOption" id="tr'+unique+'"><td><span>'+varName+'</span>';
    string += "<input class='hiddenList form-control' type='hidden' name='variables[]' value='"+varName+"' />";
    string += '</td><td><span>'+varDef+'</span>';
    string += "<input class='hiddenList form-control' type='hidden' name='variabledefs[]' value='"+varDef+"' />";
    string += '</td><td style="text-align: center;"><a class="sBox" style="display: none;" href="javascript:void(0)" title="Save" onclick="save_row(\'tr'+unique+'\')"></a>';
    string += '<a class="eBox" href="javascript:void(0)" title="Edit" onclick="edit_row(\'tr'+unique+'\')"></a> &nbsp; ';
    string += '<a class="xBox" href="javascript:void(0)" title="Remove" onclick="remove_row(\'tr'+unique+'\', \'\',\'\')"></a></td></tr>';

    // Write output to new table/form
    $('#tblVariables').append(string);
    $('#txtVariablename').val("");
    $('#txtVariablevalue').val("");
}

function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g,'&apos;');
}

function insertTimeperiod(varName, varDef)
{
    // Grab text fields if nothing has been passed
    if (varName == false && varDef == false) {
        varName = $('#txtTimedefinition').val();
        varDef = $('#txtTimerange').val();
    }

    if (varName == '' || varDef == '') {
        alert('Invalid entry, please enter both a time definition and a time range');
        return;
    }

    // Create data id that ties to option
    unique++;

    // Input string for group or service selections 
    var string = '<tr class="trOption" id="tr'+unique+'"><td>'+varName+'</td><td>'+varDef+'</td>';
    string += "<input class='hiddenList' type='hidden' name='timedefinitions[]' value='"+varName+"' />";
    string += "<input class='hiddenList' type='hidden' name='timeranges[]' value='"+varDef+"' />";
    string += '<td style="text-align: right;"><div class="xBox tt-'+unique+'" style="cursor: pointer;" data-placement="left" title="Remove" onclick="remove_row(\'tr'+unique+'\', \'\',\'\')"></div></td></tr>';

    // Write output to new table/form
    $('#tblTimeperiods').append(string);

    $('.tt-'+unique).tooltip();

    // Clear fields 
    $('#txtTimedefinition').val('');
    $('#txtTimerange').val('');
}

// Removes item from output table and arrays of selected items
// id - removes the <tr> by unique id
// arrayType - tells which array to remove the selected item from
// value - tells what value to erase from the selected array 
function remove_row(id, arrayType, value)
{
    var ID = '#'+id;
    var oldID = '#'+$(ID).attr('oldid'); 

    $(ID).remove();

    uniqueIDs[id] = null;
    delete uniqueIDs[id];

    $(oldID).show();
    $(oldID).prop('disabled', false);
    $(oldID).prop('selected', 'selected');   
}

// Edit a row (mostly for free variables)
function edit_row(id)
{
    var ID = '#'+id;

    // Show editing the row
    $(ID).find('input.hiddenList').attr('type', 'text').css('width', '100%');
    $(ID).find('span').hide();
    $(ID).find('.eBox').hide();
    $(ID).find('.xBox').hide();
    $(ID).find('.sBox').show();
}

// Saves a row (mostly for free variables)
function save_row(id)
{
    var ID = '#'+id;

    $(ID).find('input.hiddenList').attr('type', 'hidden').css('width', 'auto');
    $(ID).find('input.hiddenList').each(function(i, v) {
        $(v).parents('td').find('span').text($(v).val());
    });
    $(ID).find('span').show();
    $(ID).find('.eBox').show();
    $(ID).find('.xBox').show();
    $(ID).find('.sBox').hide();
}

// Reorder the templates
function reorder_templates(inputDiv)
{
    var stack = Array();
    stack = genereate_reordered_stack(inputDiv);

    display_reordered_templates(stack, inputDiv);
    bind_reorder_buttons(stack);
}

// Update the HTML for the reordered templates
function display_reordered_templates(stack, inputDiv)
{
    // Replace the stack in the HTML
    $.each(stack, function(k, v) {
        $('#' + inputDiv + ' tbody').append(v);
    });
}

function bind_reorder_buttons(stack)
{
    // Rebind the buttons
    $('.move').unbind('click');
    $('.move').click(function()
    {
        var inputDiv = $(this).parents('table.table-assigned').attr('id');
        var id = $(this).parent().attr('stackid');

        if ($(this).hasClass('up')) {
            // Move the item up
            id = parseInt(id);
            up = id-1;
            var tmp = stack[id];
            var tmp2 = stack[up];
            stack[id] = tmp2;
            stack[up] = tmp;
        } else if ($(this).hasClass('down')) {
            // Move the item down
            id = parseInt(id);
            down = id+1;
            var tmp = stack[id];
            var tmp2 = stack[down];
            stack[id] = tmp2;
            stack[down] = tmp;
        }

        display_reordered_templates(stack, inputDiv);
        reorder_templates(inputDiv);
    });
}

function genereate_reordered_stack(inputDiv)
{
    var stack = [];

    // Pull the stack out of the HTML and add on buttons to change order
    var size = $("#" + inputDiv + " tr.trOption").size() - 1;
    $("#" + inputDiv + " tr.trOption").each(function(i)
    {
        // Remove all the elements
        $(this).find('.order-buttons').html('');
        $(this).find('.order-buttons').attr("stackid", i);

        // Append the buttons
        if (i == 0) {
            $(this).find(".order-buttons").append('<div class="move down"></div>');
        } else if (i < size) {
            $(this).find(".order-buttons").append('<div class="move up"></div><div class="move down"></div>');
        } else {
            $(this).find(".order-buttons").append('<div class="move up"></div>');
        }

        // Add to the new stack
        stack[i] = $(this).detach();
    });

    return stack;
}

function getHelpOverlay(type)
{
    var opt = $('#helpList').val();
    var token = $('#token').val();
    if (opt == '') {
        return; 
    }
    var url='ajax.php?cmd=getinfo&type='+type+'&opt='+opt+'&token='+token; 
    $('#documentation').load(url,function() {
        overlay('documentation');
    });
}