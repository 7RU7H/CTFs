#include <windows.h>
#include <stdio.h>

int main( void )
{
	FILE *fp = fopen("C:\\programdata\\c.exe", "rb");
	if (fp == NULL) {
	    printf("Failed to open file\n");
	    return 1;
	}
	fseek(fp, 0, SEEK_END);
	long filesize = ftell(fp);
	fseek(fp, 0, SEEK_SET);

	char *buffer = malloc(filesize);
	if (buffer == NULL) {
	    printf("Failed to allocate memory\n");
	    fclose(fp);
	    return 1;
	}

	if (fread(buffer, 1, filesize, fp) != filesize) {
	    printf("Failed to read file\n");
	    free(buffer);
	    fclose(fp);
	    return 1;
	}

	fclose(fp);

	PIMAGE_DOS_HEADER dosHeader = (PIMAGE_DOS_HEADER)buffer;
	PIMAGE_NT_HEADERS ntHeader = (PIMAGE_NT_HEADERS)((DWORD_PTR)buffer + dosHeader->e_lfanew);

	PVOID pImage = VirtualAlloc(NULL, ntHeader->OptionalHeader.SizeOfImage, MEM_COMMIT, PAGE_EXECUTE_READWRITE);

	if (pImage == NULL) {
		printf("Failed to allocate memory\n");
		free(buffer);
  	 	return 1;
	}
	
	memcpy(pImage, buffer, ntHeader->OptionalHeader.SizeOfHeaders);
	void (*entryPoint)() = (void(*)())(pImage + ntHeader->OptionalHeader.AddressOfEntryPoint);
	entryPoint();
}