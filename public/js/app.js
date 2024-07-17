

$(document).ready(function () {
    // AJAX function to fetch friend requests
    $('#friendRequestsBtn').on('click', function () {
        $('.friend-requests-container').empty();

        var csrfToken = $('meta[name="csrf-token"]').attr('content');

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
                window.location.href = '/friendflow/error';
            }
        });
    });

    // Function to create friend request HTML
    function createFriendRequestElement(request) {
        var html = `
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
        var container = document.createElement('div');
        container.innerHTML = html.trim();

        // Set data attributes or IDs dynamically if needed
        // Example: container.dataset.requestId = request.id;

        return container.firstChild;
    }

    // Function to append friend requests to the container
    function appendFriendRequests(data) {
        var container = document.querySelector('.friend-requests-container');

        data.forEach(function (request) {
            var friendRequestElement = createFriendRequestElement(request);
            container.appendChild(friendRequestElement);
        });
    }

    $('.friend-requests-container').on('click', '.deny-friend-request', function(){
        console.log("aaa");
    });
    // Accept friend request
    $('.friend-requests-container').on('click', '.accept-friend-request', function () {

        let csrfToken = $('meta[name="csrf-token"]').attr('content');
        let friendRequestId = $(this).closest('.friend-request').data('friend-request-id');

        if(csrfToken === undefined || friendRequestId===undefined){
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
        var parentDiv = $(this).closest('.suggestion');
        var receiverId = parentDiv.data('user-id');
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

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
        var postId = $(this).closest('div').data('post-id');
        var content = $(this).closest('div').find('.comment-content').val();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

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
    var postIdToDelete;

    $(".delete-btn").click(function () {
        postIdToDelete = $(this).data('post-id');
        $('#deleteModal').modal('show');
    });

    $("#confirmDelete").click(function () {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

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
        var postIdToUpdate = $(this).data('post-id');
        var currentText = $('#post-content-' + postIdToUpdate).text().trim();

        $('#post-content-' + postIdToUpdate).replaceWith('<input type="text" class="form-control edit-input" id="edit-input-' + postIdToUpdate + '" value="' + currentText + '">');
        $('#edit-input-' + postIdToUpdate).focus();

        $(document).on('keypress', '.edit-input', function (e) {
            if (e.which == 13) {
                var postIdToUpdate = $(this).attr('id').split('-')[2];
                var newContent = $(this).val().trim();
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                var requestData = {
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

// Other app logic
$(document).ready(function () {
    const maxChats = 5;
    const openChats = new Set();

    function updateChatPositions() {
        $('.chat-box').each(function (index) {
            $(this).css('right', 20 + index * 320 + 'px');
        });
    }

    $('.friend').on('click', function () {
        var friendId = $(this).data('id');
        var friendName = $(this).data('name');
        var friendImage = $(this).find('img').attr('src');

        if (openChats.has(friendId)) {
            return;
        }

        if (openChats.size >= maxChats) {
            alert('You can only open up to ' + maxChats + ' chat boxes.');
            return;
        }

        openChats.add(friendId);

        var chatBox = $(
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

        chatBox.find('.close-chat').on('click', function () {
            var chatBox = $(this).closest('.chat-box');
            var friendId = chatBox.data('id');
            openChats.delete(friendId);
            chatBox.remove();
            updateChatPositions();
        });

        chatBox.find('.send-message').on('click', function () {
            var messageInput = chatBox.find('input');
            var message = messageInput.val();
            if (message) {
                chatBox.find('.messages').append('<div class="message">' + message + '</div>');
                messageInput.val('');
            }
        });
    });
});

$('[data-toggle="collapse"]').on('click', function () {
    var target = $(this).data('target');
    $(target).collapse('toggle');
});

function showAlert(message, type = 'success') {
    var $alert = $('#alertTemplate').clone();
    $alert.find('#alertMessage').text(message);

    $alert.addClass('alert alert-dismissible fade show alert-' + type)
        .removeClass('d-none');

    $('#alerts-container').empty().append($alert);

    setTimeout(function () {
        $alert.alert('close');
    }, 5000);
}
