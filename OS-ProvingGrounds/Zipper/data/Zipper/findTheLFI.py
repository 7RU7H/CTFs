#!/usr/bin/python3

import requests
import re

# Globals TODO - localise!
uploads_upload_pattern = 'uploads/uploads_^[0-9]{10}\.zip'
wordlist =

# Load a file for payloads
with open(wordlist, "r") as f:
	payloads = f.read()
	for i,payload in enumerate(payloads):

# Send request of the correct format replace FUZZ with a line from the payload file
	r = requests.get()
	
# Take the request and extract the link for line containing 'uploads/upload_'
	payload_zipped_path_line = re.findall(uploads_upload_pattern, r.text)
	
# some f string function to cut out the noise for just uploads/upload_NUMS.zip
	
	payload_zipped_path = #  payload_zipped_path_line 
# download the file 
	url_concat_with_payload_path = f"{URL}{payload_zipped_path}"

# get the name in the response
	payload_zip_filename

# TODO set a path of /tmp
	r = requests.get('url_concat_with_payload_path')

# unzip and cat content to stdin
	unzippable_filepath = f"tmp/{payload_zip_filename}"
	# unzip some how and getting the name
exfil_filename = # TODO ?
exfil_filename_with_path
	with open(exfil_filename_with_path, "r") as f:
	    exfil_data = f.read()	
		print(f"ZIP name - Exfil Filename - Payload")
		print(f"{payload_zip_filename} - {} {}")
		print(exfil_data)

exit
