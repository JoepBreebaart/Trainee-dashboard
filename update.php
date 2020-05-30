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
            'email' => array(
                'required' => true,
                'min' => 2,
                'max' => 50
            )
        ));

        if($validation->passed()) {
            try {
                $gebruiker->update( array(
                    'email' => Input::get('email')
                ));

                Session::flash('Home', 'de gegevens zijn geupdate.');
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
        <label>Email
            <input type="text" name="email" value="<?php echo escape($gebruiker->data()->email); ?>">

            <input type="submit" value="Update">
            <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
        </label>
    </div>