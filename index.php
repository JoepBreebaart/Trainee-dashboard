<?php 

require_once 'core/init.php';

if(Session::exists('home')) {
    echo '<p>' . Session::flash('home') . '</p>';
}



$gebruiker = new Gebruiker();
if($gebruiker->isLoggedIn()) {
?>
    <p>Hello <a href="#"><?php echo escape($gebruiker->data()->email);?></a></p>
    <ul>
        <li><a href="logout.php">Log out</a></li>
        <li><a href="update.php">Update</a></li>
        <li><a href="changepassword.php">Verander wachtwoord</a></li>
        
    </ul>
<?php

if($gebruiker->hasPermission('begeleider')) {
    echo '<p>je bent een begeleider</p>';
}

} else {
    echo '<p>you need to <a href="login.php">log in</a> or <a href="register.php">register</a></p>';
}

