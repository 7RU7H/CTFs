# This is more the notes rather than the script itself


# From the 

# cython classes not used, this is just hack for a CTF


# Original Comments:
# Return a random element of `\Q/n\Z`.
# The denominator is selected
# using the ``1/n`` distribution on integers, modified to return
# a positive value.  The numerator is then selected uniformly.
# EXAMPLES::
#    sage: G = QQ/(6*ZZ)
#    sage: G.random_element().parent() is G
#    True 

class integer_ring:
    
    def __init__(self)
        self.ring = []
        return self


class rational_field:


# n is always generated as 0
# self.n = QQ(n).abs() 
# 0th elem 


def random_element(self):
            

       #if self == 0:
       #         return self(rational_field.random_element())
       #         d = integer_ring.random_element()
       # Basically make d an array
       d = []
       d_len = len(self)
       d  = [0 for i in range(d_len)] 
       if d >= 0:
           d = 2 * d + 1
       else:
           d = -2 * d
       n = ZZ.random_element((self.n * d).ceil())
       return self(n / d)


p = 7493025776465062819629921475535241674460826792785520881387158343265274170009282504884941039852933109163193651830303308312565580445669284847225535166520307
q = 7020854527787566735458858381555452648322845008266612906844847937070333480373963284146649074252278753696897245898433245929775591091774274652021374143174079
n = p*q
print(f"P * Q = N: \n{n}\n")
phi = (p-1)*(q-1)
print(f"(P-1)*(Q-1) = phi: \n{phi}\n")

phi_size = len(phi)
integer_ring_ZZ = []

# Pick a random number from phi 
# Len of phi,
# d = -2 * d ->  0* d.ceil() -> always be 0 ->self() n/d 0 -> 0th of phi is always
e = integer_ring_ZZ.random_element(phi)
print(f"Random Element testing: {E}")
while gcd(e, phi) != 1:
    e = ZZ.random_element(phi)




# Reverse pow() 
# Password is used as base, E as Expression and N is mod



def egcd(a, b):
    x,y, u,v = 0,1, 1,0
    while a != 0:
        q, r = b//a, b%a
        m, n = x-u*q, y-v*q
        b,a, x,y, u,v = a,r, u,v, m,n
        gcd = b
    return gcd, x, y
