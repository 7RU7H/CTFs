# Notes

## Data 

IP: 10.129.163.128
OS: Ubuntu bionic
Hostname:
Domain:  ssa.htb
Machine Purpose: 
Services:
Service Languages:
Users:
Credentials:

## Objectives

## Solution Inventory Map

### Todo 

nginx 1.18
- https://redfoxsec.com/blog/nginx-zero-day-vulnerability/ if ldap then not vuln
- nginxpwner !

flask - Python!

https://ssa.htb/contact
https://ssa.htb/guide
pgp encrypt shell code to ping to box with cmdi

https://ssa.htb/process

- Fields to control
	- https://ssa.htb/guide
		- Decrypt message - /guide `encrypted_text=asd` 
		- Encrypt message  - /guide/encrypt - `pub_key=asd`
		- Verify signature - public key and signed text - /process `signed_text=asd&public_key=asd`
	- https://ssa.htb/contact
		- Encrypted text - /contact `encrypted_text=X`


Our verification to decrypt there messages - original misread as this is 
```c
-----BEGIN PGP SIGNATURE-----

iQIzBAEBCAAdFiEE1rqUIwIaCDnMxvPIxh1CkRC2JdQFAmRT2bsACgkQxh1CkRC2
JdTCLhAAqdOcrfOsmkffKwdDKATwEpW1aLXkxYoklkH+DCDc58FgQYDNunMQvXjp
Hd41hbrzQNTm4mMwFfYgFR5oNywAfa0D5L+qTrk05DqwvT7uIZF4/Q/iNp8zElKM
rAVci7c6dBSKLzyGOd7c7/ZnM3Clt4krPGD3L4nQB1Vu7Hav8Oj0R2bL3eaNr0sL
lnj84lbWcGqM9sRfnhfSlpqueK0qbZy02PdzsZ/Ox6KI6s1lAZL5v4eynwrZjXVB
S20SeQAzPr+2m0LGQajPaxYvdEs4BkfyApwzauES0X3bckKdZrXZb/iImQxTrhDq
ZKwGG2qoa6xj4zV32l2JYLKfCcZQh/VE3pslfYb5btuZ/h+oSz6rWT0py/gigbX5
j4ps6gzS16uuVLePyw6kZN2tXgqWqR9IRydCqFJSajwanm9n1I99DH+r9vb7CYOI
PFKwIqynqNX6ddpkCpUl3wgGnUoGdox5DLnE7DZGFM4mOhsNuwd8EBgpU18inFb2
MMZO8Qk5bp2qsK9b4LFWDMEL+pzakgJwA1+H5auJLOak+Xee7UcYeqsq1fjpkwIX
mItshTOG0jrtlvAf/PjqZ54yOPCWoyJQr5ZR7m4bh/kicXZVg5OiWrtVCuN0iUlD
7sXs10Js/pgvZfA6xFipfvs7W+lOQ0febeNmjuKcGk0VVewv8oc=
=/yGe
-----END PGP SIGNATURE-----
```


```c
SSA <atlas@ssa.htb>  
Key ID: D6BA9423021A0839CCC6F3C8C61D429110B625D4
```

```bash
# For GUI manager
sudo apt-get install gnupg2 gpa
```


```bash
# generate a key
gpg --full-generate-key
# Provide answer for the options
# Key type
# Key size
# Expiration
# Name
# Email




gpg --edit-key D6BA9423021A0839CCC6F3C8C61D429110B625D4

# Import public key 
gpg --import public.key

gpg --keyserver key_server --refresh-keys


gpg --list-keys

# encrypt to text file
gpg --encrypt --sign --armor -r person@email.com name_of_file

# encrypt file contents to a encrypted file
cat file.txt | gpg -e -r KEYID > message.txt.gpg

# Decrypting
gpg -d -o secret.txt secret.txt.gpg
# Omitting `-o|--output` will print the unencrypted contents to `stdout`

```
https://www.youtube.com/watch?v=CEADq-B8KtI
https://linux.die.net/man/1/gpg
https://www.digitalocean.com/community/tutorials/how-to-use-gpg-to-encrypt-and-sign-messages
https://devhints.io/gnupg
https://git.gnupg.org/cgi-bin/gitweb.cgi?p=gnupg.git;a=blob_plain;f=doc/DETAILS

```bash
curl https://ssa.htb/pgp -o public.key
gpg --import public.key
echo -n "my secret message" | gpg -e --text -r D6BA9423021A0839CCC6F3C8C61D429110B625D4
```

GPA clipboard then came with the ease of not have to make text files just to then clearsign them
```
```


learnthepgp.png

[Message in PGP hurry!](https://www.youtube.com/watch?v=4m3CMTeo-nU)
pgpmessage.png


pingmessages.png

[For your eyes only pinging your eyes till Roger Moore starts acting like an RCE](https://www.youtube.com/watch?v=RkZf-Hfqx_A)
```bash
sudo tcpdump -i tn0 icmp
```



Another hilarious song in a [James Bond](https://www.youtube.com/watch?v=pq18mx3irtg) that make KEKW far too hard given the juxtaposition of tension for Q making food for someone and 007 going Daniel-Ape-\X-Craig. 


[Spy Harder](https://www.youtube.com/watch?v=F7KMMxAQp8I) into a reverse shell

### Done

```
;bash -c 'ping -c 3 10.10.14.106'
&& bash -c 'ping -c 3 10.10.14.106'
| bash -c 'ping -c 3 10.10.14.106'
```

```
-----BEGIN+PGP+MESSAGE-----%0D%0A%0D%0AhQIMA2u3M9ko0UzmAQ%2F%2FREeR7OHvVvYDXgjy7FFKP3hAoHhW50F5GoMyXRlepxqa%0D%0ASkGwe7X18rJW20gc9WGOQXJC6sp%2B6wQ5i26zt73TstIDd0G3xjlZD0WIypzIYdNE%0D%0A0nnOx5ZkV4hAKkEmWbJN418W5%2Bu2NvyGMe%2BKZHypmGZEDNu%2BG4qCltiQKwG2ksA4%0D%0Arg5Tr%2B70Q16nCbMejJkVNDRQNndHegtMdRjAEwiPKX2wa%2BuxpI6FzDiiVV49xKfR%0D%0Au8pK0FUEmjuLrwfd34Kc9Hw6P9%2FIzoGu3uFOCUYi2rcdX4Jj24Cl8eQAjqSADcZs%0D%0ApeKv0qwv8j86Kf2udRZjQs65N1aLSEfLOE9dLCAY0RvbcmHKHpKymX9QPTwxcD9%2B%0D%0AyrXPRDvIX4peL4bpHFkX4pxtogQzJox3C1U%2BMcNjkRtVlL3q9jiAlln%2FIYed3vq6%0D%0AIU4gVgsPxcQzkipRvHrG2osTcHB1P7CSll6GePSPDS7YboEAPX13nAG%2BH7uQbpz1%0D%0AJ5jaXfje1wbn%2B309Huxp2L5eYKBB8LX7jypbgbr64ZyHJtecWjfhvVZD37B8%2BAen%0D%0ActK8Uio6C%2BUM7AWESgJUaDGL0ZUhYUQL9pC%2BwH4KoulNDojhOowhg5cE4Gc1nfH9%0D%0AfHse7oS89iqnoDJ7Q7ov78Wkw2jIzeBlErp6DEBH2evYfZvoOsEqUjVwPdEk6%2BPS%0D%0AWQEOLb2DS3Lgmkh8UsOFb4HkbGp%2FEGupbAgM7wJa8iAkYw7hrVEL0gx9VZ6KLsD8%0D%0AEzWBnnlfeY9VDG2aof2XTLgUt0pUMHc9B6CAMf1a8IwOJ0wLrvkvFhbG%0D%0A%3Dkpzj%0D%0A-----END+PGP+MESSAGE-----%0D%0A
```

Consider how the application works on the ssa.htb end:
- Decode URL for `\r\n=+/`]
- **EXPLOIT** when  handles the parametre 
- Checks if it is a key
- Either
	- it is saving to a file then decrypting - `gpg --decrypt filename.txt.gpg`
	- decrypting a stream
- [Flask - hacktricks](https://book.hacktricks.xyz/network-services-pentesting/pentesting-web/flask) so could be a [SSTI - Hacktricks](https://book.hacktricks.xyz/pentesting-web/ssti-server-side-template-injection)

- **EXPLOIT** when reflects the content

There is also filtering of quotes! Another big indicator of SSTI
```
| bash -c &#39;ping -c 3 10.10.14.106&#39;
```

```
{{7*7}}
${7*7}
<%= 7*7 %>
${{7*7}}
#{7*7}
*{7*7}
```

Went back to the [hint](https://forum.hackthebox.com/t/official-sandworm-discussion/286654/56). It is *" what fields in your key-pair you can control and which you can see later on the web page"*

- Part our key pair has to break parsing it and execute, which is why we need ssa handed out private key 

We may need that to decrypt
```
-----BEGIN PGP SIGNATURE-----

iQIzBAEBCAAdFiEE1rqUIwIaCDnMxvPIxh1CkRC2JdQFAmRT2bsACgkQxh1CkRC2
JdTCLhAAqdOcrfOsmkffKwdDKATwEpW1aLXkxYoklkH+DCDc58FgQYDNunMQvXjp
Hd41hbrzQNTm4mMwFfYgFR5oNywAfa0D5L+qTrk05DqwvT7uIZF4/Q/iNp8zElKM
rAVci7c6dBSKLzyGOd7c7/ZnM3Clt4krPGD3L4nQB1Vu7Hav8Oj0R2bL3eaNr0sL
lnj84lbWcGqM9sRfnhfSlpqueK0qbZy02PdzsZ/Ox6KI6s1lAZL5v4eynwrZjXVB
S20SeQAzPr+2m0LGQajPaxYvdEs4BkfyApwzauES0X3bckKdZrXZb/iImQxTrhDq
ZKwGG2qoa6xj4zV32l2JYLKfCcZQh/VE3pslfYb5btuZ/h+oSz6rWT0py/gigbX5
j4ps6gzS16uuVLePyw6kZN2tXgqWqR9IRydCqFJSajwanm9n1I99DH+r9vb7CYOI
PFKwIqynqNX6ddpkCpUl3wgGnUoGdox5DLnE7DZGFM4mOhsNuwd8EBgpU18inFb2
MMZO8Qk5bp2qsK9b4LFWDMEL+pzakgJwA1+H5auJLOak+Xee7UcYeqsq1fjpkwIX
mItshTOG0jrtlvAf/PjqZ54yOPCWoyJQr5ZR7m4bh/kicXZVg5OiWrtVCuN0iUlD
7sXs10Js/pgvZfA6xFipfvs7W+lOQ0febeNmjuKcGk0VVewv8oc=
=/yGe
-----END PGP SIGNATURE-----
```

Meaning we can crontol the name and email
```bash
gpg --full-generate-key
# Provide answer for the options
# Key type
# Key size
# Expiration
# Name
# Email
gpg --list-secret-keys
gpg -o key.asc --armor --export 5D3EE7C22717B054013064769A3D8E44316F4F5F
```

This definitely a template message so I tried twice once with cmdi, then ssti and then ssti all the fields  
```python
This is an encrypted message for {{7*7}} (49) <49@49.htb>.

If you can read this, it means you successfully used your private PGP key to decrypt a message meant for you and only you.

Congratulations! Feel free to keep practicing, and make sure you also know how to encrypt, sign, and verify messages to make your repertoire complete.

SSA: 06/18/2023-13;40;52
```

My generation
proof.png

and proof
sstiintheemail.png

But further testing 
Not Jinja2 or Twig

- Add disclaimer at the top
[Hints used](https://forum.hackthebox.com/t/official-sandworm-discussion/286654/56)
- Hint for user: as already mentioned earlier check what fields in your key-pair you can control and which you can see later on the web page
- escape a linux jail
- process and create link - hopefully the intended way


https://www.google.com/search?client=firefox-b-e&q=Sandworm+exploit
