# Access
Name: Access
Date:  
Difficulty:  Intermediate 
Goals:  OSCP Prep Do all the PG!
Learnt:

## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.

![ping](OS-ProvingGrounds/Access/Screenshots/ping.png)

No DNS Zone transfer
#### Nmap Discovery
```lua
http-enum:
/forms/: Potentially interesting directory w/ listing on 'apache/2.4.48 (win64) openssl/1.1.1k php/8.0.7'
/icons/: Potentially interesting folder w/ directory listing
/uploads/: Potentially interesting directory w/ listing on 'apache/2.4.48 (win64) openssl/1.1.1k php/8.0.7'


dnsHostName: SERVER.access.offsec


```

![](swiper.png)

![csrf-vulns](nmap-csrf-vuln.png)

- /Ticket.php 
```html
<form action="/Ticket.php" method="post" enctype="multipart/form-data">
	<div class="form-group">
		<input type="text" class="form-control" name="your-name" placeholder="Your Name">
	</div>
	<div class="form-group mt-3">
		<input type="text" class="form-control" name="your-email" placeholder="Your Email">
	</div>
	<div class="form-group mt-3">
		<select id="ticket-type" name="ticket-type" class="form-select">
			<option value="">-- Select Your Ticket Type --</option>
			<option value="standard-access">Standard Access</option>
			<option value="pro-access">Pro Access</option>
			<option value="premium-access">Premium Access</option>
		</select>
</div>
```

#### Nuclei

![leak](cgi-leaking-env-vars.png)


Directory Busting Protection, but Gospider still enough. SMB related tools all access denied, include rpcclient with null authentication.

Weird naming..
![](weird-domain-naming.png)

![diff](diff-theldapsearch.png)

![](forbidden-cgi-bin.png)

```
593/tcp   open  ncacn_http    Microsoft Windows RPC over HTTP 1.0
```

![](https://docs.microsoft.com/en-us/windows/win32/midl/ncacn-http)

Identifying the Microsoft Internet Information Server (IIS) as the protocol family allows client and server applications to communicate across the internet by using the Microsoft Internet Information Server (IIS) as a proxy. Because calls are tunneled through an established HTTP port, they can cross firewalls.

Any RPC client and server applications can support the **ncacn_http** protocol as long as they are networked to an Internet Information Server.



![upload](image-upload.png)

No subdomains found with FFUF

![pending](upload-pending.png)

1. Check if email is sent somewhere - No Email sent
2. Intercept Web request
	1. File extension can changed, without issue - php is forbidden
	![extension](upload-fileextensive.png)
Check upload directory.


## Exploit

## Foothold

## PrivEsc

      
