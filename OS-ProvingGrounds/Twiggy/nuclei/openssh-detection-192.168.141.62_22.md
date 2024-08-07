### OpenSSH Detection (openssh-detection) found on http://192.168.141.62
---
**Details**: **openssh-detection**  matched at http://192.168.141.62

**Protocol**: NETWORK

**Full URL**: 192.168.141.62:22

**Timestamp**: Sat Sep 17 21:22:19 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | OpenSSH Detection |
| Authors | r3dg33k, daffainfo, iamthefrogy |
| Tags | seclists, network, ssh, openssh |
| Severity | info |
| Description | OpenSSH is the premier connectivity tool for remote login with the SSH protocol. It encrypts all traffic to eliminate eavesdropping, connection hijacking, and other attacks. In addition, OpenSSH provides a large suite of secure tunneling capabilities, several authentication methods, and sophisticated configuration options.
 |

**Response**
```http
SSH-2.0-OpenSSH_7.4

```

**Extra Information**

**Extracted results**:

- SSH-2.0-OpenSSH_7.4


References: 
- http://www.openwall.com/lists/oss-security/2016/08/01/2
- http://www.openwall.com/lists/oss-security/2018/08/15/5
- https://nvd.nist.gov/vuln/detail/cve-2016-6210
- https://nvd.nist.gov/vuln/detail/cve-2018-15473
- http://seclists.org/fulldisclosure/2016/jul/51

---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)