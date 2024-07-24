
export function addComment(postId, content, csrfToken) {
    return new Promise((resolve, reject) => {
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
                    resolve(true);
                }
                if (response.status == 'error') {
                    showAlert(response.message, "danger");
                    reject(new Error(response.message));
                }
            },
            error: function (xhr, status, error) {
                console.error('Error occurred during delete:', error);
            }
        });
    });
}
export function updatePost(postIdToUpdate, currentText) {
    // Create input field and move cursor to the end of the text
    $('#post-content-' + postIdToUpdate).replaceWith('<input type="text" class="form-control edit-input" id="edit-input-' + postIdToUpdate + '" value="' + currentText + '">');
    const input = $('#edit-input-' + postIdToUpdate).focus();
    const inputElement = input[0];
    const valueLength = inputElement.value.length;
    inputElement.setSelectionRange(valueLength, valueLength);

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
}

export function deletePost(postIdToDelete) {

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
                console.error('Error occurred during delete:', error);
            }
        });
    });

} 