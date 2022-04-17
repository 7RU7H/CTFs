# Building Your Own Dark... er Deathstar and beyond!...


While waiting for the Kali VM to update so I could finally sit down and brutespray the current network, I ssh-ed into PROD as PetersJ. Got the flag for this user and began trying to get some [winprivchecker](https://github.com/Tib3rius/windowsprivchecker) on to the box, without any success.

![enum1](Screenshots/deathstar-prod-enum1.png)
```powershell

Microsoft Windows [Version 10.0.17763.1282]
# wmic and powershell both avaliable

```

This PetersJ account is pretty low status...

![enum1](Screenshots/deathstar-lowstatus.png)

##  Building Your Own Dark... er Deathstar 

Now that Empire and Starkiller is on the Kali VM the only differenece is we need to start the empire server without the `--rest` flag and just... After sometime configuring, testing and troubleshooting I have concluded that this section is very, very outdated. [See the installation guide for the updated installation](https://bc-security.gitbook.io/empire-wiki/quickstart/installation). If you are using Kali Linux just use:

```bash
sudo apt install powershell-empire
powershell-empire
```

You will still need to download the starkiller image and `chmod +x` it.

##  Deploy the Grunts! 

Follow the login instruction and continue on following the specified instruction to make a listener:

![sk1](Screenshots/ds-fst-listener.png)

Here is the stager settings:

![sk2](Screenshots/ds-stager-fst.png)

Click the download drop button from the Actions column on the Stagers tab and save it to the same directory as server to host it. Realising too late that the PROD has wget, just make sure you run powershell.exe as petersj user. 

![wpck](Screenshots/ds-wpck-transfer.png)


Launcher.bat did not work.
![sk3](Screenshots/sk-unable-to-connect.png)

Checked `ss -tulnp`: 
![sk4](Screenshots/sk-ss-check.png)
It is definately running on port 53, but the /download/powershell directory is probably the issue. I think probably the problem might be that the port 53 and my IP is not the tun0 for the Kali VM from THM so I started from scratch again. With these configurations being the configurations that the launcher.bat is looking for.. Hello there: 
![sk5](Screenshots/sk-agent.png)

## Get-Help Invoke-WorldDomination 

Familiarised myself with some menus and tested a shell command queuing.

![sk6](Screenshots/sk-agent-testcmd.png)

##  SEATBELT CHECK! 

Setup the seatbelt module, screenshot contain a description of this C# module. Use the "<" to see commands in the Agent tasks tab  
![sk7](Screenshots/sk-seatbelt-setup.png)
Completed! [To see full output ](prod-seatbelt.out). *spoiler* CredEnum Errored out 
![sk8](Screenshots/sk-seatbelt-completed.png)

Instead tried manually running seatbelt:
![fail](Screenshots/credenum-man-fail.png)

