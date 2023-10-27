<?php

abstract class CRM_EasyAccountCreator_User {
  private const MIN_PWD_LENGTH = 32;
  private const MAX_PWD_LENGTH = 48;

  abstract public function exists($email);
  abstract public function create($contactId, $name, $email);

  public function getRandomPassword() {
    $characters = '!@#$%*(){};:.,/0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';

    $n = rand(self::MIN_PWD_LENGTH, self::MAX_PWD_LENGTH);
    for ($i = 0; $i < $n; $i++) {
      $index = rand(0, strlen($characters) - 1);
      $randomString .= $characters[$index];
    }

    return $randomString;
  }

  public function linktoContact($contactId, $ufId, $email) {
    $id = CRM_Core_DAO::singleValueQuery("select id from civicrm_uf_match where contact_id = $contactId and uf_id = $ufId");
    if (!$id) {
      Civi\Api4\UFMatch::create(FALSE)
        ->addValue('uf_id', $ufId)
        ->addValue('contact_id', $contactId)
        ->addValue('uf_name', $email)
        ->execute();
    }
  }

  public function sendWelcomeMail($contactId, $contactName, $contactEmail, $passwordResetLink) {
    $optionGroup = CRM_EasyAccountCreator_Config::getOptionGroupForWorkflowMessage();
    $msgTemplate = CRM_EasyAccountCreator_Config::getMsgTemplate();

    [$sent, $subject, $message, $html] = CRM_Core_BAO_MessageTemplate::sendTemplate([
      'groupName' => $optionGroup['name'],
      'workflow' => $msgTemplate['workflow_name'],
      'messageTemplateID' => $msgTemplate['id'],
      'contactId' => $contactId,
      'tplParams' => [
        'passwordResetLink' => $passwordResetLink,
      ],
      'tokenContext' => ['contactId' => $contactId],
      'from' => '"' . Civi::settings()->get(CRM_EasyAccountCreator_Config::SETTING_EMAIL_FROM_NAME) . '"  <' . Civi::settings()->get(CRM_EasyAccountCreator_Config::SETTING_EMAIL_FROM_ADDRESS) . '>',
      'toName' => '"' . $contactName . '"',
      'toEmail' => $contactEmail,
    ]);

    return $sent;
  }
}
