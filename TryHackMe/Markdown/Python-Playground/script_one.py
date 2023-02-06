#!/usr/share/python3

def int_array_to_string(int_array):
    result = ""
    i = 0
    while i < len(int_array):
        char_code = int_array[i] * 26
        i += 1
        char = chr(char_code + int_array[i])
        result += char
        i += 1

    return result


def string_to_int_array(string):
    result = []
    for i in string:
        result.append(ord(i) - 97)
    return result


def main():
    hash = "dxeedxebdwemdwesdxdtdweqdxefdxefdxdudueqduerdvdtdvdu"
    print(int_array_to_string(string_to_int_array(int_array_to_string(string_to_int_array(hash)))))
    exit()
    
if __name__ == '__main__':
    main()
