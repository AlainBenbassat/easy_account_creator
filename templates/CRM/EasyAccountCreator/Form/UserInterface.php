<?php

interface CRM_EasyAccountCreator_UserInterface {
  public function exists($loginName);
  public function create();
}
