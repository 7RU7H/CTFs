
```bash
ping -c 3 10.129.1.175                                                                        
PING 10.129.1.175 (10.129.1.175) 56(84) bytes of data.
64 bytes from 10.129.1.175: icmp_seq=1 ttl=63 time=47.8 ms
64 bytes from 10.129.1.175: icmp_seq=2 ttl=63 time=61.4 ms
64 bytes from 10.129.1.175: icmp_seq=3 ttl=63 time=44.9 ms

--- 10.129.1.175 ping statistics ---
3 packets transmitted, 3 received, 0% packet loss, time 2003ms
rtt min/avg/max/mdev = 44.943/51.410/61.439/7.190 ms


nikto -h 10.129.1.175 -C all                                                  
- Nikto v2.1.6
---------------------------------------------------------------------------
+ Target IP:          10.129.1.175
+ Target Hostname:    10.129.1.175
+ Target Port:        80
+ Start Time:         2022-05-16 19:46:47 (GMT1)
---------------------------------------------------------------------------
+ Server: Apache/2.4.18 (Ubuntu)
+ The anti-clickjacking X-Frame-Options header is not present.
+ The X-XSS-Protection header is not defined. This header can hint to the user agent to protect against some forms of XSS
+ The X-Content-Type-Options header is not set. This could allow the user agent to render the content of the site in a different fashion to the MIME type
+ Server may leak inodes via ETags, header found with file /, inode: 89, size: 559ccac257884, mtime: gzip
+ Apache/2.4.18 appears to be outdated (current is at least Apache/2.4.37). Apache 2.2.34 is the EOL for the 2.x branch.
+ Allowed HTTP Methods: OPTIONS, GET, HEAD, POST 
+ OSVDB-3233: /icons/README: Apache default file found.
+ 26522 requests: 0 error(s) and 7 item(s) reported on remote host                                                  
+ End Time:           2022-05-16 20:10:51 (GMT1) (1444 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested
```
