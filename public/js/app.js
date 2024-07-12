

$(document).ready(function () {

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
                if(response.status=='error'){
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
