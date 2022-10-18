### FingerprintHub Technology Fingerprint (fingerprinthub-web-fingerprints:mongodb) found on http://192.168.125.143:27017
---
**Details**: **fingerprinthub-web-fingerprints:mongodb**  matched at http://192.168.125.143:27017

**Protocol**: HTTP

**Full URL**: http://192.168.125.143:27017

**Timestamp**: Sat Oct 15 16:52:14 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | FingerprintHub Technology Fingerprint |
| Authors | pdteam |
| Tags | tech |
| Severity | info |
| Description | FingerprintHub Technology Fingerprint tests run in nuclei. |
| CWE-ID | [CWE-200](https://cwe.mitre.org/data/definitions/200.html) |
| CVSS-Score | 0.00 |

**Request**
```http
GET / HTTP/1.1
Host: 192.168.125.143:27017
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.3319.102 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.0 200 OK
Content-Length: 85
Connection: close
Content-Type: text/plain

It looks like you are trying to access MongoDB over HTTP on the native driver port.

```

References: 
- https://github.com/0x727/fingerprinthub

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.3319.102 Safari/537.36' 'http://192.168.125.143:27017'
```
---
Generated by [Nuclei 2.7.8](https://github.com/projectdiscovery/nuclei)