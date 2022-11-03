# Brainfuck
Name: Brainfuck
Date:  26/08/2022
Difficulty:  Insane
Description:  Walkthough
Better Description:  [Ippsec Video](https://www.youtube.com/watch?v=o5x1yg3JnYI)
Goals:  OSCP Prep, Ippsec handholding, but read atleast another method from a written source, if possible try practice on-the-fly python scripting
Learnt: 
- Hosting PHP to request a cookie for my browser to 
- Inspecting Webpage to view dotted password obfuscation in console!


## Recon Pre Video Handheldery

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Brainfuck/Screenshots/ping.png)

Nmap, Certs:
brainfuck.htb
sup3rs3cr3t.brainfuck.htb
![cert](certviewer1.png)


#### brainfuck.htb

Wordpress
orestis@brainfuck.htb
![nuc](nuclei-cve2017-5487.png)

```bash
wpscan --url https://brainfuck.htb -e -o wpscan-brainfuck.htb --disable-tls-checks --api-token $(cat ~/wpscanAPI)
```

There is potential an potential to reset admin's password with email request to controlled email server, then access the Dovecot Pop3 service hosted OR the forum! on Brainfuck or WP get a Web Shell after authenicating into WP.
https://nvd.nist.gov/vuln/detail/CVE-2017-8295



#### sup3rs3cr3t.brainfuck.htb
![usersonforum](usersonforum.png)

![cookies](forumcookies.png)

## Start the Video

Started the [Brainfuck Ippsec Video](https://www.youtube.com/watch?v=o5x1yg3JnYI) as a good hour of scanning and walking each subdomain and testing and gathering information. Paused Video at 6:54, checked [RCE directory exists](https://wpscan.com/vulnerability/85d3126a-34a3-4799-a94b-76d7b835db5f) - Nope.

```bash
grep "includes/admin/attachment/" feroxbuster/brainfuck-raft-small-words
# Nothing..
```
Continued with video, I tried admin@brainfuck.htb to pre-emptively skip a step of trying this exploit. It failed. Changed it to the email found in the Cert and nmap scan.
```html
<form method="post" action="http://wp/wp-admin/admin-ajax.php">
        Username: <input type="text" name="username" value="admin">
        <input type="hidden" name="email" value="admin@brainfuck.htb">
        <input type="hidden" name="action" value="loginGuestFacebook">
        <input type="submit" value="Login">
</form>
```

## Exploit
This was awesome cookie exploit. Simply T(Undescriptive);D(id)R(ead, but hey here is an explanation) the 41006.txt file is that wp_set_auth_cookie can leveraged to force the wp service request the attacker browser set the cookies to the cookies of a logged in user. 

![wow](thatisnuts.png)

Paused the video again and tried to create or edit a wordpress plugin for a web shell; the connection hangs for the former and any changes are lost given that the files on box need to be writable to allow for pasting in a webshell. Continued with the video...

Settings ->  Easy WP SMTP -> 

![](smtpcreds.png)

Never done this before!
```
SMTP:orestis kHGuERB29DNiNE
```
Paused video, research other people OSCP SMTP Cheatsheets to compile into my own. Also want to find a good cli email client that can be scripted instead of evolution. This seemed like a bit of rabbit hole, for another day..

## Returning two days later
![rootsendsmail](rootsendsmail.png)

```
orestis : kIEnnfEKJ#9UmdO
```

Paused here and 
![](cthululanguage.png)

![](sshaccess-noorestis.png)


## Foothold

Best site in the known universe for code-breaking that I found was [boxentriq](https://www.boxentriq.com/). It has analysis tools to attempt to figure out the encoding. Most can perform an autosolve. With autosolve I just changed the defaults to: min key length 1; max key length 16.  It is [Vigenère Cipher](https://www.boxentriq.com/code-breaking/vigenere-cipher), with key  `fuckmybrain`, decoded it is:
```
Hey give me the url for my key bitch

Orestis - Hacking for fun and profit
Vko bwlbbr dgg s zgda nrtkm gy ia...

Toggdxhhu....
Kgivvkr - Ahraeck iqt enu pdz evrhks

There you go you stupid fuck, I hope you remember your key password because I dont
https://10.10.10.17/8ba5aa10e915218697d1c658cdee0bb8/orestis/id_rsa

Sa ewgxcjo, P'hq ngzla wttja nf
Dwwoknu - Owhwxsy bfw hbj fzs ujkwnv
```


Download and crack the SSH key with:
```bash
ssh2john id_rsa > id_rsa_crackable
```

![](crackthesshkey.png)

Remember to `chmod 600 id_rsa`, using the creds:
```
3poulakia!
```

## PrivEsc

Still paused I wanna sharpen some Linux PrivEsc...

![](likesencryption.png)

How random is it...
![](shannon-entropy.png)
Not really potential

![](linux-priv-esc-suggester.png)

Went back to the video was surprise how just google a line of the script revealed it to be RSA, paused it at the creating a script part as I really need to practice on the fly scripting.

[RSA Wiki](https://en.wikipedia.org/wiki/RSA_(cryptosystem)#Operation)
[StackOverFlow](https://stackoverflow.com/questions/58750417/how-to-decrypt-an-rsa-encryption)
[Euler constant with Python](https://datagy.io/python-e-euler/)
[Python Math](https://docs.python.org/3/library/math.html)

p and q are not secret

![notsohard](wikisecurity.png)

Debug.txt contains, both p and q:
```python
p = 7493025776465062819629921475535241674460826792785520881387158343265274170009282504884941039852933109163193651830303308312565580445669284847225535166520307
```

```python
q = 7020854527787566735458858381555452648322845008266612906844847937070333480373963284146649074252278753696897245898433245929775591091774274652021374143174079
```

```python
e = 30802007917952508422792869021689193927485016332713622527025219105154254472344627284947779726280995431947454292782426313255523137610532323813714483639434257536830062768286377920010841850346837238015571464755074669373110411870331706974573498912126641409821855678581804467608824177508976254759319210955977053997
```

```python
n = p*q
52607443949523684204017785703334000062938570714097986630590264896550409660884195770611782961328739044415119649238794734477667608013099487155120771988003786008644669185439528981642994732956155433879097964154609491499493838655719750895219082205272585290719645694003192699681821689790204932179071790474589522253
```


λ(n) = lcm(p − 1, q − 1); lowest common multiples as prime numbers are awesome.
This is the `phi` var in the script.

```python
phi =
52607443949523684204017785703334000062938570714097986630590264896550409660884195770611782961328739044415119649238794734477667608013099487155120771988003771494764364932809973892863137642261832650207296912020821259493213503048069367649430050615158480078856785603105463963127579348618667488619572543565279827868
```

![](python-hot-examples.png)

nbits is the proof to check up to 

Fortran gcd = Returns the greatest common divisor of two integers

math.e

[SageMath](https://stackoverflow.com/questions/15724000/using-sage-math-library-within-python) -> [FAQ](https://doc.sagemath.org/html/en/faq/faq-usage.html)

https://github.com/sagemath/sage/blob/056b8d4e7bcdc936af39a33d3885925f1c850f80/src/sage/groups/additive_abelian/qmodnz.py

Why get the application when we a *"repurpose"* the source code

```python
def random_element(self):

r"""

Return a random element of `\Q/n\Z`.

The denominator is selected

using the ``1/n`` distribution on integers, modified to return

a positive value. The numerator is then selected uniformly.

EXAMPLES::

sage: G = QQ/(6*ZZ)

sage: G.random_element().parent() is G

True

"""

if self.n == 0:

return self(QQ.random_element())

d = ZZ.random_element()

if d >= 0:

d = 2 * d + 1

else:

d = -2 * d

n = ZZ.random_element((self.n * d).ceil())

return self(n / d)
```

ZZ is a interger ring (cyclic array 0->n->0) - which already exists in python as feature of arrays, but this is mathematics so probably has be a class itself because.. 
as abstraction is needed mechanical seperate from the abstration of arrays in python, urm because a philospher of mathematics imagines it as a concept  it then in  logic can be represented and therefore best just rewrite a special int array struct. Sorry these are more notes and python is not my first language so up-abstracting back is weird sometimes

https://mathworld.wolfram.com/Field.html
A rational field is field - an set of numbers that must satify the rules about how each element is computed together  -  of rational numbers, the set must contain all rational numbers
https://mathworld.wolfram.com/FieldofRationals.html

The field axioms are generally written in additive and multiplicative pairs.
https://mathworld.wolfram.com/FieldAxioms.html

asssociativity, communativity, distributivity, identify, inverse

cython -  cdef class are c-like cdef class used in the above source code. So 


n is always 0
https://github.com/sagemath/sage/blob/develop/src/sage/rings/rational_field.py

meaning everything is always 0th elem of phi


Which also means the while loop [gcd part](https://www.geeksforgeeks.org/gcd-in-python/)
```python 

while gcd(e, phi) != 1:
    e = ZZ.random_element(phi)
# is ACTUALLY
while gcd(5, phi) != 1:
	e = phi_as_integer_ring.random_element(phi)
# 
```


![five](fiveisthecorrectnumber.png)


So back to output.txt for M which is root.txt
```
44641914821074071930297814589851746700593470770417111804648920018396305246956127337150936081144106405284134845851392541080862652386840869768622438038690803472550278042463029816028777378141217023336710545449512973950591755053735796799773369044083673911035030605581144977552865771395578778515514288930832915182


password above = (M(root.txt),  e=5, n=52607443949523684204017785703334000062938570714097986630590264896550409660884195770611782961328739044415119649238794734477667608013099487155120771988003786008644669185439528981642994732956155433879097964154609491499493838655719750895219082205272585290719645694003192699681821689790204932179071790474589522253 

# inverse pow( , E , MOD is N) 
# Password is used as base, E as Expression and N is mod
```

"solving discre logs modulo 2N is easy"
[stackexchange](https://crypto.stackexchange.com/questions/68937/finding-a-base-of-an-exponent-in-mod-field#68939)

And the proper way to do it
[maths stackexchange](https://math.stackexchange.com/questions/1962368/how-do-i-calculate-the-base-of-an-exponent-if-i-know-the-result-and-what-it-was)

reRead [pow](https://docs.python.org/3.8/library/functions.html#pow)

[dcodefr](https://www.dcode.fr/modular-inverse)

[Convert to hex](https://www.rapidtables.com/convert/number/decimal-to-hex.html)
```
31564466369714210522410671422000400037763142428458791978354158937930245796530517462367069776797243426649071789543276840686600564807859692293072463192802271605186801511263717388985796839773693260327458778492765694899696303193431850537131449323163551174431787416401915619809093013874122959307443074284753713352

2CF304CF5E4644C8BD57665B1E0ABD61BBE80DA29357318727EF2411A165B30D0F1C5530D56C2689ABD7E3A6B76ED1ECBB0B537833BD6F4F2DF6B6BF9A8D793F0F21F6B36263A27912CF451DED912CE457A42E9D8173FE70813295B089C0282B310E676163BE485D6DC80DF957DCEAEC2CCD9EF24155892F2A552D2BA826F8C8
```

seems to long for a flag.. ran out of time 2.56 hours... watch the rest of walkthrough.
Ok I went deep off trying everything myself. Did not see stackexchange script. If I had thought of the larger picture at some point. E in the MY NOTES!!

[RSA given q p and e](https://crypto.stackexchange.com/questions/19444/rsa-given-q-p-and-e)

Then just python3 
```python3

    hex_pt = str(hex(pt)[2:])
    root = bytearray.fromhex(hex_pt)

    print(f"{root.decode()}")
```