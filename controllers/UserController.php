<?php

namespace controllers;

use core\BaseController;
use models\UserModel;

class UserController extends BaseController
{
    public function __construct(){
        $this->layot=true;
    }
    public function actionIndex()
    {          
        // $this->redirect('/list/index');
        // $this->redirect('/user/create');
        
        $this->render('index',['model'=>['id'=>1 , 'task' => 'task']]);
    }

    public function actionLogin()
    {
        var_dump($_POST);
        die();
    }

    public function actionRegister()
    {
        $user = new UserModel;
        if($user->loadPost() && $user->validate()){
            if($user->save()){
                if(!isset($_SESSION)){
                    session_start();
                }
                $_SESSION['success'] = 'User is registered';
                $this->render('index');
                
            }  else {
                if(!isset($_SESSION)){
                    session_start();
                }
                $_SESSION['error'] = "Error in user's register";
                $this->render('index');
            } 
        } else {          
            $this->render('index');
        }

    }
}  