### DNS SaaS Service Detection (dns-saas-service-detection) found on hat-valley.htb.

----
**Details**: **dns-saas-service-detection** matched at hat-valley.htb.

**Protocol**: DNS

**Full URL**: hat-valley.htb

**Timestamp**: Fri Oct 6 20:10:30 +0100 BST 2023

**Template Information**

| Key | Value |
| --- | --- |
| Name | DNS SaaS Service Detection |
| Authors | noah @thesubtlety, pdteam |
| Tags | dns, service |
| Severity | info |
| Description | A CNAME DNS record was discovered |

**Request**
```http
;; opcode: QUERY, status: NOERROR, id: 63646
;; flags: rd; QUERY: 1, ANSWER: 0, AUTHORITY: 0, ADDITIONAL: 1

;; OPT PSEUDOSECTION:
; EDNS: version 0; flags:; udp: 4096

;; QUESTION SECTION:
;hat-valley.htb.	IN	 CNAME

```

**Response**
```http
;; opcode: QUERY, status: NXDOMAIN, id: 63646
;; flags: qr rd ra; QUERY: 1, ANSWER: 0, AUTHORITY: 1, ADDITIONAL: 1

;; OPT PSEUDOSECTION:
; EDNS: version 0; flags:; udp: 512

;; QUESTION SECTION:
;hat-valley.htb.	IN	 CNAME

;; AUTHORITY SECTION:
.	86382	IN	SOA	a.root-servers.net. nstld.verisign-grs.com. 2023100602 1800 900 604800 86400

```

References: 
- https://ns1.com/resources/cname
- https://www.theregister.com/2021/02/24/dns_cname_tracking/
- https://www.ionos.com/digitalguide/hosting/technical-matters/cname-record/

----

Generated by [Nuclei v2.9.11](https://github.com/projectdiscovery/nuclei)