# Dante Writeup

Name: Dante
Start Date: 13/4/2026  
Difficulty:  Beginner
Goals:  
- Something something, so on and so on
Learnt:
- Cowboys exist
Beyond Root:
- Not sure

- [[Dante-Notes]]
- [[Dante-CMD-by-CMDs]]


![](Dante-map.excalidraw.md)

## Initial Recon

```bash
sudo masscan -p22,80,443,3389 -oG masscan.log --rate=$beware -e tun0 10.10.110.0/24 
```

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

	
## Exploit

## Foothold

## Privilege Escalation

## Post-Root-Reflection  

![](Dante-map.excalidraw.md)

## Beyond Root


