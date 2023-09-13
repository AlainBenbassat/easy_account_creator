<?php

class CRM_EasyAccountCreator_Drupal7User implements CRM_EasyAccountCreator_UserInterface {
  public function exists($loginName) {
    return TRUE;
  }

  public function create() {
    // zie Utils/System/Drupal en Utils/System/Drupal8.php
  }
}
