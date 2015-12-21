<?php
/*
 * (c) Kévin Dunglas <dunglas@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
require_once(dirname(__FILE__).'/../../../../external/sfDoctrineGuardPlugin/modules/sfGuardAuth/actions/actions.class.php');


/**
 * Actions.
 *
 * @package kdDoctrineGuardFacebookConnectPlugin
 * @subpackage actions
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class BasekdGuardAuthFacebookConnectActions extends sfGuardAuthActions {
  /**
   *
   * @see  sfGuardAuthActions
   */
  public function executeSignin($request)
  {
    $this->facebook = kdDoctrineGuardFacebookConnect::getFacebook();
    parent::executeSignin($request);
  }

  protected function renderJson($content, $lifetime = null, $status = 200)
  {
      return ApiOutputUtil::renderJson($this->getResponse(), $this->getRequest(), $content, $lifetime, $status);
  }


  /**
   * If a <code>Facebook</code> session if open signout from Facebook
   * then redirect to <code>sfGuardAuthActions</code> signout.
   * Else forward to <code>sfGuardAuthActions</code> signout.
   *
   * @param sfWebRequest $request
   */
  public function executeSignout($request) {
    kdDoctrineGuardFacebookConnectFilter::log('signout');

    $facebook = kdDoctrineGuardFacebookConnect::getFacebook();

    $url = $this->getContext()->getRouting()->generate('sf_guard_signout', array(), true);

    if ($facebook->getSession()) {
      $url = $facebook->getLogoutUrl(array (
        'next' => $url
      ));
      $facebook->destroySession();

      $this->redirect($url);
    }

     $this->forward('sfGuardAuth', 'signout');
  }

  public function isCached(){
      return false;
  }

}
