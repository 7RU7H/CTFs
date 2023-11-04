#!/usr/bin/env python3
from flask import Flask, jsonify, request, render_template, Response
from Crypto.Hash import MD5
import json, os, binascii
app = Flask(__name__)

@app.route('/')
def home():
    return(render_template("home.html"))

@app.route('/update', methods = ["POST"])
def update():
    if request.headers['Content-Type'] != "application/json":
        return("Invalid content type.")
    else:
        data = json.loads(request.data)
        if data['user'] != "clumsyadmin":
            return("Invalid username.")
        else:
            os.system("curl {} -o /home/clumsyadmin/app".format(data['url']))
            return("Update requested by {}. Restart the software for changes to take effect.".format(data['user']))

@app.route('/logs')
def readlogs():
  if request.headers.getlist("X-Forwarded-For"):
        ip = request.headers.getlist("X-Forwarded-For")[0]
  else:
        ip = "1.3.3.7"
  if ip == "localhost" or ip == "127.0.0.1":
    if request.args.get("file") == None:
        return("Error! No file specified. Use file=/path/to/log/file to access log files.", 404)
    else:
        data = ''
        with open(request.args.get("file"), 'r') as f:
            data = f.read()
            f.close()
        return(render_template("logs.html", data=data))
  else:
       return("WAF: Access Denied for this Host.",403)

@app.route('/version')
def version():
    hasher = MD5.new()
    appHash = ''
    with open("/home/clumsyadmin/app", 'rb') as f:
        d = f.read()
        hasher.update(d)
        appHash = binascii.hexlify(hasher.digest()).decode()
    return("1.0.0b{}".format(appHash))

@app.route('/restart', methods = ["GET", "POST"])
def restart():
    if request.method == "GET":
        return(render_template("restart.html"))
    else:
        os.system("killall app")
        os.system("bash -c '/home/clumsyadmin/app&'")
        return("Restart Successful.")
