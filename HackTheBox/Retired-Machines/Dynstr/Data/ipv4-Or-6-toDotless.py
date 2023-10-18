import ipaddress

# Define the IP address
ipv4_address = '10.10.14.70'
ipv6_address = '2001:db8::1'

# Create an IP address object
ipv4_obj = ipaddress.ip_address(ipv4_address)
ipv6_obj = ipaddress.ip_address(ipv6_address)

# Convert the IP address object to an integer
dotless_ipv4 = int(ipv4_obj)
dotless_ipv6 = int(ipv6_obj)

print(f"The dotless varient of your ipv4 address is: {dotless_ipv4}")
print(f"The dotless varient of your ipv4 address is: {dotless_ipv6}")

