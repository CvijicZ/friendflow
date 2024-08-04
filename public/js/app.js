
import { initChat, getNumberOfUnseenMessages, createFriendsModal, showMessage, updateChatPositions } from '/friendflow/public/js/chat.js';
import { initializeObserver } from '/friendflow/public/js/observer.js';
import { initWebSockets, sendMessage, sendComment } from '/friendflow/public/js/websockets.js';
import { deletePost, updatePost, addComment, generateCommentSection, convertAllPostsDates, loadPosts } from '/friendflow/public/js/post.js';
import { addFriend, acceptFriendRequest } from '/friendflow/public/js/friend.js';


$(document).ready(function () {
    // Initializing required components on page load
    initWebSockets();
    getNumberOfUnseenMessages();
    initializeObserver();
    initChat();

    setInterval(convertAllPostsDates, 60000); // Converts posts created_at to human readable, updates every 1 min

    // Pagination for loading posts, TODO: make this logic modular, so it can be reused for loading comments
    let offset = 0;
    const limit = 10;
    let isLoading = false;

    function handlePostsScroll() {
        const mainContent = $('.main-content');
        const scrollTop = mainContent.scrollTop();
        const scrollHeight = mainContent.prop('scrollHeight');
        const innerHeight = mainContent.innerHeight();

        if (!isLoading && scrollTop + innerHeight >= scrollHeight - 1) {
            isLoading = true;
            offset += limit;
            loadPosts(limit, offset)
                .then(posts => {
                    displayPosts(posts);
                    convertAllPostsDates();
                    isLoading = false;
                })
                .catch(error => {
                    console.log(error.message);
                    isLoading = false;
                });
        }
    }

    function getPosts() {
        loadPosts(limit, offset)
            .then(posts => {
                displayPosts(posts);
                $('#load-posts-message').empty();
                convertAllPostsDates();
            })
            .catch(error => {
                $('#load-posts-message').text("No posts to be shown");
            });
    }

    getPosts(); // Initial load posts

    $('.main-content').on('scroll', handlePostsScroll);
    // End of posts pagination


    $(document).on('click', '.comments-button', function () {
        let postId = $(this).data('target').replace('#comments_', '');
        let numberOfComments = $('#number_of_comments_' + postId).text();
        let commentsDiv = $('#comments_' + postId);

        if (commentsDiv.hasClass('show')) { // If comments element has class show that means that user is closing element (no need to create another request)
            return;
        }

        if (numberOfComments < 1) {
            commentsDiv.html(
                '<p class="text-center">Be first to comment on this post</p>' +
                '<div class="mt-3 comment-form" data-post-id="' + postId + '">' +
                '<textarea class="form-control comment-content" rows="2" placeholder="Add a comment..."></textarea>' +
                '<button class="btn btn-primary mt-2 add-comment">Post Comment</button>' +
                '</div>'
            );
        }
        else {
            // Implement logic to load and display comments :)
            generateCommentSection(postId);
        }
    });

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
                initChat(); // Call initChat to display new friend in chat section
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
    $(document).on('click', '.add-comment', function () {
        let postId = $(this).closest('div').data('post-id');
        let content = $(this).closest('div').find('.comment-content').val();
        let csrfToken = $('meta[name="csrf-token"]').attr('content');

        addComment(postId, content, csrfToken)
            .then(result => {
                generateCommentSection(postId);
                sendComment(postId);
            })
    });

    // Post delete
    $(document).on('click', '.delete-btn', function () {
        let postIdToDelete = $(this).data('post-id');
        $('#deleteModal').modal('show');
        deletePost(postIdToDelete);
    });
    // Update post request
    $(document).on('click', '.edit-btn', function () {
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
        let friendImage = $('#friend-image_'+friendId).attr('src');

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
                            let senderImage = message.sender_id == userId ? "app/storage/images/profile_images/" + $('#auth-user-image-name').val() : friendImage;
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
            '<img src="' + friendImage + '" alt="Friend" id="friend-image_' + friendId +'">' +
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

function displayPosts(posts) {
    const $container = $('#posts-container');
    const userId = $('#auth-user-id').val();

    posts.forEach(post => {
        const postHtml = `
            <div class="card mb-3 w-auto bg-secondary text-light single-post" id="post-${post.id}">
                <div class="card-body">
                    <div class="media">
                        <!-- If post is created by auth user show options -->
                        ${post.user_id == userId ? `
                            <div class="dropdown" style="position: absolute; top: 10px; right: 10px;">
                                <button class="btn btn-dark p-1" type="button" id="dropdownMenuButton${post.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right text-center mx-auto bg-dark" aria-labelledby="dropdownMenuButton${post.id}">
                                    <button type="button" class="btn btn-outline-primary edit-btn" data-post-id="${post.id}">Edit</button>
                                    <button type="button" class="btn btn-outline-danger delete-btn" data-post-id="${post.id}">Delete</button>
                                </div>
                            </div>
                        ` : ''}
                        <img src="app/storage/images/profile_images/${post.user.profile_image_name}" class="mr-3 rounded-circle" alt="User Profile" style="width:64px;height:64px;">
                        <div class="media-body">
                            <h5 class="mt-0 text-dark">
                                ${post.user.name} ${post.user.surname}
                            </h5>
                            <small class="post_date text-dark" data-datetime="${post.created_at}" style="display:block;margin-top:0;font-size:11px;"></small>
                            <p style="font-weight:bold;" class="border-bottom" id="post-content-${post.id}">
                                ${post.content}
                            </p>
                            ${post.image_name ? `
                                <div class="post-image-container">
                                    <img src="app/storage/images/post_images/${post.image_name}" class="post-image" alt="Post Image">
                                </div>
                            ` : ''}
                        </div>
                    </div>
                    <button class="btn btn-primary mt-3 comments-button" type="button" data-toggle="collapse" data-target="#comments_${post.id}" aria-expanded="false" aria-controls="commentsSection">
                        Comments <i class="fa-regular fa-comment"></i> <span id="number_of_comments_${post.id}">${post.numberofComments}</span>
                    </button>
                    <div class="collapse" id="comments_${post.id}">
                        <!-- Comments loaded from AJAX will be here -->
                        <p class="text-center" id="loading-message_${post.id}">Loading comments...</p>
                    </div>
                </div>
            </div>
        `;

        // Append the generated HTML to the container
        $container.append(postHtml);
    });
}