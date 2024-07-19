<?php
#
# Copyright (c) 2006-2008 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_snmp_int.pl (COUNTER)
# $Id$
#
#
$opt[1] = " --vertical-label \"Traffic $UNIT[1]\" -E --title \"Interface Traffic for $hostname / $servicedesc\" ";
$def[1] = "DEF:var1=$rrdfile:$DS[1]:AVERAGE " ;
$def[1] .= "DEF:var2=$rrdfile:$DS[2]:AVERAGE " ;
$def[1] .= "CDEF:real1=var1,1,* " ;
$def[1] .= "CDEF:real2=var2,1,* " ;
$def[1] .= "LINE1:real1#003300:\"in  \" " ;
$def[1] .= "GPRINT:real1:LAST:\"%7.2lf $UNIT[1] last\" " ;
$def[1] .= "GPRINT:real1:AVERAGE:\"%7.2lf $UNIT[1] avg\" " ;
$def[1] .= "GPRINT:real1:MAX:\"%7.2lf $UNIT[1] max\\n\" " ;
$def[1] .= "LINE1:real2#00ff00:\"out \" " ;
$def[1] .= "GPRINT:real2:LAST:\"%7.2lf $UNIT[1] last\" " ;
$def[1] .= "GPRINT:real2:AVERAGE:\"%7.2lf $UNIT[1] avg\" " ;
$def[1] .= "GPRINT:real2:MAX:\"%7.2lf $UNIT[1] max\\n\" ";
if($NAGIOS_TIMET != ""){
    $def[1] .= "VRULE:".$NAGIOS_TIMET."#000000:\"Last Service Check \\n\" ";
}
if($NAGIOS_LASTHOSTDOWN != ""){
    $def[1] .= "VRULE:".$NAGIOS_LASTHOSTDOWN."#FF0000:\"Last Host Down\\n\" ";
}
?>
