### Kubelet Metrics (kubelet-metrics) found on http://192.168.164.181:3000
---
**Details**: **kubelet-metrics**  matched at http://192.168.164.181:3000

**Protocol**: HTTP

**Full URL**: http://192.168.164.181:3000/metrics

**Timestamp**: Thu Jun 2 10:12:20 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | Kubelet Metrics |
| Authors | sharath |
| Tags | tech, k8s, kubernetes, devops, kubelet |
| Severity | info |
| Description | Scans for kubelet metrics |

**Request**
```http
GET /metrics HTTP/1.1
Host: 192.168.164.181:3000
User-Agent: Mozilla/5.0 (X11; OpenBSD i386) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Transfer-Encoding: chunked
Cache-Control: no-cache
Content-Type: text/plain; version=0.0.4; charset=utf-8
Date: Thu, 02 Jun 2022 09:12:20 GMT
Expires: -1
Pragma: no-cache
X-Content-Type-Options: nosniff
X-Frame-Options: deny
X-Xss-Protection: 1; mode=block

# HELP access_evaluation_duration Histogram for the runtime of evaluation function.
# TYPE access_evaluation_duration histogram
access_evaluation_duration_bucket{le="1e-05"} 0
access_evaluation_duration_bucket{le="4e-05"} 0
access_evaluation_duration_bucket{le="0.00016"} 0
access_evaluation_duration_bucket{le="0.00064"} 0
access_evaluation_duration_bucket{le="0.00256"} 0
access_evaluation_duration_bucket{le="0.01024"} 0
access_evaluation_duration_bucket{le="0.04096"} 0
access_evaluation_duration_bucket{le="0.16384"} 0
access_evaluation_duration_bucket{le="0.65536"} 0
access_evaluation_duration_bucket{le="2.62144"} 0
access_evaluation_duration_bucket{le="+Inf"} 0
access_evaluation_duration_sum 0
access_evaluation_duration_count 0
# HELP access_permissions_duration Histogram for the runtime of permissions check function.
# TYPE access_permissions_duration histogram
access_permissions_duration_bucket{le="1e-05"} 0
access_permissions_duration_bucket{le="4e-05"} 0
access_permissions_duration_bucket{le="0.00016"} 0
access_permissions_duration_bucket{le="0.00064"} 0
access_permissions_duration_bucket{le="0.00256"} 0
access_permissions_duration_bucket{le="0.01024"} 0
access_permissions_duration_bucket{le="0.04096"} 0
access_permissions_duration_bucket{le="0.16384"} 0
access_permissions_duration_bucket{le="0.65536"} 0
access_permissions_duration_bucket{le="2.62144"} 0
access_permissions_duration_bucket{le="+Inf"} 0
access_permissions_duration_sum 0
access_permissions_duration_count 0
# HELP cortex_experimental_features_in_use_total The number of experimental features in use.
# TYPE cortex_experimental_features_in_use_total counter
cortex_experimental_features_in_use_total 0
# HELP deprecated_flags_inuse_total The number of deprecated flags currently set.
# TYPE deprecated_flags_inuse_total counter
deprecated_flags_inuse_total 0
# HELP go_gc_duration_seconds A summary of the pause duration of garbage collection cycles.
# TYPE go_gc_duration_seconds summary
go_gc_duration_seconds{quantile="0"} 9.8e-06
go_gc_duration_seconds{quantile="0.25"} 4.56e-05
go_gc_duration_seconds{quantile="0.5"} 5.672e-05
go_gc_duration_seconds{quantile="0.75"} 7.231e-05
go_gc_duration_seconds{quantile="1"} 0.0114227
go_gc_duration_seconds_sum 0.023149309
go_gc_duration_seconds_count 127
# HELP go_goroutines Number of goroutines that currently exist.
# TYPE go_goroutines gauge
go_goroutines 124
# HELP go_info Information about the Go environment.
# TYPE go_info gauge
go_info{version="go1.17.2"} 1
# HELP go_memstats_alloc_bytes Number of bytes allocated and still in use.
# TYPE go_memstats_alloc_bytes gauge
go_memstats_alloc_bytes 2.0793104e+07
# HELP go_memstats_alloc_bytes_total Total number of bytes allocated, even if freed.
# TYPE go_memstats_alloc_bytes_total counter
go_memstats_alloc_bytes_total 1.296973896e+09
# HELP go_memstats_buck_hash_sys_bytes Number of bytes used by the profiling bucket hash table.
# TYPE go_memstats_buck_hash_sys_bytes gauge
go_memstats_buck_hash_sys_bytes 1.610053e+06
# HELP go_memstats_frees_total Total number of frees.
# TYPE go_memstats_frees_total counter
go_memstats_frees_total 1.2105836e+07
# HELP go_memstats_gc_cpu_fraction The fraction of this program's available CPU time used by the GC since the program started.
# TYPE go_memstats_gc_cpu_fraction gauge
go_memstats_gc_cpu_fraction 0.00013463686662076497
# HELP go_memstats_gc_sys_bytes Number of bytes used for garbage collection system metadata.
# TYPE go_memstats_gc_sys_bytes gauge
go_memstats_gc_sys_bytes 6.132368e+06
# HELP go_memstats_heap_alloc_bytes Number of heap bytes allocated and still in use.
# TYPE go_memstats_heap_alloc_bytes gauge
go_memstats_heap_alloc_bytes 2.0793104e+07
# HELP go_memstats_heap_idle_bytes Number of heap bytes waiting to be used.
# TYPE go_memstats_heap_idle_bytes gauge
go_memstats_heap_idle_bytes 5.234688e+06
# HELP go_memstats_heap_inuse_bytes Number of heap bytes that are in use.
# TYPE go_memstats_heap_inuse_bytes gauge
go_memstats_heap_inuse_bytes 2.3207936e+07
# HELP go_memstats_heap_objects Number of allocated objects.
# TYPE go_memstats_heap_objects gauge
go_memstats_heap_objects 151286
# HELP go_memstats_heap_released_bytes Number of heap bytes released to OS.
# TYPE go_memstats_heap_released_bytes gauge
go_memstats_heap_released_bytes 0
# HELP go_memstats_heap_sys_bytes Number of heap bytes obtained from system.
# TYPE go_memstats_heap_sys_bytes gauge
go_memstats_heap_sys_bytes 2.8442624e+07
# HELP go_memstats_last_gc_time_seconds Number of seconds since 1970 of last garbage collection.
# TYPE go_memstats_last_gc_time_seconds gauge
go_memstats_last_gc_time_seconds 1.6541611397112153e+09
# HELP go_memstats_lookups_total Total number of pointer lookups.
# TYPE go_memstats_lookups_total counter
go_memstats_lookups_total 0
# HELP go_mems.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; OpenBSD i386) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36' 'http://192.168.164.181:3000/metrics'
```
---
Generated by [Nuclei 2.7.1](https://github.com/projectdiscovery/nuclei)