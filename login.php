<?php 

require_once 'core/init.php';
if(Input::exists()) {
    if(Token::check(Input::get('token'))) {

        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'email' => array('required' => true),
            'wachtwoord' => array('required' => true)
        ));

        if($validation->passed()) {
            $gebruiker = new Gebruiker();

            $remember = (Input::get('remember') === 'on') ? true : false;
            $login = $gebruiker->login(Input::get('email'), Input::get('wachtwoord'), $remember);

            if($login) {
                Redirect::to('index.php');
            } else {
                echo '<p>inloggen niet gelukt</p>';
            }

        } else {
            foreach($validation->errors() as $error) {
                echo $error . '<br>';
            }
        }

    }
}

?>

<form action="" method="post">
    <div class="field">
        <label>E-mail
            <input type="text" name="email" id="email" autocomplete="on">
        </label>
    </div>

    <div class="field">
        <label>Wachtwoord
            <input type="password" name="wachtwoord" id="wachtwoord" autocomplete="on">
        </label>
    </div>

    <div class="field">
        <label>Onthoud mij
            <input type="checkbox" name="remember" id="remember" autocomplete="on">
        </label>
    </div>

    <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
    <input type="submit" value="Log in">
</form>