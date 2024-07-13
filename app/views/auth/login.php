<?php 
if(App\Middlewares\AuthMiddleware::isLoggedIn()) {
    header("Location: /friendflow/");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <h1>Login Page</h1>
    <form action="/friendflow/login" method="POST">
        <input type="email" name="email" placeholder="Email">
        <input type="password" name="password" placeholder="Password">
        <input type="hidden" name="csrf_token" value="<?= \App\Middlewares\CSRFMiddleware::getToken() ?>">
        <button type="submit">Login</button>
    </form>
</body>

</html>