<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Social Network</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/friendflow/public/css/style.css">
</head>

<body>
    <!-- Mobile Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top navbar-dark bg-gradient-dark mobile-navbar d-none">
        <div class="container-fluid">
            <!-- Home Button -->
            <a class="navbar-brand" href="/friendflow/">
                <i class="fas fa-home"></i>
            </a>

            <!-- Friend Requests and Chat Buttons -->
            <?php if (App\Middlewares\AuthMiddleware::isLoggedIn()) : ?>

                <div class="d-flex mx-auto">
                    <button type="button" class="btn btn-outline-primary position-relative mx-2" id="friendRequestsBtn">
                        <i class="fas fa-user-friends"></i>
                        <span class="badge badge-danger badge-pill friendRequestsNumber" style="position: absolute; top: -5px; right: -5px;">0</span>
                    </button>
                    <button type="button" id="chatBtn" data-bs-target="#chatModal" data-bs-toggle="modal" class="btn btn-outline-primary position-relative mx-2">
                        <i class="fa-solid fa-comment-dots"></i>
                        <span class="badge badge-danger badge-pill unseen-messages-number" style="position: absolute; top: -5px; right: -5px;">0</span>
                    </button>
                </div>
            <?php endif; ?>
            <!-- Toggler Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Collapsible Navbar Content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ml-auto d-flex">
                    <?php if (\App\Middlewares\AuthMiddleware::isLoggedIn()) : ?>
                        <!-- Authenticated User Links -->
                        <a href="/friendflow/" class="btn btn-outline-primary mr-2">
                            <i class="fas fa-home"></i>
                        </a>
                        <a href="/friendflow/profile" class="btn btn-outline-primary mr-2">
                            <i class="fas fa-user"></i>
                        </a>
                        <a href="/friendflow/logout" class="btn btn-outline-primary">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    <?php else : ?>
                        <!-- Login Form -->
                        <form class="d-flex" action="/friendflow/login" method="POST">
                            <input class="form-control mr-2" type="email" name="email" placeholder="E-mail" aria-label="Email">
                            <input class="form-control mr-2" type="password" name="password" placeholder="Password" aria-label="Password">
                            <input type="hidden" name="csrf_token" value="<?= \App\Middlewares\CSRFMiddleware::getToken() ?>">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="fas fa-sign-in-alt"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <!-- End of navbar -->
    <!-- Desktop Navbar -->
    <nav class="navbar sticky-top navbar-dark bg-gradient-dark desktop-navbar d-none">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="navbar-brand" href="/friendflow/">FriendFlow</a>

            <?php if (\App\Middlewares\AuthMiddleware::isLoggedIn()) : ?>
                <div class="d-flex flex-grow-1 justify-content-between align-items-center">
                    <!-- Centered Buttons -->
                    <div class="d-flex mx-auto">
                        <button type="button" class="btn btn-outline-primary mr-2 position-relative" id="friendRequestsBtn">
                            <i class="fa-solid fa-user-plus"></i>
                            <span class="badge badge-danger badge-pill friendRequestsNumber"  style="position: absolute; top: -5px; right: -5px;">0</span>
                        </button>
                        <button type="button" class="btn btn-outline-primary position-relative mx-2">
                            <i class="fa-solid fa-comment-dots"></i>
                            <span class="badge badge-danger badge-pill unseen-messages-number" style="position: absolute; top: -5px; right: -5px;">0</span>
                        </button>
                    </div>
                    <!-- Right Aligned Links -->
                    <div class="d-flex">
                        <a href="/friendflow/" class="btn btn-outline-primary mr-2">Home <i class="fas fa-home"></i></a>
                        <a href="/friendflow/profile" class="btn btn-outline-primary mr-2">Profile <i class="fa-solid fa-user"></i></a>
                        <a href="/friendflow/logout" class="btn btn-outline-primary">Logout <i class="fa-solid fa-right-from-bracket"></i></a>
                    </div>
                </div>
            <?php else : ?>
                <form class="d-flex" action="/friendflow/login" method="POST">
                    <input class="form-control mr-2" type="email" name="email" placeholder="E-mail" aria-label="Email">
                    <input class="form-control mr-2" type="password" name="password" placeholder="Password" aria-label="Password">

                    <input type="hidden" name="csrf_token" value="<?= \App\Middlewares\CSRFMiddleware::getToken() ?>">
                    <button class="btn btn-outline-primary" type="submit">Login</button>
                </form>
            <?php endif; ?>
        </div>
    </nav>
    <!-- End of navbar -->
    <!-- Flash messages -->
    <?php if ($messages = \App\Core\Flash::get('error')) : ?>
        <?php foreach ($messages as $message) : ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Success flash messages -->
    <?php if ($messages = \App\Core\Flash::get('success')) : ?>
        <?php foreach ($messages as $message) : ?>
            <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <!-- End of flash messages -->

    <?php if (\App\Middlewares\AuthMiddleware::isLoggedIn()) : ?>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script>
            $(document).ready(function() { // TODO: Create web sockets or SSE to display this instead of calling it every 10 seconds
                function fetchFriendRequestsCount() {
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');

                    $.ajax({
                        url: '/friendflow/count-friend-requests',
                        type: 'POST',
                        data: {
                            csrf_token: csrfToken
                        },
                        success: function(response) {
                            if (response.status === "success") {
                                $('.friendRequestsNumber').text(response.number_of_requests);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error: ' + status + ' ' + error);
                        }
                    });
                }

                fetchFriendRequestsCount();
                setInterval(fetchFriendRequestsCount, 10000);
            });
        </script>
    <?php endif; ?>