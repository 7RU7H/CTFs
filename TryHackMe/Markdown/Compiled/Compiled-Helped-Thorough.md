# Compiled Helped-Thorough

Name: Compiled
Date:  23/01/2024
Difficulty:  Easy
Goals:  
- Learn more BinEx
- Learn more ASM 
- Learn more Reverse Enigneering
Learnt:
- Re-remembering: Intel dst src 
Beyond Root:
- Starting to brain out my bad of brain in the method

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

- WRONG WRITE - WOOPS [write](https://man7.org/linux/man-pages/man2/write.2.html) up to _count_ bytes from the buffer starting at `buf` to the file referred to by the file descriptor `fd`.

`js` means jump if signed flag is set by an earlier instruction

[Wikipedia: x86 Instruction `test`](https://en.wikipedia.org/wiki/TEST_(x86_instruction) states the `test` [instruction](https://en.wikipedia.org/wiki/Instruction_(computing) "Instruction (computing)") performs a [bitwise AND](https://en.wikipedia.org/wiki/Bitwise_AND "Bitwise AND") on two [operands](https://en.wikipedia.org/wiki/Operand "Operand"). The [flags](https://en.wikipedia.org/wiki/FLAGS_register "FLAGS register") [`SF`](https://en.wikipedia.org/wiki/Sign_flag "Sign flag"), [`ZF`](https://en.wikipedia.org/wiki/Zero_flag "Zero flag"), [`PF`](https://en.wikipedia.org/wiki/Parity_flag "Parity flag") are modified while the result of the [AND](https://en.wikipedia.org/wiki/Bitwise_AND "Bitwise AND") is discarded.
![1080](desimpleobfuscation.png)

How do arguments get passed to `call` instructions?
- The calling convention is defined in detail in [System V Application Binary Interface—AMD64 Architecture Processor Supplement.](http://www.x86-64.org/documentation/abi.pdf)

Returning to our options:
- Either cheesy binary patching instructions till we have the password visible in a register, on the stack, at a memory address.   
- Input the correct-enough input based on conditionals 
	- We need to binary patch as test will set flag

- Research `_dso_handle` but found it to be https://stackoverflow.com/questions/34308720/where-is-dso-handle-defined to do with destruction of variables
- I decided that the `DoYouEven%sCTF` is the answer where `%s` is formatted but I do not understand where. Input likely candidates like `Crack` or itself as string returns Try Again.

I decide to covert this to Helped-Thorough up given that I had given this a good hour and half. [0xb0b](https://0xb0b.gitbook.io/writeups/tryhackme/2023/compiled). TLDR: Use Ghidra as the NSA intended and do not worry:
- about the whether the my skill issue of some language being entirely lacking
- future capabilities of Ghidra with some language not being supported
![](shouldwappedoutmyghidra.png)
Big Slap in the face of this..

Test this: 
![](testtheDoYouEven.png)

![](root.png)

## Post-Cracking-Reflection  

- Wow I learnt alot
- Myself
	- I got really hung up on one thing that I either missed or associated `_init` with `_dso_handler`
	- I do not trust my initial check with `%s` - to it seemed to be a rabbit hole as it got stored, but that need how it would formatted bugged me somewhere into a no where
	- My binary patching a previous CTF has made a massive impression on my brain.
- ASM
	- `js`, `test`, 
- Reversing
	- GBD is not a good reversing tool
	- Cheesing is defeat-able by making sure that you cannot just binary patch it 
	- TOOLS LIKE GHIDRA EXIST AND SOMETIMES JUST TRYING THINGS IS GOOD


## Beyond Crackage

Just exposing my flaws to myself thankful there are lots of easy challenges ahead that will feel good slapping down in the future.



