#!/usr/bin/perl
#
# ============================== SUMMARY =====================================
#
# Program : check_netstat.pl (originally check_tcpconnections.pl)
# Version : 0.2
# Date    : Oct 8 2006
# Author  : William Leibzon - william@leibzon.org
# Summary : This is a nagios plugin that allows to check number of TCP
#           connections on or to given set of ports. It is using
#           'netstat' when run locally or 'snmpnetstat' for remote host.
# Licence : GPL - summary below, full text at http://www.fsf.org/licenses/gpl.txt
#
# =========================== PROGRAM LICENSE ================================
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GnU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
#
# ===================== INFORMATION ABOUT THIS PLUGIN ========================
#
# This is a nagios plugin that checks number of TCP connections from or
# to the system. The plugin gets the data either from local computer with
# 'netstast' or from remote system using 'snmpnetstat' (if '-H' and '-C'
# are not specified then its local).
#
# You can set this up to provide warning and critical values if number of
# connections for specific port out or in is too low or too high. Data is
# also made available for nagios 2.0 post-processing based on specified ports.
#
# This program is written and maintained by:
#   William Leibzon - william(at)leibzon.org
#
# =============================== SETUP NOTES ================================
#
# 1. Make sure to check and if necessary adjust the the path to utils.pm
# 2. Make sure you have snmpnetstat in /usr/bin or adjust the path below
# 3. Beware that all service port names are ALWAYS retrieved from system
#    running plugin and in case of SNMP check this might not be the same
#    as what is set on remote system.
#
# TCP Port names are specified with '-p' or '-a' option for checking number
# of connections based on specified warning and critical values and with '-A'
# for those ports which you want in perfomance output. Each TCP port name
# (or number) should be prefixed with either '>' or '<' to specify if you
# want to check incoming connections to the specified port ('<') or outgoing
# connections ('>') to the specified port on remote system.  For example
# using '--ports >smtp' means you want to check on number of outgoing SMTP
# (port 25) connections. Special value of '@' (or just '>') allow to check
# check on total number connections (they will be reported as port 'all').
# Even more interesting is another special value of '*' (or just '<') which
# expands to mean all ports your system is listening on [Note: this is
# currently in testing, it'll be released with 0.3 version of the plugin].
# Note that when this is used in '-p' then numbers you specify for warning
# and critical would become default for any such port.
#
# The values retrieved are compared to specified warning and critical levels.
# Warning and critical levels are specified with '-w' and '-c' and each one
# must have exact same number of values (separated by ',') as number of
# attribute (tcp port names) specified with '-p'. Any values you dont want
# to compare you specify as ~. There are also number of other one-letter
# modifiers that can be used before actual data value to direct how data is
# to be checked. These are as follows:
#    > : issue alert if data is above this value (default)
#    < : issue alert if data is below this value
#    = : issue alert if data is equal to this value
#    ! : issue alert if data is NOT equal to this value
# A special modifier '^' can also be used to disable checking that warn values
# are less then (or greater then) critical values (it is rarely needed).
#
# Additionally if you want performance output then use '-f' option to get all
# the ports specified with '-p' or specify particular list of of ports for
# performance data with '-A' (this list can include names not found in '-p').
# Note that when reporting for in perfomance data instead of saying ">smtp'
# or "<smtp" the plugin will report it as 'smtp_out=' or 'smtp_in='.
#
# ============================= SETUP EXAMPLES ===============================
#
# The first example is for your server to check SMTP connections - warnings
# would be sent here fore more then 15 incoming connections or more then 10
# outgoing and critical alerts for more then 40 incoming or 20 outgoing
# ---- 
# define command {
#        command_name check_smtp_connections
#        command_line $USER1$/check_netstat.pl -L "SMTP Load" -H $HOSTADDRESS$ -C $ARG1$ -2 -p "<smtp,>smtp" -w $ARG2$ -c $ARG3$ -f -A "@"
# }
#
# define service{
#       use                             std-service
#       service_description             SMTP Load
#       hostgroups                      mailserv
#       check_command                   check_smtp_connections!public!">15,>10"!">40,>20"
# }
#
# ----
# The second example is for a webserver to check HTTP connections. In
# this case the server is always little loaded (i.e. you have busy website)
# so not only is their upper bound of 30 for warning and 100 for critical
# but also a bound to send alert if there are < 5 connections (and to
# specify this http name is repeated twice at '-p'; the result is a
# little strange though as it will report "http in connections" twice -
# its on my "todo list" to get this taken care of and only report same
# port/direction information once).
#
# define command {
#        command_name check_http_connections
#        command_line $USER1$/check_netstat.pl -L "HTTP Load" -H $HOSTADDRESS$ -C $ARG1$ -2 -p "http,http" -w $ARG2$ -c $ARG3$ -A "http,@"
# }
#
# define service{
#       use                             std-service
#       service_description             HTTP Load
#       hostgroups                      webserv
#       check_command                   check_http_connections!public!"<5,>30"!"~,100"
# }
#
# Note: those who want to minimize load on nagios server and number of extra
#       lookups should really combine above into one command if your server
#       is doing both HTTP and SMTP (but of course then it might not look
#       as nice in the services list)
#
# =================================== TODO ===================================
#
#  1. Think what other netstat data is good to check on with nagios
#     (unix sockets?)
#  2. Add reporting all incoming connections for all services which system
#     is listening on (this is '*' feature already mentioned in description
#     but not in 'help' function so add it there and also make certain
#     it works as just '<')
#  3. Add capability to report ports that are not within certain range
#     (to guess list all 'incoming' ports but discard random ports from
#     outgoing connections)
#  4. Fix it so that port is reported only once even if listed more
#     then once (see example above) this is actually bigger rewrite
#     then it may seem at first
#  5. [DONE 0.2] Allow to use just '-w' or just '-c' (making the other all '~')
#  6. [DONE 0.2] Report an error if getservbyname can not find service
#  7. [DONE 0.2] Prefix output with "tcp_" when its just a port number
#  8. [DONE 0.2] Add label into output for custom results
#  9. [DONE 0.2] Add '-F' option to get performance-only data into main output
#
# ========================== START OF PROGRAM CODE ===========================

use strict;
use Getopt::Long;

use lib "/usr/lib/nagios/plugins";
# use utils qw(%ERRORS $TIMEOUT);
# uncomment two lines below and comment out two above lines if you do not have nagios' utils.pm
my $TIMEOUT = 20;
my %ERRORS=('OK'=>0,'WARNING'=>1,'CRITICAL'=>2,'UNKNOWN'=>3,'DEPENDENT'=>4);
my $snmpnetstat='/usr/bin/snmpnetstat'; # you may want to modify this too

my $Version='0.2';

my $o_host=     undef;          # hostname
my $o_community= undef;         # community
my $o_snmpport= undef;          # SNMP port (default 161)
my $o_help=     undef;          # help option
my $o_timeout=  10;             # Default 10s Timeout
my $o_verb=     undef;          # verbose mode
my $o_version=  undef;          # version info option
my $o_version2= undef;          # use snmp v2c
# SNMPv3 specific - not supported right now
my $o_login=    undef;          # Login for snmpv3
my $o_passwd=   undef;          # Pass for snmpv3

my $o_perf=     undef;          # Performance data option
my $o_perfcopy= undef;          # Opposite to o_perf to cause perfomance attributes be listed within main output
my $o_attr=	undef;  	# What port(s) to check (specify more then one separated by '.')
my @o_attrL=    ();             # array for above list
my @o_attrLn=   ();             # array of port to check in numeric form
my @o_attrLp=   ();             # array of attribute name modifiers ('>' or '<')
my $o_perfattr= undef;		# List of attributes to only provide values in perfomance data but no checking
my @o_perfattrL=();		# array for above list
my @o_perfattrLp=();            # array of modifiers for perfomance attribute modifiers ('>' or '<')
my @o_perfattrLn=();            # array of ports in numeric form
my $o_warn=     undef;          # warning level option
my @o_warnLv=   ();             # array of warn values
my @o_warnLp=	();		# array of warn data processing modifiers
my $o_crit=     undef;          # Critical level option
my @o_critLv=   ();             # array of critical values
my @o_critLp=	();		# array of critical data processing modifiers
my $o_label=    '';             # Label used to show what is in plugin output
my $o_established=undef;        # only count established TCP sessions

my $netstat_pid=undef;

sub print_version { print "$0: $Version\n" };

sub print_usage {
	print "Usage: $0 [-v] [-H <host> -C <snmp_community> [-2] [-P <snmp port>]] [-t <timeout>] [-p <ports to check> -w <warn levels> -c <crit levels> [-f]] [-A <ports for perfdata> [-F]] [-e] [-V]\n";
}

# Return true if arg is a number - in this case negative and real numbers are not allowed
sub isnum {
	my $num = shift;
	if ( $num =~ /^[+]?(\d*)$/ ) { return 1 ;}
	return 0;
}

sub help {
	print "\nNetstat (TCP Connections) Monitor Plugin for Nagios version ",$Version,"\n";
	print " by William Leibzon - william(at)leibzon.org\n\n";
	print_usage();
	print <<EOD;
-v, --verbose
	print extra debugging information
-h, --help
	print this help message
-L, --label
        Plugin output label
-H, --hostname=HOST
	name or IP address of host to check with SNMP (using snmpnetstat)
-C, --community=COMMUNITY NAME
	community name for SNMP - can only be used if -H is also specified
-2, --v2c 
        use SNMP v2 (instead of SNMP v1)
-P, --snmpport=PORT
	port number for SNMP - can only be used if -H is specified
-p, --ports=STR[,STR[,STR[..]]] or --attributes=STR[,STR[,[STR[...]]]
	Which tcp ports (attributes) to check. The value here can be either numeric or name that
        would be looked from /etc/services. The value should be prefixed with:
	   > : check outgoing TCP connections
	   < : check incoming TCP connections (default)
	Special value of '>@' or just '>' allow to specify that you want to check on total number
        of TCP connections (this is reported in perfomance data as 'all_out').
-A, --perf_attributes=STR[,STR[,STR[..]]]
	Which tcp ports to add as part of performance data output. These names can be different then 
	the ones listed in '--ports' to only output these ports in perf data but not check. 
-w, --warn=STR[,STR[,STR[..]]]
	Warning level(s) - usually numbers (same number of values specified as number of attributes)
	Warning values can have the following prefix modifiers:
	   > : warn if data is above this value (default)
	   < : warn if data is below this value
	   = : warn if data is equal to this value
	   ! : warn if data is not equal to this value
	   ~ : do not check this data (must not be followed by number)
	   ^ : this disables checks that warning is less then critical
-c, --crit=STR[,STR[,STR[..]]]
	critical level(s) (if more then one name is checked, must have multiple values)
	Critical values can have the same prefix modifiers as warning (see above) except '^'
-t, --timeout=INTEGER
	timeout for SNMP in seconds (Default : 5)
-e, --established_sessions
        specifies that listing should include only TCP sessions in ESTABLISHED status
-V, --version
	prints version number
-f, --perfparse
        Used only with '-p'. Causes to output data not only in main status line but also as perfparse output
-F, --perf_copy				    
        Used only with '-A'. Causes to get ports listed '-A' to also be reported as normal plugin output
EOD
}

# For verbose output - don't use it right now
sub verb { my $t=shift; print $t,"\n" if defined($o_verb) ; }

# Get the alarm signal (just in case snmp timeout screws up)
$SIG{'ALRM'} = sub {
     print ("ERROR: Alarm signal (Nagios time-out)\n");
     kill 9, $netstat_pid if defined($netstat_pid);
     exit $ERRORS{"UNKNOWN"};
};

sub check_options {
    my $i;
    Getopt::Long::Configure ("bundling");
    GetOptions(
        'v'     => \$o_verb,            'verbose'       => \$o_verb,
        'h'     => \$o_help,            'help'          => \$o_help,
        'H:s'   => \$o_host,            'hostname:s'    => \$o_host,
        'P:i'   => \$o_snmpport,        'snmpport:i'    => \$o_snmpport,
        'C:s'   => \$o_community,       'community:s'   => \$o_community,
        't:i'   => \$o_timeout,         'timeout:i'     => \$o_timeout,
        'V'     => \$o_version,         'version'       => \$o_version,
        '2'     => \$o_version2,        'v2c'           => \$o_version2,
	'L:s'   => \$o_label,           'label:s'       => \$o_label,
        'c:s'   => \$o_crit,            'critical:s'    => \$o_crit,
        'w:s'   => \$o_warn,            'warn:s'        => \$o_warn,
        'f'     => \$o_perf,            'perfparse'     => \$o_perf,
        'F'     => \$o_perfcopy,        'perf_copy'     => \$o_perfcopy,
        'a:s'   => \$o_attr,         	'attributes:s' 	=> \$o_attr,
        'p:s'   => \$o_attr,         	'ports:s' 	=> \$o_attr,
	'A:s'	=> \$o_perfattr,	'perf_attributes:s' => \$o_perfattr,
	'e'	=> \$o_established,	'established_sessions' => \$o_established
    );
    if (defined($o_help) ) { help(); exit $ERRORS{"UNKNOWN"}; }
    if (defined($o_version)) { p_version(); exit $ERRORS{"UNKNOWN"}; }
    if (defined($o_host))
    {
	if (!defined($o_community) && (!defined($o_login) || !defined($o_passwd)) )
	{ print "Specify SNMP community!\n"; print_usage(); exit $ERRORS{"UNKNOWN"}; }
    }
    elsif (defined($o_community) || defined($o_version2) || defined($o_snmpport) ||
	    defined($o_login) || defined($o_passwd)) {
	print "Can not use snmp-specific attributes without specifying host!\n"; 
	print_usage();
	exit $ERRORS{"UNKNOWN"};
    }
    if (defined($o_perfattr)) {
        @o_perfattrL=split(/,/ ,$o_perfattr) if defined($o_perfattr);
        for ($i=0; $i<scalar(@o_perfattrL); $i++) {
	    my ($name,$aliases);
	    $o_perfattrL[$i] =~ s/^([>|<]?)//;
	    $o_perfattrLp[$i] = $1;
	    if ($o_perfattrL[$i] eq '@' || !$o_perfattrL[$i]) {
		$o_perfattrLn[$i] = 0;
		$o_perfattrL[$i]='all';
	    }
	    elsif (isnum($o_perfattrL[$i])) {
		$o_perfattrLn[$i] = $o_perfattrL[$i];
	    } else {
		($name, $aliases, $o_perfattrLn[$i]) = getservbyname($o_perfattrL[$i],'tcp');
		if ($? || !$o_perfattrLn[$i]) {
		    print "Failed to find port number for service named \"$o_perfattrL[$i]\"\n";
		    print_usage();
		    exit $ERRORS{"UNKNWON"};

		}
	    }
	    $o_perfattrLp[$i] = '<' if !$o_perfattrLp[$i];
	}
    }
    if (defined($o_warn) || defined($o_crit) || defined($o_attr)) {
        if (defined($o_attr)) {
          @o_attrL=split(/,/, $o_attr);
          @o_warnLv=split(/,/ ,$o_warn) if defined($o_warn);
          @o_critLv=split(/,/ ,$o_crit) if defined($o_crit);
        }
        else {
          print "Specifying warning and critical levels requires '-p' parameter with port names\n";
          print_usage();
          exit $ERRORS{"UNKNOWN"};
        }
        if (scalar(@o_warnLv)!=scalar(@o_attrL) || scalar(@o_critLv)!=scalar(@o_attrL)) {
	    if (scalar(@o_warnLv)==0 && scalar(@o_critLv)==scalar(@o_attrL)) {
		verb('Only critical value check is specified - setting warning to ~');
		for($i=0;$i<scalar(@o_attrL);$i++) { $o_warnLv[$i]='~'; }
	    }
	    elsif (scalar(@o_critLv)==0 && scalar(@o_warnLv)==scalar(@o_attrL)) {
		verb('Only warning value check is specified - setting critical to ~');
		for($i=0;$i<scalar(@o_attrL);$i++) { $o_critLv[$i]='~'; }
	    }
	    else {
		printf "Number of spefied warning levels (%d) and critical levels (%d) must be equal to the number of attributes specified at '-p' (%d). If you need to ignore some attribute specify it as '~'\n", scalar(@o_warnLv), scalar(@o_critLv), scalar(@o_attrL);
		print_usage();
		exit $ERRORS{"UNKNOWN"};
	    }
	}
        for ($i=0; $i<scalar(@o_attrL); $i++) {
	    my ($name,$aliases);
	    $o_attrL[$i] =~ s/^([>|<]?)//;
	    $o_attrLp[$i] = $1;
	    if ($o_attrL[$i] eq '@' || !$o_attrL[$i]) {
		$o_attrLn[$i] = 0;
		$o_attrL[$i] = 'all';
	    }
	    elsif (isnum($o_attrL[$i])) {
		$o_attrLn[$i] = $o_attrL[$i];
	    }
	    else {
		($name, $aliases, $o_attrLn[$i]) = getservbyname($o_attrL[$i],'tcp');
		if ($? || !$o_attrLn[$i]) {
		    print "Failed to find port number for service named \"$o_attrL[$i]\"\n";
		    print_usage();
		    exit $ERRORS{"UNKNWON"};
		}
	    }
	    $o_warnLv[$i] =~ s/^(\^?[>|<|=|!|~]?)//;
	    $o_warnLp[$i] = $1;
	    $o_critLv[$i] =~ s/^([>|<|=|!|~]?)//;
            $o_critLp[$i] = $1;

	    if ($o_warnLv[$i] && !isnum($o_warnLv[$i])) {
		printf "%s is invalid - warning values must be numeric or ~\n", $o_warnLv[$i]; 
		print_usage();
		exit $ERRORS{"UNKNOWN"};
            }
	    if ($o_critLv[$i] && !isnum($o_critLv[$i])) {
		printf "%s is invalid - critical values must be numeric or ~\n", $o_critLv[$i]; 
		print_usage();
		exit $ERRORS{"UNKNOWN"};
            }
	    if ($o_warnLp[$i] eq $o_critLp[$i] && $o_warnLp[$i] ne "~" && (
		($o_warnLv[$i]>=$o_critLv[$i] && $o_warnLp[$i] !~ /</) ||
		($o_warnLv[$i]<=$o_critLv[$i] && $o_warnLp[$i] =~ /</)
	    )) {
		print "Problem with warning value $o_warnLv[$i] and critical value $o_critLv[$i] :\n";
		print "All numeric warning values must be less then critical (or greater then when '<' is used)\n";
		print "Note: to override this check prefix warning value with ^\n";
                print_usage();
                exit $ERRORS{"UNKNOWN"};
	    }
	    $o_warnLp[$i] =~ s/\^//;
	    $o_warnLp[$i] = '>' if !$o_warnLp[$i];
	    $o_warnLp[$i] = '>' if !$o_critLp[$i];
	    $o_attrLp[$i] = '<' if !$o_attrLp[$i];
        }
    }
    if (scalar(@o_attrL)==0 && scalar(@o_perfattrL)==0) {
        print "You must specify list of ports with either '-p' ('-a') or '-A'\n";
        print_usage();
        exit $ERRORS{"UNKNOWN"};
    }
}

sub check_value {
    my ($tcpname, $inout, $data, $level, $modifier) = @_;
    my $attrib = tcpportname($tcpname, $inout);

    return "" if $modifier eq '~';
    return $attrib . " is " . $data . "(equal to " . $level .")" if $modifier eq '=' && $data eq $level;
    return $attrib . " is " . $data . "(not equal to " . $level .")" if $modifier eq '!' && $data ne $level;
    return $attrib . " is " . $data . "(more then " . $level .")" if $modifier eq '>' && $data>$level;
    return $attrib . " is " . $data . "(less then " . $level .")" if $modifier eq '<' && $data<$level;
    return "";
}

sub parse_netstatline {
    my ($line, $type) = @_;

    my @ar = split(/\s+/, $line);
    my ($loc, $rem, $state);
    ($loc, $rem, $state) = ($ar[3],$ar[4],$ar[5]) if $type eq 1;
    ($loc, $rem, $state) = ($ar[1],$ar[2],$ar[3]) if $type eq 2;
    $loc = $1 if $type eq 1 && $loc =~ /:(\d+)$/;
    $rem = $1 if $type eq 1 && $rem =~ /:(\d+)$/;
    $loc = $1 if $type eq 2 && $loc =~ /\.(\d+)$/;
    $rem = $1 if $type eq 2 && $rem =~ /\.(\d+)$/;
    return ($loc, $rem, $state);
}

sub tcpportname {
    my ($name, $inout) = @_;
    if (isnum($name)) { return "tcp".$name."_".$inout; }
    else { return $name."_".$inout; }
}

########## MAIN #######

check_options();

# Check global timeout if something goes wrong
if (defined($TIMEOUT)) {
  verb("Alarm at ".($TIMEOUT+10));
  alarm($TIMEOUT+10);
} else {
  verb("no timeout defined : $o_timeout + 10");
  alarm ($o_timeout+10);
}

# next part of the code builds list of attributes to be retrieved
my $statuscode = "OK";
my $statusinfo = "";
my $statusdata = "";
my $perfdata = "";
my $chk = "";
my $i;
my $port_local;
my $port_remote;
my $conn_state;
my %locports=(0=>0);
my %remports=(0=>0);
my $shell_command="";
my $shell_command_auth="";
my $netstat_format;
my $nlines=0;

if (defined($o_host) && defined($o_community)) {
    $shell_command = $snmpnetstat . " $o_host -n -P tcp -t $o_timeout";
    $shell_command.= " -v 2c" if defined($o_version2);
    $shell_command.= " -p $o_snmpport" if defined($o_snmpport);
    $shell_command_auth= "-c $o_community ";
    $netstat_format = 2;
}
else {
    $shell_command = 'netstat -n';
    $shell_command_auth = '';
    $netstat_format = 1;
}
verb("Executing $shell_command $shell_command_auth 2>&1");

# I would have prefrerred open3 [# if (!open3($cin, $cout, $cerr, $shell_command))]
# but there are problems when using it within nagios embedded perl

$netstat_pid=open(SHELL_DATA, "$shell_command $shell_command_auth 2>&1 |");
if (!$netstat_pid) {
    print "UNKNOWN ERROR - could not execute $shell_command - $!";
    exit $ERRORS{'UNKNOWN'};
}
while (<SHELL_DATA>) {
    $nlines++;
    verb("got line: $_");
    if (/^tcp/) {
	($port_local, $port_remote, $conn_state) = parse_netstatline($_, $netstat_format);
	verb("local_port: $port_local | remote_port: $port_remote | state: $conn_state");
	if (!defined($o_established) || $conn_state eq "ESTABLISHED") {
	    $locports{0}++;
	    if (defined($locports{$port_local})) { $locports{$port_local}++; }
	    else { $locports{$port_local}=1; }
	    $remports{0}++;
	    if (defined($remports{$port_remote})) { $remports{$port_remote}++; }
	    else { $remports{$port_remote}=1; }
	}
    }
    else {
      # codepace for the future for non-tcp checks
    }
}
if (!close(SHELL_DATA)) {
    print "UNKNOWN ERROR - execution of $shell_command resulted in an error $? - $!";
    exit $ERRORS{'UNKNOWN'};
}
if ($nlines eq 0) {
    print "UNKNOWN ERROR - did not receive any results from $shell_command";
    exit $ERRORS{'UNKNOWN'};
}

# loop to check if warning & critical attributes are ok
for ($i=0;$i<scalar(@o_attrL);$i++) {
    if ($o_attrLp[$i] eq '<') {
	$locports{$o_attrLn[$i]}=0 if !defined($locports{$o_attrLn[$i]});
	if ($chk = check_value($o_attrL[$i],"in",$locports{$o_attrLn[$i]},$o_critLv[$i],$o_critLp[$i])) {
		$statuscode = "CRITICAL";
		$statusinfo .= $chk;
	}
	elsif ($chk = check_value($o_attrL[$i],"in",$locports{$o_attrLn[$i]},$o_warnLv[$i],$o_warnLp[$i])) {
               	$statuscode="WARNING" if $statuscode eq "OK";
                $statusinfo .= $chk;
        }
    	else {
		$statusdata .= "," if ($statusdata);
		$statusdata .= " ". tcpportname($o_attrL[$i],"in") ." is ". $locports{$o_attrLn[$i]};
    	}
        $perfdata .= " ". tcpportname($o_attrL[$i],"in") ."=". $locports{$o_attrLn[$i]} if defined($o_perf);
    }
    if ($o_attrLp[$i] eq '>') {
	$remports{$o_attrLn[$i]}=0 if !defined($remports{$o_attrLn[$i]});
	if ($chk = check_value($o_attrL[$i],"out",$remports{$o_attrLn[$i]},$o_critLv[$i],$o_critLp[$i])) {
		$statuscode = "CRITICAL";
		$statusinfo .= $chk;
	}
	elsif ($chk = check_value($o_attrL[$i],"out",$remports{$o_attrLn[$i]},$o_warnLv[$i],$o_warnLp[$i])) {
               	$statuscode="WARNING" if $statuscode eq "OK";
                $statusinfo .= $chk;
        }
    	else {
		$statusdata .= "," if ($statusdata);
		$statusdata .= " ". tcpportname($o_attrL[$i],"out") ." is ". $remports{$o_attrLn[$i]};
    	}
        $perfdata .= " ". tcpportname($o_attrL[$i], "out") ."=". $remports{$o_attrLn[$i]} if defined($o_perf);
    }
}
# add data for performance-only attributes
for ($i=0;$i<scalar(@o_perfattrL);$i++) {
    if ($o_perfattrLp[$i] eq '<') {
	$locports{$o_perfattrLn[$i]}=0 if !defined($locports{$o_perfattrLn[$i]});
	$perfdata .= " " . tcpportname($o_perfattrL[$i],"in") ."=". $locports{$o_perfattrLn[$i]};
	$statusdata.= "," if ($statusdata) && defined($o_perfcopy);
	$statusdata.= " ". tcpportname($o_perfattrL[$i],"in") ." is ". $locports{$o_perfattrLn[$i]} if defined($o_perfcopy);
    }
    if ($o_perfattrLp[$i] eq '>') {
	$remports{$o_perfattrLn[$i]}=0 if !defined($remports{$o_perfattrLn[$i]});
	$perfdata .= " " . tcpportname($o_perfattrL[$i],"out") ."=". $remports{$o_perfattrLn[$i]};
	$statusdata.= "," if ($statusdata) && defined($o_perfcopy);
	$statusdata.= " ". tcpportname($o_perfattrL[$i],"out") ." is ". $remports{$o_perfattrLn[$i]} if defined($o_perfcopy);
    }
}

$o_label .= " " if $o_label ne '';
print $o_label . $statuscode;
print " -".$statusinfo if $statusinfo;
print " -".$statusdata if $statusdata;
print " |".$perfdata if $perfdata;
print "\n";

exit $ERRORS{$statuscode};
