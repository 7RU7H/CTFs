### FTP Anonymous Login (ftp-anonymous-login) found on https://10.10.193.71
---
**Details**: **ftp-anonymous-login**  matched at https://10.10.193.71

**Protocol**: NETWORK

**Full URL**: 10.10.193.71:21

**Timestamp**: Thu Jan 19 12:00:17 +0000 GMT 2023

**Template Information**

| Key | Value |
|---|---|
| Name | FTP Anonymous Login |
| Authors | c3l3si4n, pussycat0x |
| Tags | network, ftp, default-login |
| Severity | medium |
| Description | Anonymous FTP access allows anyone to access your public_ftp folder, allowing unidentified visitors to download (and possibly upload) files on your website. Anonymous FTP creates the potential for a security hole for hackers and is not recommended.
 |

**Request**
```http
USER anonymous
PASS anonymous

```

**Response**
```http
230 Login successful.

```

References: 
- https://tools.ietf.org/html/rfc2577

---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)