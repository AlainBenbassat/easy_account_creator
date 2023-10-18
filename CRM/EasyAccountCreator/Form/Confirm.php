<?php

use CRM_EasyAccountCreator_ExtensionUtil as E;

class CRM_EasyAccountCreator_Form_Confirm extends CRM_Core_Form {
  public function buildQuickForm() {
    $this->setTitle(E::ts('Create User'));

    $cid = $this->getContactIdFromUrl();

    $this->assign('intro', E::ts('Create a user for contact:'));
    $this->addFormElements($cid);
    $this->addFormButtons();

    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function setDefaultValues() {
    $cid = $this->getContactIdFromUrl();
    if ($cid) {
      $contact = $this->getContactDetails($cid);

      $defaultValues = [
        'contact_name' => $contact['first_name'] . ' ' . $contact['last_name'],
        'contact_email' => $contact['email.email'],
        'password' => 'some random password',
        'send_mail' => 1,
      ];

      $this->setDefaults($defaultValues);
    }
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
    $values = $this->controller->exportValues($this->_name);

    $user = CRM_EasyAccountCreator_UserFactory::getUser();
    if ($user->exists($email)) {
      $this->setElementError('contact_email', E::ts('A user with this email address already exists.'));
    }
  }

  public function postProcess() {
    $values = $this->controller->exportValues($this->_name);

    try {
      $user = CRM_EasyAccountCreator_UserFactory::getUser();
      $account = $user->create($values['contact_id'], $values['contact_email'], $values['contact_email']);
      $user->linktoContact($values['contact_id'], $account->uid, $values['contact_email']);

      if ($values['send_mail']) {
        $sent = $user->sendWelcomeMail($values['contact_id'], $values['contact_name'], $values['contact_email']);
        if (!$sent) {
          throw new Exception(E::ts('Could not send welcome mail'));
        }

        $successMessage = E::ts('User account created, and a welcome mail was sent.');
      }
      else {
        $successMessage = E::ts('User account created. No welcome mail was sent.');
      }

      CRM_Core_Session::setStatus(E::ts('Success'), $successMessage, 'success');
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/contact/view', 'reset=1&cid=' . $values['contact_id']));
    }
    catch (Exception $e) {
      CRM_Core_Session::setStatus(E::ts('Error'), $e->getMessage(), 'error');
    }

    parent::postProcess();
  }

  private function addFormElements($cid) {
    $this->add('hidden', 'contact_id', $cid);
    $this->add('text', 'contact_name', E::ts('Contact Name:'));
    $this->add('text', 'contact_email', E::ts('Login Name:'));
    $this->add('password', 'password', E::ts('Password:'));
    $this->add('checkbox', 'send_mail', E::ts('Send Welcome Mail?'));
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
    $values = $this->controller->exportValues($this->_name);//$this->exportValues();
    if (!empty($values['contact_id'])) {
      return $values['contact_id'];
    }
    else {
      return CRM_Utils_Request::retrieveValue('cid', 'Positive', 0, FALSE);
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
