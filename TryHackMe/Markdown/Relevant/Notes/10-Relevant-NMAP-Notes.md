# 10 - FINALLY NMAP


```lua
# Nmap 7.94 scan initiated Thu Dec  7 17:10:00 2023 as: nmap -Pn -sC -sV -p 80,135,139,445,3389,49663,49668,49670 -vvv --min-rate 500 -e tun0 -oN extra-vvv-restart-and-scan-sc-sv-someports 10.10.108.195
Nmap scan report for relevant.thm (10.10.108.195)
Host is up, received user-set (0.088s latency).
Scanned at 2023-12-07 17:10:00 GMT for 101s

PORT      STATE SERVICE       REASON          VERSION
80/tcp    open  http          syn-ack ttl 127 Microsoft IIS httpd 10.0
| http-methods:
|   Supported Methods: OPTIONS TRACE GET HEAD POST
|_  Potentially risky methods: TRACE
|_http-server-header: Microsoft-IIS/10.0
|_http-title: IIS Windows Server
135/tcp   open  msrpc         syn-ack ttl 127 Microsoft Windows RPC
139/tcp   open  netbios-ssn   syn-ack ttl 127 Microsoft Windows netbios-ssn
445/tcp   open
            syn-ack ttl 127 Windows Server 2016 Standard Evaluation 14393 microsoft-ds
3389/tcp  open  ms-wbt-server syn-ack ttl 127 Microsoft Terminal Services
|_ssl-date: 2023-12-07T17:10:59+00:00; -42s from scanner time.
| ssl-cert: Subject: commonName=Relevant
| Issuer: commonName=Relevant
| Public Key type: rsa
| Public Key bits: 2048
| Signature Algorithm: sha256WithRSAEncryption
| Not valid before: 2023-12-06T16:14:38
| Not valid after:  2024-06-06T16:14:38
| MD5:   dbea:25ab:0ceb:4832:c46c:1fe3:9971:25a9
| SHA-1: e23d:7e73:6f3f:f498:8270:2f17:5a04:b95f:6bf7:90ca
| -----BEGIN CERTIFICATE-----
| MIIC1DCCAbygAwIBAgIQKGYybA1TX5tMdo4zdfzmlzANBgkqhkiG9w0BAQsFADAT
| MREwDwYDVQQDEwhSZWxldmFudDAeFw0yMzEyMDYxNjE0MzhaFw0yNDA2MDYxNjE0
| MzhaMBMxETAPBgNVBAMTCFJlbGV2YW50MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8A
| MIIBCgKCAQEA7D33NCnq6JCj8zKw7dx1bktBQk8lva3mwGIsBBdf01Z1TXWkBe/s
| R2jvcpO/lv2Ehp3sXUWN3kXL7vy6ARA988ZrmZ71bQ4MD9rNQi8Ny4xMkKHKNoEj
| lPbGuRRjhkgLGhBMtVvYu9Stv3lPYPVmth/yHrX1v0dW4fqMGna2RcbbxSkMkoCP
| qeAlsA+76ZuUc1CHetNw2R0lVYwbVX3PD0FojpRS9sCzSCxgRVFsHDpFt2kYtdqe
| x5y2p5mvGYjhgcULrjYW+GcRXvgUkZARt5oum9Z4b4T63RZwiT8Pzhc/IoChLRrw
| qayf68AM2PqmhHnOhYTAP+5ZiF9MGkIa2QIDAQABoyQwIjATBgNVHSUEDDAKBggr
| BgEFBQcDATALBgNVHQ8EBAMCBDAwDQYJKoZIhvcNAQELBQADggEBANexUlfe+7av
| 2UvMlDbwhtTqkIxfcPplEaxTxT13IxuLM9k0A5y7InWKTVbZbJLJ+YHUu9T7ePGc
| uWZv4G+uIAl8EdpDXrMMUXjK6YbR+M4PHVYZoxTD1td2x9HDaYMeiFanS3JQIZzo
| TG2ZzlauD1Clnki8eMk3E3Pb4ApvxQ6dxmWBEE7M7vwk6YIFZk3xmX/SyKjxGK6B
| 6n6pY1zjI0a2+JzcLZcgO+SIeVQ67K0JIOWq470AJDpooasEApEp/PS8j8EWbuz+
| cT+7Bfr39LUmBLkoOCkm4XvixEeQqL1q0B41HNmtJAeTsKt4QoYAcjlrPOzZNh5a
| pawLh3e9LiY=
|_-----END CERTIFICATE-----
| rdp-ntlm-info:
|   Target_Name: RELEVANT
|   NetBIOS_Domain_Name: RELEVANT
|   NetBIOS_Computer_Name: RELEVANT
|   DNS_Domain_Name: Relevant
|   DNS_Computer_Name: Relevant
|   Product_Version: 10.0.14393
|_  System_Time: 2023-12-07T17:10:21+00:00
49663/tcp open  http          syn-ack ttl 127 Microsoft IIS httpd 10.0
|_http-server-header: Microsoft-IIS/10.0
| http-methods:
|   Supported Methods: OPTIONS TRACE GET HEAD POST
|_  Potentially risky methods: TRACE
|_http-title: IIS Windows Server
49668/tcp open  msrpc         syn-ack ttl 127 Microsoft Windows RPC
49670/tcp open  msrpc         syn-ack ttl 127 Microsoft Windows RPC
Service Info: OSs: Windows, Windows Server 2008 R2 - 2012; CPE: cpe:/o:microsoft:windows

Host script results:
|_clock-skew: mean: 1h35m19s, deviation: 3h34m42s, median: -42s
| smb-security-mode:
|   account_used: guest
|   authentication_level: user
|   challenge_response: supported
|_  message_signing: disabled (dangerous, but default)
| p2p-conficker:
|   Checking for Conficker.C or higher...
|   Check 1 (port 6188/tcp): CLEAN (Timeout)
|   Check 2 (port 16232/tcp): CLEAN (Timeout)
|   Check 3 (port 25237/udp): CLEAN (Timeout)
|   Check 4 (port 17994/udp): CLEAN (Timeout)
|_  0/4 checks are positive: Host is CLEAN or ports are blocked
| smb-os-discovery:
|   OS: Windows Server 2016 Standard Evaluation 14393 (Windows Server 2016 Standard Evaluation 6.3)
|   Computer name: Relevant
|   NetBIOS computer name: RELEVANT\x00
|   Workgroup: WORKGROUP\x00
|_  System time: 2023-12-07T09:10:24-08:00
| smb2-time:
|   date: 2023-12-07T17:10:22
|_  start_date: 2023-12-07T16:14:49
| smb2-security-mode:
|   3:1:1:
|_    Message signing enabled but not required

Read data files from: /usr/bin/../share/nmap
Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
# Nmap done at Thu Dec  7 17:11:41 2023 -- 1 IP address (1 host up) scanned in 101.69 seconds
```