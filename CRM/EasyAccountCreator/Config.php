<?php

class CRM_EasyAccountCreator_Config {
  public const SETTING_EMAIL_FROM_NAME = 'easy_account_creator_email_from_name';
  public const SETTING_EMAIL_FROM_ADDRESS = 'easy_account_creator_email_from_address';

  public static function init() {
    self::getMsgTemplate();
  }

  public static function getConfirmationScreenUrl() {
    return 'civicrm/easy-account-creator/confirm';
  }

  public static function getOptionGroupForWorkflowMessage() {
    try {
      return Civi\Api4\OptionGroup::get(FALSE)
        ->addWhere('name', '=', 'msg_tpl_workflow_welcome')
        ->execute()
        ->single();
    }
    catch (Exception $e) {
      $optionGroup = Civi\Api4\OptionGroup::create(FALSE)
        ->addValue('name', 'msg_tpl_workflow_welcome')
        ->addValue('is_active', 1)
        ->addValue('title', 'Account Creation Welcome')
        ->execute();

      return $optionGroup[0];
    }
  }

  public static function getOptionValueForWorkflowMessage() {
    try {
      return Civi\Api4\OptionValue::get(FALSE)
        ->addWhere('option_group_id', '=', self::getOptionGroupForWorkflowMessage()['id'])
        ->execute()
        ->single();
    }
    catch (Exception $e) {
      $optionGroup = Civi\Api4\OptionValue::create(FALSE)
        ->addValue('option_group_id', self::getOptionGroupForWorkflowMessage()['id'])
        ->addValue('is_active', 1)
        ->addValue('name', 'account_creation_welcome')
        ->addValue('label', 'Account Creation Welcome')
        ->execute();

      return $optionGroup[0];
    }
  }

  public static function getMsgTemplate() {
    try {
      return Civi\Api4\MessageTemplate::get(FALSE)
        ->addWhere('workflow_id', '=',  self::getOptionValueForWorkflowMessage()['id'])
        ->execute()
        ->single();
    }
    catch (Exception $e) {
      $workflow = self::getOptionValueForWorkflowMessage();
      $optionGroup = Civi\Api4\MessageTemplate::create(FALSE)
        ->addValue('workflow_id', $workflow['id'])
        ->addValue('workflow_name', $workflow['name'])
        ->addValue('is_active', 1)
        ->addValue('msg_title', 'account_creation_welcome')
        ->addValue('msg_subject', 'Account Creation Welcome')
        ->addValue('msg_text', 'Account Creation Welcome')
        ->addValue('msg_html', '<p>Account Creation Welcome</p>')
        ->execute();

      return $optionGroup[0];
    }
  }
}
