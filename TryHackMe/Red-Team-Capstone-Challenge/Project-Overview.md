# Project Overview

TryHackMe, a cybersecurity consultancy firm, has been approached by the government of Trimento to perform a red team engagement against their Reserve Bank (TheReserve).![Trimento Logo](https://tryhackme-images.s3.amazonaws.com/user-uploads/6093e17fa004d20049b6933e/room-content/a29cffb908b62b9564316df5a43f69e9.png) Trimento is an island country situated in the Pacific. While they may be small in size, they are by no means not wealthy due to foreign investment. Their reserve bank has two main divisions:

-   **Corporate** - The reserve bank of Trimento allows foreign investments, so they have a department that takes care of the country's corporate banking clients. banking clients, such as foreign investments.  
    
-   **Bank** - The reserve bank of Trimento is in charge of the core banking system in the country, which connects to other banks around the world.

The Trimento government has stated that the assessment will cover the entire reserve bank, including both its perimeter and internal networks. They are concerned that the corporate division, while boosting the economy, may be endangering the core banking system due to insufficient segregation. The outcome of this red team engagement will determine whether the corporate division should be spun off into its own company.

# Project Go﻿al

The purpose of this assessment is to evaluate whether the corporate division can be compromised and, if so, determine if it could result in the compromise of the bank division. To fully demonstrate the compromise, a simulated fraudulent money transfer must be performed.

To do this safely, TheReserve will create two new core banking accounts for you. You will need to demonstrate that it's possible to transfer funds between these two accounts. The only way this is possible is by gaining access to SWIFT, the core backend banking system.

_**Note:** SWIFT_ (Society for Worldwide Interbank Financial Telecommunications) _is the actual system that is used by banks for backend transfers. In this assessment, a core backend system has been created. However, for security reasons, intentional inaccuracies have been introduced into this process. If you wish to learn more about actual SWIFT and its security, feel free to go do some research! To put it in other words, the information that follows here has been **made up**._

To help you understand the project goal, the government of Trimento has shared some information about the SWIFT backend system. SWIFT runs in an isolated secure environment with restricted access. While the word impossible should not be used lightly, the likelihood of the compromise of the actual hosting infrastructure is so slim, that it is fair to say that it is impossible to compromise this infrastructure.

However, the SWIFT backend exposes an internal web application at [http://swift.bank.thereserve.loc/,](http://swift.bank.thereserve.loc/,) which TheReserve uses to facilitate transfers. The governmnet has provided a general process for transfers. To transfer funds:  

1.  A customer makes a request that funds should be transferred and receives a transfer code.
2.  The customer contacts the bank and provides this transfer code.  
    
3.  An employee with the capturer role authenticates to the SWIFT application and _captures_ the transfer.
4.  An employee with the approver role reviews the transfer details and, if verified, _approves_ the transfer. This has to be performed from a jump host.  
    
5.  Once approval for the transfer is received by the SWIFT network, the transfer is facilitated and the customer is notified.

Separation of duties is performed to ensure that no single employee can both capture and approve the same transfer.

Project Scope

This section details the project scope.

In-Scope

-   Security testing of TheReserve's internal and external networks, including all IP ranges accessible through your VPN connection.
-   OSINTing of TheReserve's corporate website, which is exposed on the external network of TheReserve. Note, this means that all OSINT activities should be limited to the provided network subnet and no external internet OSINTing is required.  
    
-   Phishing of any of the employees of TheReserve.
-   Attacking the mail boxes of TheReserve employees on the WebMail host (.11).
-   Using any attack methods to complete the goal of performing the transaction between the provided accounts.

Out-of-Scope

-   Security testing of any sites not hosted on the network.
-   Security testing of the TryHackMe VPN and scoring servers, or attempts to attack any other user connected to the network.
-   Any security testing on the WebMail server (.11) that alters the mail server configuration or its underlying infrastructure.
-   Attacking the mail boxes of other red teamers on the WebMail portal (.11).
-   External (internet) OSINT gathering.
-   Attacking any hosts outside of the provided subnet range. Once you have completed the questions below, your subnet will be displayed in the network diagram. This 10.200.X.0/24 network is the only only in-scope network for this challenge.  
    
-   Coducting DoS attacks or any attack that renders the network inoperable for other users.

Project Tools

In order to perform the project, the government of Trimento has decided to disclose some information and provide some tools that might be useful for the exercise. You do not have to use these tools and are free to use whatever you prefer. If you wish to use this information and tools, you can either find them on the AttackBox under `/root/Rooms/CapstoneChallenge` or download them as a task file using the blue button at the top of this task 9above the video). If you download them as a task file, use the password of `Capstone` to extract the zip. Note that these tools will be flagged as malware on Windows machines.

**Note**: For the provided password policy that requires a special character, the characters can be restricted to the following: `!@#$%^`  

Patching In

**Note: If your network goes offline while you are working, please make sure to refresh the room page before clicking the Start button again. If you click extend instead, you will place the network in a locked stated where the timer first has to run out before you are able to restart the network.**  

If you use the AttackBox for this challenge, you will automatically be connected to the network. You can verify this connection by typing `ifconfig` and look for the **capstone** adapter. Make note of this IP, since you will most likely require it to complete the challenge.

You are, however, welcome to use your own machine. Should you wish to do so, go to your [access](https://tryhackme.com/access) page. Select 'Redteamcapstonechallenge' from the VPN servers (under the network tab) and download your configuration file.

![VPN Connection](https://tryhackme-images.s3.amazonaws.com/user-uploads/6093e17fa004d20049b6933e/room-content/25be9645e9e9b9c2975427b08ce9fd46.png)  

Use an OpenVPN client to connect. This example is shown on the [Linux](https://tryhackme.com/access#pills-linux) machine; use this guide to connect using [Windows](https://tryhackme.com/access#pills-windows) or [macOS](https://tryhackme.com/access#pills-macos).

Terminal

         `[thm@thm]$ sudo openvpn redteamcapstonechallenge.ovpn Fri Mar 11 15:06:20 2022 OpenVPN 2.4.9 x86_64-redhat-linux-gnu [SSL (OpenSSL)] [LZO] [LZ4] [EPOLL] [PKCS11] [MH/PKTINFO] [AEAD] built on Apr 19 2020 Fri Mar 11 15:06:20 2022 library versions: OpenSSL 1.1.1g FIPS  21 Apr 2020, LZO 2.08 [....] Fri Mar 11 15:06:22 2022 /sbin/ip link set dev tun0 up mtu 1500 Fri Mar 11 15:06:22 2022 /sbin/ip addr add dev tun0 10.50.2.3/24 broadcast 10.50.2.255 Fri Mar 11 15:06:22 2022 /sbin/ip route add 10.200.4.0/24 metric 1000 via 10.50.2.1 Fri Mar 11 15:06:22 2022 WARNING: this configuration may cache passwords in memory -- use the auth-nocache option to prevent this Fri Mar 11 15:06:22 2022 Initialization Sequence Completed`
      

The message "Initialization Sequence Completed" tells you that you are now connected to the network. Return to your access page. You can verify you are connected by looking on your access page. Refresh the page, and you should see a green tick next to Connected. It will also show you your internal IP address.

![VPN Connection verified](https://tryhackme-images.s3.amazonaws.com/user-uploads/6093e17fa004d20049b6933e/room-content/022b0e257034c9879bb4237cbc9cd33e.png)

Project Registration

The Trimento government mandates that all red teamers from TryHackMe participating in the challenge must register to allow their single point of contact for the engagement to track activities. As the island's network is segregated, this will also provide the testers with access to an email account for communication with the government and an approved phishing email address, should phishing be performed.

To register, you need to get in touch with the government through its e-Citizen communication portal that uses SSH for communication. Here are the SSH details provided:

**SSH Username**  

e-citizen  

**SSH Password**  

stabilitythroughcurrency  

**SSH IP**  

X.X.X.250  

Once you complete the questions below the network diagram at the start of the room will show the IP specific to your network. Use that information to replace the X values in your SSH IP.

Once you authenticate, you will be able to communicate with the e-Citizen system. Follow the prompts to register for the challenge, and save the information you get for future reference. Once registered, follow the instructions to verify that you have access to all the relevant systems.

The VPN server and the e-Citizen platform are not in scope for this assessment and any security testing of these systems may lead to a ban from the challenge.

As you make your way through the network, you will need to prove your compromises. In order to do that, you will be requested to perform specific steps on the host that you have compromised. Please note the hostnames in the network diagram above, as you will need this information. Flags can only be accessed from matching hosts, so even if you have higher access, you will need to lower your access to the specific host required to submit the flag.

**Note: If the network has been reset or if you have joined a new subnet after your time in the network expired, your e-Citizen account will remain active. However, you will need to request that the system recreates your mailbox for you. This can be done by authenticating to e-Citizen and then selecting option 3.**

# Summary

Please make sure you understand the points below before starting. If any point is unclear, please reread this task.

-   The purpose of this assessment is to evaluate whether the corporate division can be compromised and, if so, determine if it could result in the compromise of the bank division.
-   To demonstrate the compromise, a simulated fraudulent money transfer must be performed by gaining access to the SWIFT core backend banking system.
-   The SWIFT backend infrastructure is secure, but exposes an internal web application used by TheReserve to facilitate transfers.
-   A general process for transfers involves separation of duties to ensure that one employee cannot both capture and approve the same transfer.
-   You have been provided with some information and tools that you may find helpful in the exercise, including a password policy, but you are free to use your own.
-   There are rules in place that determine what you are allowed and disallowed to do. Failure to adhere to these rules might result in a ban from the challenge.  
    
-   After gaining access to the network, you need to register for challenge through e-Citizen communication portal using provided SSH details.
-   You will need to prove compromises by performing specific steps on the host that you have compromised. These steps will be provided to you through the e-Citizen portal.