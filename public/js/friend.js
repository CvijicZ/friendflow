
export function addFriend(receiverId, csrfToken) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: '/friendflow/add-friend',
            method: 'POST',
            data: {
                receiverId: receiverId,
                csrf_token: csrfToken
            },
            success: function (response) {
                if (response.status == "success") {
                    resolve(true);
                }
                if (response.status == "error") {
                    reject(new Error(response.message));
                }
            },
            error: function (xhr, status, error) {
                console.error('Error occurred during delete:', error);
            }
        });

    });

}

export function acceptFriendRequest(friendRequestId, csrfToken) {
    return new Promise((resolve, reject) => {

        $.ajax({
            url: '/friendflow/accept-friend-request',
            method: 'POST',
            data: {
                friendRequestId: friendRequestId,
                csrf_token: csrfToken
            },
            success: function (response) {

                if (response.status == "success") {
                    resolve(true);
                }
                if (response.status == "error") {
                    reject(new Error(response.message));
                }
            },
            error: function (xhr, status, error) {
                console.error('Error occurred during delete:', error);
            }
        });
    });
}