# FriendZone Notes

## Data 

IP: 10.129.165.17
OS:
Hostname: 
Domain:  friendzone.red
organization Name: CODERED
Machine Purpose: 
Services:  21,22,53,80,139,443. 445
Service Languages:
Users:
Credentials:


## Objectives

## Solution Inventory Map


### Todo 


smb 
- /etc/Files

usernames

[https://security.stackexchange.com/questions/262357/how-can-you-exploit-a-website-that-resolves-to-localhost](https://security.stackexchange.com/questions/262357/how-can-you-exploit-a-website-that-resolves-to-localhost)

Oooh, that's very dangerous. There are two main attack vectors against such systems.

1. Cross-user same-machine access. Loopback network sockets are notable for being the only mainstream form of IPC with absolutely no support for authentication or access control (you can try bolting something on at the application layer, like TLS, of course). As such, an attacker on the same machine - likely even one within a sandbox such as an app from an app store or a malicious page in a browser - can connect to the server as a client, or possibly even spoof the server (launching before the server does and squatting its port) and wait for the legit client to connect. This can allow local EoP to attack other users and/or break out of a sandbox.
2. Spoof DNS and impersonate the server. If you have local network access (e.g. on public WiFi at an airport or cafe) you can spoof DNS responses and make the DNS query return your own IP instead of the loopback address. You can then run a server yourself and impersonate the real server (optionally forwarding traffic _back_ to the real server - after modification, if desired - assuming it listens on external interfaces). Obviously this won't work if the client expects the server to use a trusted TLS certificate, but this sort of system almost never does. This allows a remote attack on the system, and depending what the client does, you might be able to gain local code execution, steal credentials, or otherwise compromise the user.

---

> sometimes i see domains named like 127-0-0-1.domain.com and it resolves to localhost, why do the developers even need such a thing ?

CORS and/or cookies, mostly. They want to be able to serve content (almost always web content) from a local process, but also want it to be treated as a "real" subdomain that shares a root domain with "the rest of" their servers. This means you can share cookies across the subdomains, use a simple CORS policy that forbids foreign domains, or even lower the page origin to the same root domain on both the local and remote servers' pages and actually be treated as same-origin for purposes such as iframes. Some browsers and platforms (mostly Apple's) also treat "real" domains better than IP addresses even when not required by the spec.

It can also be done for TLS - no real CA will issue a cert for "127.0.0.1" or even for "localhost", but "127-0-0-1.domain.com" works - but it would be very weird (and possibly dangerous) if that's happening here. A CA-issued cert for that domain would require that the private key be distributed with the application so that the server could use it, and then of course anybody with the app could steal the key and use it themselves, completely breaking the point of TLS (and also requiring, by policy, immediate revocation of the certificate). Alternatively, you can create a self-signed certificate and install it in the machine's/browser's trust store, but you can do that without bothering to use a "real" domain on DNS.

[](https://security.stackexchange.com/a/263030 "Short permalink to this answer")


### Done

friendzone.png
cmenosigning.png
nmapsmbenum.png
cmesmbshares.png
cmeftpnoanonymous.png
seriously.png
notarealwordpresssite.png

Sharing Localhost over DNS! Amazing
dnslocalhost.png

Consider my options with the above I pillaged the smb shares
cmeandsmbmaptodownloadthecredentials.png

` admin : WORKWORKHhallelujah@# ` 

cmeadmintestingshares.png
sadftpandssh.png

friend user from admin enum4linux
e4lwithadmin.png

nopasswordreuse.png

two times the javascript what could go wrong
jsjs.png


aXZFU0ZYQkJpTDE2ODY4Mzc1MDNCamNXb0pEOWxT
weirdness.png

NjVpMGVWVHBkbjE2ODY4Mzc2MTBzVEljeXAySEdH
weirdnesschanges.png

YlA5cWpWbmVxVjE2ODY4Mzc2Mzc5VW1WWFdkMUpp


zoneadmin.png
This could be a nasty rabbithole

2023-06-15T15:08:07.995Z justgotzoned zonedman

- Try ` admin : WORKWORKHhallelujah@# `  as a cookie

https://www.rapid7.com/db/vulnerabilities/cifs-smb-signing-not-required/