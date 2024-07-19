<?php

function get_alarm_sounds() {
    $dir = get_base_dir() . '/sounds/alarm';

    $dh = opendir($dir);
    if ($dh === false) {
        return array();
    }

    $sounds = array();
    while (false !== $current_file = readdir($dh)) {
        $abs_path = $dir . '/' . $current_file;
        if (!is_file($abs_path)) {
            continue;
        }

        $mime = mime_content_type($abs_path);

        if (strpos($mime, 'audio') === false) {
            continue;
        }

        $sounds[] = array('file' => $current_file,
                          'web_path' => get_base_url() . 'sounds/play.php?name=' . $current_file,
                          'short_name' => substr($current_file, 0, strrpos($current_file, '.')),
                          'mime' => $mime);
    }
    closedir($dh);

    return $sounds;
}

function get_alarm_file_path($name) {
    return get_base_dir() . '/sounds/alarm/' . $name;
}

// make sure to call basename on $name first
function delete_alarm_sound($name) {
    return unlink(get_alarm_file_path($name));
}
