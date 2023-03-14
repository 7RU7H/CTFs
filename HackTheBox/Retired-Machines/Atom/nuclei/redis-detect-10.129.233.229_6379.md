### Redis Service - Detect (redis-detect) found on http://10.129.233.229
---
**Details**: **redis-detect**  matched at http://10.129.233.229

**Protocol**: NETWORK

**Full URL**: 10.129.233.229:6379

**Timestamp**: Fri Feb 24 12:16:49 +0000 GMT 2023

**Template Information**

| Key | Value |
|---|---|
| Name | Redis Service - Detect |
| Authors | pussycat0x |
| Tags | network, redis |
| Severity | info |
| Description | Redis service was detected. |
| CVSS-Metrics | [CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:N](https://www.first.org/cvss/calculator/3.1#CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:N) |
| CWE-ID | [CWE-200](https://cwe.mitre.org/data/definitions/200.html) |
| CVSS-Score | 0.00 |
| shodan-query | product:"redis" |

**Request**
```http
*1
$4
info

```

**Response**
```http
-NOAUTH Authentication required.

```


---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)