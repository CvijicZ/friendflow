<div class="container-fluid">
    <div class="row">
        <!-- Left Sidebar -->
        <div class="col-md-3">
            <div class="profile-info">
                <h4>Profile Info</h4>
                <p>Name: <?= $data['name'] . " " . $data['surname'] ?></p>
                <p>Email: <?= $data['email'] ?></p>
            </div>
            <div class="suggestions">
                <h4>Suggestions</h4>
                <div class="suggestion">
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/40" alt="Friend">
                        <span>Jane Smith</span>
                    </div>
                    <button class="btn btn-primary btn-sm">Add Friend</button>
                </div>
                <div class="suggestion">
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/40" alt="Friend">
                        <span>Mike Johnson</span>
                    </div>
                    <button class="btn btn-primary btn-sm">Add Friend</button>
                </div>
            </div>
        </div>
        <!-- Main Section -->
        <div class="col-md-6">
            <div class="main-section">
                <h4>Posts</h4>
                <div class="post">
                    <p>Post content goes here...</p>
                </div>
                <div class="post">
                    <p>Post content goes here...</p>
                </div>
            </div>
        </div>
        <!-- Right Sidebar -->
        <div class="col-md-3">
            <div class="weather">
                <h4>Weather</h4>
                <p>Weather info will be here...</p>
            </div>
            <div class="chat">
                <h4>Available Friends</h4>
                <div class="friend" data-id="1" data-name="Anna Williams">
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/40" alt="Friend">
                        <span>Anna Williams</span><span class="status-dot"></span>
                    </div>
                </div>
                <div class="friend" data-id="2" data-name="David Brown">
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/40" alt="Friend">
                        <span>David Brown</span><span class="status-dot"></span>
                    </div>
                </div>
                <div class="friend" data-id="3" data-name="Random User">
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/40" alt="Friend">
                        <span>Random User</span><span class="status-dot"></span>
                    </div>
                </div>
                <div class="friend" data-id="4" data-name="Random User2">
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/40" alt="Friend">
                        <span>Random User2</span><span class="status-dot"></span>
                    </div>
                </div>
                <div class="friend" data-id="5" data-name="Random User3">
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/40" alt="Friend">
                        <span>Random User3</span><span class="status-dot"></span>
                    </div>
                </div>
                <div class="friend" data-id="6" data-name="Random User4">
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/40" alt="Friend">
                        <span>Random User4</span><span class="status-dot"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
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
</script>