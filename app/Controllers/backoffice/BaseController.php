<?php

class BaseController {
    public function model($model) {
        require_once __DIR__ . '/../../Models/backoffice/' . $model . '.php';
        return new $model();
    }

    public function view($view, $data = []) {
        extract($data);
        
        $viewPath = __DIR__ . '/../../Views/backoffice/' . $view . '.php';
        if(file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die('View does not exist: ' . $viewPath);
        }
    }
}

?>

