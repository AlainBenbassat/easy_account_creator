<?php

// Rest password YOURSITE/user/password?name=LOGIN-NAME

use Drupal\user\Entity\User;

class CRM_EasyAccountCreator_Drupal10User extends CRM_EasyAccountCreator_User {
  public function exists($email) {
    $users = \Drupal::entityTypeManager()->getStorage('user')
      ->loadByProperties(['mail' => $email]);
    $user = reset($users);
    if ($user) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  public function create($contactId, $name, $email) {
    $user = User::create();
    $user->setUsername($name); // This username must be unique and accept only [a-Z,0-9, - _ @].
    $user->setPassword($this->getRandomPassword());
    $user->setEmail($email);
    $user->enforceIsNew();
    $user->activate();
    $user->save();

    return $user;
  }

  public function getOneTimeLoginUrl($user) {
    return user_pass_reset_url($user);
  }

}
