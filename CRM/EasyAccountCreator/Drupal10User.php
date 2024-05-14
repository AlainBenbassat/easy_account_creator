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
    $user->addRole('authenticated');
    $user->enforceIsNew();
    $user->activate();
    $user->save();

    $this->removeDuplicate($contactId, $user->id(), $email);

    return $user;
  }

  public function getOneTimeLoginUrl($user) {
    return user_pass_reset_url($user);
  }

  private function removeDuplicate($contactId, $userId, $email) {
    // correct uf match table if needed
    // drupal or civi sometimes create a duplicate entry
    $sql = "
      select
        *
      from
        civicrm_uf_match
      where
        uf_id = $userId
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);
    if ($dao->fetch()) {
      if ($dao->contact_id != $contactId) {
        $ufContactId = $dao->contact_id;
        CRM_Core_DAO::executeQuery("update civicrm_uf_match set contact_id = $contactId where id = {$dao->id}");
        CRM_Core_DAO::executeQuery("delete from civicrm_contact where id= $ufContactId");
      }
    }
  }
}
