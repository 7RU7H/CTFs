package main

import (
	"bufio"
	"flag"
	"fmt"
	"log"
	"os"
	"sort"
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

func printAndExit() {
	usage := "\nUsage wlSmash [options]\n-h\thelp\n-i\t a file with list of file paths to wordlists\n\t-o\tfile path to output\n"
	fmt.Printf("%s", usage)
	flag.PrintDefaults()
	os.Exit(1)

}

func validFilePathsFromFile(fileslist string) bool {
	checkFileExists(fileslist)

	filepaths, err := os.Open(fileslist)
		if err != nil {
			fmt.Println("Error opening file:", err)
			continue
		}
		// 
		


		defer f.Close()

	return 
}

func main() {
	var helpFlag string
	var inputFileFlag string
	var outputFileFlag string
	var userInputChoice string
	var userOutputChoice string
	var urltypeFlag string

	fmt.Printf("\n")
	flag.StringVar(&helpFlag, "-h", "help", "")
	flag.StringVar(&inputFileFlag, "-i", "needsinputfilepath", "")
	flag.StringVar(&outputFileFlag, "-o", "needsoutputfilepath", "")
	flag.Parse()
	
	args := flag.Args()
	//argsLen := len(args)

	switch os.Args[1] {
	case "-h":
		printAndExit()
	case "-o":
		userOutputChoice = os.args[1]
	case "-i":
		userInputChoice = os.args[1]
	default:
		//Invalid flag
		printAndExit()
	}

	switch os.Args[2] {
	case "-o":
		userOutputChoice = os.args[2]
	case "-i":
		userInputChoice = os.args[2]
	default:
		//Invalid flag
		printAndExit()
	}

	// Check valid file paths
	files := validFilePathsFromFile()
	// Prompt if user wants to exit if some but not all files found

	// Check if output file exists
	if !checkFileExist(userOutputChoice) {
		outputFileName = userOutputChoice
	} else {
		fmt.Printf("Invalid output file path, the file already exists!\n\n")
		printAndExit()
	}

	for _, file := range files {
		f, err := os.Open(file)
		if err != nil {
			fmt.Println("Error opening file:", err)
			continue
		}
		defer f.Close()

		scanner := bufio.NewScanner(f)
		scanner.Split(bufio.ScanWords)
		// Create a map to store unique words
		uniqueWords := make(map[string]bool)

		for scanner.Scan() {
			word := scanner.Text()
			// Add the word to the map if it's not already there
			if !uniqueWords[word] {
				uniqueWords[word] = true
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
