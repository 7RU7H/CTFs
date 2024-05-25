#!/bin/sh
KVER=`uname -r`
if [ -e /lib/modules/$KVER/kpaniclog.ko ]; then
    MTD_FOR_PANIC=`syscfg get mtd.for.panic`
    if [ $MTD_FOR_PANIC != "" ]; then
        mtd_offs=`syscfg get mtd.for.panic.offset`
        skip_count=`expr $mtd_offs / 512`
        mtd_for_panic=`cat /proc/mtd | grep $MTD_FOR_PANIC | awk -F ":" '{print $1}'`
        if [ $mtd_for_panic != "" ]; then
            if [ -e /tmp/var/config/panic.md5 ]; then
                dd if=/dev/$mtd_for_panic of=/tmp/panic skip=$skip_count count=256 &> /dev/null
                md5sum /tmp/panic > /tmp/panic.md5
                crash=`diff /tmp/panic.md5 /tmp/var/config/panic.md5`
                if [ "$crash" != "" ]; then
                    echo "---------------Crash detected-----------------"
                    tr $'\xff' ' ' < /tmp/panic | tr -s ' ' > /tmp/panic.txt
                    mv /tmp/panic.txt /tmp/panic
	        else
	            rm /tmp/panic
	        fi
    	        rm /tmp/panic.md5
            else
	        flash_erase -q /dev/$mtd_for_panic $mtd_offs 1 &>/dev/null
	        dd if=/dev/$mtd_for_panic of=/tmp/panic skip=$skip_count count=256 &>/dev/null
	        md5sum /tmp/panic > /tmp/var/config/panic.md5
	        rm /tmp/panic
            fi		
        /sbin/modprobe kpaniclog mtdname=$MTD_FOR_PANIC mtdoffset=$mtd_offs debug=1
        fi
    fi
fi
