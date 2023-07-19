<?php
// load chat history
$chat = json_decode(
    file_get_contents( "chats/" . basename( $_POST['chat_id'] ) . ".json" ),
    true,
);

// handle file upload

if( isset( $_FILES['file']['name'] ) ) {
    $uploads_dir = __DIR__ . "/uploads";
    if( ! file_exists( $uploads_dir ) ) {
        mkdir( $uploads_dir );
    }

    // WARNING: This allows uploading of ANY files!
    //          Don't use this in production, or someone
    //          can upload PHP files to your uploads folder
    //          and run arbitrary code!
    move_uploaded_file( $_FILES['file']['tmp_name'], $uploads_dir . "/" . basename( $_FILES['file']['name'] ) );

    $file_message = "I have uploaded the file " . basename( $_FILES['file']['name'] ) . "\n\n";
} else {
    $file_message = "";
}

// concatenate messages
$message = trim( $file_message . $_POST['message'] );

// add new message to message history
$chat["messages"][] = [
    "role" => "user",
    "content" => $message
];

// save message history
file_put_contents(
    "chats/" . basename( $_POST['chat_id'] ) . ".json",
    json_encode( $chat )
);

// render message and get response
echo '<div class="user message" hx-trigger="load" hx-get="/get_response.php?chat_id='.htmlspecialchars( $_POST['chat_id'] ).'" hx-target=".messages" hx-swap="beforeend">'.nl2br( htmlspecialchars( $message ) ).'</div>';

// reset message form
echo '<script>messageform.reset()</script>';
