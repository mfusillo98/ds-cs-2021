<?php

/**
 * @description: Interfaccia gestire l'autenticazione degli admin del sistema, effettuare il logo logout e sapere chi è attualmente loggato
 * */
class EmptyRequestFixService extends FuxServiceProvider implements IServiceProvider
{
    public static function bootstrap(){ /* Do nothing */ }

    public static function fix(){
        //Fallback per richieste fatte con axios
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (empty($_POST)){
                $_POST = json_decode(file_get_contents("php://input"), true);
                $_REQUEST = $_POST;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === "OPTIONS"){
            echo ""; exit;
        }
    }


}
