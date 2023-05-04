qq# Notes

## Data 

IP: 
OS:
Hostname:
Domain:  / Domain SID:
Machine Purpose: 
Services:
Service Languages:
Users:
Credentials:

## Objectives

## Solution Inventory Map

### Todo 

### Done


## CMD-by-CMDs

```bash
sed -i 's///g' *-CMD-by-CMDs.md
```
      
```bash
echo "10.10.10.221 attended.htb" | sudo tee -a /etc/hosts
# Setup SMTP server with python3 modules
python3 -m smtpd -n -c DebuggingServer 10.10.14.6:25
```