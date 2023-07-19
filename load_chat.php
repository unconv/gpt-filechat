<?php
$chat = json_decode(
    file_get_contents( "chats/" . basename( $_GET['chat_id'] ) . ".json" ),
    true,
);

echo '<div class="messages">';
foreach( $chat["messages"] as $message ) {
    echo '<div class="'.$message['role'].' message">'.nl2br( htmlspecialchars( $message["content"] ) ).'</div>';
}
echo '</div>';

?>
<form id="messageform" autocomplete="off" hx-encoding="multipart/form-data" hx-post="/send_message.php" hx-target=".messages" hx-swap="beforeend">
    <input type="hidden" name="chat_id" value="<?php echo htmlspecialchars( $_GET['chat_id'] ); ?>" />
    <div class="upload-field">
        Upload file: <input type="file" id="upload-file" name="file" />
    </div>
    <div class="message-div">
        <input type="text" name="message" />
        <button>Send</button>
    </div>
</form>