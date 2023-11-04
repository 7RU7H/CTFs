# XposedAPI Notes

## Data 

IP: 
OS:
Arch:
Hostname:
DNS:
Domain:  / Domain SID:
Machine Purpose: 
Services:
Service Languages:
Users:
Email and Username Formatting:
Credentials:

## Objectives

`\#\# Target Map ![](XposedAPI-map.excalidraw.md)`

#### Machine Connects to list and reason:

[[]] - Reason X

## Data Collected

#### Credentials
```
```

#### HUMINT


#### Solution Inventory Map


#### Todo 


#### Timeline of tasks complete
      
```json
{"user" :"gunicorn", "url":  "/logs",}
{"user" :true, "url":  "/logs",}
{"user" :null, "url":  "/logs",}
{"user" :1, "url":  "/logs",}
{"user" :[true], "url":  "/logs",}
{"user" :["gunicorn", true], "url":  "/logs",}
{"user" :$neq:"gunicorn", "url":  "/logs",}
// Send array 
{user=gunicorn, "url":  "/logs",}
```
- tried also www-data, root
- checked hints names confirming RCE which seems obvious but time 
- took hint:
	- One endpoint has a vulnerability you need to leverage to exploit another endpoint. Find a username.

https://hacken.io/discover/how-to-bypass-waf-hackenproof-cheat-sheet/
```
[**w3af**](https://github.com/andresriancho/w3af) — Web Application Attack and Audit Framework

[**wafw00f**](https://github.com/EnableSecurity/wafw00f) — Identify and fingerprint Web Application Firewall

[**BypassWAF**](https://github.com/vincentcox/bypass-firewalls-by-DNS-history) **–** Bypass firewalls by abusing DNS history. This tool will search for old DNS A records and check if the server replies for that domain. 

[**CloudFail**](https://github.com/m0rtem/CloudFail) – is a tactical reconnaissance tool that tries to find the original IP address behind the Cloudflare WAF.
```