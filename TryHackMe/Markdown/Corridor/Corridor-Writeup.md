# Corridor Writeup

Name: Corridor
Date:  08/01/2023
Description: Can you escape the Corridor?
Difficulty: Easy  
Goals: 
- Hack while tired and ill 
- Warm up
Learnt:
Beyond Root:
- Learn about general python webserver file directory and such
- Harden the webserver

This and [[Jason-Helped-Through]] are from [Newbie Tuesdays with Alh4zr3d](https://www.youtube.com/watch?v=2e9pGJbZpJg). I wanted to warm up while ill, get THM streak, harden some webservers mostly for the Json - mostly have fun before I before return to [[Support-Writeup]]. I just work this one out and added some bash kung fu to grab the hashes to check.

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

Root source
![](sourceofroot.png)

If you click one you get:
![](emptyroom.png)

Grabbing all the hashes with some bash-fu
```bash
curl http://10.10.141.181 | grep href | grep -v 'stylesheet' | awk '{print $5}' | sed 's/href="//g' | tr -d '"'
```
      
Each md5hash is just a number 
![](crackstation.png)

So before Al figures this out I and can change this back to a Write-up.
```bash
echo -n "flag" | md5sum # this does not work try the number zero 
echo -n "0" | md5sum
```