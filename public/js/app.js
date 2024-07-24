
import { initChat, getNumberOfUnseenMessages, createFriendsModal, showMessage, updateChatPositions } from '/friendflow/public/js/chat.js';
import { initializeObserver } from '/friendflow/public/js/observer.js';
import { initWebSockets, sendMessage } from '/friendflow/public/js/websockets.js';
import { deletePost, updatePost, addComment } from '/friendflow/public/js/post.js';
import { addFriend, acceptFriendRequest } from '/friendflow/public/js/friend.js';

$(document).ready(function () {
    // Initializing required components on page load
    initWebSockets();
    getNumberOfUnseenMessages();
    initializeObserver();
    initChat();

    // Chat related functions, script: chat.js
    $(document).on('click', '#friendRequestsBtn', function () {
        createFriendsModal();
    });

    // ** Friend requests related functions, script: friend.js

    // TODO: add logic to deny friend request
    $('.friend-requests-container').on('click', '.deny-friend-request', function () {
        console.log("aaa");
    });

    // Accept friend request
    $('.friend-requests-container').on('click', '.accept-friend-request', function () {

        let csrfToken = $('meta[name="csrf-token"]').attr('content');
        let friendRequestId = $(this).closest('.friend-request').data('friend-request-id');

        acceptFriendRequest(friendRequestId, csrfToken)
            .then(() => {
                $('.friend-request').filter(`[data-friend-request-id="${friendRequestId}"]`).remove();
            })
            .catch(error => {
                showAlert(error.message, "danger");
            });
    });

    // Add friend
    $(".add-friend").click(function () {
        let parentDiv = $(this).closest('.suggestion');
        let receiverId = parentDiv.data('user-id');
        let csrfToken = $('meta[name="csrf-token"]').attr('content');

        addFriend(receiverId, csrfToken)
            .then(() => {
                showAlert("Friend request sent");
                parentDiv.remove();

            })
            .catch(error => {
                showAlert(error.message, "danger");
            });
    });

    // ** Posts related functions, script: post.js
    // Add comment TODO: there is too much logic just for animation to be shown, fix it
    $(".add-comment").click(function () {
        let postId = $(this).closest('div').data('post-id');
        let content = $(this).closest('div').find('.comment-content').val();
        let csrfToken = $('meta[name="csrf-token"]').attr('content');

        let userName = $('#auth-user-name').text();
        let parentDiv = $('#comments_' + postId);
        let contentDiv = parentDiv.find('.card.card-body');
        let commentFormDiv = parentDiv.find('.comment-form');
        let textArea = parentDiv.find('.comment-content');
        let imageName=$('#auth-user-image-name').val();

        addComment(postId, content, csrfToken)
            .then(result => {
                let commentDiv = ` 
            <div class="media mb-3 new-comment">
                <img src="app/storage/images/profile_images/${imageName}" class="mr-3 rounded-circle" alt="Commenter Profile" style="width:48px;height:48px;">
                  <div class="media-body">
                    <h6 class="mt-0">
                      ${userName}
                    </h6>
                 <p>${content}</p>
                  </div>
            </div>
            <hr class="bg-light">`;

                textArea.val('');
                commentFormDiv.before(commentDiv);

                contentDiv.find('.new-comment').hide();
                contentDiv.find('.new-comment').slideDown(800);
            });
    });

    // Post delete
    $(".delete-btn").click(function () {
        let postIdToDelete = $(this).data('post-id');
        $('#deleteModal').modal('show');
        deletePost(postIdToDelete);
    });
    // Update post request
    $('.edit-btn').click(function () {
        let postIdToUpdate = $(this).data('post-id');
        let currentText = $('#post-content-' + postIdToUpdate).text().trim();

        updatePost(postIdToUpdate, currentText);
    });

    const maxChats = 5;
    const openChats = new Set();

    // Function to load messages via AJAX
    function loadMessages(chatBox, friendId, userId, prepend = false) {
        let limit = 10;
        let offset = chatBox.find('.message').length;
        let csrfToken = $('meta[name="csrf-token"]').attr('content');

        let friendName = chatBox.find('#chat-friend-name').text();
        let friendImage = "https://via.placeholder.com/40";

        let scrollTop = chatBox.find('.messages').scrollTop();
        let initialHeight = chatBox.find('.messages')[0].scrollHeight;

        $.ajax({
            url: '/friendflow/get-friend-messages',
            method: 'POST',
            data: {
                user_id: userId,
                friend_id: friendId,
                limit: limit,
                offset: offset,
                csrf_token: csrfToken
            },
            dataType: 'json',
            success: function (data) {
                if (data.status === 'success') {
                    let messages = data.messages;

                    // Sort messages in ascending order by created_at
                    messages.sort(function (a, b) {
                        return new Date(a.created_at) - new Date(b.created_at);
                    });

                    if (prepend) {
                        messages.reverse();
                    }

                    function appendMessages(messages, prepend) {
                        messages.forEach(function (message) {
                            let senderName = message.sender_id == userId ? 'You' : friendName;
                            let senderImage = message.sender_id == userId ? "https://via.placeholder.com/40" : friendImage;
                            let senderId = message.sender_id == userId ? userId : friendId;

                            let messageHtml = showMessage(chatBox, message.message, senderId, senderName, senderImage, prepend);
                            if (message.status == 'sent' && message.recipient_id == userId) {
                                $(messageHtml).attr('data-id', message.id);
                                $(messageHtml).addClass('unseen');
                            }

                            if (prepend) {
                                chatBox.find('.messages').prepend(messageHtml);
                            } else {
                                chatBox.find('.messages').append(messageHtml);
                            }
                        });
                    }

                    if (prepend) {
                        appendMessages(messages, true);
                        let newHeight = chatBox.find('.messages')[0].scrollHeight;
                        chatBox.find('.messages').scrollTop(newHeight - initialHeight + scrollTop);
                    } else {
                        appendMessages(messages, false);
                        chatBox.find('.messages').scrollTop(chatBox.find('.messages')[0].scrollHeight);
                    }

                    // Reinitialize the observer for the newly appended unseen messages
                    initializeObserver();
                }
            },

            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    }

    // Detect scroll event for loading older messages
    $('body').on('scroll', '.messages', function () {
        let chatBox = $(this).closest('.chat-box');
        let scrollTop = $(this).scrollTop();
        let friendId = chatBox.data('id');
        let userId = $('#auth-user-id').val();

        if (scrollTop === 0) {
            loadMessages(chatBox, friendId, userId, true);
        }
    });

    $('body').on('click', '.friend', function () {
        let friendId = $(this).data('id');
        let friendName = $(this).data('name');
        let friendImage = $(this).find('img').attr('src');

        if (openChats.has(friendId)) {
            $('#chatModal').modal('hide');
            return;
        }

        if (openChats.size >= maxChats) {
            alert('You can only open up to ' + maxChats + ' chat boxes.');
            return;
        }

        openChats.add(friendId);

        let chatBox = $(
            '<div class="chat-box" data-id="' + friendId + '">' +
            '<div class="chat-header">' +
            '<div class="d-flex align-items-center">' +
            '<img src="' + friendImage + '" alt="Friend">' +
            '<span id="chat-friend-name">' + friendName + '</span>' +
            '</div>' +
            '<div class="close-chat">&times;</div>' +
            '</div>' +
            '<div class="messages"></div>' +
            '<input type="text" class="form-control" placeholder="Type a message...">' +
            '<button class="btn btn-primary btn-sm mt-2 send-message">Send</button>' +
            '</div>'
        );

        $('body').append(chatBox);
        updateChatPositions();
        chatBox.show();

        $('#chatModal').modal('hide');

        loadMessages(chatBox, friendId, $('#auth-user-id').val());
        initializeObserver();

        chatBox.on('click', '.close-chat', function () {
            let chatBox = $(this).closest('.chat-box');
            let friendId = chatBox.data('id');
            openChats.delete(friendId);
            chatBox.remove();
            updateChatPositions();
        });

        // Handle send message button click
        chatBox.on('click', '.send-message', function () {
            let messageInput = chatBox.find('input');
            let userId = $('#auth-user-id').val();
            let userName = $('#auth-user-name').val();
            let receiverId = chatBox.data('id');
            let message = messageInput.val();
            let userImage = "https://via.placeholder.com/40";

            if (message) {
                sendMessage(receiverId, message);
                showMessage(chatBox, message, userId, userName, userImage);
                initializeObserver();
                messageInput.val('');
                $('.messages').scrollTop($('.messages')[0].scrollHeight);
            }
        });

        // Handle Enter key press for sending messages
        chatBox.on('keydown', 'input', function (e) {
            if (e.keyCode === 13 && !e.shiftKey) {
                e.preventDefault();
                chatBox.find('.send-message').click();
            }
        });

        // Attach scroll event to the messages div within the newly created chat box
        chatBox.find('.messages').on('scroll', function () {
            let scrollTop = $(this).scrollTop();

            if (scrollTop === 0) {
                loadMessages(chatBox, friendId, $('#auth-user-id').val(), true);
            }
        });

        $('[data-toggle="collapse"]').on('click', function () {
            let target = $(this).data('target');
            $(target).collapse('toggle');
        });
    });

});

function showAlert(message, type = 'success') {
    let $alert = $('#alertTemplate').clone();
    $alert.find('#alertMessage').text(message);

    $alert.addClass('alert alert-dismissible fade show alert-' + type)
        .removeClass('d-none');

    $('#alerts-container').empty().append($alert);

    setTimeout(function () {
        $alert.alert('close');
    }, 5000);
}