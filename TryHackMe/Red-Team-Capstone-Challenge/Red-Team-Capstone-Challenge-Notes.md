# Red-Team-Capstone-Challenge Notes

## Current Objectives

Modern Wordlist generator are bad in many really annoying ways - mentalist is old but good.

## Inventory of ToDo

Cookie
```
{"iv":"EPQ0\/Sazdv5CQPK5hf2vUw==","value":"nkLpPod23yxTUpcEHAAGANwRPm7rHDfERLQWMhjDokKZnebjm7eKWnVRKR1GZQuVdBBHzoW8gda7ghcERlarWCd2OW\/eA5SCYD+r6NB1NDJFdJggDXbcO5lTIZ8J5UR2","mac":"f5edadc6779af48f790256524566d7f9c208a8997b6794a7e44c4bea439de59b"}
```      

There is a vpns directory to fuzz. If we have a username format we can fuzz it.

add swift.bank.thereserve.loc


# General Project Details Reminder


## The Tranfer - swift.bank.thereserve.loc

To transfer funds:  

1.  A customer makes a request that funds should be transferred and receives a transfer code.
2.  The customer contacts the bank and provides this transfer code.  
3.  An employee with the capturer role authenticates to the SWIFT application and _captures_ the transfer.
4.  An employee with the approver role reviews the transfer details and, if verified, _approves_ the transfer. This has to be performed from a jump host.  
5.  Once approval for the transfer is received by the SWIFT network, the transfer is facilitated and the customer is notified.

## Project Scope

This section details the project scope.

In-Scope
-   Security testing of TheReserve's internal and external networks, including all IP ranges accessible through your VPN connection.
-   OSINTing of TheReserve's corporate website, which is exposed on the external network of TheReserve. Note, this means that all OSINT activities should be limited to the provided network subnet and no external internet OSINTing is required.   
-   Phishing of any of the employees of TheReserve.
-   Attacking the mailboxes of TheReserve employees on the WebMail host (.11).
-   Using any attack methods to complete the goal of performing the transaction between the provided accounts.

Out-of-Scope
-   Security testing of any sites not hosted on the network.
-   Security testing of the TryHackMe VPN (.250) and scoring servers, or attempts to attack any other user connected to the network.
-   Any security testing on the WebMail server (.11) that alters the mail server configuration or its underlying infrastructure.
-   Attacking the mailboxes of other red teamers on the WebMail portal (.11).
-   External (internet) OSINT gathering.
-   Attacking any hosts outside of the provided subnet range. Once you have completed the questions below, your subnet will be displayed in the network diagram. This 10.200.X.0/24 network is the only in-scope network for this challenge.  
-   Conducting DoS attacks or any attack that renders the network inoperable for other users.
