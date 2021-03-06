<?php

// Demarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_name('laborne_register_app');
    session_start();
}



/**
 * definition des constantes
 */
app_const : {
    define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);
    define('WEBROOT', ROOT . "public");
    define('ENV', 'development');
    define('APPURL', 'http://laborne.larytech.com');
}


/**
 * Si un administrateur est connecté & Message d'information (flash)
 */
app_auth : {
    $isLogged = isset($_SESSION['isLogged']) ? $_SESSION['isLogged'] : false;
    $flashMsg = [
        'registration_success' => 'Félicitation, votre identification a été effectuée avec succès',
        'registration_failed' => 'Oups, le formulaire a été mal rempli',
        'registration_existe' => 'Ces informations ont été déjà enregistrées',
        'login_success' => 'Vous êtes maintenant connecté',
        'login_failed' => 'Une erreur est survenue lors de la tentive de connexion',
        'logout' => 'vous êtes maintent déconnecté',

        'delete_success' => 'Suppression effectuée',
        'delete_failed' => 'Echec de la suppression',
        'update_success' => 'Données mises à jour',
        'update_failed' => 'Echec de la mise à jour',
        'search_failed' => 'Aucun enregistrement ne correspond à votre recherche',
        'image_upload_failed' => "Votre photo n'a pu être téléchargée, veuillez vérifier
        que c'est bien une image, qu'elle pèse moins de 15mo, si cela se repoduit, 
        essayez avec une autre photo.",
        'image_required' => 'Veuillez ajouter un photo pour votre identification',
        'failed' => 'Oups une erreur est survenue dans votre navigation'
    ];
}


/**
 * Choix de la langue de l'application
 */
app_lang : {
    $supportedLang = ['fr', 'en'];
    $lang = isset($_GET['lang']) ? strval($_GET['lang']) : 'fr';
    $lang = in_array($lang, $supportedLang) ? $lang : 'fr';
    $appText = require_once(ROOT . "src/lang/{$lang}.php");
}


/**
 * Definition des fonctions core de l'application
 */
app_core_func : {
    /**
     * get a msg in the messages array thanks
     * to a key
     *
     * @param string $key
     * @return string|null
     */
    function getMsg($key)
    {
        global $flashMsg;
        if (array_key_exists($key, $flashMsg)) {
            return $flashMsg[$key];
        }
        return null;
    }


    /**
     * get a msg in the messages array thanks
     * to a key
     *
     * @param string $key
     * @return string|null
     */
    function _tl($key)
    {
        global $appText;
        if (array_key_exists($key, $appText)) {
            return $appText[$key];
        }
        return null;
    }


    /**
     * redirect to a view file
     *
     * @param string $file
     * @return void
     */
    function redirect($file = '', $option = '')
    {
        if (empty($file)) {
            header("Location: index.php");
            exit();
        }
        header("Location: {$file}.php{$option}");
        exit();
    }


    // Defintion des fonction pour le traitement du formulaire
    /**
     * whether data are posted
     *
     * @return boolean
     */
    function isPosted()
    {
        return isset($_POST) && !empty($_POST);
    }


    /**
     * whether a file is uploaded
     *
     * @param string $file
     * @return boolean
     */
    function hasUpload($file)
    {
        return isset($_FILES[$file]['name']) && !empty($_FILES[$file]['name']);
    }



    // Definition des fonction pour les message flash
    /**
     * set a flash in the session
     *
     * @param string $type
     * @param string $message
     * @return void
     */
    function setFlash($type, $message)
    {
        $_SESSION['flash'][$type] = $message;
    }


    /**
     * whether the session has flashes
     *
     * @return boolean
     */
    function hasFlashes()
    {
        return array_key_exists('flash', $_SESSION);
    }


    /**
     * retrieve flashes
     *
     * @return array|null
     */
    function getFlashes()
    {
        return $_SESSION['flash'];
    }


    /**
     * unset a flashes in the session
     *
     * @return void
     */
    function unsetFlash()
    {
        unset($_SESSION['flash']);
    }


    /**
     * redirecte if the user is not 
     * connected
     *
     * @return void
     */
    function loggedOnly()
    {
        global $isLogged;
        if (!$isLogged) {
            redirect("login");
        }
    }



    /**
     * check if login are correct
     *
     * @param string $name
     * @param string $password
     * @return bool
     */
    function checkAuth($name, $password)
    {
        $logins = require('auth/logins.php');

        if (in_array($name, $logins['name'])) {
            if (password_verify($password, $logins['password'][$name])) {
                return true;
            }
            return false;
        }
        return false;
    }
}
