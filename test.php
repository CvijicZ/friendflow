<?php session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Page</title>
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include your custom JavaScript -->
    <script src="path/to/your/script.js"></script>
</head>
<body>
    <!-- Your HTML content -->
    <input type="text" id="messageInput" placeholder="Type your message...">
    <button id="sendBtn">Send</button>

    <script>
        $(function() {
            // WebSocket connection setup
            var websocket = new WebSocket("ws://localhost:8080");

            // WebSocket event handlers
            websocket.onopen = function() {
                console.log("WebSocket connection established.");
            };

            websocket.onmessage = function(event) {
                var message = event.data;
                console.log("Message received: " + message);
                // Handle message display or processing
            };

            websocket.onerror = function(event) {
                console.error("WebSocket error: " + event);
            };

            websocket.onclose = function() {
                console.log("WebSocket connection closed.");
            };

            // Function to send message
            function sendMessage(toUserId, messageContent) {
                var message = {
                    to: toUserId,
                    message: messageContent
                };
                websocket.send(JSON.stringify(message));
            }

         
        });
    </script>
</body>
</html>
