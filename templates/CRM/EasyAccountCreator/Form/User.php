<?php

abstract class CRM_EasyAccountCreator_User {
  private const MIN_PWD_LENGTH = 32;
  private const MAX_PWD_LENGTH = 48;

  abstract public function exists($name, $email);
  abstract public function create();

  public function getRandomPassword() {
    $characters = '!@#$%*(){};:.,/0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';

    $n = rand(self::MIN_PWD_LENGTH, self::MAX_PWD_LENGTH);
    for ($i = 0; $i < $n; $i++) {
      $index = rand(0, strlen($characters) - 1);
      $randomString .= $characters[$index];
    }

    return $randomString;
  }
}
