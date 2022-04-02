# Webshell And You

Realising from the opening line, thankful I have behind me on this as [default credientals](https://docs.netgate.com/pfsense/en/latest/usermanager/defaults.html) seem to be a stumbling block of my. I have yet to hammer into my head that lowing hanging fruit like this are alway worth the effort. I closed it, went back to trying to Blackbox more, forgive some lack of circularity in my problem solving. Just not something I would ever do and as inexperienced as I am it just seem so alien to do so for some reason. 

![login](Screenshots/pfSenseLogin)

And inital dashboard screen:

![dashboard](Screenshots/pfsense-admin-dsahboard)

You can change the firewall rules 

![rules](Screenshots/fw-rules)

You can add users

![addusers](Screenshots/fw-addusers)

These pictures are for more demonstrative purposes. As you could alot more and the examples above more ones that I forsee exploiting. Depending on how under staffed Throwback Hacks is *joke* you could edit certificates, routing to attack to controlled network as pre-foothold to monitor traffic and man in the middle more important AD forest this one connects to, while spoof traffic internally to make it seem that all is normal. Provide the admins on this forest some spoofed layered version of pfsense tool middle manning their input, while being aware of their actions and adapting accordingly. This would provide a safehold and time to enuemrate the local internal network. Wildly silly day dreaming aside...

Using a command prompt we can: execute commands, download and upload commands.
![cmd](Screenshots/fw-cmdprompt)

## Answers
What username was used to access the configuration portal?
```{toggle}
admin
```
What password was used to access the configuration portal?  
```{toggle}
pfsense
```
What menu tab contains a command prompt tab in the PFSense Configuration panel?
```{toggle}
Diagnostics
```