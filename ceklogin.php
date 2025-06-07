<?php

require 'function.php';

if(isset($_SESSION['login'])){
    //yaudah
} else {
    //kalo belom login
    header('location:login.php');
}

?>