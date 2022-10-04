### WAF Detection (waf-detect:apachegeneric) found on http://10.129.1.185
---
**Details**: **waf-detect:apachegeneric**  matched at http://10.129.1.185

**Protocol**: HTTP

**Full URL**: http://10.129.1.185/

**Timestamp**: Tue Oct 4 12:37:05 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | WAF Detection |
| Authors | dwisiswant0, lu4nx |
| Tags | waf, tech, misc |
| Severity | info |
| Description | A web application firewall was detected. |
| CWE-ID | [CWE-200](https://cwe.mitre.org/data/definitions/200.html) |
| CVSS-Score | 0.00 |

**Request**
```http
POST / HTTP/1.1
Host: 10.129.1.185
User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2919.83 Safari/537.36
Connection: close
Content-Length: 27
Content-Type: application/x-www-form-urlencoded
Accept-Encoding: gzip

_=<script>alert(1)</script>
```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Accept-Ranges: bytes
Content-Type: text/html
Date: Tue, 04 Oct 2022 11:37:05 GMT
Etag: "2a0e-565becf5ff08d-gzip"
Last-Modified: Wed, 21 Feb 2018 20:31:20 GMT
Server: Apache/2.4.18 (Ubuntu)
Vary: Accept-Encoding

<!DOCTYPE html>
<body style="background-color:black;">
<font color="YellowGreen">
<font size="3">
<html>
<head>
    <title>Landing Page</title>
</head>
<body>
<center>                         <h1 style="font-family:Arial;">Welcome to TartarSauce</h1>
<pre>

                                                                                                     
                                  ```..---::://////+++//////::-..```                                
                             `-/oosssyyyyyyyyyyyyyyyyyyyyyyyyyyyyysso/.                             
                           `ossyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyysyyyyys`                            
                           -yyyyyyysssssssysyyyyyysyssssssssosyyyyyyyyy.                            
                           +yyyyyyyyysooossssyyyyyyyyyyysssssyyyyyyyyyy.                            
                           /yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy.                            
                           /yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy.                            
                           :yyyyyyyyyysoosyyyyyyysyyyyyysoosyyyyyyyyyyy.                            
                           -yyyyyyyyyhhhhhhhhhhhhyhhhhhhhhhhyyyyyyyyyyy-                            
                           -yyyyyyyyyyyyyyyhhhhhyyhhhhhyyyyyyyyyyyyyyyy-                            
                           -yyyyyyyyyyyyyyyyyyyyyyyyyyysyyyyyyyyyyyyyyy-                            
                           .syyyyyyyyyyyyyyyyyyyyyyyyyssyyyyyyyyyyyyyyy.                            
                           .+syyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyysso`                            
                           :ooossooooooossssssssssssssssssssooooossssoo.                            
                           `.-:++++++////+++o++oooooooo++++++++++++/-.`                             
                              ./////////++++++++ooo++++++++++//////:.                               
                            `-::::::::::::///++++++/////:::::::::::::.                              
                           .::-------------------------------:::::::::-`                            
                         `-:-----------...----------------------------::.                           
                        `-----------------------------------------------:-`                         
                       .-----------------..--.-.---------------------------`                        
                     `----------....----------------------..-........-------.                       
                    `--------.....-.--:-------..-------------------.----------`                     
                  `.--...---......-------.-------------------------.--..-------.                    
                 `----............--.--..------------------------...-----.------.`                  
                .---........--...-.........-----------------------......-..-------`                 
              `.---...------.-----.......---------------------------.......-...----.                
             `--.......----------.--------------.------------------------.---..------`              
            .--........-.-----------.----------------------------------------..-------`             
          `-----.....---------------------------------------------------------.--....--.            
         `-------....------------------------------------------------------------...-.---`          
        .--.--...-------------------------------------------------------------------...---`         
       `---.----------------------:::///////++++++++++/////::::----------------------------`        
       ---.--..-----------:+oosssssssssyyyyyyyhhhhhhhhhhhhhhhhhyyso+/:---------------------.        
       ----.---.-------:+shhhhhyysssyyyyyhhhhhhhhhhhhhhhhhhhhhhhhhhhyys+:-------------------        
       --------------:oyhhhhhhhyyyyyyyyhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhyyy+:-----------------        
      `-------------:shhhhhhhhhhhhhhhhhhhhhhhhhyyyyyyyyyyyyyyyyyyyyhhhhhhhs/--------::------        
      `------------:shhhhhhhhhhhhhhhhhyyyssssssssssssssssyyyyyyyyyyyyhhhhhhy/-------:-----:-        
      `------------oyyhhhhhhhhhhs+////////////////////////////++syyyyhhhhhhhs-----------::::        
       -:::-------:syyyyhhhhhhy+:://:--//..:+-/++++/-+++/--://///oyyyhhhhhhyy/------:-:::::-        
        :::-------/ssyyyyhhhhs/::/:-..-yo--oy-:/h+::oy:+h/---:////+hhyhhhhyyyo------::::::-`        
        .:::------/yyyyyyhhhh+////-.--/hsosh+--+h---ysoys-.---::///yyyyyyyyyyo------:::::-`         
         -:::-----:sssyyhhhhhyo////-.-ss--+h:--yo--/h+/ss--.-/:///syyyyyyyyyy+-----::::::`          
         `:::::----oyyyhhhhhhhyo+///::+/--//---+:--:+///:--:////+yyyyyyyyyyys:----::::::-           
          .:::-----/hhhhhyyyyyyyyso+++++////////////////++++.... Truncated ....
```

References: 
- https://github.com/ekultek/whatwaf

**CURL Command**
```
curl -X 'POST' -d '_=<script>alert(1)</script>' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Host: 10.129.1.185' -H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2919.83 Safari/537.36' 'http://10.129.1.185/'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)