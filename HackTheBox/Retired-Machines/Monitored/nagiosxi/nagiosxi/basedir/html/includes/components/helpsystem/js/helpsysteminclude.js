/**
 *    Help system include for global scope of XI.
 */

function help_system(id)
{
    display_child_popup(520);

    // Prepare container for graph
    var content = "<div style='padding-top:5px; clear:both;' id='helpcontainer'></div>";
    content += "<div id='scriptcontainer'></div>";
    $("#child_popup_container").width(520);
    $("#child_popup_layer").width(550);
    $("#child_popup_layer").css('position', 'fixed');
    center_child_popup();

    set_child_popup_content(content);

    var args = 'helpid=' + id;

    // Load the actual help system popup content
    $("#scriptcontainer").load(base_url + 'includes/components/helpsystem/get_help.php?' + args, function () {
        center_child_popup();
    });
}

function help_system_load_video(youtube_id)
{
    var url = 'https://www.youtube.com/embed/' + youtube_id + '?rel=0&iv_load_policy=3';

    $('#helpsystem_video iframe').attr('src', url);
    $('#helpsystem_thumbnails').toggle();
    $('#helpsystem_resources').toggle();
    $('#helpsystem_video').fadeToggle("slow", "linear");
    center_child_popup();

    $('#child_popup_close').click(function () {
        helpsystem_return_from_video();
    });
}

function helpsystem_return_from_video()
{
    var url = ''
    $('#helpsystem_video').toggle();
    $('#helpsystem_thumbnails').fadeToggle("slow", "linear");
    $('#helpsystem_resources').fadeToggle("slow", "linear");
    $('#helpsystem_video iframe').attr('src', url);
}

function help_system_admin(id)
{
    display_child_popup(520);

    // Prepare container for graph
    var content = "<div style='padding-top:5px; clear:both;' id='helpcontainer'></div>";
    content += "<div id='scriptcontainer'></div>";
    $("#child_popup_container").width(520);
    $("#child_popup_layer").width(550);
    $("#child_popup_layer").css('position', 'fixed');

    whiteout();
    set_child_popup_content(content);
    center_child_popup();

    var args = JSON.stringify(helpsystem_request_vars);
    $("#scriptcontainer").load(base_url + 'includes/components/helpsystem/helpsystem_admin.php?id=' + id + '&req=' + args + '&request_uri=' + location.pathname, '', function() {
        center_child_popup();
    });
}

