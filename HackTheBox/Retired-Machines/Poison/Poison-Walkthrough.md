# Poison Walkthrough
Name: Poison
Date:  
Difficulty:  Medium
Goals: 
- OSCP Prep Handholding from Ippsec to save my terrible situation. 
- LFI there is a PG box with a LFI vulnerability that I failed at and dont want to get hints for or walkthrough
- Practice log poisoning 
Learnt:

This is purely me having a terrible day yesterday and I wont allow myself to let yesterday spill into today. Ippsec is an amazing person and resource and I am subscribed and like all the videos I watch. I am rewatching this as I watched all these over a year ago from this playlist to learn how far I needed to go am still going to improve. And today I need a sit down with someone just to get back up and continue. 

This has Ippsec unique way apperently so I follow along and also do the alternative easier way if necessary. 

## Recon
The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![](ping.png)

charix

[TL;DR this white paper makes a request a large enough to cuaese a rece condition such that the subsequent script enough time get code execution on the target before the cache flushes the file](https://insomniasec.com/downloads/publications/LFI%20With%20PHPInfo%20Assistance.pdf). [The script for this is from Payload all the things](https://raw.githubusercontent.com/swisskyrepo/PayloadsAllTheThings/master/File%20Inclusion/phpinfolfi.py).

1. Edited script change the request in the http request to the correct url. 
1. Added php-reverse-shell code
1. Researched with python encoding translation - and thought about actually solution to running intio the problem with python

```bash
  File "/tmp/lfiexploit.py", line 341, in <module>
    main()
  File "/tmp/lfiexploit.py", line 301, in main
    offset = getOffset(host, port, reqphp)
  File "/tmp/lfiexploit.py", line 246, in getOffset
    s.send(phpinforeq)
TypeError: a bytes-like object is required, not 'str'
```

## Exploit

## Foothold

## PrivEsc

      
