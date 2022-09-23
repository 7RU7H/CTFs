# Hunit Writeup
Name: Hunit
Date:  23/09/2022
Difficulty:  Medium
Goals:  OSCP 3/5 Medium in done walkthrough or writeup in a day 
Learnt:

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

ok...
![](ok.png)
/api/user/
```
[{"login":"rjackson","password":"yYJcgYqszv4aGQ","firstname":"Richard","lastname":"Jackson","description":"Editor","id":1},{"login":"jsanchez","password":"d52cQ1BzyNQycg","firstname":"Jennifer","lastname":"Sanchez","description":"Editor","id":3},{"login":"dademola","password":"ExplainSlowQuest110","firstname":"Derik","lastname":"Ademola","description":"Admin","id":6},{"login":"jwinters","password":"KTuGcSW6Zxwd0Q","firstname":"Julie","lastname":"Winters","description":"Editor","id":7},{"login":"jvargas","password":"OuQ96hcgiM5o9w","firstname":"James","lastname":"Vargas","description":"Editor","id":10}] 

```

Feel annoyed at how CTF-y this one has been. Especially as the privEsc went well without Walkthrough till I ran into the Git shell and this is really cool PrivEsc



## Foothold
Git user's ssh
![](sshkey.png)

## PrivEsc


oh god
![](ohgod.png)
