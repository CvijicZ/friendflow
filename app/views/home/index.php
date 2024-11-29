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
        <div class="sidebar bg-body custom-text-color col-2" style="height: 100vh; overflow: hidden;">

            <div class="profile-info mb-3 bg-dark-blue-gray">
                <h4>Profile Info</h4>
                <p id="auth-user-name"><?= htmlspecialchars($data['auth_user']['name']) . " " . htmlspecialchars($data['auth_user']['surname']) ?>
                </p>
                <p><?= htmlspecialchars($data['auth_user']['email']) ?></p>
                <p>Birthday: <?= htmlspecialchars($data['auth_user']['birthday']) ?></p>
                <input type="hidden" id="auth-user-id" value="<?= $data['auth_user']['id'] ?>">
                <input type="hidden" id="auth-user-image-name" value="<?= $data['auth_user']['profile_image_name'] ?>">

            </div>

            <div class="suggestions bg-dark-blue-gray custom-border">
                <h4>Suggestions</h4>
                <?php foreach ($data['suggested_friends'] as $user) : ?>

                    <div class="suggestion mb-2" data-user-id="<?= $user['id'] ?>">
                        <div class="d-flex align-items-center">
                            <img src="app/storage/images/profile_images/<?= $user['profile_image_name'] ?>" alt="Friend" class="mr-2">
                            <span><?= htmlspecialchars($user['name']) . " " . htmlspecialchars($user['surname']) ?></span>
                        </div>
                        <a class="add-friend"><i class="fa-solid fa-user-plus"></i></a>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
        <!-- End of left sidebar -->

        <!-- Main Section -->
        <div class="main-content col-8 overflow-auto pb-5 bg-body custom-text-color" style="height: 100vh;overflow-x: hidden;">

            <!-- New Post Form -->
            <div class="card mb-3 w-auto bg-dark-blue-gray custom-text-color">
                <div class="card-body">
                    <div class="media">
                        <img src="app/storage/images/profile_images/<?= $data['auth_user']['profile_image_name'] ?>" class="mr-3 rounded-circle" alt="User Profile" style="width:64px;height:64px;">
                        <div class="media-body">
                            <h5 class="mt-0">
                                <?= htmlspecialchars($data['auth_user']['name']) . " " . htmlspecialchars($data['auth_user']['surname']) ?>
                            </h5>

                            <form action="/friendflow/post" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <textarea class="form-control" id="postText" name="post_text" rows="3" placeholder="What's on your mind?"></textarea>
                                </div>
                                <div class="form-group d-flex align-items-center">
                                    <label class="btn btn-info m-0">
                                        <i class="fa fa-upload"></i> Image
                                        <input type="file" name="post_image" class="form-control-file d-none">
                                    </label>
                                    <button type="submit" class="btn btn-primary ml-auto">Post</button>
                                </div>
                                <input type="hidden" name="csrf_token" value="<?= \App\Middlewares\CSRFMiddleware::getToken() ?>">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of new post form -->

            <!-- List of all posts -->
            <p class="text-center" id="load-posts-message">Loading posts...</p>

            <!-- In #posts-container section posts will be dinamically added from JS -->
            <section id="posts-container"></section>

        </div>

        <!-- Right Sidebar -->
        <div class="sidebar bg-body custom-text-color col-2" style="height: 100vh; overflow: hidden;">
            <div class="weather mb-3 bg-dark-blue-gray">
                <h4>Weather</h4>
                <p>Weather info will be here...</p>
            </div>

            <div class="chat bg-dark-blue-gray custom-border">
                <h4>
                    Chat <span class="badge badge-danger badge-pill unseen-messages-number">0</span>
                </h4>
            </div>
        </div>
        <!-- End of right sidebar -->
    </div>
</div>

<!-- Chat Modal -->
<div class="modal fade" id="chatModal" tabindex="-1" role="dialog" aria-labelledby="chatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header">
                <h5 class="modal-title" id="chatModalLabel">Chat</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="chatModalBody">
            </div>
        </div>
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
        <div class="modal-content bg-dark-blue-gray custom-text-color custom-border">
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
    <script type="module" src="/friendflow/public/js/app.js"></script>