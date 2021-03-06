### WAF Detection (waf-detect:apachegeneric) found on http://10.129.88.158
---
**Details**: **waf-detect:apachegeneric**  matched at http://10.129.88.158

**Protocol**: HTTP

**Full URL**: http://10.129.88.158/

**Timestamp**: Tue Jun 7 21:56:55 +0100 BST 2022

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
Host: 10.129.88.158
User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36
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
Content-Length: 2106
Accept-Ranges: bytes
Content-Type: text/html; charset=UTF-8
Date: Tue, 07 Jun 2022 20:56:55 GMT
Etag: "83a-552d2a27c66d2"
Last-Modified: Mon, 26 Jun 2017 01:11:46 GMT
Server: Apache/2.4.6 (CentOS)

<pre>
     \                  ###########                  /
      \                  #########                  /
       \                                           /
        \                                         /
         \                                       /
          \                                     /
           \                                   /
            \_________________________________/
            |                                 |
            |                                 |
            |                                 |
            |            _________            |
            |           |         |           |
            |           |   ___   |           |
            |           I  |___|  |           |
            |           |         |           |
            |           | J A I L |           |
            |           |        _|           |
            |           |       |#|           |  ;,
    -- ___  |           |         |           |   ;'
    H*/   ` |           |         |      _____|    .,`
    */     )|           I         |     \_____\     ;'
    /___.,';|           |         |     \\     \     ."`
    |     ; |___________|_________|______\\     \      ;:
    | ._,'  /                             \\     \      .
    |,'    /                               \\     \
    ||    /                                 \\_____\
    ||   /                                   \_____|
    ||  /              ___________                \
    || /              / =====o    |                \
    ||/              /  |   /-\   |                 \
    //              /   |         |                  \
   //              /    |   ____  |______             \
  //              /    (O) |    | |      \             \
 //              /         |____| |  0    \             \
//              /          o----  |________\             \
/              /                  |     |  |              \
              /                   |        |               \
             /                    |        |
            /                     |        |
</pre>

```

References: 
- https://github.com/ekultek/whatwaf

**CURL Command**
```
curl -X 'POST' -d '_=<script>alert(1)</script>' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Host: 10.129.88.158' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36' 'http://10.129.88.158/'
```
---
Generated by [Nuclei 2.7.1](https://github.com/projectdiscovery/nuclei)