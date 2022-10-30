# Conceal HelpThrough

Name: Conceal
Date:  
Difficulty: Hard 
Goals: OSCP Prep
Learnt:
- snmp-check output is bonkers
- 5353 (multicat dns)
- 5355 (LLMNR) - responder catch hashes from this 
- ike-scan -m  for better output

Having a bad hardware day... Therefore to keep the proverbial paddling  of a good life going ever on, Ippsec video on standby to learn why this box is so hard to enumerate and how I can encorporate more dilegence when facing challenges that do not want to be found.

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

[isakmp](https://en.wikipedia.org/wiki/Internet_Security_Association_and_Key_Management_Protocol)

I did an Ikescan to find this, awhile ago knowing the box is hard. Just here to learn and textual fill in the gaps,:

```
CONCEAL : 9C8B1A372B1878851BE2C097031B6E43
iso.3.6.1.2.1.1.4.0 = STRING: "IKE VPN password PSK - 9C8B1A372B1878851BE2C097031B6E43"
```

On hearing the introduction I have never step up a vpn from nothing. So I will also do that. 

![](ntlmdudecake.png)

`Dudecake1!`

```bash
snmp-check -l 
```

At this point I fired up researching Ipsec protocol, because I think I have not covered it much to little in my notes. The VPN protocol - it authenicates and encrypts packets to provide secure communication between two computer. I will release my articles on making my own tomorrow evening along with the Ipsec related information.

Strongswan is apparently really complex and frail as of 4 years ago, will look into a better version.

ip sec manual


`$ip %any : PSK "$Key"`

28800 seconds to hours.

We need to install strongswan
https://en.wikipedia.org/wiki/IPsec
https://www.youtube.com/watch?v=tuDVWQOG0C0

https://www.youtube.com/watch?v=8HCb2g8IT8I
20:23
https://www.youtube.com/watch?v=1ae64CdwLHE


netgate ipsec troubleshoot
## Exploit

## Foothold

## PrivEsc

      
