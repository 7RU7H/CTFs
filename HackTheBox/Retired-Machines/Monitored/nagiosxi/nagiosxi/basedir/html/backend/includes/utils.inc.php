<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/common.inc.php');

////////////////////////////////////////////////////////////////////////
// INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////

function check_backend_prereqs()
{
    // Make ALL database connections
    $dbok = db_connect_all();

    // Handle bad db connection
    if ($dbok == false) {
        handle_backend_db_error();
    }
}

////////////////////////////////////////////////////////////////////////
// OUTPUT FUNCTIONS
////////////////////////////////////////////////////////////////////////

// Generate headers for API output
function output_backend_header()
{
    global $request;

    // What output are we using...
    $outputtype = grab_request_var("outputtype", "");

    // We usually output XML, except if debugging or if we want JSON, duh
    $debug = grab_request_var("debug", "");
    if ($debug == "text") {
        $outputtype = "text";
    } else if ($debug == "html") {
        $outputtype = "html";
    }

    // Always use text when debugging SQL
    if (isset($request["debugsql"])) {
        $outputtype = "text";
    }

    // Allow access to this XML via AJAX calls
    header("Access-Control-Allow-Origin: *");

    // Display headers based on output
    switch ($outputtype) {

        case "json":
            header("Content-type: application/json");
            break;

        case "text":
            header("Content-type: text/plain");
            break;

        case "html":
            header("Content-type: text/html");
            break;

        case "xml":
        default: // xml by default
            header("Content-type: text/xml");
            echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
            break;
    }
}

/**
 * @param        $code
 * @param string $msg
 */
function begin_backend_result($code, $msg = "")
{
    echo "<result>\n";
    echo "<code>$code</code>\n";
    echo "<message>$msg</message>\n";
}

function end_backend_result()
{
    echo "</result>\n";
}


////////////////////////////////////////////////////////////////////////
// DB FUNCTIONS
////////////////////////////////////////////////////////////////////////

/**
 * @param        $level
 * @param        $rs
 * @param        $fieldname
 * @param string $nodename
 * @param bool   $return
 *
 * @return string
 */
function xml_db_field($level, $rs, $fieldname, $nodename = "", $return = false)
{
    $temp = get_xml_db_field($level, $rs, $fieldname, $nodename);
    if ($return) {
        return $temp;
    } else {
        echo $temp;
    }
}

/**
 * @param $rs
 * @param $fieldname
 *
 * @return string|XML
 */
function db_field($rs, $fieldname)
{
    return get_xml_db_field_val($rs, $fieldname);
}

/**
 * @param      $level
 * @param      $nodename
 * @param      $nodevalue
 * @param bool $return
 *
 * @return string
 */
function xml_field($level, $nodename, $nodevalue, $return = false)
{
    $temp = get_xml_field($level, $nodename, $nodevalue);
    if ($return) {
        return $temp;
    } else {
        echo $temp;
    }
}

function json_format($json)
{
  if (!is_string($json)) {
    if (phpversion() && phpversion() >= 5.4) {
      return json_encode($json, JSON_PRETTY_PRINT);
    }
    $json = json_encode($json);
  }
  
  $result      = '';
  $pos         = 0;
  $strLen      = strlen($json);
  $indentStr   = "   ";
  $newLine     = "\n";
  $prevChar    = '';
  $outOfQuotes = true;

  for ($i = 0; $i < $strLen; $i++) {
    // Speedup: copy blocks of input which don't matter re string detection and formatting.
    $copyLen = strcspn($json, $outOfQuotes ? " \t\r\n\",:[{}]" : "\\\"", $i);
    if ($copyLen >= 1) {
      $copyStr = substr($json, $i, $copyLen);
      // Also reset the tracker for escapes: we won't be hitting any right now
      // and the next round is the first time an 'escape' character can be seen again at the input.
      $prevChar = '';
      $result .= $copyStr;
      $i += $copyLen - 1;      // correct for the for(;;) loop
      continue;
    }
    
    // Grab the next character in the string
    $char = substr($json, $i, 1);
    
    // Are we inside a quoted string encountering an escape sequence?
    if (!$outOfQuotes && $prevChar === '\\') {
      // Add the escaped character to the result string and ignore it for the string enter/exit detection:
      $result .= $char;
      $prevChar = '';
      continue;
    }
    // Are we entering/exiting a quoted string?
    if ($char === '"' && $prevChar !== '\\') {
      $outOfQuotes = !$outOfQuotes;
    }
    // If this character is the end of an element,
    // output a new line and indent the next line
    else if ($outOfQuotes && ($char === '}' || $char === ']')) {
      $result .= $newLine;
      $pos--;
      for ($j = 0; $j < $pos; $j++) {
        $result .= $indentStr;
      }
    }
    // eat all non-essential whitespace in the input as we do our own here and it would only mess up our process
    else if ($outOfQuotes && false !== strpos(" \t\r\n", $char)) {
      continue;
    }
    // Add the character to the result string
    $result .= $char;
    // always add a space after a field colon:
    if ($outOfQuotes && $char === ':') {
      $result .= ' ';
    }
    // If the last character was the beginning of an element,
    // output a new line and indent the next line
    else if ($outOfQuotes && ($char === ',' || $char === '{' || $char === '[')) {
      $result .= $newLine;
      if ($char === '{' || $char === '[') {
        $pos++;
      }
      for ($j = 0; $j < $pos; $j++) {
        $result .= $indentStr;
      }
    }
    $prevChar = $char;
  }
  return $result;
}
