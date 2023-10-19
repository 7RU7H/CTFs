from flask import Flask, request
import hashlib, os

app = Flask(__name__)

@app.route('/')
def f0():
   return "{'/generate', '/verify'}"

@app.route('/generate', methods=['GET','POST'])
def f1():
   if request.method == 'GET':
       return "{'email@domain'}"
   else:
       email = request.form['email'].encode('utf-8')
       return hashlib.sha256(email).hexdigest()

@app.route('/verify', methods=['GET','POST'])
def f2():
   if request.method == 'GET':
       return "{'code'}"
   else:
       code = request.form['code']
       result = eval(code)
       return str(result)

if __name__ == '__main__':
    app.run()
