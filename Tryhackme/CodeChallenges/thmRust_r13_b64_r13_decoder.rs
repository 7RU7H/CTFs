extern crate base64;
use std::str;

fn main() { //rot13 -> base64 -> ROT13 -> plaintext
    let encoded = "M3I6r2IbMzq9"; 
    let first: String = rot13_decoder(encoded);
    print!("\n{}", first);
    let second = b64helper(&first);
    print!("\n{:?}", second);
    let third: String = rot13_decoder(&second);
    print!("\n{}", third);
}
    fn b64helper(todecode: &str) -> String {
        let vb64 = base64::decode(todecode).unwrap();
        //print!("\n{:?}", vb64);
        let thm64 = str::from_utf8(&vb64).unwrap();
    return thm64.to_string()
}
fn rot13_decoder(todecode: &str) -> String {
    let upper_case_dict = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    let lower_case_dict = "abcdefghijklmnopqrstuvwxyz";
    let rot_uppercase:[&str;26] =["N","O","P","Q","R","S","T","U","V","W","X","Y","Z","A","B","C","D","E","F","G","H","I","J","K","L","M"];
    let rot_lowercase:[&str;26] =["n","o","p","q","r","s","t","u","v","w","x","y","z","a","b","c","d","e","f","g","h","i","j","k","l","m"];
    let mut thm = "".to_owned();
    let secret = todecode.chars();
    for x in secret {
    if x.is_ascii_lowercase() {
        let lc_index = lower_case_dict.chars().position(|c| c == x).unwrap();
        let conv_lc = rot_lowercase[lc_index];
        thm.push_str(conv_lc);
    } else if x.is_ascii_uppercase() {
        let uc_index = upper_case_dict.chars().position(|c| c == x).unwrap();
        let conv_uc = rot_uppercase[uc_index];
        thm.push_str(conv_uc);
    } else if x.is_ascii_digit() {
        thm.push(x);
    } else if x.is_ascii_punctuation() {
        thm.push(x);
    } else {
        eprint!("\nError non ascii alphanumberic {}", x);
    }
    }
    return thm
    }
