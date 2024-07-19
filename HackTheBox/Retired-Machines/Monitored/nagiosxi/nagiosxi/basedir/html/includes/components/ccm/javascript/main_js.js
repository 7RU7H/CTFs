//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: main_js.js
//  Desc: Defines all common Javascript functions for the CCM. Included in every page.
//

$(document).ready(function() {

    //bind Go button 
    $('#goButton').click(function() {
    
        selVal = $('#selModify').val();
        type = $('#type').val();
        
        //confirmation for multiple deletion
        if(selVal =='delete_multi') {
            var conf = confirm("Do you really want to delete all selected objects? This action cannot be undone.");
            if(!conf)
                return;
        }
        
        if(selVal != 'none') {
            whiteout();
            show_throbber();
            $('#cmd').val(selVal);
            $('#frmDatalist').submit();
        }
    }); 
    
    $('#closeReturnLink').click(function() {
        $('#returnContent').hide();
        $('#applyConfigOutput').hide();
    }); 
    
    $('#clear').click(function() {
            $('#search').val('');
            $('#frmDatalist').submit();
    }); 
    
    // Run throbber functions
    // Form submit
    $('#frmDatalist').submit(function() {
        whiteout();
        show_throbber();
    });

    // Pagelimit
    $('#pagelimit').change(function() {
        whiteout();
        show_throbber();
    });

    /////////////////////////////////////////////////////////
    //ccm table header sort binds
    // object name 
    $('.sortname').click(function() {
        $('#orderby').val($('#typeName').val());
        if($('#sort').val()=='ASC')
            $('#sort').val('DESC');
        else
            $('#sort').val('ASC');
        $('#sortlist').val('true');
        $('#cmd').val('view');
        $('#frmDatalist').submit();
    }); 
    //alias or description
    $('.sortdesc').click(function() {
        $('#orderby').val($('#typeDesc').val());
        if($('#sort').val()=='ASC')
            $('#sort').val('DESC');
        else
            $('#sort').val('ASC');
        $('#sortlist').val('true');
        $('#cmd').val('view');
        $('#frmDatalist').submit();
    });

    //sort by active 
    $('.sortactive').click(function() {
        $('#orderby').val('active');
        if($('#sort').val()=='ASC')
            $('#sort').val('DESC');
        else
            $('#sort').val('ASC');
        $('#sortlist').val('true');
        $('#cmd').val('view');
        $('#frmDatalist').submit();
    });

    //sort by last modified (sync status) //doesn't work right
    $('.sortsync').click(function() {
        $('#orderby').val('last_modified');
        if($('#sort').val()=='ASC')
            $('#sort').val('DESC');
        else
            $('#sort').val('ASC');
        $('#sortlist').val('true');
        $('#cmd').val('view');
        $('#frmDatalist').submit();
    });

    //sort by DB id
    $('.sortid').click(function() {
        $('#orderby').val('id');
        if($('#sort').val()=='ASC')
            $('#sort').val('DESC');
        else
            $('#sort').val('ASC');
        $('#sortlist').val('true');
        $('#cmd').val('view');
        $('#frmDatalist').submit();
    });
    
    // Adding the functionality to "jump" to a new page -JO
    $('#jumpToPageBox').change(function() {
        whiteout();
        show_throbber();
        var url = $(this).val();
        window.location.href = url;
    });

    $('#rel-popup').on('click', '.close', function() {
        $(this).parents('#rel-popup').hide();
        clear_whiteout();
    });

    $('.ccm-tt-bind').tooltip({ container: 'body' });

    $(window).resize(function() {
        if ($('#rel-popup').is(':visible')) {
            $('#rel-popup').center();
        }
    });

}); 

// Function for the control of the function symbols
function actionPic(mode, id, name) 
{
    switch(mode)
    {
        case 'download':      
            getDownload(id, name);           
            break;

        case 'clear':
            $('#search').val(''); 
            //$('#cmd').val('view');
            $('#name_filter').val(''); 
            //alert($('#cmd').val()); 
            //return; 
            $('#frmDatalist').submit();  
            break;

        case 'delete':
            $('#cmd').val(mode); 
            $('#id').val(id);
            $('#objectName').val(name);
            var conf = confirm("Do you really want to delete this database entry:\n"+name+"?");
            if(conf) {
                $('#frmDatalist').attr('action', $('#returnUrl').val());
                $('#frmDatalist').submit();
            }
            break;

        default:  //submit form 
            $('#cmd').val(mode); 
            $('#id').val(id);
            $('#objectName').val(name);
            $('#frmDatalist').attr('action', $('#returnUrl').val());
            $('#frmDatalist').submit(); //submit form if it's not a delete command 
            break;
    }
}

function show_relationship_popup(type, name, id, token)
{
    whiteout();

    $('#rel-popup').css('width', '60%')
                   .css('min-width', '500px')
                   .css('height', 'auto')
                   .css('min-height', '420px');
    $('#rel-popup').center();

    $.post('ajax.php', { cmd: 'info', type: type, objectName: name, id: id, token: token }, function(data) {

        $('#rel-popup').html(data).show();

    });
}


/**
*   wrapper function for log deletion navigation.  Mostly a page routing hack. 
*/ 
function delete_single_log(id) 
{
    $('#delete_single').val(id);
    $('#frmDatalist').submit(); 
}

// Download function
function getDownload(id,name) {
    var time = new Date();
    // pass table name to this function
    var type = $('#type').val();
    var url = "download.php?type="+type+"&timestamp="+time.getTime();
    if (id != 0) {
        url += "&config="+id;
    }
    window.open(url); 
}

function doConfig(mode) 
{
    $('#type').val(mode); 
    $('#writeConfigForm').submit();
}

// nagiosql function - probably not used in XI 
function writeConfig(mode) {
     alert(mode); 
    $('#type').val(mode); 
    $('#writeConfigForm').submit();
}


// "Go" button for multiple checked items 
function checkMode() {
    if ($('#selModify').val() == "delete") {
        confirminit("Do you really want to delete all marked entries?","Secure question",2,"Yes","No",2);
    } else {
        $('#frmDatalist').submit();
        document.frmDatalist.subDo.disabled = true; 
    }
}

var allChecked=false;
var allCheckedServices=false;
var allCheckedHosts=false

//check all items on page, i = number of results on page  
function checkAll() {

    if(allChecked) {
           $('input:checkbox').each(function() {
                this.checked = '';
            });
        $('#checkAll').text('Check All');
        allChecked = false;     
    }
    else {
        //var checked_status = this.checked;
        $('input:checkbox').each(function() { 
            this.checked = 'checked';     
        });
        $('#checkAll').text('Deselect All');
        allChecked = true;
    }
}

//bad hack for bulk modification tool 
function checkAllType(type) {

    bool = (type=='host') ? allCheckedHosts : allCheckedServices;
    if(bool) {
           $('input.'+type).each(function() {
                this.checked = '';
            });
        $('#checkAll'+type).text('Check All');
        if(type=='host')
            allCheckedHosts=false;
        else
            allCheckedServices=false;           
    }
    else {
        //var checked_status = this.checked;
        $('input.'+type).each(function() { 
            this.checked = 'checked';     
        });
        $('#checkAll'+type).text('Deselect All');
        if(type=='host')
            allCheckedHosts=true;
        else
            allCheckedServices=true;
    }
}
  
//used to clear search field 
function del(key) {
    if (key == "search") {
      document.frmDatalist.txtSearch.value = "";
//      document.frmDatalist.submit();
    }
}

 //'onchange' function for hostname filter 
function reloadFilter() {
    document.forms.frmDatalist.modus.value = "filter";
    document.forms.frmDatalist.hidLimit.value = "0";
    document.forms.frmDatalist.submit();    
}
    
/* form functions for bulk modification tool */
function updateBulkForm() {
    var bools = new Array('active_checks_enabled','passive_checks_enabled','check_freshness','obsess_over_host','event_handler_enabled','flap_detection_enabled','retain_status_information','retain_nonstatus_information','process_perf_data','notifications_enabled'); 
    var intForm = '<input type="radio" value="1" id="rad1" name="intForm" /> <label for="rad1">on&nbsp;</label>';
    intForm    += '<input type="radio" value="0" id="rad0" name="intForm" /> <label for="rad0">off&nbsp;</label>';
    intForm    += '<input type="radio" value="2" id="rad2" name="intForm" /> <label for="rad2">skip&nbsp;</label>';
    intForm    += '<input type="radio" value="3" id="rad3" name="intForm" /> <label for="rad3">null&nbsp;</label>';
    var txtForm = '<label for="txtForm">Value: </label><input type="text" size="2" value="" name="txtForm" id="txtForm" />';

    var selected = $('#option_list').val(); 
    if($.inArray(selected,bools) != -1)
        $('#inner_config_option').html(intForm);
    else
        $('#inner_config_option').html(txtForm);    
        
    $('#saveButton').css('display','inline');
}   

function submitBulk() {
    alert('submitted!');
}

function getContactRelationships(token) {  

    //alert('hi');
    var id = $('#contact').val();
    var bulkType = $('input:radio.bulkType:checked').val(); 
    var contact = $("#contact option:selected").text();


    url = 'ajax.php?cmd=getcontacts&token='+token+'&contact='+contact+'&id='+id+'&opt='+bulkType; 
    //alert(url);
    $('#relationships').load(url); 
    $('#saveButton').css('display','inline'); 
}

function getContactGroupRelationships(token) {  

    //alert('hi');
    var id = $('#contactgroups').val();
    var bulkType = $('input:radio.bulkType:checked').val(); 
    var contactgroup = $("#contactgroups option:selected").text();


    url = 'ajax.php?cmd=getcontactgroups&token='+token+'&contactgroup='+contactgroup+'&id='+id+'&opt='+bulkType; 
    //alert(url);
    $('#relationships').load(url); 
    $('#saveButton').css('display','inline'); 
}


function bulkWizard1(id) {

    switch(id) {
    
    case 'changeConfig':
        $('#bulkCmd').val('change'); //update post 
        $('#addContact').fadeOut('fast'); //hide options 
        $('#removeContact').fadeOut('fast');
        $('#addContactGroup').fadeOut('fast'); //hide options 
        $('#removeContactGroup').fadeOut('fast');       
        $('#bulk_change_option').fadeIn('fast'); //show available options 
    break;
    
    case 'addContact':
        $('#bulkCmd').val('add'); //update POST 
        $('#changeConfig').fadeOut('fast');
        $('#removeContact').fadeOut('fast');
        $('#addContactGroup').fadeOut('fast'); //hide options 
        $('#removeContactGroup').fadeOut('fast');       
        $('#contact_edit').fadeIn('fast');
        $('#overlayOptions').fadeIn('fast'); 
    break;
    
    case 'removeContact':
        $('#bulkCmd').val('remove');
        $('#changeConfig').fadeOut('fast');
        $('#addContact').fadeOut('fast');
        $('#addContactGroup').fadeOut('fast'); //hide options 
        $('#removeContactGroup').fadeOut('fast');       
        $('#findRelationships').fadeIn('fast'); 
        $('#contact_edit').fadeIn('fast');
    break; 
    
    case 'addContactGroup':
        $('#bulkCmd').val('addcg'); //update POST 
        $('#changeConfig').fadeOut('fast');
        $('#addContact').fadeOut('fast');
        $('#removeContact').fadeOut('fast'); //hide options         
        $('#removeContactGroup').fadeOut('fast');
        $('#contactgroup_edit').fadeIn('fast');
        $('#overlayOptionsCg').fadeIn('fast'); 
    break;
    
    case 'removeContactGroup':
        $('#bulkCmd').val('removecg');
        $('#changeConfig').fadeOut('fast');
        $('#addContact').fadeOut('fast');
        $('#removeContact').fadeOut('fast'); //hide options         
        $('#addContactGroup').fadeOut('fast');
        $('#findCgRelationships').fadeIn('fast'); 
        $('#contactgroup_edit').fadeIn('fast');
    break; 
    
    }

}

function getStaticFile() {

    var file = $('#staticFiles').val();
    var token = $('#token').val();
    var url = 'ajax.php?cmd=getfile&token='+token+'&opt='+file;
    $('#newcfg').load(url); 

}