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
<div id="alerts-container"></div>

<meta name="csrf-token" content="<?= App\Middlewares\CSRFMiddleware::getToken(); ?>">

<div class="container-fluid mt-1 vh-100 ">

    <div class="row">

        <!-- Left Sidebar -->
        <div class="sidebar bg-dark text-light col-2" style="height: 100vh; overflow: hidden;">

            <div class="profile-info mb-3">
                <h4>Profile Info</h4>
                <p id="auth-user-name"><?= htmlspecialchars($data['auth_user']['name']) . " " . htmlspecialchars($data['auth_user']['surname']) ?>
                </p>
                <p><?= htmlspecialchars($data['auth_user']['email']) ?></p>
                <p>Birthday: <?= htmlspecialchars($data['auth_user']['birthday']) ?></p>
                <input type="hidden" id="auth-user-id" value="<?= $data['auth_user']['id'] ?>">
            </div>

            <div class="suggestions">
                <h4>Suggestions</h4>
                <?php foreach ($data['all_users'] as $user) : if($user['id'] == $_SESSION['user_id']){continue;} ?>

                    <div class="suggestion mb-2" data-user-id="<?= $user['id'] ?>">
                        <div class="d-flex align-items-center">
                            <img src="https://via.placeholder.com/40" alt="Friend" class="mr-2">
                            <span><?= htmlspecialchars($user['name']) . " " . htmlspecialchars($user['surname']) ?></span>
                        </div>
                        <button class="btn btn-primary btn-sm mt-2 add-friend">Add Friend</button>
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
                        <img src="https://via.placeholder.com/40" class="mr-3 rounded-circle" alt="User Profile" style="width:64px;height:64px;">
                        <div class="media-body">
                            <h5 class="mt-0">
                                <?= htmlspecialchars($data['auth_user']['name']) . " " . htmlspecialchars($data['auth_user']['surname']) ?>
                            </h5>

                            <form action="/friendflow/post" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <textarea class="form-control" name="post_text" rows="3" placeholder="What's on your mind?"></textarea>
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
            foreach ($data['posts'] as $post) : ?>
                <div class="card mb-3 w-auto bg-dark text-light" id="post-<?= $post['id'] ?>">

                    <div class="card-body">
                        <div class="media">
                            <!-- If post is created by auth user show options -->
                            <?php if ($post['user_id'] == $_SESSION['user_id']) : ?>
                                <div class="dropdown" style="position: absolute; top: 10px; right: 10px;">
                                    <button class="btn btn-link p-0" type="button" id="dropdownMenuButton<?= $post['id'] ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right text-center mx-auto bg-dark" aria-labelledby="dropdownMenuButton<?= $post['id'] ?>">
                                        <button type="button" class="btn btn-outline-primary edit-btn" data-post-id="<?= $post['id'] ?>">Edit</button>
                                        <button type="button" class="btn btn-outline-danger delete-btn" data-post-id="<?= $post['id'] ?>">Delete</button>
                                    </div>

                                </div>
                            <?php endif; ?>
                            <img src="app/storage/images/post_images/<?= htmlspecialchars($post['image_name']) ?>" class="mr-3 rounded-circle" alt="User Profile" style="width:64px;height:64px;">
                            <div class="media-body">
                                <h5 class="mt-0">
                                    <?= htmlspecialchars($post['user']['name']) . " " . htmlspecialchars($post['user']['surname']) ?>
                                </h5>
                                <small style="display:block;margin-top:0;font-size:11px;"><?= $post['created_at'] ?></small>
                                <p style="font-weight:bold;" id="post-content-<?= $post['id'] ?>">
                                    <?= htmlspecialchars($post['content']) ?>
                                </p>

                                <?php if (!empty($post['image_name'])) : ?>
                                    <img src="app/storage/images/post_images/<?= htmlspecialchars($post['image_name']) ?>" class="img-fluid" alt="Post Image">

                                <?php endif; ?>
                            </div>
                        </div>
                        <button class="btn btn-link mt-3" type="button" data-toggle="collapse" data-target="#comments_<?= $post['id'] ?>" aria-expanded="false" aria-controls="commentsSection">
                            Show Comments
                        </button>

                        <div class="collapse" id="comments_<?= $post['id'] ?>">
                            <div class="card card-body mt-3 bg-dark text-light">

                                <?php foreach ($post['comments'] as $comment) : ?>

                                    <div class="media mb-3">
                                        <img src="commenter1.jpg" class="mr-3 rounded-circle" alt="Commenter Profile" style="width:48px;height:48px;">
                                        <div class="media-body">
                                            <h6 class="mt-0">
                                                <?= htmlspecialchars($comment['user']['name']) . " " . htmlspecialchars($comment['user']['surname']) ?>
                                            </h6>
                                            <p><?= htmlspecialchars($comment['content']) ?></p>
                                        </div>
                                    </div>

                                <?php endforeach; ?>

                                <div class="mt-3" data-post-id="<?= $post['id'] ?>">
                                    <textarea class="form-control comment-content" rows="2" placeholder="Add a comment..."></textarea>
                                    <button class="btn btn-primary mt-2 add-comment">Post Comment</button>
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
            </div>
        </div>
        <!-- End of right sidebar -->
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this post?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Friend requests modal -->
<div class="modal fade" id="friendRequestModal" tabindex="-1" role="dialog" aria-labelledby="friendRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title" id="friendRequestModalLabel">Friend Request</h5>
            </div>
            <div class="modal-body">
                <div class="friend-requests-container">
                    <div class="friend-request">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="alertTemplate" class="alert alert-dismissible fade show d-none" role="alert">
        <!-- Alert content -->
        <strong>Message:</strong> <span id="alertMessage"></span>
        <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="/friendflow/public/js/app.js"></script>