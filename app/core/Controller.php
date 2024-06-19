<?php
namespace App\Core;

require_once 'Template.php';
class Controller {
    protected function view($view, $data = []) {
        Template::render($view, $data);
    }
}
