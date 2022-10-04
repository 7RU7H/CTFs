### Apache Detection (apache-detect) found on http://10.129.1.185
---
**Details**: **apache-detect**  matched at http://10.129.1.185

**Protocol**: HTTP

**Full URL**: http://10.129.1.185

**Timestamp**: Tue Oct 4 12:36:55 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | Apache Detection |
| Authors | philippedelteil |
| Tags | tech, apache |
| Severity | info |
| Description | Some Apache servers have the version on the response header. The OpenSSL version can be also obtained |

**Request**
```http
GET / HTTP/1.1
Host: 10.129.1.185
User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Accept-Ranges: bytes
Content-Type: text/html
Date: Tue, 04 Oct 2022 11:36:55 GMT
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

**Extra Information**

**Extracted results**:

- Apache/2.4.18 (Ubuntu)



**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36' 'http://10.129.1.185'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)