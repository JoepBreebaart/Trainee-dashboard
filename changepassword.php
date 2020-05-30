<?php 
require_once 'core/init.php';

$gebruiker = new Gebruiker();

if(!$gebruiker->isLoggedIn()) {
    Redirect::to('index.php');
}

if(Input::exists()) {
    if(Token::check(Input::get('token'))) {

        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'huidig_wachtwoord' => array(
                'required' => true,
                'min' => 6
            ),
            'nieuw_wachtwoord' => array(
                'required' => true,
                'min' => 6
            ),
            'nieuw_wachtwoord_opnieuw' => array(
                'required' => true,
                'min' => 6,
                'matches' => 'nieuw_wachtwoord'
            )
        ));

            if($validation->passed()) {
                
                if(Hash::make(Input::get('huidig_wachtwoord'), $gebruiker->data()->salt) !== $gebruiker->data()->wachtwoord) {
                    echo 'uw huidige wachtwoord is incorect';
                } else {
                    $salt = Hash::salt(16);
                    $gebruiker->update(array(
                        'wachtwoord' => hash::make(Input::get('nieuw_wachtwoord'), $salt),
                        'salt' => $salt
                    ));

                    Session::flash('home', 'wachtwoord is verandert.');
                    Redirect::to('index.php');
                }

            } else {
                foreach($validation->errors() as $error) {
                    echo $error, '<br>';
                }
            }


    }
}

?>

<form action="" method="post">
    <div class="field">
        <label>huidige wachtwoord
            <input type="password" name="huidig_wachtwoord" id="huidig_wachtwoord">
        </label>
    </div>
    <div class="field">
        <label>nieuw wachtwoord
            <input type="password" name="nieuw_wachtwoord" id="nieuw_wachtwoord">
        </label>
    </div>
    <div class="field">
        <label>nieuw wachtwoord opnieuw
            <input type="password" name="nieuw_wachtwoord_opnieuw" id="nieuw_wachtwoord_opnieuw">
        </label>
    </div>
    <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
    <input type="submit" value="register">