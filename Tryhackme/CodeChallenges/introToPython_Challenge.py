import base64
file = open("PYEflag.txt", "r") 
data = file.read()
for i in range (0,5):
        data = base64.b16decode(data)
for i in range (0,5):
        data = base64.b32decode(data)
for i in range (0,5):
        data = base64.b64decode(data)
print(data)
file.close()
exit()
