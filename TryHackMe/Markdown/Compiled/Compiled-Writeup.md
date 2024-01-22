# Compiled Writeup

Name: Compiled
Date:  
Difficulty:  
Goals:  
- Learn more BinEx
- Learn more ASM 
- Learn more Reverse Enigneering
Learnt:
- Re-remembering: Intel dst src 
Beyond Root:

- [[Compiled-Notes.md]]
- [[Compiled-CMD-by-CMDs.md]]


Basic enumeration of the binary with `file` 
![1080](fileoutput.png)

Mapping out a general understanding of what matters:
![1080](r2pdfAtmain.png)

While briefly examining the `./Compiled.Compiled` before finishing [[Binary-Heaven-Helped-Through]] I formulated solutions:
- Either cheesy binary patching instructions till we have the password visible in a register, on the stack, at a memory address.    
- Input the correct-enough input based on conditionals 

Setup a virtual environment
```bash
python3 -m venv myenv; source myenv/bin/activate; pip install angr
```

```python
import angr
import sys

# Author XCT from https://www.youtube.com/watch?v=UnZj5zzcBG4

# Use symbolic execution to explore all flow control possibilities of a program
# Then print out all the deadends of these explored states
def main(argv):
    binary = "./Compiled.Compiled"
    project = angr.Project(binary)
    init = project.factory.entry_state()
    simulation_manager = project.factory.simgr(init)
    simulation_manager.explore()
    for state in simulation_manager.deadended:
        print(state.posix.dumps(sys.stdin.fileno()))

if __name__ == '__main__':
    main(sys.argv)

```

![1080](256possiblesolutions.png)

[write](https://man7.org/linux/man-pages/man2/write.2.html) up to _count_ bytes from the buffer starting at `buf` to the file referred to by the file descriptor `fd`.

`js` means jump if signed flag is set by an earlier instruction

[Wikipedia: x86 Instruction `test`]https://en.wikipedia.org/wiki/TEST_(x86_instruction) states the `test` [instruction](https://en.wikipedia.org/wiki/Instruction_(computing) "Instruction (computing)") performs a [bitwise AND](https://en.wikipedia.org/wiki/Bitwise_AND "Bitwise AND") on two [operands](https://en.wikipedia.org/wiki/Operand "Operand"). The [flags](https://en.wikipedia.org/wiki/FLAGS_register "FLAGS register") [`SF`](https://en.wikipedia.org/wiki/Sign_flag "Sign flag"), [`ZF`](https://en.wikipedia.org/wiki/Zero_flag "Zero flag"), [`PF`](https://en.wikipedia.org/wiki/Parity_flag "Parity flag") are modified while the result of the [AND](https://en.wikipedia.org/wiki/Bitwise_AND "Bitwise AND") is discarded.


![1080](desimpleobfuscation.png)

How do arguments get passed to `call` instructions?
- The calling convention is defined in detail in [System V Application Binary Interface—AMD64 Architecture Processor Supplement.](http://www.x86-64.org/documentation/abi.pdf)


```
0x55e2096f01e7
```

## Post-Cracking-Reflection  

## Beyond Root


```
e asm.syntax=att // Change syntax
```


https://staffwww.fullcoll.edu/aclifton/courses/cs241/syntax.html

