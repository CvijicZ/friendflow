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
            <a class="navbar-brand">Navbar</a>

            <?php if (\App\Middlewares\AuthMiddleware::isLoggedIn()): ?>
                <button type="button" class="btn btn-outline-primary"><a href="/friendflow/logout">Logout</a></button>

            <?php else: ?>
                <form class="d-flex" action="/friendflow/login" method="POST">
                    <input class="form-control me-2" type="email" name="email" placeholder="E-mail" aria-label="Email">
                    <input class="form-control me-2" type="password" name="password" placeholder="Password"
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