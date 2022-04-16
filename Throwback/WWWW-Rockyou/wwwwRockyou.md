# We Will, We Will, Rockyou

Given my some faults with brutespray and generally life schedule stalling this endeavour. I know that using the JeffersD pasword for PROD, PetersJ is actually the Adminstrator and probably the most valuable target on the network. BUT there were multiple PetersJs on the box so probably part of partitioning and redundancy to in a real world scenario used a canary accounts that would trigger alarms. I previously cracked the HumpreyW password with crackstation, so I would use hashcat and the onruletorulethemall rule for the PetersJ hash. All the while I will run john the ripper in the background:

![johnpetersj](Screenshots/wwwwRY-john.png)

```bash
hashcat -m 5600 petersjHASH /usr/share/wordlists/rockyou.txt -r oRtRtA.rule --debug-mode=1 --debug-file=matched.rule
```

And...
![hashcatpetersj](Screenshots/wwwwRY-hashcat.png)

## Answers

What is the cracked password from the pfSense hash?
```{toggle}
securitycenter
```
What is the cracked password from LLMNR poisoning?
```{toggle}
Throwback317
```