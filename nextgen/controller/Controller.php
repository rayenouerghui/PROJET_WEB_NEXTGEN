<?php
/**
 * Classe de base pour tous les contrôleurs
 * Déplacée depuis `core/Controller.php` pour intégration MVC
 */
abstract class Controller
{
    protected $db;
    protected $view;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->view = new View();
    }

    protected function render($viewPath, $data = [])
    {
        $this->view->render($viewPath, $data);
    }

    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    protected function jsonResponse($data, $statusCode = 200)
    {
        // Clean any accidental output (warnings, notices, stray HTML) so JSON remains valid
        if (ob_get_length() !== false && ob_get_length() > 0) {
            @ob_end_clean();
        }
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function setFlash($type, $message)
    {
        $_SESSION[$type] = $message;
    }

    protected function getFlash($type)
    {
        if (isset($_SESSION[$type])) {
            $message = $_SESSION[$type];
            unset($_SESSION[$type]);
            return $message;
        }
        return null;
    }
}

?>
