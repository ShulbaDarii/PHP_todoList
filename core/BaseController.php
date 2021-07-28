<?php 

namespace core;

class BaseController
{
    protected $layot;

    public function render($view, array $params = [])
    {
        $className = lcfirst( str_replace('Controller','', substr(get_class($this),strpos(get_class($this),'\\') + 1)));
        $viewPath = './views/' . $className . '/' . $view . '.php';
        if(file_exists($viewPath)){
            if($this->layot){
                require_once './views/_shared/header.php';   
            }
            extract ($params);
            require_once $viewPath;
            if($this->layot){
                require_once './views/_shared/footer.php';   
            }
        }else{
            require_once './views/_shared/error.php';
        }  
    }

    public function redirect($path)
    {
        Header('location: '. $path);
    }


    public function passwordHasher($value){
        return sha1(SALT . $value . SALT);
    }
}

