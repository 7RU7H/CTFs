# Vector Writeup

Name: Vector
Date:  
Difficulty:  
Goals:  
Learnt:
Beyond Root:

![](grumpy.png)

- [[Vector-Notes.md]]
- [[Vector-CMD-by-CMDs.md]]


## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

Re did this for the screenshot with `crackmapexec` while I updated my notes on RDP
![](nozeroauthftprecheckwithcme.png)

![](cme.png)

![](nozeroauthrpc.png)

![](burpinit80.png)

![](mssingcparameter.png)

![](whoamizeroreturn.png)

![](2plus2doesnotequalzero.png)

![](mathisfun.png)

![](badcat.png)

![](doublecheckingms17.png)

![](http80loginadminadmin.png)

![](grumpycatpicturenoleaking.png)

![](desperatehydra.png)

![](rdpenum.png)

Rethinking HTTP 80 and 2290 while also avoid `nuclei` for the absolute safety of not auto exploiting the machine
![1080](nikto80.png)

And for 2290
![](Ifailedmynikto.png)

`curl` to `burpsuite` proxying revealed:
![](victorbigmistake.png)

- AES-256-CBC-PKCS7 `ciphertext: 4358b2f77165b5130e323f067ab6c8a92312420765204ce350b1fbb826c59488`
- Victor

Decipher the semanatics:
- PKCS7

PKCS7
![](pkcs7sematics.png)
CBC
![](cipherblockchaining.png)

Simply it is not a simple mash the `hashcat` or `jtr` job.

- AES-256 & CBC 
- PKCS7 is separate
	- certificate

Takeways: we need the cryptographic keys so this is rabbit hole
![](cyberchefhintthroughsystem.png)

Just in case the size indicate something later on
![](64ciphersize.png)

![](lowhangbruteforcelogin.png)

No ...
```sql
victor' OR 1=1 -- -
```
either...

The cat picture makes more sense now that this a potential a weak cryptography section to get the login credentials and then Sonus authenticated RCE exploit.
![](shannonscalesayitsweak.png)

Returning to understand what and how the PKCS7 works
![](wehavemissingcomponents.png)

Coudl just feroxbuster till I get a cert.p7b extension
![](findthep7bextension.png)

Release the FeroxDoSclusterBustering! Using brain on the wordlist choice...
![](ferox80.png)
And for port 2290
![](ferox2290.png)

[Good news everyone](https://www.youtube.com/watch?v=g8IVI0sZ6F8) - Phind has saved my nuclei - markdown and noting setup by reading while I simmer in the self-knowing knowledge of utter past failures of decrypting anything in any CTF.  
![](phindthebaconsavinggrease.png)

![](weirdfalsepositive.png)

I also cross check the versions of nuclei I have been using big oof 1 major version behind forgive me please.

[Microsoft Windows 10 < build 17763 - AppXSvc Hard Link Privilege Escalation (Metasploit)](https://www.exploit-db.com/exploits/47128)
[dualfade gist](https://gist.github.com/dualfade/48c45fb47ff273a3996c9a4f10ac9d72)

Pass the Hash 

## Exploit



## Foothold

## Privilege Escalation

## Post-Root-Reflection  

![](Vector-map.excalidraw.md)

## Beyond Root


Try getting the reverse shell with hoaxshell