package main

import (
	"bufio"
	"flag"
	"fmt"
	"log"
	"os"
	"sort"
	"strings"
)

func createFile(filepath string) {
	filePtr, err := os.Create("")
	if err != nil {
		log.Fatal(err)
	}
	defer filePtr.Close() // close the file
	// We can read from and write to the file
}

func checkFileExists(path string) bool {
	_, err := os.Stat(path)
	if err == nil {
		log.Fatal(err)
		return false
	}
	if os.IsNotExist(err) {
		log.Fatal("Path does not Exists")
		return false
	}
	return true
}
func buildSpecificMsg() {
	fmt.Printf("Files should not contain spaces in current build \n")
}

func printAndExit() {
	usage := "\nUsage wlSmash [options]\n-h\thelp\n-i\t a file with list of file paths to wordlists\n\t-o\tfile path to output\n"
	fmt.Printf("%s", usage)
	buildSpecificMsg()
	flag.PrintDefaults()
	os.Exit(1)
}

func covertByteToStr(b []byte) string {
	result := string(b[:])
	return result
}

// For file containing filepaths extract paths and return as a slice - no whitespace atm :(
func FilePathsAsSliceFromFile(fileslist string) []string {
	fileB, err := os.ReadFile(fileslist)
	if err != nil {
		fmt.Println("Error Reading file:", err)
	}
	fileStr := covertByteToStr(fileB)
	filepathSlice := strings.SplitAfterN(fileStr, " ", -1)
	return filepathSlice
}

func validFilePathsFromFile(fileslist string) bool {
	if !checkFileExists(fileslist) {
		return false
	}

	filepathsB, err := os.ReadFile(fileslist)
	if err != nil {
		fmt.Println("Error opening file:", err)
	}
	filepathsStr := covertByteToStr(filepathsB)

	filepathSlice := strings.SplitAfterN(filepathsStr, " ", -1)
	for _, path := range filepathSlice {
		if checkFileExists(path) != true {
			return false
		}
	}

	return true
}

func main() {
	var helpFlag string
	var inputFileFlag string
	var outputFileFlag string
	var userInputChoice string
	var userOutputChoice string

	fmt.Printf("\n")

	// Build specific message
	buildSpecificMsg()

	flag.StringVar(&helpFlag, "-h", "help", "")
	flag.StringVar(&inputFileFlag, "-i", "needsinputfilepath", "")
	flag.StringVar(&outputFileFlag, "-o", "needsoutputfilepath", "")
	flag.Parse()

	args := os.Args[:1]

	switch args[1] {
	case "-h":
		printAndExit()
	case "-o":
		userOutputChoice = args[1]
	case "-i":
		userInputChoice = args[1]
	default:
		//Invalid flag
		printAndExit()
	}

	switch args[2] {
	case "-o":
		userOutputChoice = args[2]
	case "-i":
		userInputChoice = args[2]
	default:
		//Invalid flag
		printAndExit()
	}

	// Check valid file paths
	if validFilePathsFromFile(userInputChoice) != true {
		fmt.Printf("Invalid intput file path, \n\n")
		printAndExit()
	}

	// Check if output file exists
	if checkFileExists(userOutputChoice) != true {
		fmt.Printf("Invalid output file path, the file already exists!\n\n")
		printAndExit()
	}
	fmt.Printf("All file check complete\n")
	files := FilePathsAsSliceFromFile(userInputChoice)
	fmt.Printf("Smashing Files together\n")
	for _, file := range files {
		uniqWordCounter := 0
		f, err := os.Open(file)
		if err != nil {
			fmt.Println("Error opening file:", err)
			continue
		}
		defer f.Close()

		scanner := bufio.NewScanner(f)
		scanner.Split(bufio.ScanWords)
		// Create a map to store unique words
		uniqueWords := make(map[int]string)

		for scanner.Scan() {
			words := scanner.Text()
			// Add the word to the map if it's not already there
			//
			//	RUNES != Strings
			// 
			for i, word := range words {
				if uniqueWords[i] != word {
					uniqueWords[uniqWordCounter] = word
					uniqWordCounter += 1
				}
			}
		}

		if err := scanner.Err(); err != nil {
			fmt.Println("Error reading file:", err)
		}
	}

	// Sort the words
	sortedWords := make([]string, 0, len(uniqueWords))
	for word := range uniqueWords {

		sortedWords = append(sortedWords, word)
	}
	sort.Strings(sortedWords)

	// Write the sorted, unique words to a new file
	outputFile, err := os.Create(outputFileName)
	if err != nil {
		fmt.Println("Error creating output file:", err)
		return
	}
	defer outputFile.Close()

	for _, word := range sortedWords {
		outputFile.WriteString(word + "\n")
	}
}
