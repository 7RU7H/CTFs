
TEXT ·NgajmJIYwmzCRemhV(SB),$0-56
        XORQ AX,AX
        MOVW callid+0(FP), AX
        MOVQ PHandle+8(FP), CX
        MOVQ SP, DX
        ADDQ $0x48, DX
        MOVQ $0,(DX)
        MOVQ ZeroBits+35(FP), R8
        MOVQ SP, R9
        ADDQ $40, R9
        ADDQ $8,SP
        MOVQ CX,R10
        SYSCALL
        SUBQ $8,SP
        RET



//Shout out to C-Sto for helping me solve the issue of  ... alot of this also based on https://golang.org/src/runtime/sys_windows_amd64.s
#define maxargs 16
//func Syscall(callid uint16, argh ...uintptr) (uint32, error)
TEXT ·DOYXMsGAaJkHSV(SB), $0-56
        XORQ AX,AX
        MOVW callid+0(FP), AX
        PUSHQ CX
        MOVQ argh_len+16(FP),CX
        MOVQ argh_base+8(FP),SI
        MOVQ    0x30(GS), DI
        MOVL    $0, 0x68(DI)
        SUBQ    $(maxargs*8), SP
        CMPL    CX, $4
        JLE     loadregs
        CMPL    CX, $maxargs
        JLE     2(PC)
        INT     $3
        MOVQ    SP, DI
        CLD
        REP; MOVSQ
        MOVQ    SP, SI
loadregs:
        SUBQ    $8, SP
        MOVQ    0(SI), CX
        MOVQ    8(SI), DX
        MOVQ    16(SI), R8
        MOVQ    24(SI), R9
        MOVQ    CX, X0
        MOVQ    DX, X1
        MOVQ    R8, X2
        MOVQ    R9, X3
        MOVQ CX, R10
        SYSCALL
        ADDQ    $((maxargs+1)*8), SP
        POPQ    CX
        MOVL    AX, errcode+32(FP)
        RET


TEXT ·MadZzSmGIXnlzHEJtJe(SB), $0-56
    PUSHQ CX
	BYTE $0x90			//NOP
	XORQ AX,AX
	BYTE $0x90			//NOP
	MOVW callid+0(FP), AX
	BYTE $0x90			//NOP
    XORQ R15,R15
    MOVQ syscallA+8(FP), R15
	BYTE $0x90			//NOP
	//put variadic size into CX
	MOVQ argh_len+24(FP),CX
	BYTE $0x90			//NOP
	//put variadic pointer into SI
	MOVQ argh_base+16(FP),SI
	BYTE $0x90			//NOP
	// SetLastError(0).
	MOVQ	0x30(GS), DI
	BYTE $0x90			//NOP
	MOVL	$0, 0x68(DI)
	BYTE $0x90			//NOP
	SUBQ	$(maxargs*8), SP	// room for args
	BYTE $0x90			//NOP
	//no parameters, special case
	CMPL CX, $0
	JLE callz
	// Fast version, do not store args on the stack.
	CMPL	CX, $4
	BYTE $0x90			//NOP
	JLE	loadregs
	// Check we have enough room for args.
	CMPL	CX, $maxargs
	BYTE $0x90			//NOP
	JLE	2(PC)
	INT	$3			// not enough room -> crash
	BYTE $0x90			//NOP
	// Copy args to the stack.
	MOVQ	SP, DI
	BYTE $0x90			//NOP
	CLD
	BYTE $0x90			//NOP
	REP; MOVSQ
	BYTE $0x90			//NOP
	MOVQ	SP, SI
	BYTE $0x90			//NOP
loadregs:
	MOVQ	0(SI), CX
	BYTE $0x90			//NOP
	MOVQ	8(SI), DX
	BYTE $0x90			//NOP
	MOVQ	16(SI), R8
	BYTE $0x90			//NOP
	MOVQ	24(SI), R9
	BYTE $0x90			//NOP
	MOVQ	CX, X0
	BYTE $0x90			//NOP
	MOVQ	DX, X1
	BYTE $0x90			//NOP
	MOVQ	R8, X2
	BYTE $0x90			//NOP
	MOVQ	R9, X3
	BYTE $0x90			//NOP
callz:
    //mov r10, rax
	MOVQ CX, R10
	BYTE $0x90			//NOP
    //syscall;ret
    CALL R15
    BYTE $0x90			//NOP
    ADDQ	$((maxargs)*8), SP
    // Return result.
    POPQ	CX
    MOVL	AX, errcode+40(FP)
    RET


