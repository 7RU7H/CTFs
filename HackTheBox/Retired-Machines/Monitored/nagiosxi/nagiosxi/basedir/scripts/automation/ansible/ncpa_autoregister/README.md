# Setup

You will need to edit two files to get this working:

1.  Set the IP address of your XI server in `ncpa_install_and_register.yml` like so:

```yml
  vars:
    xi_ip: '192.168.1.100'
```

2.  Set the XI API Key and the NCPA Token in the encrypted `secrets.yml` file:

  1.  Run `ansible-vault edit secrets.yml`
    * It may ask you for the password three times, this is a known ansible bug
  2.  Enter password `nagiosxi` as this is the example used
    * Feel free to change this as you see fit
  3.  File will look something like this, update to suit your environment:

```yml
---
xi_api_key: 'XI_API_KEY_HERE'
ncpa_token: 'NCPA_TOKEN_HERE'
```

Finally, just run `run.sh` and it should be good to go.

Currently supports the following OS/versions:

- CentOS 6
- CentOS 7
- Debian 9
- Ubuntu 17
- SLES 12

# Special Configuration

If you'd like to register hosts/services using domain names instead of ip addreses then run the following before running the playbook:

sed -i -e 's/ansible_default_ipv4.address/ansible_fqdn/g' /usr/local/nagiosxi/scripts/automation/ansible/ncpa_autoregister/roles/register_with_xi/tasks/main.yml
