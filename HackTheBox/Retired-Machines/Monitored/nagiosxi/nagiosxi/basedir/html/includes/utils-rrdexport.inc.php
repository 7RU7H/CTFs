<?php
//
// Copyright (c) 2016-2020 Nagios Enterprises, LLC. All rights reserved.
//


/**
 * Function to get exported rrd data
 *
 * @param   string  $host                   Hostname to pull from
 * @param   string  $service                Service of hostname to pull from, will default to _HOST_
 * @param   string  $return_type            Optional DEFAULT=EXPORT_RRD_XML, otherwise EXPORT_RRD_JSON | EXPORT_RRD_CSV | EXPORT_RRD_ARRAY
 * @param   int     $start                  Optional DEFAULT=-24hrs from now, otherwise the start time, in seconds since epoch
 * @param   int     $end                    Optional DEFAULT=now, otherwise the end time, in seconds since epoch
 * @param   int     $step                   Optional DEFAULT=300sec step of data to return from rrd
 * @param   array   $columns_to_display     Optional DEFAULT=ALL, otherwise the array of strings that correspond to datasources in the rrd file
 * @param   int     $maxrows                Optional DEFAULT=400 rows of data to return from rrd
 * @return  mixed                           Returns either a string containing final csv,xml or json, or an array
 */
function get_rrd_data($host, $service = "", $return_type = EXPORT_RRD_XML, $start = "", $end = "", $step = "", $columns_to_display = "", $maxrows = "")
{
    $rrd_file = get_rrd_file_name_from_host_and_service($host, $service);

    // First check if file and corresponding xml metadata file exists
    if (!file_exists($rrd_file)) {
        return false;
    }
    $xml_file_info = pathinfo($rrd_file);
    $xml_file = "{$xml_file_info['dirname']}/{$xml_file_info['filename']}.xml";
    if (!file_exists($xml_file)) {
        return false;
    }

    // Get all valid columns from metadata and get their ds/label information
    $possible_columns_to_display = get_rrd_metadeta_datasource_as_array($xml_file);

    // Then check if columns specified in function arguments are valid
    $valid_columns_to_display = array();
    if (!empty($columns_to_display)) {

        // Convert to array if not already
        if (!is_array($columns_to_display)) {
            $columns_to_display = array($columns_to_display);
        }

        // Cycle through the array of column information and try and find a match
        // if the current column is found in the known possibles, add this to the valid column array
        foreach ($columns_to_display as $current_column) {
            if ($key = @array_search($current_column, $possible_columns_to_display)) {
                $valid_columns_to_display[$key] = $possible_columns_to_display[$key];
            }
        }

    } else {
        $valid_columns_to_display = $possible_columns_to_display;
    }

    // Build the command to export the data
    $baseCmd = "rrdtool xport ";
    $cmd = $baseCmd;
    $showtime = "--showtime ";

    // Error checking for input based on start/end needs to happen somewhere else, this function can *actually* accept anything that rrdtool xport can... (read the manpage)
    if (!empty($start))
        $cmd .= "--start ".escapeshellarg($start)." ";
    if (!empty($end))
        $cmd .= "--end ".escapeshellarg($end)." ";
    if (!empty($step))
        $cmd .= "--step ".escapeshellarg($step)." ";
    if (!empty($maxrows))
        $cmd .= "--maxrows ".escapeshellarg($maxrows)." ";

    //
    // Bug Fix - Missing timestamp in CSV douwnloaded file
    // https://git.nagios.com/products/nagiosxi/-/issues/1
    //
    // Only use option --showtime, if it exists for this version of rrdtool xport
    // rrdtool 1.4.9 (CentOS 7) includes timestamp by default.
    //         1.7.0 (CentOS 8) requires --showtime for timestamp.
    //         1.7.2 (CentOS 9) requires --showtime for timestamp.
    //
    // NOTE: Only testing the acceptance of the --showtime option, instead of just adding it
    //       to $cmd, in case the dataset returned is huge/slow.
    //

    $showtimeTest = $baseCmd.$showtime." 2>&1";
    exec($showtimeTest, $outputArray, $result_code);

    // If --showtime is not valid, truncate the string.
    if (is_array($outputArray) && preg_match('/(unknown)*(option)*(--showtime)/', $outputArray[0])) {
        $showtime = "";
    }

    //
    // END: Bug Fix 1
    //

    $vname = 0;
    foreach ($valid_columns_to_display as $ds => $label) {
        $cmd .= $showtime."DEF:$vname=$rrd_file:$ds:AVERAGE ";
        $label2 = str_replace(':', '\:', $label);
        $cmd .= "XPORT:$vname:\"$label2\" ";
        $vname++;
    }

    // Run the rrdtool xport command, get the output and return
    exec($cmd, $lined_output, $return_output);
    if ($return_output !== 0)
        return FALSE;

    $xml_output = implode("", $lined_output);

    // Check return type and return accordingly
    switch ($return_type) {
        case EXPORT_RRD_ARRAY:
            return json_decode(convert_xml_string_to_json($xml_output), true);

        case EXPORT_RRD_CSV:
            return convert_xml_string_to_csv($xml_output);

        case EXPORT_RRD_JSON:
            return convert_xml_string_to_json($xml_output);

        case EXPORT_RRD_XML:
        default:
            return $xml_output;
    }
}


/**
 * @param   string  $xml_string     The xml string to perform the conversion on
 * @return  string                  JSON encoded data
 */
function convert_xml_string_to_json($xml_string)
{
    $xml = simplexml_load_string($xml_string, "SimpleXMLElement", LIBXML_NOCDATA);
    return json_encode($xml);
}


/**
 * Function to load xml (rrd metadata) and return specific key/value pairs
 *
 * @param   string  $xml_file   XML file location
 * @return  array               Array of datasource in the form of array[DS] = LABEL (where DS and LABEL are pulled from <DATASOURCE> tags in the specified xml file)
 */
function get_rrd_metadeta_datasource_as_array($xml_file)
{
    $rrd_metadata_xml = simplexml_load_file($xml_file);
    $output_array = array();

    foreach($rrd_metadata_xml->DATASOURCE as $datasource) {
        $output_array[(string) $datasource->DS] = (string) $datasource->LABEL;
    }

    if (empty($output_array)) {
        return FALSE;
    }

    return $output_array;
}


/**
 * Function to take an xml string, and parse that data into a string full of csv data
 *
 * @param   string  $xml_string The xml data in string format
 * @param   string  $sec        Optional the string/character used to delimit strings in the csv, defaults to single quote (')
 * @param   bool    $csv_header Optional to generate a header row or not? default is true
 * @return  string              String containing csv data
 */
function convert_xml_string_to_csv($xml_string, $sec = "'", $csv_header = true)
{
    $csv_output = "";
    $xml = simplexml_load_string($xml_string);
    $ec = $sec;

    if ($csv_header) {
        $csv_output .= "{$ec}timestamp{$ec}";
        foreach($xml->meta->legend->entry as $key => $value) {
            $csv_output .= ",{$ec}$value{$ec}";
        }
        $csv_output .= "\n";
    }

    foreach ($xml->data->row as $row) {
        $csv_output .= $ec . $row->t . $ec;
        foreach($row->v as $key => $value) {
            $csv_output .= ",{$ec}$value{$ec}";
        }
        $csv_output .= "\n";
    }

    return $csv_output;
}


/**
 * Get file rrd file name from host service
 *
 * @param   string  $host       Hostname
 * @param   string  $service    Optional service name, DEFAULT = "_HOST_"
 * @return  string              String containing full path to rrd file
 */
function get_rrd_file_name_from_host_and_service($host, $service = "")
{
    global $cfg;
    $perfdata = $cfg['component_info']['pnp']['perfdata_dir'];

    $host = pnp_convert_object_name($host);
    $service = pnp_convert_object_name($service == "" ? "_HOST_" : $service);

    if (pnp_chart_exists($host, $service)) {
        return str_replace(" ", "_", "$perfdata/$host/$service.rrd");
    }

    return false;
}
