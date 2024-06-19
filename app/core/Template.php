<?php
namespace App\Core;
class Template {
    public static function render($view, $data = []) {
        extract($data);

        // Include header
        require_once 'app/views/partials/header.php';

        // Include the view
        require_once "app/views/$view.php";

        // Include footer
        require_once 'app/views/partials/footer.php';
    }
}
