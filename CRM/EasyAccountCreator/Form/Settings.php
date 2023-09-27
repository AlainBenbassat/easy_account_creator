<?php

use CRM_EasyAccountCreator_ExtensionUtil as E;

class CRM_EasyAccountCreator_Form_Settings extends CRM_Core_Form {

  public function buildQuickForm() {
    $this->setTitle(E::ts('Easy Account Creator Settings'));

    $this->addFormElements();
    $this->addFormButtons();

    $this->assign('elementNames', $this->getRenderableElementNames());

    parent::buildQuickForm();
  }

  public function postProcess() {
    $vals = $this->controller->exportValues($this->_name);

    Civi::settings()->set(CRM_EasyAccountCreator_Config::SETTING_EMAIL_FROM_NAME, $vals['email_from_name']);
    Civi::settings()->set(CRM_EasyAccountCreator_Config::SETTING_EMAIL_FROM_ADDRESS, $vals['email_from_address']);

    CRM_Core_Session::setStatus('', E::ts( 'Settings saved.'), 'success');

    parent::postProcess();
  }

  public function setDefaultValues() {
    $defaultValues = [
      'email_from_name' => Civi::settings()->get(CRM_EasyAccountCreator_Config::SETTING_EMAIL_FROM_NAME),
      'email_from_address' => Civi::settings()->get(CRM_EasyAccountCreator_Config::SETTING_EMAIL_FROM_ADDRESS),
    ];

    $this->setDefaults($defaultValues);
  }

  private function addFormElements() {
    $this->add('text', 'email_from_name', E::ts('Email from name'), [], TRUE);
    $this->add('text', 'email_from_address', E::ts('Email from address'), [], TRUE);

    $msgTemplate = CRM_EasyAccountCreator_Config::getMsgTemplate();
    $this->assign('msg_template_label', E::ts('Message template'));
    $this->assign('msg_template_id', $msgTemplate['id']);
    $this->assign('msg_template_subject', $msgTemplate['msg_subject']);
  }

  private function addFormButtons() {
    $this->addButtons([
      [
        'type' => 'submit',
        'name' => E::ts('Save'),
        'isDefault' => TRUE,
      ],
      [
        'type' => 'cancel',
        'name' => E::ts('Cancel'),
      ],
    ]);
  }

  private function getMessageTemplates() {
    $messageTemplateList = [];

    $messageTemplates = \Civi\Api4\MessageTemplate::get(FALSE)
      ->addSelect('id', 'msg_title')
      ->addWhere('is_active', '=', TRUE)
      ->execute();
    foreach ($messageTemplates as $messageTemplate) {
      $messageTemplateList[$messageTemplate['id']] = $messageTemplate['msg_title'];
    }

    return $messageTemplateList;
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
