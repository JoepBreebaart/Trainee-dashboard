<?php 
require_once 'core/Init.php';


    if(Input::exists()) {
        if (Token::check(Input::get('token'))) {
        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'email' => array(
                'required' => true,
                'min' => 2,
                'max' => 50,
                'unique' => 'gebruikers'
            ),
            'wachtwoord' => array(
                'required' => true,
                'min' => 6
            ),
            'wachtwoord_opnieuw' => array(
                'required' => true,
                'matches' => 'wachtwoord'
            ),
            'naam' => array(
                'required' => true,
                'min' => 2,
                'max' => 50
            )
        ));

        if($validation->passed()) {
            $gebruiker = new Gebruiker();
            $salt = Hash::salt(16);  
                   
            try {
                $gebruiker->create(array(
                    'email' => Input::get('email'),
                    'wachtwoord' => Hash::make(Input::get('wachtwoord'), $salt),
                    'salt' => $salt,
                    'naam' => Input::get('naam'),
                    'groep' => 1
                ));

                Session::flash('home' , 'Netjes geregistreerd al zeg ik het zelf.');
                Redirect::to('index.php');
            } catch(Exception $e) {
                die($e->getMessage());
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
        <label>email
            <input type="text" name="email" value="<?php echo escape(Input::get('email')) ?>" id="email">
        </label>
    </div>
    <div class="field">
        <label>wachtwoord
            <input type="password" name="wachtwoord" id="wachtwoord">
        </label>
    </div>
    <div class="field">
        <label>wachtwoord opnieuw
            <input type="password" name="wachtwoord_opnieuw" id="wachtwoord_opnieuw">
        </label>
    </div>
    <div class="field">
        <label>naam
            <input type="text" name="naam" value="<?php echo escape(Input::get('naam')) ?>" id="naam">
        </label>
    </div>
    <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
    <input type="submit" value="register">
    
</form>