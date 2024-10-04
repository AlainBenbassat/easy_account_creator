<?php

require_once 'easy_account_creator.civix.php';
// phpcs:disable
use CRM_EasyAccountCreator_ExtensionUtil as E;
// phpcs:enable


function easy_account_creator_civicrm_summaryActions(&$actions, $contactID) {
  if (!empty($actions['otherActions']['user-add'])) {
    $originalLink = 'civicrm/contact/view/useradd';
    $newLink = CRM_EasyAccountCreator_Config::getConfirmationScreenUrl();

    $actions['otherActions']['user-add']['href'] = str_replace($originalLink, $newLink, $actions['otherActions']['user-add']['href']);
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function easy_account_creator_civicrm_config(&$config): void {
  _easy_account_creator_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function easy_account_creator_civicrm_install(): void {
  _easy_account_creator_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function easy_account_creator_civicrm_enable(): void {
  _easy_account_creator_civix_civicrm_enable();
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 */
//function easy_account_creator_civicrm_preProcess($formName, &$form): void {
//
//}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
function easy_account_creator_civicrm_navigationMenu(&$menu): void {
  _easy_account_creator_civix_insert_navigation_menu($menu, 'Administer/System Settings', [
    'label' => E::ts('Easy Account Creator Settings'),
    'name' => 'easy_account_creator_settings',
    'url' => 'civicrm/admin/easy-account-creator',
    'permission' => 'administer CiviCRM',
  ]);
  _easy_account_creator_civix_navigationMenu($menu);
}
