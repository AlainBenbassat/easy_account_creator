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

  }

  public function sendWelcomeMail() {
    [$domainEmailName, $domainEmailAddress] = CRM_Core_BAO_Domain::getNameAndEmail();
    $sendFrom = '"' . $domainEmailName . '" <' . $domainEmailAddress . '>';

    [$sent, $subject, $message, $html] = CRM_Core_BAO_MessageTemplate::sendTemplate([
      'groupName' => 'msg_tpl_workflow_contribution',
      'workflow' => $tplName,
      'contactId' => $pcpInfo['contact_id'],
      'tplParams' => $tplParams,
      'from' => $sendFrom,
      'toName' => $name,
      'toEmail' => $address,
    ]);
    return $sent;

    CRM_Core_BAO_MessageTemplate::sendTemplate($sendTemplateParams);
    CRM_Utils_Mail::send($mailParams);
  }
}
