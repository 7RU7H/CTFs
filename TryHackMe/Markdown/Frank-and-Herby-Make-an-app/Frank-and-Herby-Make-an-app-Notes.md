# Notes

## Data 

IP: 
OS:
Hostname:
Domain:  / Domain SID:
Machine Purpose: 
Services:
Service Languages:
Users:
Credentials:

## Objectives

## Solution Inventory Map

### Todo 

### Done

```bash
microk8s kubectl get pod nginx-deployment-7b548976fd-77v4r -o yaml
apiVersion: v1
kind: Pod
metadata:
  annotations:
    cni.projectcalico.org/podIP: 10.1.133.238/32
    cni.projectcalico.org/podIPs: 10.1.133.238/32
  creationTimestamp: "2021-10-27T19:48:23Z"
  generateName: nginx-deployment-7b548976fd-
  labels:
    app: nginx
    pod-template-hash: 7b548976fd
  name: nginx-deployment-7b548976fd-77v4r
  namespace: default
  ownerReferences:
  - apiVersion: apps/v1
    blockOwnerDeletion: true
    controller: true
    kind: ReplicaSet
    name: nginx-deployment-7b548976fd
    uid: 3e23e71f-b91a-41de-a65a-e50629eb51ec
  resourceVersion: "1811248"
  selfLink: /api/v1/namespaces/default/pods/nginx-deployment-7b548976fd-77v4r
  uid: 29879983-7b7f-4143-a8b9-1eb34951fd6d
spec:
  containers:
  - image: localhost:32000/bsnginx
    imagePullPolicy: Always
    name: nginx
    ports:
    - containerPort: 80
      protocol: TCP
    resources: {}
    terminationMessagePath: /dev/termination-log
    terminationMessagePolicy: File
    volumeMounts:
    - mountPath: /usr/share/nginx/html
      name: local-stuff
    - mountPath: /var/run/secrets/kubernetes.io/serviceaccount
      name: kube-api-access-hc88j
      readOnly: true
  dnsPolicy: ClusterFirst
  enableServiceLinks: true
  nodeName: dev-01
  preemptionPolicy: PreemptLowerPriority
  priority: 0
  restartPolicy: Always
  schedulerName: default-scheduler
  securityContext: {}
  serviceAccount: default
  serviceAccountName: default
  terminationGracePeriodSeconds: 30
  tolerations:
  - effect: NoExecute
    key: node.kubernetes.io/not-ready
    operator: Exists
    tolerationSeconds: 300
  - effect: NoExecute
    key: node.kubernetes.io/unreachable
    operator: Exists
    tolerationSeconds: 300
  volumes:
  - hostPath:
      path: /home/frank/repos/dk-ml/assets
      type: ""
    name: local-stuff
  - name: kube-api-access-hc88j
    projected:
      defaultMode: 420
      sources:
      - serviceAccountToken:
          expirationSeconds: 3607
          path: token
      - configMap:
          items:
          - key: ca.crt
            path: ca.crt
          name: kube-root-ca.crt
      - downwardAPI:
          items:
          - fieldRef:
              apiVersion: v1
              fieldPath: metadata.namespace
            path: namespace
status:
  conditions:
  - lastProbeTime: null
    lastTransitionTime: "2021-10-27T19:48:23Z"
    status: "True"
    type: Initialized
  - lastProbeTime: null
    lastTransitionTime: "2022-12-24T09:25:26Z"
    status: "True"
    type: Ready
  - lastProbeTime: null
    lastTransitionTime: "2022-12-24T09:25:26Z"
    status: "True"
    type: ContainersReady
  - lastProbeTime: null
    lastTransitionTime: "2021-10-27T19:48:23Z"
    status: "True"
    type: PodScheduled
  containerStatuses:
  - containerID: containerd://b3f66b396d14e779cf72a245192eeb756f21275865c30662dcd95f14916c12f9
    image: localhost:32000/bsnginx:latest
    imageID: localhost:32000/bsnginx@sha256:59dafb4b06387083e51e2589773263ae301fe4285cfa4eb85ec5a3e70323d6bd
    lastState:
      terminated:
        containerID: containerd://a56f86268143a36ec7c2c06cd92ea57e2014e5a692e22f592865985c841243a0
        exitCode: 255
        finishedAt: "2021-10-29T12:09:13Z"
        reason: Unknown
        startedAt: "2021-10-29T02:17:45Z"
    name: nginx
    ready: true
    restartCount: 2
    started: true
    state:
      running:
        startedAt: "2022-12-24T09:25:25Z"
  hostIP: 10.10.123.185
  phase: Running
  podIP: 10.1.133.238
  podIPs:
  - ip: 10.1.133.238
  qosClass: BestEffort
  startTime: "2021-10-27T19:48:23Z"

```

      
      
