#!/usr/bin/python3


all_chars = bytearray(range(1,256))

bad_chars = [
        b"\x23",
        b"\x3c",
        b"\x83",
        ]

# Add bad char to bad_chars as we discover them
for bad_char in bad_chars:
    all_chars = all_chars.replace(bad_char, b"")

result = ''.join(format(x, '02x') for x in all_chars)

print(f"{str(result)}")
