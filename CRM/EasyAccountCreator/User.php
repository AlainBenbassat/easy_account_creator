<?php

abstract class CRM_EasyAccountCreator_User {
  private const MIN_PWD_LENGTH = 32;
  private const MAX_PWD_LENGTH = 48;

  abstract public function exists($email);
  abstract public function create($name, $email);

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

  public function linktoContact($contactId, $ufId) {
    Civi\Api4\UFMatch::create(FALSE)
      ->addValue('uf_id', $ufId)
      ->addValue('contact_id', $contactId)
      ->execute();
  }

  public function sendWelcomeMail($contactId, $contactName, $contactEmail) {
    $optionGroup = CRM_EasyAccountCreator_Config::getOptionGroupForWorkflowMessage();
    $msgTemplate = CRM_EasyAccountCreator_Config::getMsgTemplate();

    [$sent, $subject, $message, $html] = CRM_Core_BAO_MessageTemplate::sendTemplate([
      'groupName' => $optionGroup['name'],
      'workflow' => $msgTemplate['workflow_name'],
      'contactId' => $contactId,
      'tplParams' => [],
      'from' => '"' . Civi::settings()->get(CRM_EasyAccountCreator_Config::SETTING_EMAIL_FROM_NAME) . '"  <' . Civi::settings()->get(CRM_EasyAccountCreator_Config::SETTING_EMAIL_FROM_ADDRESS) . '>',
      'toName' => $contactName,
      'toEmail' => $contactEmail,
    ]);

    return $sent;
  }
}
