# Pelican Writeup
Name: Pelican
Date:  
Difficulty:  
Goals:  
Learnt:

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/Pelican/Screenshots/ping.png)

Apache ZooKeeper was able to be accessed without any required authentication.See [[exposed-zookeeper-192.168.141.98_2181.md]]

`charles` username found!


![](zero-ouput-8080gospider.png)
Zero output from `http://$ip:8080`, but gospider ran on 8081 gets 8080 pages! 
![](8080-8081-gospider-connection.png)
nikto for 8081 reports: `Root page / redirects to: http://192.168.141.98:8080/exhibitor/v1/ui/index.html`

[The **RMI** (Remote Method Invocation) is an API that provides a mechanism to create distributed application in **java**. The **RMI** allows an object to invoke methods on an object running in another JVM. The **RMI** provides remote communication between the applications using two objects stub and skeleton..](https://www.javatpoint.com/RMI)

## Exploit

## Foothold

## PrivEsc

      
