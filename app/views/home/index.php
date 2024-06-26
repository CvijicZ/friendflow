<style>
    body {
        overflow: hidden;
    }

    .col-8::-webkit-scrollbar {
        display: none;
    }

    .col-8 {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<div class="container-fluid mt-1 vh-100 ">

    <div class="row">

        <!-- Left Sidebar -->
        <div class="sidebar bg-dark text-light col-2" style="height: 100vh; overflow: hidden;">

            <div class="profile-info mb-3">
                <h4>Profile Info</h4>
                <p><?= htmlspecialchars($data['auth_user']['name']) . " " . htmlspecialchars($data['auth_user']['surname']) ?>
                </p>
                <p><?= htmlspecialchars($data['auth_user']['email']) ?></p>
                <p>Birthday: <?= htmlspecialchars($data['auth_user']['birthday'])?></p>
            </div>

            <div class="suggestions">
                <h4>Suggestions</h4>
                <?php foreach ($data['all_users'] as $user): ?>

                    <div class="suggestion mb-2">
                        <div class="d-flex align-items-center">
                            <img src="https://via.placeholder.com/40" alt="Friend" class="mr-2">
                            <span><?= htmlspecialchars($user['name']) . " " . htmlspecialchars($user['surname']) ?></span>
                        </div>
                        <button class="btn btn-primary btn-sm mt-2">Add Friend</button>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
        <!-- End of left sidebar -->

        <!-- Main Section -->
        <div class="main-content col-8 overflow-auto pb-5" style="height: 100vh;overflow-x: hidden;">

            <!-- New Post Form -->
            <div class="card mb-3 w-auto bg-dark text-light ">
                <div class="card-body">
                    <div class="media">
                        <img src="https://via.placeholder.com/40" class="mr-3 rounded-circle" alt="User Profile"
                            style="width:64px;height:64px;">
                        <div class="media-body">
                            <h5 class="mt-0">
                                <?= htmlspecialchars($data['auth_user']['name']) . " " . htmlspecialchars($data['auth_user']['surname']) ?>
                            </h5>

                            <form action="/friendflow/post" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <textarea class="form-control" name="post_text" rows="3"
                                        placeholder="What's on your mind?"></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="btn btn-secondary">
                                        <i class="fa fa-upload"></i> Upload Image
                                        <input type="file" name="post_image" class="form-control-file d-none">
                                    </label>
                                </div>
                                <input type="hidden" name="csrf_token" value="<?= \App\Middlewares\CSRFMiddleware::getToken() ?>">
                                <button type="submit" class="btn btn-primary">Post</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
            <!-- End of new post form -->

            <!-- List of all posts -->
            <?php
            foreach ($data['posts'] as $post): ?>
                <div class="card mb-3 w-auto bg-dark text-light">

                    <div class="card-body">
                        <div class="media">
                            <!-- If post is created by auth user show options -->
                            <?php if ($post['user_id'] == $_SESSION['user_id']): ?>
                                <div class="dropdown" style="position: absolute; top: 10px; right: 10px;">
                                    <button class="btn btn-link p-0" type="button" id="dropdownMenuButton<?= $post['id'] ?>"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right"
                                        aria-labelledby="dropdownMenuButton<?= $post['id'] ?>">
                                        <a class="dropdown-item" href="#">Edit Post</a>
                                        <a class="dropdown-item" href="#">Delete Post</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <img src="app/storage/images/post_images/<?= htmlspecialchars($post['image_name']) ?>"
                                class="mr-3 rounded-circle" alt="User Profile" style="width:64px;height:64px;">
                            <div class="media-body">
                                <h5 class="mt-0">
                                    <?= htmlspecialchars($post['user']['name']) . " " . htmlspecialchars($post['user']['surname']) ?>
                                </h5>
                                <small style="display:block;margin-top:0;font-size:11px;"><?= $post['created_at'] ?></small>
                                <p style="font-weight:bold;"><?= htmlspecialchars($post['content']) ?></p>

                                <?php if (!empty($post['image_name'])): ?>
                                    <img src="app/storage/images/post_images/<?= htmlspecialchars($post['image_name']) ?>"
                                        class="img-fluid" alt="Post Image">

                                <?php endif; ?>
                            </div>
                        </div>
                        <button class="btn btn-link mt-3" type="button" data-toggle="collapse"
                            data-target="#comments_<?= $post['id'] ?>" aria-expanded="false"
                            aria-controls="commentsSection">
                            Show Comments
                        </button>

                        <div class="collapse" id="comments_<?= $post['id'] ?>">
                            <div class="card card-body mt-3 bg-dark text-light">
                                <div class="media mb-3">
                                    <img src="commenter1.jpg" class="mr-3 rounded-circle" alt="Commenter Profile"
                                        style="width:48px;height:48px;">
                                    <div class="media-body">
                                        <h6 class="mt-0">Commenter Name</h6>
                                        <p>This is a comment content.</p>
                                    </div>
                                </div>
                                <div class="media">
                                    <img src="commenter2.jpg" class="mr-3 rounded-circle" alt="Commenter Profile"
                                        style="width:48px;height:48px;">
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
            <?php endforeach; ?>
        </div>

        <!-- Right Sidebar -->
        <div class="sidebar bg-dark text-light col-2" style="height: 100vh; overflow: hidden;">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>

    $(document).ready(function () {
        $('[data-toggle="collapse"]').on('click', function () {
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