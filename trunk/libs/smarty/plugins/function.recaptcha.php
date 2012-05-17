<?php

/**
 * Build reCaptcha output HTML
 *
 * @param array $params
 * @param object $smarty
 * @return string
 */
function smarty_function_recaptcha($params, & $smarty)
{
   require_once ('libs/recaptcha/recaptchalib.php');
   $error = (!empty ($params['error']) ? $params['error'] : null);
   return recaptcha_get_html(RECAPTCHA_PUBLIC_KEY, $error);
}

?>