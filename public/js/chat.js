
export function initChat() {

    fetchFriends().then(function (friends) {

        friends.forEach(friend => {
            const friendDiv = `
                <div class="friend mb-2" data-id="${friend.id}" data-name="${friend.name} ${friend.surname}">
                    <div class="d-flex align-items-center">
                        <img src="app/storage/images/profile_images/${friend.profile_image_name}" alt="Friend" class="mr-2">
                        <span>${friend.name} ${friend.surname}</span>
                        <span id="status-dot-${friend.id}" class="status-dot-offline"></span>
                    </div>
                </div>
            `;
            $('.chat').append(friendDiv);
        });
    });
}

export function updateChatPositions() {
    $('.chat-box').each(function (index) {
        $(this).css('right', 20 + index * 320 + 'px');
    });
}

// For mobile users, on click on chat icon it generates and show modal with friend list
export function createFriendsModal() {
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
                $('.friend-requests-container').html("<p class='text-center'>No friend requests");
                $('#friendRequestModal').modal('show');
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX error: ' + status + ' ' + error);
        }
    });
}
// Updates number of unseen messages
export function getNumberOfUnseenMessages() {
    $.ajax({
        url: '/friendflow/count-unseen-messages',
        method: 'POST',
        data: {
            csrf_token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.status == 'success') {
                $('.unseen-messages-number').text(response.number_of_messages);
                console.log("Unseen number: " + response.number_of_messages);
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', error);
        }
    });
}

function fetchFriends() {
    let csrfToken = $('meta[name="csrf-token"]').attr('content');

    return $.ajax({
        url: '/friendflow/get-all-friends',
        type: 'POST',
        data: {
            csrf_token: csrfToken
        },
        dataType: 'json',
    }).then(function (response) {
        if (response.status === "success") {
            return response.data;
        } else if (response.status === 'error') {
            console.error('Error fetching friends:', response.message);
            return [];
        }
    }).fail(function (xhr, status, error) {
        console.error('AJAX error:', status, error);
        return [];
    });
}

// Function to append friend requests to the container
function appendFriendRequests(data) {
    let container = document.querySelector('.friend-requests-container');

    data.forEach(function (request) {
        let friendRequestElement = createFriendRequestElement(request);
        container.appendChild(friendRequestElement);
    });
}

// Function to create friend request HTML
function createFriendRequestElement(request) {
    let html = `
<div class="friend-request" data-friend-request-id="${request.id}">
    <div class="d-flex align-items-center justify-content-between w-100">
        <div class="d-flex align-items-center">
            <img src="app/storage/images/profile_images/${request.profile_image_name}" alt="Friend" class="mr-2">
            <span>${request.name} ${request.surname}</span>
        </div>
        <div class="ml-auto">
            <div class="btn-group">
                <button class="btn btn-success btn-sm accept-friend-request">Accept <i class="fa-solid fa-check"></i></button>
                <button class="btn btn-danger btn-sm ml-2 deny-friend-request">Remove <i class="fa-solid fa-ban"></i></button>
            </div>
        </div>
    </div>
</div>
`;
    let container = document.createElement('div');
    container.innerHTML = html.trim();
    return container.firstChild;
}

export function showMessage(chatBox, messageContent, senderId, senderName, senderImage) {

    let isCurrentUser = senderId == $('#auth-user-id').val();
    let messageHtml = `
                       <div class="message">
                         <img src="${senderImage}" alt="User Image" class="chat-user-image">
                         <span class="message-text">
                           <strong>${isCurrentUser ? 'You' : senderName}:</strong> ${messageContent}
                         </span>
                       </div>`;

    chatBox.find('.messages').append(messageHtml);
    return chatBox.find('.message').last();
}