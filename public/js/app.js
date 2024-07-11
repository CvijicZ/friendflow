$(document).ready(function () {
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
                postId: postIdToDelete,
                csrf: csrfToken
            },
            success: function (response) {
                $('#deleteModal').modal('hide');
                if(response.status == "success"){
                    $("#post-" + postIdToDelete).remove();
                }
                if(response.status == "error"){
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

$(document).ready(function () {

$('.edit-btn').click(function () {
    var postId = $(this).data('post-id');
    // Perform AJAX call for edit action
    $.ajax({
        url: 'edit.php', // Replace with your edit endpoint
        method: 'POST', // Use POST or GET as needed
        data: {
            postId: postId // Pass postId for edit
        },
        success: function (response) {
            // Handle success response
            console.log('Edit successful:', response);
            // Optionally, update UI or perform additional actions
        },
        error: function (xhr, status, error) {
            // Handle error
            console.error('Error occurred during edit:', error);
        }
    });
});
});



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


