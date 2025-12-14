<?php
/**
 * Classe pour gérer le rendu des vues
 * Déplacée depuis `core/View.php` vers la racine pour intégration
 */
class View
{
    private $viewsPath;
    
    public function __construct() {
        $this->viewsPath = __DIR__ . '/views';
    }
    private $data = [];

    public function render($viewPath, $data = [])
    {
        $this->data = $data;
        extract($data);
        $file = $this->viewsPath . '/' . $viewPath . '.php';
        if (!file_exists($file)) {
            throw new Exception("Vue non trouvée: {$file}");
        }
        require $file;
    }

    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    public function url($controller, $action = 'index', $params = [])
    {
        if (!defined('WEB_ROOT')) {
            require_once __DIR__ . '/config/paths.php';
        }
        $url = WEB_ROOT . "/index.php?c={$controller}&amp;a={$action}";
        foreach ($params as $key => $value) {
            $url .= "&amp;{$key}=" . urlencode($value);
        }
        return $url;
    }
}
?>
