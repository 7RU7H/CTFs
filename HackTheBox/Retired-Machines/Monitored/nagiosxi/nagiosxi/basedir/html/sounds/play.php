<?php
$name = $_GET['name'];
$file = 'alarm/'.$name;
if (file_exists($file)) {
    $mime = mime_content_type($file);
	if (strpos($mime, 'audio') === false) {
		exit;
	}
    header('Content-Description: Audio');
    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}