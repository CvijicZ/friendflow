
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
                console.error('Error occurred during:', error);
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

function getComments(postId) {
    return new Promise((resolve, reject) => {

        let csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: '/friendflow/get-comments',
            method: 'POST',
            data: {
                postId: postId,
                csrf_token: csrfToken
            },
            success: function (response) {
                $('#deleteModal').modal('hide');
                if (response.status == "success") {
                    resolve(response.comments);
                }
                if (response.status == "error") {
                    reject(response.message);
                }
            },
            error: function (xhr, status, error) {
                reject(error);
            }
        });

    });
}

// countComments tells the function if it needs to update number of comments displayed on the homepage
export function generateCommentSection(postId, countComments = false) {
    getComments(postId)
        .then(comments => {
            clearCommentContent(postId);
            createCommentElements(comments, postId, countComments);
        })
        .catch(error => {
            console.log(error);
        });
}

export function convertAllPostsDates() {
    $('.post_date').each(function () {
        const datetime = $(this).data('datetime');
        const humanReadable = moment(datetime).fromNow();
        $(this).text(humanReadable);
    });
}

function clearCommentContent(comment_id) {
    $('#comments_' + comment_id).empty();
}

function createCommentElements(comments, postId, countComments) {
    let allComments = comments.comments;

    allComments.sort(function (b, a) {
        return new Date(a.created_at) - new Date(b.created_at);
    });

    let commentContainerHtml = `
            <div class="card card-body mt-3 bg-secondary text-light">
    `;

    allComments.forEach(function (comment) {
        let commentHTML = `
            <div class="media mb-3">
                <img src="app/storage/images/profile_images/${comment.user.profile_image_name}" class="mr-3 rounded-circle" alt="Commenter Profile" style="width:48px;height:48px;">
                <div class="media-body">
                    <h6 class="mt-0">
                        ${comment.user.name} ${comment.user.surname}
                    </h6>
                    <p>${comment.content}</p>
                </div>
            </div>
            <hr class="bg-light">
        `;

        commentContainerHtml += commentHTML;
    });

    commentContainerHtml += `<div class="mt-3 comment-form" data-post-id="${postId}">
                                    <textarea class="form-control comment-content" rows="2" placeholder="Add a comment..."></textarea>
                                    <button class="btn btn-primary mt-2 add-comment">Post Comment</button>
                                </div></div> `;

    $('#comments_' + postId).append(commentContainerHtml);

    if (countComments) {
        updateNumberOfComments(postId, allComments.length);
    }
}

function updateNumberOfComments(postId, newNumber) {
    let $numberElement = $('#number_of_comments_' + postId);
    $numberElement.css('color', 'red');

    $numberElement
        .fadeOut(1000, function () {
            $(this).text(newNumber)
                .css('color', 'red')
                .fadeIn(1000, function () {
                    $(this).css('color', 'white');
                });
        });
}
