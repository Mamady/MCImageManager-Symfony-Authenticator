<?php
// This class is a Symfony authenticator
class Moxiecode_SymfonyAuthenticator extends Moxiecode_ManagerPlugin {

    public function Moxiecode_SymfonyAuthenticator() {}

    public function onAuthenticate(&$man) {

        if(!defined(SYMFONY_BOOTSTRAPPED)) {
            $im_config =& $man->getConfig();

            // assuming standard location for plugin web/js/tiny_mce/plugins/imagemanager/
            define('SF_ROOT_DIR',    realpath(dirname(__file__).'/../../../../../../..'));

            // symfony spoils $cmd, important variable for ImageManager action
            $temp_cmd = $GLOBALS['cmd'];

            require_once(SF_ROOT_DIR.'/config/ProjectConfiguration.class.php');

            $configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false);

            sfContext::createInstance($configuration);

            // check user is logged in
            if(!sfContext::getInstance()->getUser()->isAuthenticated()) {
                header('Location: /login');
                exit;
            }

            $sym_user_id = sfContext::getInstance()->getUser()->getGuardUser()->getId();

            $GLOBALS['cmd'] = $temp_cmd;
            unset($temp_cmd);

            // reset the error handler back to MoxieCode error handler (symfony changes it)
            set_error_handler("HTMLErrorHandler");

            $im_config['SymfonyAuthenticator.user_key'] = $sym_user_id;

            // Force update of internal state
            $man->setConfig($im_config);

            define('SYMFONY_BOOTSTRAPPED', true);
        }

        return sfContext::getInstance()->getUser()->isAuthenticated();
    }

}

// Add plugin to MCManager
$man->registerPlugin("SymfonyAuthenticator", new Moxiecode_SymfonyAuthenticator());

