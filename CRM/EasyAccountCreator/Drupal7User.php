<?php

// Rest password YOURSITE/user/password?name=LOGIN-NAME

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

  public function create($contactId, $name, $email) {
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
    $user = user_save('', $new_user);

    $this->removeDuplicate($contactId, $user->uid, $email);

    return $user;
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
