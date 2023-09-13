<?php

use CRM_EasyAccountCreator_ExtensionUtil as E;

class CRM_EasyAccountCreator_Form_Settings extends CRM_Core_Form {

  public function buildQuickForm() {
    $this->setTitle(E::ts('Easy User Creator Settings'));

    $this->addFormElements();
    $this->addFormButtons();

    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function postProcess() {
    $vals = $this->controller->exportValues($this->_name);
    Civi::settings()->set('easy_account_creator_message_template_id', $vals['message_template_id']);

    CRM_Core_Session::setStatus('', E::ts( 'Settings saved.'), 'success');

    parent::postProcess();
  }

  public function setDefaultValues() {
    $defaultValues = [
      'message_template_id' => Civi::settings()->get('easy_account_creator_message_template_id'),
    ];

    $this->setDefaults($defaultValues);
  }

  private function addFormElements() {
    $this->add('select', 'message_template_id', E::ts('Message template for welcome mail:'), $this->getMessageTemplates(), TRUE);
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
