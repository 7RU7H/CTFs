```powershell

Get-NetGPO

usncreated               : 5672
systemflags              : -1946157056
displayname              : Default Domain Policy
gpcmachineextensionnames : [{35378EAC-683F-11D2-A89A-00C04FBBCFA2}{53D6AB1B-2488-11D1-A28C-00C04FB94F17}][{827D319E-6EAC-11D2-A4EA-00C04F79F83A}{803E14A0-B4FB-11D0-A0D0-00A0C90F574B}][{B1BE8D72-6EAC-11D2-A4EA-00C04F79F83A}{53D6AB1B-2488-11D1-A28C-00
                           C04FB94F17}]
whenchanged              : 1/23/2020 5:51:29 AM
objectclass              : {top, container, groupPolicyContainer}
gpcfunctionalityversion  : 2
showinadvancedviewonly   : True
usnchanged               : 12593
dscorepropagationdata    : {1/23/2020 6:15:24 AM, 1/23/2020 5:45:31 AM, 1/1/1601 12:00:00 AM}
name                     : {31B2F340-016D-11D2-945F-00C04FB984F9}
flags                    : 0
cn                       : {31B2F340-016D-11D2-945F-00C04FB984F9}
iscriticalsystemobject   : True
gpcfilesyspath           : \\EGOTISTICAL-BANK.LOCAL\sysvol\EGOTISTICAL-BANK.LOCAL\Policies\{31B2F340-016D-11D2-945F-00C04FB984F9}
distinguishedname        : CN={31B2F340-016D-11D2-945F-00C04FB984F9},CN=Policies,CN=System,DC=EGOTISTICAL-BANK,DC=LOCAL
whencreated              : 1/23/2020 5:44:38 AM
versionnumber            : 3
instancetype             : 4
objectguid               : 176cd73e-5697-4341-907f-8c10d76ee487
objectcategory           : CN=Group-Policy-Container,CN=Schema,CN=Configuration,DC=EGOTISTICAL-BANK,DC=LOCAL

usncreated               : 5675
systemflags              : -1946157056
displayname              : Default Domain Controllers Policy
gpcmachineextensionnames : [{827D319E-6EAC-11D2-A4EA-00C04F79F83A}{803E14A0-B4FB-11D0-A0D0-00A0C90F574B}]
whenchanged              : 1/23/2020 4:49:29 PM
objectclass              : {top, container, groupPolicyContainer}
gpcfunctionalityversion  : 2
showinadvancedviewonly   : True
usnchanged               : 16432
dscorepropagationdata    : {1/23/2020 6:15:24 AM, 1/23/2020 5:45:31 AM, 1/1/1601 12:00:00 AM}
name                     : {6AC1786C-016F-11D2-945F-00C04fB984F9}
flags                    : 0
cn                       : {6AC1786C-016F-11D2-945F-00C04fB984F9}
iscriticalsystemobject   : True
gpcfilesyspath           : \\EGOTISTICAL-BANK.LOCAL\sysvol\EGOTISTICAL-BANK.LOCAL\Policies\{6AC1786C-016F-11D2-945F-00C04fB984F9}
distinguishedname        : CN={6AC1786C-016F-11D2-945F-00C04fB984F9},CN=Policies,CN=System,DC=EGOTISTICAL-BANK,DC=LOCAL
whencreated              : 1/23/2020 5:44:38 AM
versionnumber            : 3
instancetype             : 4
objectguid               : d7620d32-c1ed-4b07-8dd2-8f763e30fee9
objectcategory           : CN=Group-Policy-Container,CN=Schema,CN=Configuration,DC=EGOTISTICAL-BANK,DC=LOCAL

usncreated              : 40976
displayname             : Block downloads to powershell
whenchanged             : 1/25/2020 8:48:44 PM
objectclass             : {top, container, groupPolicyContainer}
gpcfunctionalityversion : 2
showinadvancedviewonly  : True
usnchanged              : 40981
dscorepropagationdata   : 1/1/1601 12:00:00 AM
name                    : {2619FB25-7519-4AEA-9C1E-348725EF2858}
flags                   : 0
cn                      : {2619FB25-7519-4AEA-9C1E-348725EF2858}
gpcfilesyspath          : \\EGOTISTICAL-BANK.LOCAL\SysVol\EGOTISTICAL-BANK.LOCAL\Policies\{2619FB25-7519-4AEA-9C1E-348725EF2858}
distinguishedname       : CN={2619FB25-7519-4AEA-9C1E-348725EF2858},CN=Policies,CN=System,DC=EGOTISTICAL-BANK,DC=LOCAL
whencreated             : 1/25/2020 8:48:44 PM
versionnumber           : 0
instancetype            : 4
objectguid              : d8ed7b13-5817-4739-946c-0db80c946976
objectcategory          : CN=Group-Policy-Container,CN=Schema,CN=Configuration,DC=EGOTISTICAL-BANK,DC=LOCAL

Find-InterestingDomainAcl -ResolveGUIDs


ObjectDN                : DC=EGOTISTICAL-BANK,DC=LOCAL
AceQualifier            : AccessAllowed
ActiveDirectoryRights   : ExtendedRight
ObjectAceType           : DS-Replication-Get-Changes
AceFlags                : None
AceType                 : AccessAllowedObject
InheritanceFlags        : None
SecurityIdentifier      : S-1-5-21-2966785786-3096785034-1186376766-1108
IdentityReferenceName   : svc_loanmgr
IdentityReferenceDomain : EGOTISTICAL-BANK.LOCAL
IdentityReferenceDN     : CN=L Manager,CN=Users,DC=EGOTISTICAL-BANK,DC=LOCAL
IdentityReferenceClass  : user

ObjectDN                : DC=EGOTISTICAL-BANK,DC=LOCAL
AceQualifier            : AccessAllowed
ActiveDirectoryRights   : ExtendedRight
ObjectAceType           : DS-Replication-Get-Changes-All
AceFlags                : None
AceType                 : AccessAllowedObject
InheritanceFlags        : None
SecurityIdentifier      : S-1-5-21-2966785786-3096785034-1186376766-1108
IdentityReferenceName   : svc_loanmgr
IdentityReferenceDomain : EGOTISTICAL-BANK.LOCAL
IdentityReferenceDN     : CN=L Manager,CN=Users,DC=EGOTISTICAL-BANK,DC=LOCAL
IdentityReferenceClass  : user

ObjectDN                : DC=RootDNSServers,CN=MicrosoftDNS,CN=System,DC=EGOTISTICAL-BANK,DC=LOCAL
AceQualifier            : AccessAllowed
ActiveDirectoryRights   : CreateChild, DeleteChild, ListChildren, ReadProperty, DeleteTree, ExtendedRight, Delete, GenericWrite, WriteDacl, WriteOwner
ObjectAceType           : None
AceFlags                : ContainerInherit, Inherited
AceType                 : AccessAllowed
InheritanceFlags        : ContainerInherit
SecurityIdentifier      : S-1-5-21-2966785786-3096785034-1186376766-1101
IdentityReferenceName   : DnsAdmins
IdentityReferenceDomain : EGOTISTICAL-BANK.LOCAL
IdentityReferenceDN     : CN=DnsAdmins,CN=Users,DC=EGOTISTICAL-BANK,DC=LOCAL
IdentityReferenceClass  : group

ObjectDN                : DC=@,DC=RootDNSServers,CN=MicrosoftDNS,CN=System,DC=EGOTISTICAL-BANK,DC=LOCAL
AceQualifier            : AccessAllowed
ActiveDirectoryRights   : CreateChild, DeleteChild, ListChildren, ReadProperty, DeleteTree, ExtendedRight, Delete, GenericWrite, WriteDacl, WriteOwner
ObjectAceType           : None
AceFlags                : ContainerInherit, Inherited
AceType                 : AccessAllowed
InheritanceFlags        : ContainerInherit
SecurityIdentifier      : S-1-5-21-2966785786-3096785034-1186376766-1101
IdentityReferenceName   : DnsAdmins
IdentityReferenceDomain : EGOTISTICAL-BANK.LOCAL
IdentityReferenceDN     : CN=DnsAdmins,CN=Users,DC=EGOTISTICAL-BANK,DC=LOCAL
IdentityReferenceClass  : group

ObjectDN                : DC=A.ROOT-SERVERS.NET,DC=RootDNSServers,CN=MicrosoftDNS,CN=System,DC=EGOTISTICAL-BANK,DC=LOCAL
AceQualifier            : AccessAllowed
ActiveDirectoryRights   : CreateChild, DeleteChild, ListChildren, ReadProperty, DeleteTree, ExtendedRight, Delete, GenericWrite, WriteDacl, WriteOwner
ObjectAceType           : None
AceFlags                : ContainerInherit, Inherited
AceType                 : AccessAllowed
InheritanceFlags        : ContainerInherit
SecurityIdentifier      : S-1-5-21-2966785786-3096785034-1186376766-1101
IdentityReferenceName   : DnsAdmins
IdentityReferenceDomain : EGOTISTICAL-BANK.LOCAL
IdentityReferenceDN     : CN=DnsAdmins,CN=Users,DC=EGOTISTICAL-BANK,DC=LOCAL
IdentityReferenceClass  : group

ObjectDN                : DC=B.ROOT-SERVERS.NET,DC=RootDNSServers,CN=MicrosoftDNS,CN=System,DC=EGOTISTICAL-BANK,DC=LOCAL
AceQualifier            : AccessAllowed
ActiveDirectoryRights   : CreateChild, DeleteChild, ListChildren, ReadProperty, DeleteTree, ExtendedRight, Delete, GenericWrite, WriteDacl, WriteOwner
ObjectAceType           : None
AceFlags                : ContainerInherit, Inherited
AceType                 : AccessAllowed
InheritanceFlags        : ContainerInherit
SecurityIdentifier      : S-1-5-21-2966785786-3096785034-1186376766-1101
IdentityReferenceName   : DnsAdmins
IdentityReferenceDomain : EGOTISTICAL-BANK.LOCAL
IdentityReferenceDN     : CN=DnsAdmins,CN=Users,DC=EGOTISTICAL-BANK,DC=LOCAL
IdentityReferenceClass  : group

ObjectDN                : DC=C.ROOT-SERVERS.NET,DC=RootDNSServers,CN=MicrosoftDNS,CN=System,DC=EGOTISTICAL-BANK,DC=LOCAL
AceQualifier            : AccessAllowed
ActiveDirectoryRights   : CreateChild, DeleteChild, ListChildren, ReadProperty, DeleteTree, ExtendedRight, Delete, GenericWrite, WriteDacl, WriteOwner
ObjectAceType           : None
AceFlags                : ContainerInherit, Inherited
AceType                 : AccessAllowed
InheritanceFlags        : ContainerInherit
SecurityIdentifier      : S-1-5-21-2966785786-3096785034-1186376766-1101
IdentityReferenceName   : DnsAdmins
IdentityReferenceDomain : EGOTISTICAL-BANK.LOCAL
IdentityReferenceDN     : CN=DnsAdmins,CN=Users,DC=EGOTISTICAL-BANK,DC=LOCAL
IdentityReferenceClass  : group

ObjectDN                : DC=D.ROOT-SERVERS.NET,DC=RootDNSServers,CN=MicrosoftDNS,CN=System,DC=EGOTISTICAL-BANK,DC=LOCAL
AceQualifier            : AccessAllowed
ActiveDirectoryRights   : CreateChild, DeleteChild, ListChildren, ReadProperty, DeleteTree, ExtendedRight, Delete, GenericWrite, WriteDacl, WriteOwner
ObjectAceType           : None
AceFlags                : ContainerInherit, Inherited
AceType                 : AccessAllowed
InheritanceFlags        : ContainerInherit
SecurityIdentifier      : S-1-5-21-2966785786-3096785034-1186376766-1101
IdentityReferenceName   : DnsAdmins
IdentityReferenceDomain : EGOTISTICAL-BANK.LOCAL
IdentityReferenceDN     : CN=DnsAdmins,CN=Users,DC=EGOTISTICAL-BANK,DC=LOCAL
IdentityReferenceClass  : group

ObjectDN                : DC=E.ROOT-SERVERS.NET,DC=RootDNSServers,CN=MicrosoftDNS,CN=System,DC=EGOTISTICAL-BANK,DC=LOCAL
AceQualifier            : AccessAllowed
ActiveDirectoryRights   : CreateChild, DeleteChild, ListChildren, ReadProperty, DeleteTree, ExtendedRight, Delete, GenericWrite, WriteDacl, WriteOwner
ObjectAceType           : None
AceFlags                : ContainerInherit, Inherited
AceType                 : AccessAllowed
InheritanceFlags        : ContainerInherit
SecurityIdentifier      : S-1-5-21-2966785786-3096785034-1186376766-1101
IdentityReferenceName   : DnsAdmins
IdentityReferenceDomain : EGOTISTICAL-BANK.LOCAL
IdentityReferenceDN     : CN=DnsAdmins,CN=Users,DC=EGOTISTICAL-BANK,DC=LOCAL
IdentityReferenceClass  : group

ObjectDN                : DC=F.ROOT-SERVERS.NET,DC=RootDNSServers,CN=MicrosoftDNS,CN=System,DC=EGOTISTICAL-BANK,DC=LOCAL
AceQualifier            : AccessAllowed
ActiveDirectoryRights   : CreateChild, DeleteChild, ListChildren, ReadProperty, DeleteTree, ExtendedRight, Delete, GenericWrite, WriteDacl, WriteOwner
ObjectAceType           : None
AceFlags                : ContainerInherit, Inherited
AceType                 : AccessAllowed
InheritanceFlags        : ContainerInherit
SecurityIdentifier      : S-1-5-21-2966785786-3096785034-1186376766-1101
IdentityReferenceName   : DnsAdmins
IdentityReferenceDomain : EGOTISTICAL-BANK.LOCAL
IdentityReferenceDN     : CN=DnsAdmins,CN=Users,DC=EGOTISTICAL-BANK,DC=LOCAL
IdentityReferenceClass  : group

ObjectDN                : DC=G.ROOT-SERVERS.NET,DC=RootDNSServers,CN=MicrosoftDNS,CN=System,DC=EGOTISTICAL-BANK,DC=LOCAL
AceQualifier            : AccessAllowed
ActiveDirectoryRights   : CreateChild, DeleteChild, ListChildren, ReadProperty, DeleteTree, ExtendedRight, Delete, GenericWrite, WriteDacl, WriteOwner
ObjectAceType           : None
AceFlags                : ContainerInherit, Inherited
AceType                 : AccessAllowed
InheritanceFlags        : ContainerInherit
SecurityIdentifier      : S-1-5-21-2966785786-3096785034-1186376766-1101
IdentityReferenceName   : DnsAdmins
IdentityReferenceDomain : EGOTISTICAL-BANK.LOCAL
IdentityReferenceDN     : CN=DnsAdmins,CN=Users,DC=EGOTISTICAL-BANK,DC=LOCAL
IdentityReferenceClass  : group

ObjectDN                : DC=H.ROOT-SERVERS.NET,DC=RootDNSServers,CN=MicrosoftDNS,CN=System,DC=EGOTISTICAL-BANK,DC=LOCAL
AceQualifier            : AccessAllowed
ActiveDirectoryRights   : CreateChild, DeleteChild, ListChildren, ReadProperty, DeleteTree, ExtendedRight, Delete, GenericWrite, WriteDacl, WriteOwner
ObjectAceType           : None
AceFlags                : ContainerInherit, Inherited
AceType                 : AccessAllowed
InheritanceFlags        : ContainerInherit
SecurityIdentifier      : S-1-5-21-2966785786-3096785034-1186376766-1101
IdentityReferenceName   : DnsAdmins
IdentityReferenceDomain : EGOTISTICAL-BANK.LOCAL
IdentityReferenceDN     : CN=DnsAdmins,CN=Users,DC=EGOTISTICAL-BANK,DC=LOCAL
IdentityReferenceClass  : group

ObjectDN                : DC=I.ROOT-SERVERS.NET,DC=RootDNSServers,CN=MicrosoftDNS,CN=System,DC=EGOTISTICAL-BANK,DC=LOCAL
AceQualifier            : AccessAllowed
ActiveDirectoryRights   : CreateChild, DeleteChild, ListChildren, ReadProperty, DeleteTree, ExtendedRight, Delete, GenericWrite, WriteDacl, WriteOwner
ObjectAceType           : None
AceFlags                : ContainerInherit, Inherited
AceType                 : AccessAllowed
InheritanceFlags        : ContainerInherit
SecurityIdentifier      : S-1-5-21-2966785786-3096785034-1186376766-1101
IdentityReferenceName   : DnsAdmins
IdentityReferenceDomain : EGOTISTICAL-BANK.LOCAL
IdentityReferenceDN     : CN=DnsAdmins,CN=Users,DC=EGOTISTICAL-BANK,DC=LOCAL
IdentityReferenceClass  : group

ObjectDN                : DC=J.ROOT-SERVERS.NET,DC=RootDNSServers,CN=MicrosoftDNS,CN=System,DC=EGOTISTICAL-BANK,DC=LOCAL
AceQualifier            : AccessAllowed
ActiveDirectoryRights   : CreateChild, DeleteChild, ListChildren, ReadProperty, DeleteTree, ExtendedRight, Delete, GenericWrite, WriteDacl, WriteOwner
ObjectAceType           : None
AceFlags                : ContainerInherit, Inherited
AceType                 : AccessAllowed
InheritanceFlags        : ContainerInherit
SecurityIdentifier      : S-1-5-21-2966785786-3096785034-1186376766-1101
IdentityReferenceName   : DnsAdmins
IdentityReferenceDomain : EGOTISTICAL-BANK.LOCAL
IdentityReferenceDN     : CN=DnsAdmins,CN=Users,DC=EGOTISTICAL-BANK,DC=LOCAL
IdentityReferenceClass  : group

ObjectDN                : DC=K.ROOT-SERVERS.NET,DC=RootDNSServers,CN=MicrosoftDNS,CN=System,DC=EGOTISTICAL-BANK,DC=LOCAL
AceQualifier            : AccessAllowed
ActiveDirectoryRights   : CreateChild, DeleteChild, ListChildren, ReadProperty, DeleteTree, ExtendedRight, Delete, GenericWrite, WriteDacl, WriteOwner
ObjectAceType           : None
AceFlags                : ContainerInherit, Inherited
AceType                 : AccessAllowed
InheritanceFlags        : ContainerInherit
SecurityIdentifier      : S-1-5-21-2966785786-3096785034-1186376766-1101
IdentityReferenceName   : DnsAdmins
IdentityReferenceDomain : EGOTISTICAL-BANK.LOCAL
IdentityReferenceDN     : CN=DnsAdmins,CN=Users,DC=EGOTISTICAL-BANK,DC=LOCAL
IdentityReferenceClass  : group

ObjectDN                : DC=L.ROOT-SERVERS.NET,DC=RootDNSServers,CN=MicrosoftDNS,CN=System,DC=EGOTISTICAL-BANK,DC=LOCAL
AceQualifier            : AccessAllowed
ActiveDirectoryRights   : CreateChild, DeleteChild, ListChildren, ReadProperty, DeleteTree, ExtendedRight, Delete, GenericWrite, WriteDacl, WriteOwner
ObjectAceType           : None
AceFlags                : ContainerInherit, Inherited
AceType                 : AccessAllowed
InheritanceFlags        : ContainerInherit
SecurityIdentifier      : S-1-5-21-2966785786-3096785034-1186376766-1101
IdentityReferenceName   : DnsAdmins
IdentityReferenceDomain : EGOTISTICAL-BANK.LOCAL
IdentityReferenceDN     : CN=DnsAdmins,CN=Users,DC=EGOTISTICAL-BANK,DC=LOCAL
IdentityReferenceClass  : group

ObjectDN                : DC=M.ROOT-SERVERS.NET,DC=RootDNSServers,CN=MicrosoftDNS,CN=System,DC=EGOTISTICAL-BANK,DC=LOCAL
AceQualifier            : AccessAllowed
ActiveDirectoryRights   : CreateChild, DeleteChild, ListChildren, ReadProperty, DeleteTree, ExtendedRight, Delete, GenericWrite, WriteDacl, WriteOwner
ObjectAceType           : None
AceFlags                : ContainerInherit, Inherited
AceType                 : AccessAllowed
InheritanceFlags        : ContainerInherit
SecurityIdentifier      : S-1-5-21-2966785786-3096785034-1186376766-1101
IdentityReferenceName   : DnsAdmins
IdentityReferenceDomain : EGOTISTICAL-BANK.LOCAL
IdentityReferenceDN     : CN=DnsAdmins,CN=Users,DC=EGOTISTICAL-BANK,DC=LOCAL
IdentityReferenceClass  : group

ObjectDN                : CN=DFSR-LocalSettings,CN=SAUNA,OU=Domain Controllers,DC=EGOTISTICAL-BANK,DC=LOCAL
AceQualifier            : AccessAllowed
ActiveDirectoryRights   : GenericAll
ObjectAceType           : All
AceFlags                : None
AceType                 : AccessAllowedObject
InheritanceFlags        : None
SecurityIdentifier      : S-1-5-21-2966785786-3096785034-1186376766-1000
IdentityReferenceName   : SAUNA$
IdentityReferenceDomain : EGOTISTICAL-BANK.LOCAL
IdentityReferenceDN     : CN=SAUNA,OU=Domain Controllers,DC=EGOTISTICAL-BANK,DC=LOCAL
IdentityReferenceClass  : computer

ObjectDN                : CN=Domain System Volume,CN=DFSR-LocalSettings,CN=SAUNA,OU=Domain Controllers,DC=EGOTISTICAL-BANK,DC=LOCAL
AceQualifier            : AccessAllowed
ActiveDirectoryRights   : GenericAll
ObjectAceType           : All
AceFlags                : Inherited
AceType                 : AccessAllowedObject
InheritanceFlags        : None
SecurityIdentifier      : S-1-5-21-2966785786-3096785034-1186376766-1000
IdentityReferenceName   : SAUNA$
IdentityReferenceDomain : EGOTISTICAL-BANK.LOCAL
IdentityReferenceDN     : CN=SAUNA,OU=Domain Controllers,DC=EGOTISTICAL-BANK,DC=LOCAL
IdentityReferenceClass  : computer

ObjectDN                : CN=SYSVOL Subscription,CN=Domain System Volume,CN=DFSR-LocalSettings,CN=SAUNA,OU=Domain Controllers,DC=EGOTISTICAL-BANK,DC=LOCAL
AceQualifier            : AccessAllowed
ActiveDirectoryRights   : GenericAll
ObjectAceType           : All
AceFlags                : Inherited
AceType                 : AccessAllowedObject
InheritanceFlags        : None
SecurityIdentifier      : S-1-5-21-2966785786-3096785034-1186376766-1000
IdentityReferenceName   : SAUNA$
IdentityReferenceDomain : EGOTISTICAL-BANK.LOCAL
IdentityReferenceDN     : CN=SAUNA,OU=Domain Controllers,DC=EGOTISTICAL-BANK,DC=LOCAL
IdentityReferenceClass  : computer



