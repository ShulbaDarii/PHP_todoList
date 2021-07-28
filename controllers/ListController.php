<?php

namespace controllers;

use core\BaseController;

class ListController extends BaseController
{
    public function __construct()
    {
        if(!isset($_SESSION)){
            session_start();
        }
        if(!isset($_SESSION['Auth']))
        {
            $this->redirect('/user/index');
        }
        $this->layot = true;    
    }

    public function actionIndex()
    {
        $this->render('index');
    }
}