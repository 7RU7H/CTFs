from pwn import *
import time

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



# SSH connection variables
ssh_host = '10.10.10.10'
ssh_user = '!'
ssh_pass = '!'
ssh_port = 22

def start(argv=[], *a, **kw):
    if args['SSH']:
        sh = ssh(host=ssh_host, user=ssh_user, password=ssh_pass, port=ssh_port)
        p = sh.run('/bin/bash')
        junk = p.recv(4096,timeout=2)
        p.sendline(target)
    elif args['PWN']:
        r = remote(rhost, rport) 
        # r.sendlineafter("SomeSendLineStringHere", payload)
        r.sendline(payload)
        r.recvuntil('SomeRecieveStringHere')
        flag = r.recv()
        success(flag)
        r.close()
    elif args['GEF']:
        p = process(target,setuid=True)
        gdb.attach(p, gdbscript=gdbscript)
        p.sendlineafter("SomeSendLineStringHere", payload)
        time.sleep(10)
        p.recvuntil('SomeRecieveStringHere')
        flag = p.recv()
        success(flag)
        p.close()
    else: 
        print("Vim search and replace: %s/SomeRecieveStringHere/ /g")
        print("Vim search and replace: %s/SomeSendLineStringHere/ /g")

def find_eip(payload):
    p = process(binary)
    p.sendlineafter('>', payload)
    p.wait()
    # ip_offset = cyclic_find(p.corefile.pc) # x86
    ip_offset = cyclic_find(p.corefile.read(p.corefile.sp, 4)) # x64
    info('located EIP/RIP offset at {a}'.format(a=ip_offset))
    return ip_offset

offset = find_eip(cyclic(1000))
# offset = 60

payload = flat(
        {offset: ""}
)

# Write payload to file
# write('payload-pwn', payload)

