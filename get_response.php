<?php
require_once("ChatGPT.php");
require_once("php-gpt-pdfread/gpt.php");
require_once("php-gpt-pdfread/lookup.php");
require_once("php-gpt-pdfread/pdfread.php");

function answer_question_from_file( string $filename, string $question ) {
    $keywords = get_keywords( $question );
    $filepath = __DIR__ . "/uploads/" . basename( $filename );

    if( substr( $filename, -4 ) == ".pdf" ) {
        $text_file = pdf_to_text( $filepath );
    } else {
        $text_file = $filepath;
    }

    $chunks = chunk_text_file( $text_file );
    $matches = find_matches( $chunks, $keywords );

    $chunk_number = 0;
    $limit = 5;
    foreach( $matches as $chunk_id => $points ) {
        if( $chunk_number++ > $limit ) {
            break;
        }

        $answer = answer_question( $chunks[$chunk_id], $question );

        if( isset( $answer->name ) && $answer->name == "give_response" ) {
            $arguments = json_decode( $answer->arguments, true );
            $response = $arguments["response"];
            return json_encode([
                "status" => "OK",
                "response" => $response,
            ]);
        }
    }

    if( ! isset( $answer->name ) || $answer->name != "give_response" ) {
        return json_encode([
            "status" => "FAIL",
            "response" => "Unable to find answer",
        ]);
    }
}

$chat = json_decode(
    file_get_contents( "chats/" . basename( $_GET['chat_id'] ) . ".json" ),
    true,
);

$message = end( $chat["messages"] );

$chatgpt = new ChatGPT( getenv("OPENAI_API_KEY"), $_GET['chat_id'] );
$chatgpt->loadfunction( function( $chat_id ) use ( $chat ) {
    return $chat["messages"];
} );
$chatgpt->load();
$chatgpt->add_function( "answer_question_from_file" );
$chatgpt->umessage( $message["content"] );

$response = (array)$chatgpt->response();

$chat["messages"][] = $response;

file_put_contents(
    "chats/" . basename( $_GET['chat_id'] ) . ".json",
    json_encode( $chat )
);

if( empty( $chat["name"] ) || $chat["name"] == "Untitled chat" ) {
    $create_title = 'hx-trigger="load" hx-get="/create_title.php?chat_id='.htmlspecialchars( $_GET['chat_id'] ).'" hx-target=".chat-'.htmlspecialchars( $_GET['chat_id'] ).'"';
} else {
    $create_title = '';
}

echo '<div class="assistant message" '.$create_title.'>'.nl2br( htmlspecialchars( $response["content"] ) ).'</div>';
