<?php

class CRM_EasyAccountCreator_Drupal7User extends CRM_EasyAccountCreator_User {
  public function exists($email) {
    $account = user_load_by_mail($email);
    if ($account) {
      return TRUE;
    }
    else{
      return FALSE;
    }
  }

  public function create($name, $email) {
    $new_user = [
      'name' => $name,
      'pass' => $this->getRandomPassword(),
      'mail' => $email,
      'status' => 1,
      'init' => $email,
      'roles' => [DRUPAL_AUTHENTICATED_RID => 'authenticated user'],
      'timezone' => date_default_timezone_get(),
      'language' => language_default()->language,
    ];

    user_save('', $new_user);
  }
}
