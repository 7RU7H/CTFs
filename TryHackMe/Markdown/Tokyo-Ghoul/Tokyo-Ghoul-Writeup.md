Name:  Tokyo Ghoul
Date:  07/05/2022
Difficulty: Medium
Description: Help kaneki escape jason room 
Better Description:  Not a weeb or a man of culture sorry.
Goals: 
Learnt:
 
```bash
root@kali:~# nmap -sC -sV -p- 10.10.72.49
Starting Nmap 7.80 ( https://nmap.org ) at 2022-04-11 17:16 UTC
Nmap scan report for ip-10-10-72-49.eu-west-1.compute.internal (10.10.72.49)
Host is up (0.0014s latency).
Not shown: 65532 closed ports
PORT   STATE SERVICE VERSION
21/tcp open  ftp     vsftpd 3.0.3
| ftp-anon: Anonymous FTP login allowed (FTP code 230)
|_drwxr-xr-x    3 ftp      ftp          4096 Jan 23  2021 need_Help?
| ftp-syst: 
|   STAT: 
| FTP server status:
|      Connected to ::ffff:10.10.164.83
|      Logged in as ftp
|      TYPE: ASCII
|      No session bandwidth limit
|      Session timeout in seconds is 300
|      Control connection is plain text
|      Data connections will be plain text
|      At session startup, client count was 3
|      vsFTPd 3.0.3 - secure, fast, stable
|_End of status
22/tcp open  ssh     OpenSSH 7.2p2 Ubuntu 4ubuntu2.10 (Ubuntu Linux; protocol 2.0)
| ssh-hostkey: 
|   2048 fa:9e:38:d3:95:df:55:ea:14:c9:49:d8:0a:61:db:5e (RSA)
|   256 ad:b7:a7:5e:36:cb:32:a0:90:90:8e:0b:98:30:8a:97 (ECDSA)
|_  256 a2:a2:c8:14:96:c5:20:68:85:e5:41:d0:aa:53:8b:bd (ED25519)
80/tcp open  http    Apache httpd 2.4.18 ((Ubuntu))
|_http-server-header: Apache/2.4.18 (Ubuntu)
|_http-title: Welcome To Tokyo goul
MAC Address: 02:2B:42:8C:6F:51 (Unknown)
Service Info: OSs: Unix, Linux; CPE: cpe:/o:linux:linux_kernel

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 10.05 seconds

root@ip-10-10-234-25:~# nikto -h 10.10.56.47 -C all
- Nikto v2.1.5
---------------------------------------------------------------------------
+ Target IP:          10.10.56.47
+ Target Hostname:    ip-10-10-56-47.eu-west-1.compute.internal
+ Target Port:        80
+ Start Time:         2022-05-07 20:54:29 (GMT1)
---------------------------------------------------------------------------
+ Server: Apache/2.4.18 (Ubuntu)
+ Server leaks inodes via ETags, header found with file /, fields: 0x586 0x5b998cf4b5faf 
+ The anti-clickjacking X-Frame-Options header is not present.
+ Allowed HTTP Methods: POST, OPTIONS, GET, HEAD 
+ OSVDB-3233: /icons/README: Apache default file found.
+ 6544 items checked: 0 error(s) and 4 item(s) reported on remote host
+ End Time:           2022-05-07 20:55:08 (GMT1) (39 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested

root@ip-10-10-234-25:~# gobuster dir -u http://10.10.56.47 -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt 
===============================================================
Gobuster v3.0.1
by OJ Reeves (@TheColonial) & Christian Mehlmauer (@_FireFart_)
===============================================================
[+] Url:            http://10.10.56.47
[+] Threads:        10
[+] Wordlist:       /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt
[+] Status codes:   200,204,301,302,307,401,403
[+] User Agent:     gobuster/3.0.1
[+] Timeout:        10s
===============================================================
2022/05/07 21:15:29 Starting gobuster
===============================================================
/css (Status: 301)
/server-status (Status: 403)
===============================================================
2022/05/07 21:15:51 Finished
===============================================================

```

wget -r ftp://10.10.56.47
Lots names:
Rize
Jason
Kaneki


## http
Lots of discussion of immasculation through torture and dismemberment and the alogorical teenage-adult roles in "use"  in the material sense...anime...

root and /jasonroom.html have this comment:
```html
<-- look don't tell jason but we will help you escape we will give you the key to open those chains and here is some clothes to look like us and a mask to look anonymous and go to the ftp room right there -->
```

chmod +x the ELF `need_to_talk` binary. Strings:

```
u/UH
You_founH
d_1t
[]A\A]A^A_
kamishiro
Hey Kaneki finnaly you want to talk 
Unfortunately before I can give you the kagune you need to give me the paraphrase
Do you have what I'm looking for?
Good job. I believe this is what you came for:
Hmm. I don't think this is what I was looking for.
Take a look inside of me. rabin2 -z
;*3$"
GCC: (Debian 9.3.0-15) 9.3.0
crtstuff.c
...
the_password

```


```radare2
root@ip-10-10-234-25:~/10.10.56.47/need_Help?/Talk_with_me# r2 -d need_to_talk 
Process with PID 10471 started...
= attach 10471 10471
bin.baddr 0x55c8fc08a000
Using 0x55c8fc08a000
asm.bits 64
[0x7f7e77d24090]> aaa
[x] Analyze all flags starting with sym. and entry0 (aa)
[x] Analyze len bytes of instructions for references (aar)
[x] Analyze function calls (aac)
[x] Use -AA or aaaa to perform additional experimental analysis.
[x] Constructing a function name for fcn.* and sym.func.* functions (aan)
= attach 10471 10471
10471
[0x7f7e77d24090]> pdf @main
            ;-- main:
/ (fcn) main 88
|   main ();
|              ; DATA XREF from 0x55c8fc08b10d (entry0)
|           0x55c8fc08b1d5      55             push rbp
|           0x55c8fc08b1d6      4889e5         mov rbp, rsp
|           0x55c8fc08b1d9      b800000000     mov eax, 0
|           0x55c8fc08b1de      e84a000000     call sym.print_intro
|           0x55c8fc08b1e3      b800000000     mov eax, 0
|           0x55c8fc08b1e8      e8fc000000     call sym.check_password
|           0x55c8fc08b1ed      85c0           test eax, eax
|       ,=< 0x55c8fc08b1ef      741d           je 0x55c8fc08b20e
|       |   0x55c8fc08b1f1      488d3dc80e00.  lea rdi, qword str.Good_job._I_believe_this_is_what_you_came_for: ; 0x55c8fc08c0c0 ; "Good job. I believe this is what you came for:\n"
|       |   0x55c8fc08b1f8      e895000000     call sym.slow_type
|       |   0x55c8fc08b1fd      b800000000     mov eax, 0
|       |   0x55c8fc08b202      e85f010000     call sym.print_flag
|       |   0x55c8fc08b207      b800000000     mov eax, 0
|      ,==< 0x55c8fc08b20c      eb1d           jmp 0x55c8fc08b22b
|      |`-> 0x55c8fc08b20e      488d3ddb0e00.  lea rdi, qword str.Hmm._I_don_t_think_this_is_what_I_was_looking_for. ; 0x55c8fc08c0f0 ; "Hmm. I don't think this is what I was looking for.\n"
|      |    0x55c8fc08b215      e878000000     call sym.slow_type
|      |    0x55c8fc08b21a      488d3d070f00.  lea rdi, qword str.Take_a_look_inside_of_me._rabin2__z ; 0x55c8fc08c128 ; "Take a look inside of me. rabin2 -z\n"
|      |    0x55c8fc08b221      e86c000000     call sym.slow_type
|      |    0x55c8fc08b226      b801000000     mov eax, 1
|      |       ; JMP XREF from 0x55c8fc08b20c (main)
|      `--> 0x55c8fc08b22b      5d             pop rbp
\           0x55c8fc08b22c      c3             ret
[0x7f7e77d24090]> pdf @sym.print_intro
/ (fcn) sym.print_intro 101
|   sym.print_intro ();
|           ; var int local_8h @ rbp-0x8
|           ; var int local_4h @ rbp-0x4
|              ; CALL XREF from 0x55c8fc08b1de (main)
|           0x55c8fc08b22d      55             push rbp
|           0x55c8fc08b22e      4889e5         mov rbp, rsp
|           0x55c8fc08b231      4883ec10       sub rsp, 0x10
|           0x55c8fc08b235      488b05742e00.  mov rax, qword [obj.stdout] ; [0x55c8fc08e0b0:8]=0
|           0x55c8fc08b23c      be00000000     mov esi, 0
|           0x55c8fc08b241      4889c7         mov rdi, rax
|           0x55c8fc08b244      e827feffff     call sym.imp.setbuf     ; void setbuf(FILE *stream,
|           0x55c8fc08b249      c745f8030000.  mov dword [local_8h], 3
|           0x55c8fc08b250      c745fc000000.  mov dword [local_4h], 0
|       ,=< 0x55c8fc08b257      eb24           jmp 0x55c8fc08b27d
|      .--> 0x55c8fc08b259      8b45fc         mov eax, dword [local_4h]
|      :|   0x55c8fc08b25c      4898           cdqe
|      :|   0x55c8fc08b25e      488d14c50000.  lea rdx, qword [rax*8]
|      :|   0x55c8fc08b266      488d05232e00.  lea rax, qword obj.dialogs ; 0x55c8fc08e090
|      :|   0x55c8fc08b26d      488b0402       mov rax, qword [rdx + rax]
|      :|   0x55c8fc08b271      4889c7         mov rdi, rax
|      :|   0x55c8fc08b274      e819000000     call sym.slow_type
|      :|   0x55c8fc08b279      8345fc01       add dword [local_4h], 1
|      :|      ; JMP XREF from 0x55c8fc08b257 (sym.print_intro)
|      :`-> 0x55c8fc08b27d      8b45fc         mov eax, dword [local_4h]
|      :    0x55c8fc08b280      3b45f8         cmp eax, dword [local_8h]
|      `==< 0x55c8fc08b283      7cd4           jl 0x55c8fc08b259
|           0x55c8fc08b285      bf01000000     mov edi, 1
|           0x55c8fc08b28a      e831feffff     call sym.imp.sleep      ; int sleep(int s)
|           0x55c8fc08b28f      90             nop
|           0x55c8fc08b290      c9             leave
\           0x55c8fc08b291      c3             ret
[0x7f7e77d24090]> pdf @sym.check_password
/ (fcn) sym.check_password 125
|   sym.check_password ();
|           ; var int local_100h @ rbp-0x100
|              ; CALL XREF from 0x55c8fc08b1e8 (main)
|           0x55c8fc08b2e9      55             push rbp
|           0x55c8fc08b2ea      4889e5         mov rbp, rsp
|           0x55c8fc08b2ed      4881ec000100.  sub rsp, 0x100
|           0x55c8fc08b2f4      488d3d520e00.  lea rdi, qword [0x55c8fc08c14d] ; "> "
|           0x55c8fc08b2fb      b800000000     mov eax, 0
|           0x55c8fc08b300      e87bfdffff     call sym.imp.printf     ; int printf(const char *format)
|           0x55c8fc08b305      488b15b42d00.  mov rdx, qword [obj.stdin] ; [0x55c8fc08e0c0:8]=0
|           0x55c8fc08b30c      488d8500ffff.  lea rax, qword [local_100h]
|           0x55c8fc08b313      beff000000     mov esi, 0xff           ; 255
|           0x55c8fc08b318      4889c7         mov rdi, rax
|           0x55c8fc08b31b      e870fdffff     call sym.imp.fgets      ; char *fgets(char *s, int size, FILE *stream)
|           0x55c8fc08b320      488d8500ffff.  lea rax, qword [local_100h]
|           0x55c8fc08b327      4889c7         mov rdi, rax
|           0x55c8fc08b32a      e831fdffff     call sym.imp.strlen     ; size_t strlen(const char *s)
|           0x55c8fc08b32f      4883e801       sub rax, 1
|           0x55c8fc08b333      c6840500ffff.  mov byte [rbp + rax - 0x100], 0
|           0x55c8fc08b33b      488b153e2d00.  mov rdx, qword obj.the_password ; [0x55c8fc08e080:8]=0x2008
|           0x55c8fc08b342      488d8500ffff.  lea rax, qword [local_100h]
|           0x55c8fc08b349      4889d6         mov rsi, rdx
|           0x55c8fc08b34c      4889c7         mov rdi, rax
|           0x55c8fc08b34f      e84cfdffff     call sym.imp.strcmp     ; int strcmp(const char *s1, const char *s2)
|           0x55c8fc08b354      85c0           test eax, eax
|       ,=< 0x55c8fc08b356      7507           jne 0x55c8fc08b35f
|       |   0x55c8fc08b358      b801000000     mov eax, 1
|      ,==< 0x55c8fc08b35d      eb05           jmp 0x55c8fc08b364
|      |`-> 0x55c8fc08b35f      b800000000     mov eax, 0
|      |       ; JMP XREF from 0x55c8fc08b35d (sym.check_password)
|      `--> 0x55c8fc08b364      c9             leave
\           0x55c8fc08b365      c3             ret
```
Switched to Ghidra because I don't have time for reversing at the moment
...
Hey Kaneki finnaly you want to talk 
Unfortunately before I can give you the kagune you need to give me the paraphrase
Do you have what I'm looking for?

Good job. I believe this is what you came for:
You_found_1t
```

