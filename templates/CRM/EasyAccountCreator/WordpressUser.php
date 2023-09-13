<?php

class CRM_EasyAccountCreator_WordpressUser implements CRM_EasyAccountCreator_UserInterface {
  public function exists($loginName) {
    return TRUE;
  }

  public function create() {
    // Utils/System/WordPress.php - zit hier een user create functie in?
  }
}
