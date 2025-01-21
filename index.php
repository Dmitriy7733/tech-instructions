<?php
session_start(); // Не забудьте запустить сессию, если вы используете $_SESSION

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/assets/functions.php';
require_once __DIR__ . '/app/users/upload.php';
//require_once __DIR__ . '/config/db_init.php';
include 'includes/header.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch ($page) {
    case 'register':
        include 'views/register.php';
        break;
    case 'login':
        include 'views/login.php';
        break;
    /*case 'instruction':
        include 'views/instruction.php';
        break;*/
    case 'admin':
        include 'views/admin.php';
        break;
    case 'upload':
        include 'views/upload_form.php';
        break;
            
    default:
        include 'views/home.php';
}
include 'includes/footer.php';
