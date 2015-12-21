<?php

/*
 * (c) Kévin Dunglas <dunglas@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Logs or registers automatically Facebook connected users.
 *
 * @package kdDoctrineGuardFacebookConnec
 * @subpackage lib
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class kdDoctrineGuardFacebookConnectFilter extends sfFilter {

  public static $logFile = '/var/www/tmp/log.txt';
  //https://developers.facebook.com/docs/facebook-login/permissions/v2.5
  public $fields = array(
        'id',
        'name',
        'first_name',
        'last_name',
        'age_range',
        'link',
        'gender',
        'locale',
        'timezone',
        'email',
        'location',
        'picture',
        'hometown',
        'birthday',
        'verified'
  );


  public static function log($log){
      if(is_array($log)){
          $log = json_encode($log);
      }
      file_put_contents(kdDoctrineGuardFacebookConnectFilter::$logFile, $log.PHP_EOL, FILE_APPEND);
      //kdDoctrineGuardFacebookConnectFilter::log();
  }
  /**
   * Executes the filter and chains.
   *
   * @param sfFilterChain $filterChain
   */
  public function execute($filterChain) {
      kdDoctrineGuardFacebookConnectFilter::log('kdDoctrineGuardFacebookConnectFilter::33');
      //kdDoctrineGuardFacebookConnectFilter::log($this->context->getUser()->getGuardUser()->getFacebookId());
    if ($this->isFirstCall() && $this->context->getUser()->isAnonymous()) {

      $facebook = kdDoctrineGuardFacebookConnect::getFacebook();

      //$uid = $facebook->getUser();
      $loginUrl = $facebook->getLoginUrl();
      kdDoctrineGuardFacebookConnectFilter::log($loginUrl);
      try {
        $me = $facebook->api('/me?fields='.implode(',', $this->fields));
        if ($me) {
          kdDoctrineGuardFacebookConnectFilter::log($me);
          $sfGuardUser = kdDoctrineGuardFacebookConnect::updateOrCreateUser($me);

          $this->context->getUser()->signIn($sfGuardUser);
        }
      } catch (FacebookApiException $ex) {
        $this->getContext()->getLogger()->err($ex);
      }
    }

    $filterChain->execute();
  }



}
