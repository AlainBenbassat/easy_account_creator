<?php

class CRM_EasyAccountCreator_UserFactory {
  public static function getUser() {
    $wordpress = Civi::settings()->get('wpBasePage');
    if ($wordpress) {
      return new CRM_EasyAccountCreator_WordpressUser();
    }
    else {
      return new CRM_EasyAccountCreator_Drupal7User();
    }
  }
}
