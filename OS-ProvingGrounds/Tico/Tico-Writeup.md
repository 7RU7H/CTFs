# Tico Writeup
Name: Tico
Date:  
Difficulty:  Hard
Goals:  
- OSCP Prep 
- Knowing I will fail to complete this box in a timely manner - the goal is to gain more insight into spotting web rabbit holes. I'll regular recon, each potential vector I willl enumerate for 5 minutes at most each for one hour 12 - here is x potential - y outcome and why possibly it would not. Then all three hints one every 20 minutes till i click walkthrough.
Learnt:



## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/Tico/Screenshots/ping.png)

- All services are safe, Openssl is also 
Looted the debug.pcap; indication of attacker targeting port 27017 mongodb 
![900](debugpcap1.png)

![](port51072.png)

Originally my goal to test everything on the site seemed like the best way to tackle this, but as I read some articles about rabbit holing on the machines. I realised I already had the start of how I was to get a foothold. This would not really meet my goal of being a web exploit rabbit hole finding more that realised the manner of how Offsec would actually make these machines. The debug.pcacp is to debug the client -> client uses a protocol -> therefore ; similiar to if this was a Linux PrivEsc Root is running a service use localhost address on a port -> Port redirect somehow to hijack the protocol.  


## Exploit

## Foothold

## PrivEsc

      
