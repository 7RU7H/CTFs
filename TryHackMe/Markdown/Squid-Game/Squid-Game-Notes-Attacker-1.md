
`trid` is a file identification tool - [GitHub](https://github.com/dubfr33/trid): *"TrID is a utility designed to identify file types from their binary signatures. While there are similar utilities with hard coded rules, TriID has no such rules. Instead, it is extensible and can be trained to recognize new formats in a fast and automatic way. TrID uses a database of definitions which describe recurring patterns
for supported file types."*

![](trid-v-a1.png)


```bash
# run first to prevent stdout and brain overflow 
vmonkey Desktop/maldocs/attacker1.doc
# -p output2FileAndStdout 
# -e Extract and evaluate/deobfuscate constant expressions
# -c idsplay IOCs in VBA
vmonkey -p attacker1.vmonkey -e -c Desktop/maldocs/attacker1.doc
```

oletools
```bash
# Get basic information 
oleid
```

![](oleid-a1.png)

```vb
ChrW() ' Returns the Unicode character that corresponds to the specified character code
CBool() ' convert to a boolean
Len() ' returns the number of characters in a text string.
LenB() ' returns the number of bytes used to represent the characters in a text string
Trim() ' Trims both leading and tailing string expressions
```

https://support.microsoft.com/en-gb/office/len-lenb-functions-29236f94-cedc-429d-affd-b5e33d2c67cb
https://learn.microsoft.com/en-us/office/vba/language/reference/user-interface-help/ltrim-rtrim-and-trim-functions
https://learn.microsoft.com/en-us/office/vba/language/concepts/getting-started/type-conversion-functions
https://help.libreoffice.org/latest/ro/text/sbasic/shared/03120112.html

![](vba-questioninghowtomakethis-moreeffient.png)


```vba
VBA.Shell# "CmD /C " + Trim(rjvFRbqzLtkzn) + SKKdjMpgJRQRK + Trim(Replace(pNHbvwXpnbZvS.AlternativeText + "", "[", "A")) + hdNxDVBxCTqQTpB + RJzJQGRzrc + CWflqnrJbKVBj, CInt(351 * 2 + -702)

 Set pNHbvwXpnbZvS = Shapes(Trim("h9mkae7"))
```

My guess is that `VBA.Shell` method will take everything above plus the possible key `Set pNHbvwXpnbZvS = Shapes(Trim("h9mkae7"))` to decrypt or use the key as an api call

Needing to know VBA makes this like being stuck ice as I cannot just run it so I tried a trick heard about turning CTFs into other CTFs by making it a wireshark CTF. The outliner IP did not fit the answer to Include the malicious IP and the php extension found in the maldoc.

Find the phone number in the maldoc. (Answer format: xxx-xxx-xxxx)
```bash
olemeta attacker1.doc
# 213-446-1757
# West Virginia  Samanta
```

I remember vaguely there is a tool to dump the xml and `http://schemas.openxmlformats.org/officeDocument/2006/customXml` could be another vba inside a vba...

I went a few minutes over a hour and looked the hints for some of questions just to push out all the low hanging even a monkey could get these 

Time-related malware shinagans
```bash
oletimes
# 2019-02-07 23:45:30
```

```bash
olevba
# AutoExec
```

https://www.onlinegdb.com/online_vb_compiler


```
# Get the Streams 
oledump.py $maldoc
```
