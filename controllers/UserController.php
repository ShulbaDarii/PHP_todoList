<?php

namespace controllers;

use core\BaseController;
use models\UserModel;
use service\SendEmail;

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
        
        $this->redirect('list/index');
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $email =$_POST['email'];
            $password = $_POST['password'];
            $user = UserModel::find()->where(['email' => $email, 'password' => $this->passwordHasher($password)])->one();
            if($user)
            {
                if($user->confrim_email)
                {
                    if(!isset($_SESSION)){
                        session_start();
                    }
                    $_SESSION['Auth']= $user->id;
                    $this->redirect('/list/index');
                }else {
                    if(!isset($_SESSION))
                    {
                        session_start();
                    }
                    $_SESSION['error'] = "Email doesn't confirm";
                    $this->render('index');
                }
            }
        }
    }

    public function actionRegister()
    {
  
        $user = new UserModel;
        if($user->loadPost() && $user->validate()){
            if(!UserModel::find()->where(['email' => $user->email])->one())
            {
               $user->password =$this->passwordHasher($user->password);
                if($user->save()){
                    if(!isset($_SESSION)){
                        session_start();
                    }
                    if(SendEmail::send($user->email,$user->id)){
                    $_SESSION['success'] = 'User is registered. Confrim registretion by link in the email. ';
                    $this->render('index');
                    } else {
                        $_SESSION['error'] = "Error in user's register";
                        $this->render('index');
                        //удалить пользователя 
                    }
                    
                }  else {
                    if(!isset($_SESSION)){
                        session_start();
                    }
                    $_SESSION['error'] = "Error in user's register";
                    $this->render('index');
                } 
            }else {
                if(!isset($_SESSION)){
                    session_start();
                }
                $_SESSION['error'] = "User with " . $user->email . " has alredy registred.";
                $this->render('index');
            }
        } else {          
            $this->render('index');
        }
    }

    public function actionConfirm()
    {

        if ($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $id = $_GET['id'];
            $user =UserModel::find()->where(['id'=> $id])->one();
          
            $user->confirm_email = true;
            // var_dump($user->save());
            // die();
            if($user->save())
            {               
                //вывести уведомление success
                //перехоидим на строницу list/index
                $this->redirect('/list/index');
            } else {
                //вывести уведомление error
                //перехоидим на строницу user/index
                $this->render('index');
            }
        }
    }

    public function actionLoguot(){
        if(!isset($_SESSION)){
            session_start();
        }
        session_unset();
        session_destroy();
        $this->render('index');
    }
}  