<?php
require_once( __DIR__ . "/../ChatGPT.php" );

/**
 * List keywords to the user
 *
 * @param array<string> $keywords A list of keywords
 */
function list_keywords( $keywords ) {

}

/**
 * Give the response to the user
 *
 * @param string $response The response to the user
 */
function give_response( string $response ) {

}

/**
 * Get the next excerpt from the PDF
 *
 * @param bool $next Set this to true always
 */
function next_excerpt( bool $next ) {

}

function get_keywords( string $question ) {
    $prompt = "I want to search for the answer to this question from a PDF file. Please give me a list of keywords that I could use to search for the information.

```
$question
```

Use the list_keywords function to respond.";

    $chatgpt = new ChatGPT( getenv("OPENAI_API_KEY") );
    $chatgpt->add_function( "list_keywords" );
    $chatgpt->smessage( "You a are a search keyword generator" );
    $chatgpt->umessage( $prompt );

    $response = $chatgpt->response( raw_function_response: true );
    $function_call = $response->function_call;

    $arguments = json_decode( $function_call->arguments, true );
    $keywords = strtolower( implode( " ", $arguments["keywords"] ) );
    $keywords = explode( " ", $keywords );

    return $keywords;
}

function answer_question( string $chunk, string $question ) {
    $prompt = "```
$chunk
```

Based on the above excerpt, what is the answer to the following question?

```
$question
```

If the answer to the question is included in the above text, respond with the give_response function. If it is not found in the given text, respond with the next_excerpt function.";

    $chatgpt = new ChatGPT( getenv("OPENAI_API_KEY") );
    $chatgpt->add_function( "give_response" );
    $chatgpt->add_function( "next_excerpt" );
    $chatgpt->smessage( "You are trying to find answers to the questions of the user from a PDF file. You will be provided with an excerpt of the PDF file. If the excerpt contains the answer to the question, use the give_response function to tell the answer to the user. Otherwise call the next_excerpt function to get the next excerpt from the PDF." );
    $chatgpt->umessage( $prompt );

    $response = $chatgpt->response( raw_function_response: true );

    if( ! isset( $response->function_call ) ) {
        return answer_question( $chunk, $question );
    }

    return $response->function_call;
}
