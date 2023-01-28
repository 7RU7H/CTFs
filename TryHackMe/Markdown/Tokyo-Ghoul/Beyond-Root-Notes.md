[default-packages](https://book.hacktricks.xyz/generic-methodologies-and-resources/python/bypass-python-sandboxes#default-packages)
[pip]([Hacktricks](https://book.hacktricks.xyz/generic-methodologies-and-resources/python/bypass-python-sandboxes#pip-package)
[other-libraries-that-allow-to-eval-python-code](https://book.hacktricks.xyz/generic-methodologies-and-resources/python/bypass-python-sandboxes#other-libraries-that-allow-to-eval-python-code)
[bypassing-protections-through-encodings-utf-7](https://book.hacktricks.xyz/generic-methodologies-and-resources/python/bypass-python-sandboxes#bypassing-protections-through-encodings-utf-7)
[python-execution-without-calls](https://book.hacktricks.xyz/generic-methodologies-and-resources/python/bypass-python-sandboxes#python-execution-without-calls)
[search for malicious libraries](https://book.hacktricks.xyz/generic-methodologies-and-resources/python/bypass-python-sandboxes#finding-dangerous-libraries-loaded)

```python
import argparse

def prep_user_input(user_input, isFile)
if isFile == True:
with open(file, "r") as hacktricks_list:
    hacktricks_list_contents = hacktricks_list.read()
    user_input_as_arr = hacktricks_list_contents.split("\n")
else:
	[0] = user_input

for index, word in enumerate(user_input_as_arr):
	if word not in previous_attempts:
		previous_attempts[word] = index

remain_known_possiblities
print()

def check_against_ht_wordlist(user_input, isFile)
	data = prep_user_input(user_input,isFile)
	with open(file, "r") as hacktricks_list:
	    hacktricks_list_contents = hacktricks_list.read()
	     = hacktricks_list_contents.split("\n")

found_words = []
not_found_words = []
for index, word in enumerate(args.array):
    if word in file_contents:
        found_words.append(word)
    else:
        not_found_words.append(word)


def main():
parser = argparse.ArgumentParser()

# add arguments to the parser
parser.add_argument("-s", help="Enter a string")
parser.add_argument("-f", help="Enter a file path")

# parse the arguments
args = parser.parse_args()

# check if string argument is provided
if args.string:
    print("Have attempted:", args.string)
	print("Checked against wordlist:")
	user_input = args.string
	check_against_ht_wordlist(user_input, False)

	
# check if file argument is provided
if args.file:
    try:
        with open(args.file, "r") as file:
            test_file = file.readline()            
    except FileNotFoundError:
        print("Error: File not found")
        exit()
    except:
        print("Error: Unable to read file")
        exit()
	user_input = args.file
	check_against_ht_wordlist(user_input, True)

```
	    
The only copy a paste I has done is the following script. The reason is to augement the speed of searching for way to bypass these snadboxes with a host ran script and wordlist to manage attempt made and reduce next phases of continuous enumerations. This is useful if there are custom libraries and modules, you can replicate  - [Script a recurisve search](https://book.hacktricks.xyz/generic-methodologies-and-resources/python/bypass-python-sandboxes#recursive-search-of-builtins-globals...)
```python
import os, sys # Import these to find more gadgets

SEARCH_FOR = {
    # Misc
    "__globals__": set(),
    "builtins": set(),
    "__builtins__": set(),
    "open": set(),
    
    # RCE libs
    "os": set(),
    "subprocess": set(),
    "commands": set(),
    "pty": set(),
    "importlib": set(),
    "imp": set(),
    "sys": set(),
    "pip": set(),
    "pdb": set(),
    
    # RCE methods
    "system": set(),
    "popen": set(),
    "getstatusoutput": set(),
    "getoutput": set(),
    "call": set(),
    "Popen": set(),
    "popen": set(),
    "spawn": set(),
    "import_module": set(),
    "__import__": set(),
    "load_source": set(),
    "execfile": set(),
    "execute": set()
}

#More than 4 is very time consuming
MAX_CONT = 4

#The ALREADY_CHECKED makes the script run much faster, but some solutions won't be found
#ALREADY_CHECKED = set()

def check_recursive(element, cont, name, orig_n, orig_i, execute):
    # If bigger than maximum, stop
    if cont > MAX_CONT:
        return
    
    # If already checked, stop
    #if name and name in ALREADY_CHECKED:
    #    return
    
    # Add to already checked
    #if name:
    #    ALREADY_CHECKED.add(name)
    
    # If found add to the dict
    for k in SEARCH_FOR:
        if k in dir(element) or (type(element) is dict and k in element):
            SEARCH_FOR[k].add(f"{orig_i}: {orig_n}.{name}")
    
    # Continue with the recursivity
    for new_element in dir(element):
        try:
            check_recursive(getattr(element, new_element), cont+1, f"{name}.{new_element}", orig_n, orig_i, execute)
            
            # WARNING: Calling random functions sometimes kills the script
            # Comment this part if you notice that behaviour!!
            if execute:
                try:
                    if callable(getattr(element, new_element)):
                        check_recursive(getattr(element, new_element)(), cont+1, f"{name}.{new_element}()", orig_i, execute)
                except:
                    pass
        
        except:
            pass
    
    # If in a dict, scan also each key, very important
    if type(element) is dict:
        for new_element in element:
            check_recursive(element[new_element], cont+1, f"{name}[{new_element}]", orig_n, orig_i)


def main():
    print("Checking from empty string...")
    total = [""]
    for i,element in enumerate(total):
        print(f"\rStatus: {i}/{len(total)}", end="")
        cont = 1
        check_recursive(element, cont, "", str(element), f"Empty str {i}", True)
    
    print()
    print("Checking loaded subclasses...")
    total = "".__class__.__base__.__subclasses__()
    for i,element in enumerate(total):
        print(f"\rStatus: {i}/{len(total)}", end="")
        cont = 1
        check_recursive(element, cont, "", str(element), f"Subclass {i}", True)
    
    print()
    print("Checking from global functions...")
    total = [print, check_recursive]
    for i,element in enumerate(total):
        print(f"\rStatus: {i}/{len(total)}", end="")
        cont = 1
        check_recursive(element, cont, "", str(element), f"Global func {i}", False)
    
    print()
    print(SEARCH_FOR)


if __name__ == "__main__":
    main()
```


## References

[Hacktricks](https://book.hacktricks.xyz/generic-methodologies-and-resources/python/bypass-python-sandboxes)

