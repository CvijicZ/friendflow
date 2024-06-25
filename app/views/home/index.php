<style>
        /* body {
            background-color: #121212;
            color: #e0e0e0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .content {
            display: flex;
            flex: 1;
        }
        .sidebar {
            flex: 0 0 300px;
            padding: 15px;
        }
        .main-content {
            flex: 1;
            padding: 15px;
        } */
        /* .card {
            background-color: #1e1e1e;
            color: #e0e0e0;
        }
        .btn-link {
            color: #bb86fc;
        }
        .btn-primary {
            background-color: #bb86fc;
            border: none;
        }
        .form-control {
            background-color: #2e2e2e;
            color: #e0e0e0;
            border: 1px solid #4a4a4a;
        } */
    </style>

<div class="container-fluid mt-1">
<div class="row">
        <!-- Left Sidebar -->
        <div class="sidebar bg-dark text-light position-sticky top-0 start-0 col-3">
            <div class="profile-info mb-3">
                <h4>Profile Info</h4>
                <p>Name: John Doe</p>
                <p>Email: john.doe@example.com</p>
            </div>
            <div class="suggestions">
                <h4>Suggestions</h4>
                <div class="suggestion mb-2">
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/40" alt="Friend" class="mr-2">
                        <span>Jane Smith</span>
                    </div>
                    <button class="btn btn-primary btn-sm mt-2">Add Friend</button>
                </div>
                <div class="suggestion">
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/40" alt="Friend" class="mr-2">
                        <span>Mike Johnson</span>
                    </div>
                    <button class="btn btn-primary btn-sm mt-2">Add Friend</button>
                </div>
            </div>
        </div>
        <!-- Main Section -->
        <div class="main-content overflow-auto col-6">

            <div class="card mb-3 w-auto bg-dark text-light">
                <div class="card-body">
                    <div class="media">
                        <img src="https://via.placeholder.com/40" class="mr-3 rounded-circle" alt="User Profile" style="width:64px;height:64px;">
                        <div class="media-body">
                            <h5 class="mt-0">User Name</h5>
                            <p>This is a post content. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                            <img src="https://via.placeholder.com/40" class="img-fluid" alt="Post Image">
                        </div>
                    </div>
                    <button class="btn btn-link mt-3" type="button" data-toggle="collapse" data-target="#commentsSection" aria-expanded="false" aria-controls="commentsSection">
                        Show Comments
                    </button>
                    <div class="collapse" id="commentsSection">
                        <div class="card card-body mt-3 bg-dark text-light">
                            <div class="media mb-3">
                                <img src="commenter1.jpg" class="mr-3 rounded-circle" alt="Commenter Profile" style="width:48px;height:48px;">
                                <div class="media-body">
                                    <h6 class="mt-0">Commenter Name</h6>
                                    <p>This is a comment content.</p>
                                </div>
                            </div>
                            <div class="media">
                                <img src="commenter2.jpg" class="mr-3 rounded-circle" alt="Commenter Profile" style="width:48px;height:48px;">
                                <div class="media-body">
                                    <h6 class="mt-0">Commenter Name</h6>
                                    <p>This is another comment content.</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <textarea class="form-control" rows="2" placeholder="Add a comment..."></textarea>
                                <button class="btn btn-primary mt-2">Post Comment</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3 w-auto bg-dark text-light">
                <div class="card-body">
                    <div class="media">
                        <img src="https://via.placeholder.com/40" class="mr-3 rounded-circle" alt="User Profile" style="width:64px;height:64px;">
                        <div class="media-body">
                            <h5 class="mt-0">User Name</h5>
                            <p>This is a post content. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                            <img src="https://via.placeholder.com/40" class="img-fluid" alt="Post Image">
                        </div>
                    </div>
                    <button class="btn btn-link mt-3" type="button" data-toggle="collapse" data-target="#commentsSection" aria-expanded="false" aria-controls="commentsSection">
                        Show Comments
                    </button>
                    <div class="collapse" id="commentsSection">
                        <div class="card card-body mt-3 bg-dark text-light">
                            <div class="media mb-3">
                                <img src="commenter1.jpg" class="mr-3 rounded-circle" alt="Commenter Profile" style="width:48px;height:48px;">
                                <div class="media-body">
                                    <h6 class="mt-0">Commenter Name</h6>
                                    <p>This is a comment content.</p>
                                </div>
                            </div>
                            <div class="media">
                                <img src="commenter2.jpg" class="mr-3 rounded-circle" alt="Commenter Profile" style="width:48px;height:48px;">
                                <div class="media-body">
                                    <h6 class="mt-0">Commenter Name</h6>
                                    <p>This is another comment content.</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <textarea class="form-control" rows="2" placeholder="Add a comment..."></textarea>
                                <button class="btn btn-primary mt-2">Post Comment</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3 w-auto text-light bg-dark">
                <div class="card-body">
                    <div class="media">
                        <img src="https://via.placeholder.com/40" class="mr-3 rounded-circle" alt="User Profile" style="width:64px;height:64px;">
                        <div class="media-body">
                            <h5 class="mt-0">User Name</h5>
                            <p>This is a post content. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                            <img src="https://via.placeholder.com/40" class="img-fluid" alt="Post Image">
                        </div>
                    </div>
                    <button class="btn btn-link mt-3" type="button" data-toggle="collapse" data-target="#commentsSection" aria-expanded="false" aria-controls="commentsSection">
                        Show Comments
                    </button>
                    <div class="collapse" id="commentsSection">
                        <div class="card card-body mt-3 bg-dark text-light">
                            <div class="media mb-3">
                                <img src="commenter1.jpg" class="mr-3 rounded-circle" alt="Commenter Profile" style="width:48px;height:48px;">
                                <div class="media-body">
                                    <h6 class="mt-0">Commenter Name</h6>
                                    <p>This is a comment content.</p>
                                </div>
                            </div>
                            <div class="media">
                                <img src="commenter2.jpg" class="mr-3 rounded-circle" alt="Commenter Profile" style="width:48px;height:48px;">
                                <div class="media-body">
                                    <h6 class="mt-0">Commenter Name</h6>
                                    <p>This is another comment content.</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <textarea class="form-control" rows="2" placeholder="Add a comment..."></textarea>
                                <button class="btn btn-primary mt-2">Post Comment</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3 w-auto text-light bg-dark">
                <div class="card-body">
                    <div class="media">
                        <img src="https://via.placeholder.com/40" class="mr-3 rounded-circle" alt="User Profile" style="width:64px;height:64px;">
                        <div class="media-body">
                            <h5 class="mt-0">User Name</h5>
                            <p>This is a post content. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                            <img src="https://via.placeholder.com/40" class="img-fluid" alt="Post Image">
                        </div>
                    </div>
                    <button class="btn btn-link mt-3" type="button" data-toggle="collapse" data-target="#commentsSection" aria-expanded="false" aria-controls="commentsSection">
                        Show Comments
                    </button>
                    <div class="collapse" id="commentsSection">
                        <div class="card card-body mt-3 bg-dark text-light">
                            <div class="media mb-3">
                                <img src="commenter1.jpg" class="mr-3 rounded-circle" alt="Commenter Profile" style="width:48px;height:48px;">
                                <div class="media-body">
                                    <h6 class="mt-0">Commenter Name</h6>
                                    <p>This is a comment content.</p>
                                </div>
                            </div>
                            <div class="media">
                                <img src="commenter2.jpg" class="mr-3 rounded-circle" alt="Commenter Profile" style="width:48px;height:48px;">
                                <div class="media-body">
                                    <h6 class="mt-0">Commenter Name</h6>
                                    <p>This is another comment content.</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <textarea class="form-control" rows="2" placeholder="Add a comment..."></textarea>
                                <button class="btn btn-primary mt-2">Post Comment</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3 w-auto text-light bg-dark">
                <div class="card-body">
                    <div class="media">
                        <img src="https://via.placeholder.com/40" class="mr-3 rounded-circle" alt="User Profile" style="width:64px;height:64px;">
                        <div class="media-body">
                            <h5 class="mt-0">User Name</h5>
                            <p>This is a post content. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                            <img src="https://via.placeholder.com/40" class="img-fluid" alt="Post Image">
                        </div>
                    </div>
                    <button class="btn btn-link mt-3" type="button" data-toggle="collapse" data-target="#commentsSection" aria-expanded="false" aria-controls="commentsSection">
                        Show Comments
                    </button>
                    <div class="collapse" id="commentsSection">
                        <div class="card card-body mt-3 bg-dark text-light">
                            <div class="media mb-3">
                                <img src="commenter1.jpg" class="mr-3 rounded-circle" alt="Commenter Profile" style="width:48px;height:48px;">
                                <div class="media-body">
                                    <h6 class="mt-0">Commenter Name</h6>
                                    <p>This is a comment content.</p>
                                </div>
                            </div>
                            <div class="media">
                                <img src="commenter2.jpg" class="mr-3 rounded-circle" alt="Commenter Profile" style="width:48px;height:48px;">
                                <div class="media-body">
                                    <h6 class="mt-0">Commenter Name</h6>
                                    <p>This is another comment content.</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <textarea class="form-control" rows="2" placeholder="Add a comment..."></textarea>
                                <button class="btn btn-primary mt-2">Post Comment</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            


        </div>



        <!-- Right Sidebar -->
        <div class="sidebar bg-dark text-light col-3">
            <div class="weather mb-3">
                <h4>Weather</h4>
                <p>Weather info will be here...</p>
            </div>
            <div class="chat">
                <h4>Available Friends</h4>
                <div class="friend mb-2" data-id="1" data-name="Anna Williams">
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/40" alt="Friend" class="mr-2">
                        <span>Anna Williams</span><span class="status-dot"></span>
                    </div>
                </div>
                <div class="friend mb-2" data-id="2" data-name="David Brown">
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/40" alt="Friend" class="mr-2">
                        <span>David Brown</span><span class="status-dot"></span>
                    </div>
                </div>
                <div class="friend mb-2" data-id="3" data-name="Random User">
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/40" alt="Friend" class="mr-2">
                        <span>Random User</span><span class="status-dot"></span>
                    </div>
                </div>
                <div class="friend mb-2" data-id="4" data-name="Random User2">
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/40" alt="Friend" class="mr-2">
                        <span>Random User2</span><span class="status-dot"></span>
                    </div>
                </div>
                <div class="friend mb-2" data-id="5" data-name="Random User3">
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/40" alt="Friend" class="mr-2">
                        <span>Random User3</span><span class="status-dot"></span>
                    </div>
                </div>
                <div class="friend" data-id="6" data-name="Random User4">
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/40" alt="Friend" class="mr-2">
                        <span>Random User4</span><span class="status-dot"></span>
                    </div>
                </div>
            </div>
        </div>

        </div>
    </div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>

        $(document).ready(function(){
        $('[data-toggle="collapse"]').on('click', function() {
            var target = $(this).data('target');
            $(target).collapse('toggle');
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
</script>