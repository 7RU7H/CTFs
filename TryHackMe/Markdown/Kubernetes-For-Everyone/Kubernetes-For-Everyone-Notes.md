# Kubernetes-For-Everyone Notes

## Data 

IP: 10.10.144.31
OS:
Hostname:
Machine Purpose: 
Services:
- 5000 Werkzeug httpd 2.0.2 (Python 3.8.12)
- 6443 Kubernetes 
Service Languages:
Users:
Credentials:

## Objectives

## Target Map

## Solution Inventory Map

### Todo 

### Done

Nmap finds Kubernetes but the API has some degree of Authentication and Authorisation implemented
```bash
nmap -n -T4 -p 443,2379,6666,4194,6443,8443,8080,10250,10255,10256,9099,6782-6784,30000-32767,44134 $IP/16

curl -k https://$IP:6443
```

```python
ARG = resource
FILE = debugger.js, console.png

console?__debugger__=yes&cmd=ARG&f=FILE 
```


[Hacktricks Werkzeug Console](https://book.hacktricks.xyz/network-services-pentesting/pentesting-web/werkzeug) is behind a pin
- `python3.8/site-packages/werkzeug/debug/__init__.py` - could not find..


Brute force against pin - must restart server too may attempt 
```
ffuf -request pin.request -request-proto http -w /usr/share/seclists/Fuzzing/4-digits-0000-9999.txt:FUZZ -fw 4
```


[[apache-rocketmq-broker-unauth-10.10.144.31_5000]] is from 2023 and this room is over a year old

Not intended way
https://securityonline.info/cve-2023-33246-apache-rocketmq-remote-code-execution-vulnerability/?utm_content=cmp-true

6443 TCP - Kubernetes API port 
Master Node 

[Hacktricks Pentesting Kubernetes](https://cloud.hacktricks.xyz/pentesting-cloud/kubernetes-security/pentesting-kubernetes-services)

script.js seem benign

Try bypass with: https://github.com/wdahlenburg/werkzeug-debug-console-bypass/tree/main


Securing Kubernetes
https://cheatsheetseries.owasp.org/cheatsheets/Kubernetes_Security_Cheat_Sheet.html

Controlling access to Kubernetes API
https://kubernetes.io/docs/concepts/security/controlling-access/


