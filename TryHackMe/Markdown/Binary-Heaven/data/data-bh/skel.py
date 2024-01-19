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

p.interactive()
