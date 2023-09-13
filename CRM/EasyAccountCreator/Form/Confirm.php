<?php

use CRM_EasyAccountCreator_ExtensionUtil as E;

class CRM_EasyAccountCreator_Form_Confirm extends CRM_Core_Form {
  public function buildQuickForm() {
    $this->setTitle(E::ts('Create User'));

    $this->addFormElements();
    $this->addFormButtons();

    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function setDefaultValues() {
    $cid = $this->getContactIdFromUrl();
    $contact = $this->getContactDetails($cid);

    $defaultValues = [
      'contact_id' => $contact['id'],
      'contact_name' => $contact['first_name'] . ' ' . $contact['last_name'],
      'contact_email' => $contact['email.email'],
      'password' => 'some random password',
      'send_mail' => 1,
    ];

    $this->setDefaults($defaultValues);
  }

  public function validate() {
    $values = $this->exportValues();

    $this->validateEmailAddress($values['contact_email']);
    $this->validateExistingUser($values['contact_email']);

    return parent::validate();
  }

  private function validateEmailAddress($email) {
    if (empty($email)) {
      $this->setElementError('contact_email', E::ts('The contact must have an email address because it is used as login name.'));
    }
  }

  private function validateExistingUser($email) {
    $user = CRM_EasyAccountCreator_UserFactory::getUser();
    if ($user->exists($email)) {
      $this->setElementError('contact_email', E::ts('A user with this email address already exists.'));
    }
  }

  public function postProcess() {
    $user = CRM_EasyAccountCreator_UserFactory::getUser();

    parent::postProcess();
  }

  private function addFormElements() {
    $this->add('text', 'contact_id', E::ts('Contact ID:'), ['disabled' => 'disabled']);
    $this->add('text', 'contact_name', E::ts('Contact Name:'), ['disabled' => 'disabled']);
    $this->add('text', 'contact_email', E::ts('Login Name:'), ['disabled' => 'disabled']);
    $this->add('password', 'password', E::ts('Password:'), ['disabled' => 'disabled']);
    $this->addYesNo('send_mail', E::ts('Send Welcome Mail?'), FALSE, TRUE);
  }

  private function getContactDetails($cid) {
    return \Civi\Api4\Contact::get(FALSE)
      ->addSelect('id', 'first_name', 'last_name', 'email.email')
      ->addJoin('Email AS email', 'LEFT', ['email.is_primary', '=', 1])
      ->addWhere('id', '=', $cid)
      ->execute()
      ->first();
  }

  private function getContactIdFromUrl() {
    $values = $this->exportValues();
    if (!empty($values['contact_id'])) {
      return $values['contact_id'];
    }
    else {
      return CRM_Utils_Request::retrieveValue('cid', 'Positive', 0, TRUE);
    }
  }

  private function addFormButtons() {
    $this->addButtons([
      [
        'type' => 'submit',
        'name' => E::ts('Create user'),
        'isDefault' => TRUE,
      ],
      [
        'type' => 'cancel',
        'name' => E::ts('Cancel'),
      ],
    ]);
  }

  private function getRenderableElementNames() {
    $elementNames = [];
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}
