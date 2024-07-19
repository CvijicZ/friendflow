

$(document).ready(function () {
    // Get all friends
    let csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
        url: '/friendflow/get-all-friends',
        type: 'POST',
        data: {
            csrf_token: csrfToken
        },
        success: function (response) {
            if (response.status === "success") {

                response.data.forEach(friend => {
                    const friendDiv = `
                        <div class="friend mb-2" data-id="${friend.id}" data-name="${friend.name + " " + friend.surname}">
                            <div class="d-flex align-items-center">
                                <img src="https://via.placeholder.com/40" alt="Friend" class="mr-2">
                                <span>${friend.name + " " + friend.surname}</span><span class="status-dot"></span>
                            </div>
                        </div>
                    `;
                    $('.chat').append(friendDiv);
                });
            }
            if (response.status == 'error') {
                const errorMessage = '<p>' + response.message + '</p>'
                $('.chat').append(errorMessage);
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX error: ' + status + ' ' + error);
        }
    });




    // AJAX function to fetch friend requests
    $('#friendRequestsBtn').on('click', function () {
        $('.friend-requests-container').empty();

        let csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: '/friendflow/get-friend-requests',
            type: 'POST',
            data: {
                csrf_token: csrfToken
            },
            success: function (response) {
                if (response.status === "success") {
                    appendFriendRequests(response.data);
                    $('#friendRequestModal').modal('show');
                }
                if (response.status == 'error') {
                    $('.friend-requests-container').html("<p>No friend requests");
                    $('#friendRequestModal').modal('show');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX error: ' + status + ' ' + error);
                // Redirect to error page or handle error as needed
                // window.location.href = '/friendflow/error';
            }
        });
    });

    // Function to create friend request HTML
    function createFriendRequestElement(request) {
        let html = `
<div class="friend-request" data-friend-request-id="${request.id}">
    <div class="d-flex align-items-center justify-content-between w-100">
        <div class="d-flex align-items-center">
            <img src="https://via.placeholder.com/40" alt="Friend" class="mr-2">
            <span>${request.name} ${request.surname}</span>
        </div>
        <div class="ml-auto">
            <div class="btn-group">
                <button class="btn btn-success btn-sm accept-friend-request">Accept</button>
                <button class="btn btn-danger btn-sm ml-2 deny-friend-request">Remove</button>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end ml-2">
        <small class="text-muted">${request.datetime}</small>
    </div>
</div>
    `;

        // Create a container element to hold the HTML
        let container = document.createElement('div');
        container.innerHTML = html.trim();

        // Set data attributes or IDs dynamically if needed
        // Example: container.dataset.requestId = request.id;

        return container.firstChild;
    }

    // Function to append friend requests to the container
    function appendFriendRequests(data) {
        let container = document.querySelector('.friend-requests-container');

        data.forEach(function (request) {
            let friendRequestElement = createFriendRequestElement(request);
            container.appendChild(friendRequestElement);
        });
    }

    $('.friend-requests-container').on('click', '.deny-friend-request', function () {
        console.log("aaa");
    });
    // Accept friend request
    $('.friend-requests-container').on('click', '.accept-friend-request', function () {

        let csrfToken = $('meta[name="csrf-token"]').attr('content');
        let friendRequestId = $(this).closest('.friend-request').data('friend-request-id');

        if (csrfToken === undefined || friendRequestId === undefined) {
            console.log('Something went wrong'); // TODO: display error to the user
            return;
        }

        $.ajax({
            url: '/friendflow/accept-friend-request',
            method: 'POST',
            data: {
                friendRequestId: friendRequestId,
                csrf_token: csrfToken
            },
            success: function (response) {
                console.log(response);
                // if (response.status == "success") {
                //     showAlert("Friend request sent");
                //     parentDiv.remove(); // Remove the parent div
                // }
                // if (response.status == "error") {
                //     showAlert(response.message, "danger");
                // }
            },
            error: function (xhr, status, error) {
                // Handle error
                console.error('Error occurred during delete:', error);
            }
        });
    });

    // Add friend
    $(".add-friend").click(function () {
        let parentDiv = $(this).closest('.suggestion');
        let receiverId = parentDiv.data('user-id');
        let csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: '/friendflow/add-friend',
            method: 'POST',
            data: {
                receiverId: receiverId,
                csrf_token: csrfToken
            },
            success: function (response) {
                if (response.status == "success") {
                    showAlert("Friend request sent");
                    parentDiv.remove(); // Remove the parent div
                }
                if (response.status == "error") {
                    showAlert(response.message, "danger");
                }
            },
            error: function (xhr, status, error) {
                // Handle error
                console.error('Error occurred during delete:', error);
            }
        });
    });

    // Add comment
    $(".add-comment").click(function () {
        let postId = $(this).closest('div').data('post-id');
        let content = $(this).closest('div').find('.comment-content').val();
        let csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: '/friendflow/comment',
            method: 'POST',
            data: {
                postId: postId,
                content: content,
                csrf_token: csrfToken
            },
            success: function (response) {
                if (response.status == 'success') {
                    showAlert("Comment created.");
                }
                if (response.status == 'error') {
                    showAlert(response.message, "danger");
                }
            },
            error: function (xhr, status, error) {
                // Handle error
                console.error('Error occurred during delete:', error);
            }
        });
    });

    // Post delete
    let postIdToDelete;

    $(".delete-btn").click(function () {
        postIdToDelete = $(this).data('post-id');
        $('#deleteModal').modal('show');
    });

    $("#confirmDelete").click(function () {
        let csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: '/friendflow/post/' + postIdToDelete,
            method: 'DELETE',
            data: {
                id: postIdToDelete,
                _token: csrfToken
            },
            success: function (response) {
                $('#deleteModal').modal('hide');
                if (response.status == "success") {
                    $("#post-" + postIdToDelete).remove();
                }
                if (response.status == "error") {
                    console.log(response.message);
                }
            },
            error: function (xhr, status, error) {
                // Handle error
                console.error('Error occurred during delete:', error);
            }
        });
    });
});

// Update post request
$(document).ready(function () {
    $('.edit-btn').click(function () {
        let postIdToUpdate = $(this).data('post-id');
        let currentText = $('#post-content-' + postIdToUpdate).text().trim();

        $('#post-content-' + postIdToUpdate).replaceWith('<input type="text" class="form-control edit-input" id="edit-input-' + postIdToUpdate + '" value="' + currentText + '">');
        $('#edit-input-' + postIdToUpdate).focus();

        $(document).on('keypress', '.edit-input', function (e) {
            if (e.which == 13) {
                let postIdToUpdate = $(this).attr('id').split('-')[2];
                let newContent = $(this).val().trim();
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                let requestData = {
                    id: postIdToUpdate,
                    newContent: newContent,
                    _token: csrfToken
                };

                $.ajax({
                    url: '/friendflow/post/' + postIdToUpdate,
                    method: 'PUT',
                    data: requestData,
                    success: function (response) {
                        if (response.status == 'success') {
                            $('#edit-input-' + postIdToUpdate).replaceWith('<p id="post-content-' + postIdToUpdate + '" style="font-weight:bold;">' + newContent + '</p>');

                        }
                        if (response.status == 'error') {
                            showAlert(response.message, "danger");
                            $('#edit-input-' + postIdToUpdate).replaceWith('<p id="post-content-' + postIdToUpdate + '" style="font-weight:bold;">' + currentText + '</p>');

                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error occurred during edit:', error);
                    }
                });
            }
        });
    });
});

// Chat
$(document).ready(function () {

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
    let websocket = new WebSocket(`ws://192.168.1.9:8080/chat?token=${encodeURIComponent(token)}&user_id=${userId}`);

    websocket.onopen = function () {
        console.log("WebSocket connection established.");
    };

    websocket.onmessage = function (event) {
        let data = JSON.parse(event.data);
        let message = data.message;
        let senderId = data.senderId;
        let senderName = data.senderName + " " + data.senderSurname;
        let senderImage = "https://via.placeholder.com/40";

        // Find the chat box for the sender
        let chatBox = $(`.chat-box[data-id="${senderId}"]`);

        // Check if the chat box is visible
        if (chatBox.is(':visible')) {
            showMessage(chatBox, message, senderId, senderName, senderImage);
        }
    };

    websocket.onerror = function (event) {
        // Log the entire event object to get detailed information
        console.error("WebSocket error:", event);
        if (event.message) {
            console.error("Error message:", event.message);
        }
    };

    websocket.onclose = function () {
        console.log("WebSocket connection closed.");
    };

    // Function to send message
    function sendMessage(toUserId, messageContent) {
        let message = {
            recipientId: toUserId,
            message: messageContent
        };
        websocket.send(JSON.stringify(message));
    }


    // Passive chat logic
    const maxChats = 5;
    const openChats = new Set();

    function updateChatPositions() {
        $('.chat-box').each(function (index) {
            $(this).css('right', 20 + index * 320 + 'px');
        });
    }

    $('body').on('click', '.friend', function () {
        let friendId = $(this).data('id');
        let friendName = $(this).data('name');
        let friendImage = $(this).find('img').attr('src');

        if (openChats.has(friendId)) {
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
            '<span>' + friendName + '</span>' +
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

        chatBox.on('click', '.close-chat', function () {
            let chatBox = $(this).closest('.chat-box');
            let friendId = chatBox.data('id');
            openChats.delete(friendId);
            chatBox.remove();
            updateChatPositions();
        });

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
                messageInput.val('');
            }
        });
    });
});


$('[data-toggle="collapse"]').on('click', function () {
    let target = $(this).data('target');
    $(target).collapse('toggle');
});

function showMessage(chatBox, messageContent, senderId, senderName, senderImage) {

    let isCurrentUser = senderId === $('#auth-user-id').val();

    let messageHtml = `
    <div class="message">
        <img src="${senderImage}" alt="User Image" class="chat-user-image">
        <span class="message-text">
            <strong>${isCurrentUser ? 'You' : senderName}:</strong> ${messageContent}
        </span>
    </div>
`;

    chatBox.find('.messages').append(messageHtml);
}

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
