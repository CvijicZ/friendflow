<?php 
if(App\Middlewares\AuthMiddleware::isLoggedIn()) {
    header("Location: /friendflow/");
    exit();
}
?>

<div class="d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 bg-dark-blue-gray custom-text-color custom-border" style="max-width: 400px; width: 100%;">
            <h1 class="text-center mb-4">Login</h1>
            <form action="/friendflow/login" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <input type="hidden" name="csrf_token" value="<?= \App\Middlewares\CSRFMiddleware::getToken() ?>">
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <p class="text-center mt-3">
                <a href="#" class="text-decoration-none">Forgot Password?</a>
            </p>
        </div>
    </div>
