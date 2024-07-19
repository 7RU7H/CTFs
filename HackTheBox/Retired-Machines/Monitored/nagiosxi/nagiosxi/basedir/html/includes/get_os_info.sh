#!/bin/bash -e
# Gets OS information - Essentially a frontend for lsb_release but with
# extra logic for RHEL-based and SuSE-based systems, because minimal RHEL installations
# don't have lsb_release by default and SuSE doesn't have it either

# Make sure we're on a Linux distro
if [ `uname -s` != Linux ]; then
    echo "ERROR: Unsupported OS detected. This script only detects Linux distributions." >&2
    exit 2
fi

# Get architecture
architecture=`uname -m`

# Get distro
distro='RPM'
os='ERROR'
version='ERROR'

# Get OS & version
if which lsb_release &>/dev/null; then
    os=`lsb_release -si`
    version=`lsb_release -sr`
else
    # Last-ditch effort to find the OS by sourcing os-release if it exists
    if [ -r /etc/os-release ]; then
        source /etc/os-release
        if [ -n "$NAME" ]; then
            os=$NAME
            version=$VERSION_ID
        fi
    fi
fi

# Figure out the Distribution Package Management Type, .deb or .rpm.
if [ -r /etc/redhat-release ]; then
    distro='RPM'
elif which yum &>/dev/null; then
    distro='RPM'
elif which apt &>/dev/null; then
    distro='DEB'
else
    distro='UNKNOWN'
fi

# Get the name of the os' rpm, if the distro type is RPM.
if [[ "$distro" == 'RPM' ]]; then
    rpmName=`yum list installed | grep "release" | grep -v "eula" | grep -v "epel" | cut -d "." -f 1` >/dev/null
fi

# If the minor level is missing from the version info, try to find it (RPM distro).
if [[ $version =~ ^[0-9]+$ ]] && [ $rpmName ]; then
    # centos-release: CentOS <= 8.2
    # centos-linux-release: CentOS 8.3+
    # centos-stream-release: CentOS 8 Stream
    altVersion=$(rpm -q $rpmName | sed "s/$rpmName-\([0-9]\+\)\([-.]\)\([0-9]\+\)\([-.]\)\([0-9]\+\).*/\1\2\3\4\5/")

    #echo "altVersion $altVersion, length: ${#altVersion}"
    #echo "version $version, length: ${#version}"

    # Compare string lengths and use the longest.
    if [ ${#altVersion} > ${#version} ]; then
        version=$altVersion
    fi
fi

# Debian release numbers no longer include the point (minor) release numbers.
# Get the "point" release number from /etc/debian_version
if [ -r /etc/debian_version ]; then
    debianVersion=$(</etc/debian_version)

    if [[ $debianVersion =~ ^([0-9.-]*)[[:space:]]*$ ]]; then
        altVersion="${BASH_REMATCH[1]}"
    fi

    # Debugging
    #echo "debianVersion: $debianVersion, length: ${#debianVersion}"
    #echo "altVersion $altVersion, length: ${#altVersion}"
    #echo "version $version, length: ${#version}"

    # Compare string lengths and use the longest.
    if [ ${#altVersion} -gt ${#version} ]; then
        version=$altVersion
    fi
fi

# Add patch level to the version of SLES (because they don't...)
if [ "$os" == "SUSE LINUX" ]; then
    if [ -r /etc/SuSE-release ]; then
        patchlevel=$(cat /etc/SuSE-release | cut -d ' ' -f 3 -s | sed -n 3p)
        version="$version.$patchlevel"
    fi
fi

# Verify that we have a os now
if [ -z "$os" ]; then
    echo "ERROR: Could not determine OS. Please make sure lsb_release is installed or your OS info is in /etc/os-release." >&2
    exit 1
fi

# Output Data as JSON
echo "{\"os\":\"${os}\",\"version\":\"${version}\",\"architecture\":\"${architecture}\",\"distro\":\"${distro}\"}"
