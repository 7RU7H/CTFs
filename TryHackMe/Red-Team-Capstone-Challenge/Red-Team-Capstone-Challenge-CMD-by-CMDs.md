# Red-Team-Capstone-Challenge CMD-by-CMDs

```bash
sed -i 's///g' *-CMD-by-CMDs.md
```

```
ssh e-citizen@10.200.121.250
stabilitythroughcurrency

=======================================
Username: nvm
Password: PvH2VJqhnPMcbc0t
MailAddr: nvm@corp.th3reserve.loc
IP Range: 10.200.121.0/24
=======================================
```

Initial
```bash
curl http://10.200.121.13/october/themes/demo/assets/images/ -o images
cat images| cut -d '"' -f 8 | grep '.jpeg' | sed 's/.jpeg//g' > users.txt
echo "aimee.walker" > users.txt
echo "patrick.edwards" > users.txt

crackmapexec <proto> 10.200.121.0/24 -u '' -p ''

echo "10.200.121.11 mail.thereserve.loc " | sudo tee -a /etc/hosts
echo "10.200.121.12 thereserve.loc " | sudo tee -a /etc/hosts
echo "10.200.121.13 thereserve.loc " | sudo tee -a /etc/hosts
```

