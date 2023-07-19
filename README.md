# GPT-FileChat

This simple chatbot made with HTMX, PHP and the ChatGPT API allows you to upload your own files (PDFs or text files) and ask questions about them.

It uses a very simple method of asking ChatGPT for keywords related to the question and then finding the part in the text which is most relevant to those keywords. This part is then passed to ChatGPT, which will attempt to answer the question based on that.

## NOT for Production!

**WARNING:** The chatbot allows for uploading of ANY files, so don't deploy it on a public server unless you implement protection against uploading unwanted files or move the uploads directory outside a public directory. Someone might upload a PHP file and run arbitrary code on your server!

## How to use

```console
$ export OPENAI_API_KEY=YOUR_API_KEY
$ php -S localhost:8080
```
