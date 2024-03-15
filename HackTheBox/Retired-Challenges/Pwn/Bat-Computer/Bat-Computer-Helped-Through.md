# Bat-Computer Helped-Through

Name: Bat-Computer
Type: Pwn
Date:  
Difficulty:  Easy
Goals:  
- Pwnage of novel kind
- Write out some helpful methodology, issues and solutions and figure out more Pwn Methodology
Learnt:
Beyond Pwn:
- [Tangerine](https://www.youtube.com/watch?v=6SuTLMp6Ytw)
- Write a bash script that updates where the pattern differs, but has to be replaced, because I keep running up against that problem and I want the oneliner.

I am almost certainly falling back to [CryptoCat's Bat Computer easy: HackTheBox Pwn Challenge (shellcode injection)](https://www.youtube.com/watch?v=NZfqLFuffYY&list=PLHUKi1UlEgOIn12nvhwwq2aTU8bG-FE0I&index=3) Writeup, but that said... I really want to try to research the attack first, then do my best trying knowing before and collecting information about shellcode injection to complete a Pwn challenge and what that means. [Play some music and I will explain](https://www.youtube.com/watch?v=ly2zdrtWLVc)...

Before diving into researching shellcode injection and doing my best with what I find I will explain my learning method here, which I have mostly used as I want to try to test what I could improve with it for the future with future technologies, ideas and actions. While learning to program with Golang and C in 2021 after many year being more interested in the Computer Science concepts and Philosophy than developing software, writing code or programming - the method I was to get good was the following:
- Find a problem - leetcode, geekforgeeks
1. Set timer and research 
	1. For only one hour, but do not read any code except pseudo-code at most - for 1 hour
		- This prevented an old problem I had of infinite researching and thinking
2. Spend two hours trying with what features of a C or Golang I knew write a solution
3. Walk away take breaks or continue for a third hour
4. If I had failed to finish it in time:
	- Continue you on, because maybe need a bit more time
	-  Another 1 hour research was allowed then 3 hours to finish it
	- If I had no Idea transpiled it from Python to Golang, C++ to Golang if desperate
5. Once done Look up how someone else coded differently whether I failed or succeed.

The problem with Hacking that I needed to iron out was that the time requirement to get good was rough. Rough in the sense that if I did not know something, misread something, not noted something, remember something, understood the application,attack,etc, or could not find anything that would get me those sweet flags and points - it was rough.
- Everything seems very very arbitrary and when a CTF is not good and you do not know any better the arbitrariness can be very bad for progress without the using this repository to figure out if it was my fault or the machine or the authors fault - helped thorough helped.
- [Personal Stories are just a joke, but this a emotional real issue that affects anyone with a disease, in war, political struggle -- whatever it is you can skip this by pressing Ctrl + F and  Research O'Clock 1 hour ](https://www.youtube.com/watch?v=ZS6bD3SpIvk) 

The problems I came up against recently were (that I analysed where):
- the shift from OFFSEC machines to real machines and real methodology an
- whether I even liked it methodology of real machines
- whether I learnt from others or leeching off others
- am I improving in a team?
	- for me teams are so problematic for so many reasons for learning if everyone is not at the same level:
		- It may iron out beginner issues of simple stuff and fastrack that, but it does not work for me as I have questions and other people have their stuff to do
- why I am not performing at a level
- I want to do exams, but I want to breeze thorough them, which is problematic for the long-term
- Insane HTB machines are monster - I want to root those without help at some point in year or so
	- Yes I could wait and get experience slowly, but that is just not smart.
- Java is everywhere in the real world, but not in my box experience
- New machines and season are not always good, maybe going with a team will get you points, but the system designed to be used not to teach - I have avoided exploiting points on HTB and has and will cost me until I get those points i.e a 2 year old THM machine being released on HTB as an active machine - mind boggling.
- Is not [impostor syndrome](https://www.youtube.com/watch?v=VgGJMS8kcok) its but not being that good syndrome and observing it
- Time is not on my side for this and I cant justify wasting it till I can on banging my head on machine like bug bounty people till money falls out - thankful I never did bug bounty (if I was a senior pentester or application tester,etc than Bug Bounty would be good, but for idiots,  beginners and intermediates just stay away from doing that; if anything it even more clique and secretive than CTFs - And unless you really like Web Applications and one exploit type what is the point) I am more the idiot at the zoo getting to hold the bucket in different enclosures not Steve Irwin of XSS 


Solution:
- Fix real weak areas that if they exist at higher level I would not be able to respond -> Pwn
	- At some point the bug bounty is just going to shrink and stabilise - and then maybe your favourite is not going to be relevant in you making money anymore  
- Novelty = Dopamine; Dopemine release rewarded from externally recognising hard work is very good -> Learn to Pwn
- Build CTFs up to insane
- Avoid LLMs 
	- It is amazing how bad they actually are in 2024 - even with OVER A DECADE OF EXPERIENCE IN DOING THIS THERE ARE STILL VERY BAD
		- Also all the data sources they had are now ruined by people not caring to add good information, people add bad information and the internet and the world being the world.
	- I went back and forth on using LLMs and the real issue is Search Engines for whatever reason just suck now thanks Race not even going to make much of a difference except having to own the liability of everyone datasets being ruined by poisoned models... 
- [Go on holiday at some point](https://www.youtube.com/watch?v=Ecv45yCJwFY)

Things I have already been going:
- Not caring about THM points 
- Analysing how I think and methodology and fixing my problems
- Staying involved, but stepping back to program while I fix this
- I am here to transcend myself, I am not here for a job, internet points or (in)fame - I just feel like a human in the organisation shift which takes more time sometimes than just turning a computer on and doing some action.

Unsolved:
- My brain is [...](https://www.youtube.com/watch?v=OuzvfPMykls)
- Points on HTB:
	- I want points and I want justifiably earnt points  
	- Be a extra wheel on a team, get points and every single Write Up I play down my effort as I did in my initial Write Ups
	- Do not roll with a team after this season (4th)

![1920](filebatcomputer.png)

## Research O'Clock 1 hour:

- https://www.crow.rip/crows-nest/mal/dev/inject/shellcode-injection
- https://0xrick.github.io/binary-exploitation/bof5/
- https://ain-kun.medium.com/shellcode-injection-ae82737d8f65
- https://www.ired.team/offensive-security/code-injection-process-injection/process-injection
- https://dhavalkapil.com/blogs/Shellcode-Injection/
- https://www.logsign.com/blog/how-to-prevent-shellcode-injection/

... I got to 0xrick's explanation of what shellcode injection was and realised it is just probably a simple buffer overflow with some shellcode. Originally I assumed it was something like crow's article on shellcode-injection with process hijacking with syscalls, so I might be able to just consider this a Write-Up if I do this myself.

The bat computer is .. a tangELF 64 bit - `strings -e S` there is a unsafe C function used 
![](stringse.png)

The memory address being printed I know is from a use of `printf` in c like: `printf("It was very hard, but Alfred managed to locate him: %p", some_var_or_pointer)`
![](comparisonofaddressprintfpercentp.png)
We need to `sendline()` a 2 in pwn  
```python
from pwn import *

sendline("2")

```

![](notsurewhy.png)

![](nothingatthismemoryaddress.png)


![](break-entry.png)

![](stillmakesmelaugh.png)

- `scan` searches for addresses of one memory region (needle) inside another region (haystack) and lists all results.
	- `scan stack libc`
- `trace-run` command is meant to be provide a visual appreciation directly in IDA disassembler of the path taken by a specific execution.
	- `trace-run <address_of_last_instruction_to_trace>`
- `xinfo 0x00000001` displays all the information known to `gef` about the specific address as argument

![](xinfoaboutheaddress.png)

![](doublecheckingthenumberedinput.png)

Turning to Ghidra:
![](ghidrasimportsummary.png)

![](ghidrasimportsummary.png)

![](alfredforgettoencryptthehardcodedpasswords.png)

`b4tp@$$w0rd!`

![](readthreadrtfm.png)

with 100 length pattern:
![](again.png)

![](ireallyreallywanttoreferencethenullpointermemebutreadisvoidbuffer.png)

Void buffers are very bad and probably why even the not so great courses that still cause these issues do not teach this
![](readingthecodeagain.png)

[My brain](https://www.youtube.com/watch?v=tqsCslriqjk) on MORE EXPLOSIONS!
![](WENEEDMOREPATTERN.png)

Unfortunately no explosions
![](testing2failed.png)

Well I gave it a solid hour, but I manage to segfault it..


[siphos - High level explanation on some binary executable security](https://blog.siphos.be/2011/07/high-level-explanation-on-some-binary-executable-security/): *"**PIE**, meaning Position Independent Executable. A **No PIE** application tells the loader which virtual address it should use (and keeps its memory layout quite static). Hence, attacks against this application know up-front how the virtual memory for this application is (partially) organized."*
- Disassembly:
	- Will not produce addresses
	- Will produce offsets

Adding `ltrace` to my life
![](ltraceaddedtoarsenal.png)

And [gribbles to the rescue](https://gribblelab.org/teaching/CBootCamp/7_Memory_Stack_vs_Heap.html)
![](stackvariablesexist.png)
No `malloc()` means stack! Sense is be made.

- https://superuser.com/questions/169051/whats-the-difference-between-c-and-d-for-unix-mac-os-x-terminal:
	- `ctrl + c`: SIGINT to current foreground process
	- `ctrl + d`: [EOF](https://en.wikipedia.org/wiki/End-of-Transmission_character#Meaning_in_Unix)


![](thesearethebytesthatwouldhavemadeittorip.png)

- 84 then the stack address from the print format.

19:20 
## Post-Completion Reflection

- Refilling in my C knowledge in the fun way 
- What questions do you need to ask to figure out what type of Pwn challenge this is? 
- more `ltrace` in your life
- do not be an idiot `Ghidra -> Strings` click and click to find the functions that matter - this is both good and bad, I guess that the issue would be contextual awareness and loss time trade off for focusing  
- Have you run `ltrace ./binary`?
- PIE will display offsets, which we can still calculate to and from 
- convert values in the ghidra use the tools
- [corefile.pc](https://docs.pwntools.com/en/stable/elf/corefile.html#pwnlib.elf.corefile.Corefile.pc) for cross platform get eip

```c
// stack variables are declared like this:
int stackVariable = 69;

// malloc return a pointer to heap variable and takes a int as size 
int *heapVariable = malloc(sizeof(int));
// then needs to have a value assigned:
*heapVariable = 69;
// Alway free memory after use
free(heapVariable);
```