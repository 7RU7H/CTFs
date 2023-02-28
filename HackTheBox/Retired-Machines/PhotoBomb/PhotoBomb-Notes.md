# Notes

## Data 

IP: 10.129.217.44
OS: Ubuntu Bionic
Hostname:
Domain:  photobomb.htb
Machine Purpose: 
Services: 22, 80 - nginx/1.18.0
Service Languages: js
Users:
Credentials:

## Objectives

## Solution Inventory Map

### Todo 

### Done

Ubuntu Bionic
https://launchpad.net/ubuntu/+source/openssh/1:7.6p1-4ubuntu0.5

photobomb.htb

welcomepacktofind.png

niktofindweirdness.png

niktoisgoingwild.png

LFI
sintraisonthe127001.png

https://sinatrarb.com/

nginx/1.18.0 - is not vulnerable
adminareas.png

[The HTTP **`WWW-Authenticate`** response header defines the [HTTP authentication](https://developer.mozilla.org/en-US/docs/Web/HTTP/Authentication) methods ("challenges") that might be used to gain access to a specific resource.](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/WWW-Authenticate)

gospideriseyes.png

It only downloads png from filetype= regardless

It is a post request. 

```
Authorization: Basic cEgwdDA6YjBNYiE=
```

seriouscreds.png
```bash
echo "cEgwdDA6YjBNYiE=" | base64 -d
pH0t0:b0Mb!
```

![](nonexistent.png)

Forum hinted
- Fuzz the parametres
![](inject.png)


addtheinjection.png

https://portswigger.net/web-security/os-command-injection
https://www.golinuxcloud.com/create-reverse-shell-cheat-sheet/
https://systemweakness.com/linux-privilege-escalation-using-path-variable-manipulation-64325ab05469