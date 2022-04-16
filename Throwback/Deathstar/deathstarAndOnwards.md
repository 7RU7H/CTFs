# Building Your Own Dark... er Deathstar and Deploy the Grunts!...

While waiting for the Kali VM to update so I could finally sit down and brutespray the current network, I ssh-ed into PROD as PetersJ. Got the flag for this user and began trying to get some [winprivchecker](https://github.com/Tib3rius/windowsprivchecker) on to the box, without any success.

![enum1](Screenshots/deathstar-prod-enum1.png)
```powershell

Microsoft Windows [Version 10.0.17763.1282]
# wmic and powershell both avaliable

```

This PetersJ account is pretty low status...

![enum1](Screenshots/deathstar-lowstatus.png)

Now that Empire and Starkiller is on the Kali VM the only differenece is we need to start the empire server without the `--rest` flag and just 