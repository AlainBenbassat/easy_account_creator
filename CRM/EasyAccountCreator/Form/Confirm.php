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
    $contact = $this->getContactDetails();

    $defaultValues = [
      'contact_id' => $contact['id'],
      'contact_name' => $contact['first_name'] . ' ' . $contact['last_name'],
      'contact_email' => $contact['email.email'],
      'password' => 'some random password',
      'send_mail' => 1,
    ];

    $this->setDefaults($defaultValues);
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

  private function getContactDetails() {
    $cid = $this->getContactIdFromUrl();

    return \Civi\Api4\Contact::get(FALSE)
      ->addSelect('id', 'first_name', 'last_name', 'email.email')
      ->addJoin('Email AS email', 'LEFT', ['email.is_primary', '=', 1])
      ->addWhere('id', '=', $cid)
      ->execute()
      ->first();
  }

  private function getContactIdFromUrl() {
    $vals = $this->controller->exportValues($this->_name);
    if (empty($vals)) {
      return CRM_Utils_Request::retrieveValue('cid', 'Positive', 0, TRUE);
    }
    else {
      return $vals['cid'];
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
