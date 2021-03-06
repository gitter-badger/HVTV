<?php

class Controller {

    public $request;
    private $vars = array();
    public $layout = 'default';
    private $rendered = false;

    public function __construct($request = null) {
        if ($request) {
            $this->request = $request;
        }
    }

    /**
     * Rend la vue demandée :
     *      Extrait les paramètres du controlleur
     *      Rend la vue
     *      Rend le layout
     * @param String $view  Le nom de la vue = de l'action demandée
     * @return boolean  Si la vue est affichée, ne l'affiche pas deux fois
     */
    public function render($view) {
        if($this->rendered){
            return false;
        }
        extract($this->vars);
        if(strpos($view, '/')===0){
            $view=ROOT.DS.'view'.$view.'.php';
        }
        else{
            $view = ROOT. DS .'view'.DS.$this->request->controller.DS.$view.'.php';

        }

        ob_start();
        require($view);
        $content_for_layout=  ob_get_clean();
        require ROOT.DS.'view'.DS.'layout'.DS.$this->layout.'.php';
        $this->rendered=true;
    }

    /**
     * Extrait les paramètres du controlleur pour les rendre à la vue
     * @param mixed $key nom de la variable OU tableau de variables
     * @param mixed $value Valeur de la variable
     */
    public function set($key, $value = null) {
        if (is_array($key)) {
            $this->vars=$key;
        } else {
            $this->vars[$key] = $value;
        }
    }
    
    /**
     * Charge le modèle correspondant à l'action
     * @param type $name
     */
    public function loadModel($name) {
        $file = ROOT . DS . 'model' . DS . $name . '.php';
        require_once($file);
        if (!isset($this->$name)) {
            $this->$name = new $name();
        }
    }
    
    
    /**
     * Rend une page erreur 404
     * @param type $message
     */
    public function e404($message) {
        header('HTTP/1.0 404 NOT FOUND');
        $this->set('message', $message);
        $this->render('/errors/404');

        die();
    }
    
    
    /**
     * Permet d'appeler un controller depuis une vue
     */
    public function request($controller,$action){
        $controller .='Controller';
        //var_dump($controller);
        require_once ROOT.DS.'controller'.DS.$controller.'.php';
        $c = new $controller();
        //var_dump($c);
        return $c->$action();
    }
    
    public function redirect($url,$code){
        if($code==301){
            header('HTTP/1,1 301 Moved Permanently');
        }
        header('Location: '.Router::url($url));
    }

}
