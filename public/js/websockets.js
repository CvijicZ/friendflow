
let websocket;

import { showMessage } from '/friendflow/public/js/chat.js';
import { initializeObserver } from '/friendflow/public/js/observer.js';
import { generateCommentSection } from '/friendflow/public/js/post.js';

export function initWebSockets() {

    const WS_IP = "192.168.1.2"; // Put your ws server IP
    const WS_PORT = "8080"; // Put the port used for ws
    const WS_ADDRESS = WS_IP + ":" + WS_PORT; // Don't change this

    // Sockets
    function getCookie(name) {
        let value = `; ${document.cookie}`;
        let parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    let userId = $('#auth-user-id').val();
    let token = getCookie('jwtToken');

    if (!token) {
        console.error("JWT token not found in cookies");
        return;
    }

    // Create WebSocket with token included in the query parameters
    websocket = new WebSocket(`ws://${WS_ADDRESS}/chat?token=${encodeURIComponent(token)}&user_id=${userId}`);

    websocket.onopen = function () {
        console.log("WebSocket connection established.");
    };

    websocket.onmessage = function (event) {
        let data = JSON.parse(event.data);

        switch (data.type) {
            case 'connectedUsers':
                setTimeout(() => updateStatuses(data.users), 100);
                break;

            case 'newComment':
                let commentForm=$('[data-post-id="' + data.postId + '"]');
                let textarea = commentForm.find('textarea');

                if(textarea.val().trim() !== ''){ // If user is writing comment do not regenerate comment section
                    return;
                }
                generateCommentSection(data.postId, true);

                break;

            case 'status':
                const userId = data.userId;
                const status = data.status;

                // Update the status dot for the user
                setStatus(userId, status);
                break;

            case 'unseenMessages':
                $('.unseen-messages-number').text(data.numberOfMessages);
                break;

            case 'newMessage':

                let message = data.message;
                let senderId = data.senderId;
                let senderName = `${data.senderName} ${data.senderSurname}`;
                let senderImage = `app/storage/images/profile_images/${data.senderImageName}`;
                // Find the chat box for the sender
                let chatBox = $(`.chat-box[data-id="${senderId}"]`);

                if (chatBox.is(':visible')) {
                    let messageHtml = showMessage(chatBox, message, senderId, senderName, senderImage);

                    $(messageHtml).attr('data-id', data.id);
                    $(messageHtml).addClass('unseen');

                    initializeObserver();
                }
                break;
        }
    };

    websocket.onerror = function (event) {
        console.error("WebSocket error:", event);
        if (event.message) {
            console.error("Error message:", event.message);
        }
    };

    websocket.onclose = function () {
        console.log("WebSocket connection closed.");
    };

    function setStatus(friendId, status) {
        const statusDot = $(`#status-dot-${friendId}`);

        if (status === 'online') {
            statusDot.removeClass('status-dot-offline').addClass('status-dot-online');
        } else if (status === 'offline') {
            statusDot.removeClass('status-dot-online').addClass('status-dot-offline');
        }
    }

    function updateStatuses(users) {
        let authUserId = $('#auth-user-id').val();
        setTimeout(() => {
            $.each(users, function (userId, status) {
                if (userId == authUserId) {
                    return;
                }
                setStatus(userId, status);
            });
        }, 100);
    }
}

export function sendMessage(recipientId, messageContent) {
    let message = {
        type: 'sendMessage',
        recipientId: recipientId,
        message: messageContent
    };
    websocket.send(JSON.stringify(message));
}

export function sendComment(postId) {
    let comment = {
        type: 'newComment',
        postId: postId
    }
    websocket.send(JSON.stringify(comment));
}