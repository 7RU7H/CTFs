# Red-Team-Capstone-Challenge-Credentials


```bash
[25][smtp] host: mail.thereserve.loc   login: laura.wood@corp.thereserve.loc   password: Password1@
[25][smtp] host: mail.thereserve.loc   login: mohammad.ahmed@corp.thereserve.loc   password: Password1!
```

```
admin : password1!
lisa.moore : Scientist2006
```


```
SMB         10.200.117.31   445    SERVER1          CORP.THERESERVE.LOC/Administrator:$DCC2$10240#Administrator#b08785ec00370a4f7d02ef8bd9b798ca
SMB         10.200.117.31   445    SERVER1          CORP.THERESERVE.LOC/svcScanning:$DCC2$10240#svcScanning#d53a09b9e4646451ab823c37056a0d6b
SMB         10.200.117.31   445    SERVER1          CORP\SERVER1$:aes256-cts-hmac-sha1-96:f16f08c1ecbec4aefaeed138fe19a350d25dc79ee574e9b2ec824bcb8bd895ae
SMB         10.200.117.31   445    SERVER1          CORP\SERVER1$:aes128-cts-hmac-sha1-96:ffc3ff1485f10fa447eec35db4175b8f
SMB         10.200.117.31   445    SERVER1          CORP\SERVER1$:des-cbc-md5:514c5be6a2f7a298
SMB         10.200.117.31   445    SERVER1          CORP\SERVER1$:plain_password_hex:b7c8375454a3c7331e6d2bca1612421c7820eb3ad08438baa869aea9680861b1cba919defae2ed3973fe8a0cba087383906b0d0220d14c8eab7ac29aa0b20fd2d8285cb52154a3d24385b514f666474ef4f6d62301a82201c3f33af12990c64b0171fd6a6f94e2e8e01f9458f2ae0d77dfb77acf67ebb83ab8e9592d3fcb467e41b429b925301d3e4177ea3b935333e44ab911eb0df5549f80cb26b9d0d3224ae8335279ce24c0376d34b838022e8321f7334e910faf93bfc2fdd30b34cd1e6eb6a569ecf3ca2211a66efffd35813477e7815b3341d128a7debe2a68b03a5f9d438699d77bc6f520ff846bbbad872803
SMB         10.200.117.31   445    SERVER1          CORP\SERVER1$:aad3b435b51404eeaad3b435b51404ee:ee0b312ba706c567436e6a9e08fa3951:::
SMB         10.200.117.31   445    SERVER1          dpapi_machinekey:0xb4cfb5032a98c1b279c92264915da1fd3d8b1a0d
dpapi_userkey:0x3cddfc2ba786e51edf1c732a21ffa1f3d19aa382
SMB         10.200.117.31   445    SERVER1          NL$KM:8dd28e67545889b1c953b95b46a2b366d43b9580927d6778b71df92da555b7a361aa4d8695854386e3129ec491cf9a5bd8bb0daefad341e0d8663d1975a2d1b2
SMB         10.200.117.31   445    SERVER1          svcBackups@corp.thereserve.loc:q9nzssaFtGHdqUV3Qv6G
```



corpUserrname.ovpn
```
client
dev tun
proto tcp
sndbuf 0
rcvbuf 0
remote 10.200.121.12.117
resolv-retry infinite
nobind
persist-key
persist-tun
remote-cert-tls server
auth SHA512
data-ciphers AES-256-CBC
key-direction 1
verb 3
<ca>
-----BEGIN CERTIFICATE-----
MIIDQjCCAiqgAwIBAgIUMgz4AevMs5WaCN3J0W7jSNaq4bswDQYJKoZIhvcNAQEL
BQAwEzERMA8GA1UEAwwIQ2hhbmdlTWUwHhcNMjAwNzA4MjAwMjUxWhcNMzAwNzA2
MjAwMjUxWjATMREwDwYDVQQDDAhDaGFuZ2VNZTCCASIwDQYJKoZIhvcNAQEBBQAD
ggEPADCCAQoCggEBANjykMrmDPlnJ1PwDvoXL5gE1mh5ukaOCuo/wbuvEBoRzCrs
ckoJewL+4vzja00J4Qi/IyQfVkW96GgrTAL0u9fXTQEwFDQrq1PnFPE9qmsPouMc
eQk9WQUd61t3smvmTPRtmPHsQIMSXx9XHbZCkBFZqbj0TVZ3CZEa0vhKnyZW0B+K
7enLA25tOsVNf2YGBAl7mEXbZv40gxN0pQJp66qRRxQ/kiehJDQlW4kBar77EzhC
zZhTYydDW6agirhspaJ1HOEm9nuoYkKVanHApdz5eJHYlkCSmbEAA0pDOnCf56l6
xZPfSciJf2nVzVDCcp530Qs3ilruoUZvzGzFYl8CAwEAAaOBjTCBijAdBgNVHQ4E
FgQUhK1M9hDxJtsXX827sgiXWMb1zJswTgYDVR0jBEcwRYAUhK1M9hDxJtsXX827
sgiXWMb1zJuhF6QVMBMxETAPBgNVBAMMCENoYW5nZU1lghQyDPgB68yzlZoI3cnR
buNI1qrhuzAMBgNVHRMEBTADAQH/MAsGA1UdDwQEAwIBBjANBgkqhkiG9w0BAQsF
AAOCAQEAZEdAzg6zDIZ6W53OnscORu9GjSTwlx5tg9y/onsxfluJcDypXyoemkZR
bpO+QrROuZaglX2oMbTSvQjuZLWeV49+8X5d+iTSJt0qSSNoxsFiaoN2RjqFhzEU
rJKGonDC40qRYgKhhqlRr5R5ytffEM7Z1Vd14BkpxHyJnhuxN7QrdNlYHU7AxZtC
DagVRK5nubTk3xJT/kGBieMbPIc3fIftAG9ddRjH7r5Hc7l1516aB47ALTbGdc9o
GQZvr4XvOa287vlpaJM0yTk/Y6rex2bCnc3e6zl3MYOsxGKh8fsmzPLILPLvTvQ3
+RZhpjf8JeNNrAe6PZhyIRLO7GkQ3g==
-----END CERTIFICATE-----
</ca>
<cert>
Certificate:
    Data:
        Version: 3 (0x2)
        Serial Number:
            25:74:28:af:9e:5b:47:c7:35:52:b4:e9:6a:0b:df:21
        Signature Algorithm: sha256WithRSAEncryption
        Issuer: CN=ChangeMe
        Validity
            Not Before: Feb 15 18:35:52 2023 GMT
            Not After : Feb 12 18:35:52 2033 GMT
        Subject: CN=temp4
        Subject Public Key Info:
            Public Key Algorithm: rsaEncryption
                RSA Public-Key: (2048 bit)
                Modulus:
                    00:f7:75:26:95:08:43:93:3f:be:a7:57:02:b1:47:
                    e5:3a:96:b9:b0:bb:52:1b:78:92:c4:27:79:36:96:
                    62:fe:8f:2f:34:91:1f:96:d1:9e:7e:36:a4:72:ce:
                    6d:25:50:88:d9:fe:61:6f:0f:6a:d9:26:1c:69:9f:
                    ce:4f:02:dc:e8:5d:6d:1b:91:c4:8b:03:e8:7d:95:
                    90:7e:5f:84:b4:d7:e8:d8:ad:4d:35:46:c0:0e:bf:
                    e2:0f:67:bf:d3:08:72:e4:d1:ff:27:77:e6:c3:20:
                    8f:b6:b3:13:61:2e:2c:79:87:67:da:04:ce:fa:8d:
                    63:f6:d3:fe:0b:64:13:a4:01:12:be:3b:9e:af:32:
                    12:a0:4a:87:fd:97:ea:cb:22:74:b2:72:42:6d:cf:
                    90:82:19:57:9c:9d:e8:ad:b5:e7:b3:bd:a0:59:54:
                    06:46:bf:1d:da:b1:43:28:29:65:4e:52:de:e4:98:
                    f2:08:99:c0:b3:8c:bb:4f:4d:8b:1a:b1:a5:52:fd:
                    7d:20:fa:00:39:c6:42:7a:16:67:51:1d:5c:f9:76:
                    b3:ec:d4:40:ab:f0:ab:bf:77:99:30:49:34:44:28:
                    a1:be:cf:e0:75:6f:56:a9:2b:06:0f:d5:75:73:9f:
                    86:3b:5a:4e:53:55:39:d6:f0:24:72:25:ca:da:9c:
                    9f:21
                Exponent: 65537 (0x10001)
        X509v3 extensions:
            X509v3 Basic Constraints:
                CA:FALSE
            X509v3 Subject Key Identifier:
                DF:35:51:73:A4:13:FB:61:B9:95:E3:E2:38:88:4C:52:55:84:14:3A
            X509v3 Authority Key Identifier:
                keyid:84:AD:4C:F6:10:F1:26:DB:17:5F:CD:BB:B2:08:97:58:C6:F5:CC:9B
                DirName:/CN=ChangeMe
                serial:32:0C:F8:01:EB:CC:B3:95:9A:08:DD:C9:D1:6E:E3:48:D6:AA:E1:BB

            X509v3 Extended Key Usage:
                TLS Web Client Authentication
            X509v3 Key Usage:
                Digital Signature
    Signature Algorithm: sha256WithRSAEncryption
         1c:00:42:a6:c7:79:8f:52:4c:64:a0:33:a1:da:1c:e5:1c:06:
         7d:10:3e:0b:7c:6e:88:6f:ba:83:44:ea:f7:cd:7f:c9:25:f5:
         20:d3:d8:3a:19:25:41:6f:a3:fe:8a:30:fa:f4:d2:b3:34:7d:
         fd:6f:94:26:e9:2f:6d:fa:03:9b:e7:ef:77:ff:04:27:2a:2d:
         35:d2:58:11:51:70:05:15:99:26:42:f3:43:d5:2f:85:78:a1:
         80:f2:25:80:62:2f:13:ae:43:95:80:1c:37:eb:4c:b6:a9:e9:
         2f:a4:62:48:a3:75:8b:9b:2e:c3:27:ce:df:49:c8:5c:f4:1b:
         a8:87:03:ea:c6:cd:44:4b:fd:ee:af:03:a9:a7:49:f0:b5:b9:
         0e:e2:37:d4:cf:5b:1e:e2:48:35:98:d6:f4:05:da:97:60:30:
         7c:99:fa:c6:10:55:7f:ca:00:be:63:a6:0e:e0:31:d8:bf:fe:
         19:1d:68:ba:9e:cf:fe:22:bc:84:10:46:57:b9:ee:b2:79:95:
         c1:63:73:29:2f:d9:18:62:be:6a:e8:f1:93:9d:60:82:26:bf:
         75:b9:c1:5b:ae:cf:f1:15:17:16:8a:d6:ae:08:4d:fd:60:97:
         21:2d:b3:31:71:d0:1b:d6:46:00:d9:7a:1b:5e:68:56:10:29:
         c4:e6:17:be
-----BEGIN CERTIFICATE-----
MIIDTTCCAjWgAwIBAgIQJXQor55bR8c1UrTpagvfITANBgkqhkiG9w0BAQsFADAT
MREwDwYDVQQDDAhDaGFuZ2VNZTAeFw0yMzAyMTUxODM1NTJaFw0zMzAyMTIxODM1
NTJaMBAxDjAMBgNVBAMMBXRlbXA0MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIB
CgKCAQEA93UmlQhDkz++p1cCsUflOpa5sLtSG3iSxCd5NpZi/o8vNJEfltGefjak
cs5tJVCI2f5hbw9q2SYcaZ/OTwLc6F1tG5HEiwPofZWQfl+EtNfo2K1NNUbADr/i
D2e/0why5NH/J3fmwyCPtrMTYS4seYdn2gTO+o1j9tP+C2QTpAESvjuerzISoEqH
/ZfqyyJ0snJCbc+QghlXnJ3orbXns72gWVQGRr8d2rFDKCllTlLe5JjyCJnAs4y7
T02LGrGlUv19IPoAOcZCehZnUR1c+Xaz7NRAq/Crv3eZMEk0RCihvs/gdW9WqSsG
D9V1c5+GO1pOU1U51vAkciXK2pyfIQIDAQABo4GfMIGcMAkGA1UdEwQCMAAwHQYD
VR0OBBYEFN81UXOkE/thuZXj4jiITFJVhBQ6ME4GA1UdIwRHMEWAFIStTPYQ8Sbb
F1/Nu7IIl1jG9cyboRekFTATMREwDwYDVQQDDAhDaGFuZ2VNZYIUMgz4AevMs5Wa
CN3J0W7jSNaq4bswEwYDVR0lBAwwCgYIKwYBBQUHAwIwCwYDVR0PBAQDAgeAMA0G
CSqGSIb3DQEBCwUAA4IBAQAcAEKmx3mPUkxkoDOh2hzlHAZ9ED4LfG6Ib7qDROr3
zX/JJfUg09g6GSVBb6P+ijD69NKzNH39b5Qm6S9t+gOb5+93/wQnKi010lgRUXAF
FZkmQvND1S+FeKGA8iWAYi8TrkOVgBw360y2qekvpGJIo3WLmy7DJ87fSchc9Buo
hwPqxs1ES/3urwOpp0nwtbkO4jfUz1se4kg1mNb0BdqXYDB8mfrGEFV/ygC+Y6YO
4DHYv/4ZHWi6ns/+IryEEEZXue6yeZXBY3MpL9kYYr5q6PGTnWCCJr91ucFbrs/x
FRcWitauCE39YJchLbMxcdAb1kYA2XobXmhWECnE5he+
-----END CERTIFICATE-----
</cert>
<key>
-----BEGIN PRIVATE KEY-----
MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQD3dSaVCEOTP76n
VwKxR+U6lrmwu1IbeJLEJ3k2lmL+jy80kR+W0Z5+NqRyzm0lUIjZ/mFvD2rZJhxp
n85PAtzoXW0bkcSLA+h9lZB+X4S01+jYrU01RsAOv+IPZ7/TCHLk0f8nd+bDII+2
sxNhLix5h2faBM76jWP20/4LZBOkARK+O56vMhKgSof9l+rLInSyckJtz5CCGVec
neitteezvaBZVAZGvx3asUMoKWVOUt7kmPIImcCzjLtPTYsasaVS/X0g+gA5xkJ6
FmdRHVz5drPs1ECr8Ku/d5kwSTREKKG+z+B1b1apKwYP1XVzn4Y7Wk5TVTnW8CRy
JcranJ8hAgMBAAECggEAPOh99aLSFzVSdRfqlr4uguxEimahABf+b/+TS0da2HNf
2B18W//+dex3L7b1kICxHo8JZm+yCf7icXEfM71tqFgOmgGYEeuFVxvwM9rI7EZU
jrihT2K5tSevucD8qzHiLcYueoV9rDughASx2XKnCca1Xile1LbmiwOE/ULFvtD0
5BHM3Tf5o1lKXiOjYTqXoVBUVPvLPc1Du5ISCCQYF8Y6PYjxLQRg14rzYKtkpgJp
PnwLeN5BtGJ0v96ajSPQrPfBREioBEKBziSNTC0J2pBbLQgcsjvbzCYGNLAk0VFc
dR0GjfltSX7Y8aZ1uH4TpAOUbylHAn5l+3KDeeXnEQKBgQD/pHgMOyIvsIMLOZP7
i+gihlC4irwFowch7VJDX6b2y9SIzlI1eC3JmqBHGs9EHIEpZypoqMWuuaR7lFpq
uRfHA0ebP1+iWM0Oqd/DgLY3jNQzuB6bA3HADKo0g7NwKmTPhR7op2kz3hzrot+S
N6U4jvUjRtdMBQEswuuN/5QcRQKBgQD3zcBR3KX40PoBm29+XYYysAsq3jPFX/Wy
1rQ4SkNvfNSCHT8wlVjpy403eces+MPuAUKDhQg53QVqfrOLWXxSPuGh5H4Y7LmP
+fIvcXZ9+EGaMeVfaWtaN6tYqkMvoP2rz/D+O263KeR4FTVgvEpZkxQ1NbH21n9g
psrpEiz7LQKBgHNnOq5Cz5b5tt6NWol2yg8z1SLJtkFRLDobmURC3OuSblpn0WLD
4t+cxvfyWVilCYW4RcPDVKIguejzurFs5xf3Gg8yV6jBL6iL9TCbXSbMk0rZti9C
KcqJrRhRz+I1sRahQKfEU1CP1zxkZUrrhdF3b6BCLZTquXIMeCu9EY6FAoGBAO3b
/n3VeS7jkQaX56Z5KUZgOuf0D+emG5aFhsn9ZsuOHAGlBzGgOY95Bl04tNSwmIlN
hkXuHR5FrFwzsC3TQIVFvJkEVZGhMOElfzXRCHcUJAxZVp2F+DjJ9NwP2pvOQZB4
8g9mhvij6pzlZq4rLobaqewFVYTlEbF5iI2+F5UlAoGBAO/2NtF9cakNt+buLGWd
fQ5p8ttaRH241rKvpPCQhTYGWi1eyFcPnK6PiUzCUFkoNuVWV7wUv4GZLp2bJBt8
GNKgybAKwVCx8qJyVyddZycKBooSK+qcgnl8roO58VcKtUI1xwkQ2jNXmoVKy1tV
EtpBte+183YIu0XU4P0pd96f
-----END PRIVATE KEY-----
</key>
<tls-auth>
#
# 2048 bit OpenVPN static key
#
-----BEGIN OpenVPN Static key V1-----
3a8d8a54048b087a6a0cc4968f12eed6
203abcc3bdfc35fb91b26e05fce3c3a8
117ade3446f1347a8eb3628577284439
de2042b152b168f386a3ab4b10baeea3
9823d93c8c42f5d2900a9b2a35cbeb42
a1ad9ac01955041059a48fe4eb6f40e1
346d1d67404264a22b53cc7568515881
a92ab404e109bb7877c28c6b71cd6d79
efb0f494eb6b1210c09ac0a0c1491955
a83de815501c242a69eaa8984b1d174f
30b354e3d64a49687061223a003cb696
7d9b46279c73bf29110703a7010ef56a
2148ebaceb3ee8c470e5778453e4db84
4f36c5c5ddca25241f137477a7ad05d2
92b16795b5ddd80d604c14f199198201
9ea3f136ef9742e417833ed19e3e0a7b
-----END OpenVPN Static key V1-----
</tls-auth>

```
