<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Social Network</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/friendflow/public/css/style.css">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar sticky-top navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/friendflow/">Navbar</a>

            <?php if (\App\Middlewares\AuthMiddleware::isLoggedIn()): ?>
                <div class="d-flex ml-auto">
                    <button type="button" class="btn btn-outline-primary mr-2 position-relative" id="friendRequestsBtn">
                        Friend requests
                        <span class="badge badge-danger badge-pill" id="friendRequestsNumber" style="position: absolute; top: -5px; right: -5px;">0</span>
                    </button>

                    <a href="/friendflow/" class="btn btn-outline-primary mr-2">Home</a>
                    <a href="/friendflow/profile" class="btn btn-outline-primary mr-2">Profile</a>
                    <a href="/friendflow/logout" class="btn btn-outline-primary">Logout</a>
                </div>
            <?php else: ?>
                <form class="d-flex" action="/friendflow/login" method="POST">
                    <input class="form-control mr-2" type="email" name="email" placeholder="E-mail" aria-label="Email">
                    <input class="form-control mr-2" type="password" name="password" placeholder="Password"
                        aria-label="Password">

                    <input type="hidden" name="csrf_token" value="<?= \App\Middlewares\CSRFMiddleware::getToken() ?>">
                    <button class="btn btn-outline-primary" type="submit">Login</button>
                </form>

            <?php endif; ?>
        </div>
    </nav>
    <!-- End of navbar -->

    <!-- Flash messages -->
    <?php if ($messages = \App\Core\Flash::get('error')): ?>
        <?php foreach ($messages as $message): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Success flash messages -->
    <?php if ($messages = \App\Core\Flash::get('success')): ?>
        <?php foreach ($messages as $message): ?>
            <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <!-- End of flash messages -->

    <?php if (\App\Middlewares\AuthMiddleware::isLoggedIn()): ?>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script>

$(document).ready(function () {  // TODO: Create web sockets or SSE to display this instead of calling it every 10 seconds
    function fetchFriendRequestsCount() {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: '/friendflow/count-friend-requests',
            type: 'POST',
            data: {
                csrf_token: csrfToken
            },
            success: function (response) {
                if (response.status === "success") {
                    $('#friendRequestsNumber').text(response.number_of_requests);
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX error: ' + status + ' ' + error);
            }
        });
    }

    fetchFriendRequestsCount();
    setInterval(fetchFriendRequestsCount, 10000);
});
</script>
    <?php endif; ?>