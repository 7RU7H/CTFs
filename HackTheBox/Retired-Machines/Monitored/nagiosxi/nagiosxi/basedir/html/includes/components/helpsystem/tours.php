<?php
//
// Help System Component
// Copyright (c) 2011-2017 - Nagios Enterprises, LLC. All rights reserved.
//


/**
 * @param string $tour
 */
function get_tour($tour = "")
{
    $show_tour = '';
    if (!empty($tour)) {
        switch ($tour) {
            case "new_user":
                $show_tour = new_user_tour();
                break;
        }
    }

    return $show_tour;
}


function set_user_tour_preferences($id, $ended)
{
    $settings_raw = get_user_meta(0, "tours");
    if (!empty($settings_raw)) {
        $settings = unserialize($settings_raw);
    }

    $settings[$id] = $ended;
    set_user_meta(0, "tours", serialize($settings), false);
}


// http://bootstraptour.com/api/
function build_tour_js($name, $steps, $opts = array())
{
    $function_vars = array('afterGetState', 'afterSetState', 'afterRemoveState', 'onStart', 'onEnd', 'onShow', 'onShown', 'onHide', 'onHidden', 'onNext', 'onPrev', 'onPause ', 'onResume', 'onRedirectError');

    $tour = 'var tour = new Tour({
                    name: "'.$name.'",
                    onEnd: function (tour) { 
                        var optsarr = {
                            "keyname": "tours",
                            "tourid": "'.$name.'",
                            "keyvalue": 1,
                            "autoload": false
                            };
                        var opts = JSON.stringify(optsarr);
                        get_ajax_data("setusermeta", opts);
                    },
                    backdropPadding: 3,
                  steps: [
                  '.$steps.'
                ],
    ';

    if (empty($opts['template']))
        $opts['template'] = "<div class='popover tour'>
                               <div class='arrow'></div>
                                <h3 class='popover-title'></h3>
                                <div class='popover-content'></div>
                                <div class='popover-navigation'>
                                 <div class='btn-group'>
                                  <button class='btn btn-default' data-role='prev'>&laquo; "._('Prev')."</button>
                                  <button class='btn btn-default' data-role='next'>"._('Next')." &raquo;</button>
                                </div>
                                <button class='btn btn-default' data-role='end'>"._('End tour')."</button>
                               </div>
                              </div>";

    foreach ($opts as $key => $value) {
            $value = str_replace(array("\r","\n"), '', $value);
            if (is_string($value) && !in_array($key, $function_vars))
                $tour .= $key . ": \"" . str_replace('"','\"', $value) . "\",\n";
            else
                $tour .= $key . ": " . $value . ",\n";
    }

    if (is_dev_mode()) {
        $tour .= 'debug: 1,';
    }

    $tour .= '});';

    return $tour;
}


// http://bootstraptour.com/api/#step-options
function build_tour_steps($steps)
{
    $function_vars = array('onShow', 'onShown', 'onHide', 'onHidden', 'onNext', 'onPrev', 'onPause ', 'onResume', 'onRedirectError');
    $tour_steps = '';

    if(!is_array($steps))
        return '{}';
    $num_steps = count($steps);
    $i = 0;
    foreach($steps as $step){
        $tour_steps .="{\n";
        foreach($step as $key => $value){
            $value = str_replace(array("\r","\n"), '', $value);
            if (is_string($value) && !in_array($key, $function_vars))
                $tour_steps .= $key . ": \"" . addslashes($value) . "\",\n";
            else
                $tour_steps .= $key . ": " . $value . ",\n";
        }
        $tour_steps .= "},\n";
    }

    return $tour_steps;
}


function new_user_tour()
{
    $steps[] = array('backdrop' => true,
                     'orphan' => true,
                     'title' => _('Welcome to Nagios XI'),
                     'content' => _('We are going to take you on a tour around the Nagios XI interface. To proceed through the tour you can either click the Prev/Next buttons or use the') . ' <i class="fa fa-long-arrow-left"></i> <i class="fa fa-long-arrow-right"></i> ' . _('keys on your keyboard. You can end the tour at any time by clicking "End Tour".')
                     );
    $steps[] = array('backdrop' => true,
                     'element' => "#header",
                     'placement' => "bottom",
                     'title' => _('Primary Menu'),
                     'content' => _('This is the primary navigation menu allowing users to move from section to section within the Nagios XI interface.')
                     );           
    $steps[] = array('backdrop' => true,
                     'element' => "#leftnav",
                     'placement' => "right",
                     'title' => _('Secondary Navigation'),
                     'content' => _('Each selection within the primary navigation will launch new set of secondary navigation menus pertaining to available options in that section.')
                     );                           
    $steps[] = array('backdrop' => true,
                     'element' => "div.ext",
                     'placement' => "left",
                     'title' => _('Extended Navigation'),
                     'content' => _('Clicking the extended navigation allows access to additional functionality available throughout the user interface.')
                     );                     
    $steps[] = array('backdrop' => true,
                     'element' => "#schedulepagereport",
                     'placement' => "left",
                     'title' => _('Extended Navigation'),
                     'content' => _('With the Enterprise version of Nagios XI you can use the Schedule Page to create an instant report out of any page in the Nagios XI interface including custom dashboards.'),
                     'onShow' => 'function (tour) { $(".ext-menu ul").css("padding", "0").toggle(); }',
                     'onHide' => 'function (tour) { $(".ext-menu ul").css("padding", "0 0 0 200px").toggle(); }'
                     );
    $steps[] = array('backdrop' => true,
                     'element' => "div.header-right.search",
                     'placement' => "bottom",
                     'title' => _('Quick Search'),
                     'content' => _('You can perform a quick search for Hosts, Hostgroups, and Servicegroups by clicking the Quick Search icon.')
                     ); 
    $steps[] = array('backdrop' => true,
                     'orphan' => true,
                     'title' => _('Welcome'),
                     'content' => _('At any time you would like additional help, look for the').' <img src="' . get_base_url() . 'includes/components/helpsystem/images/help_and_support.png" width="24"></a> '._('help icon on the top right of each page.')
                     );
    $steps = build_tour_steps($steps);
    $tour = build_tour_js("new_user", $steps);

    return $tour;
}
