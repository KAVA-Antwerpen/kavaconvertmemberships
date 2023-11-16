<?php

class CRM_Kavaconvertmemberships_MembershipStatus {
  private $cache = [];

  public static function createIfNotExists($label) {
    if (!self::existsMembershipStatus($label)) {
      self::createMembershipStatus($label);
    }
  }

  public static function get($label) {
    if (empty($cache[$label])) {
      $id = \Civi\Api4\MembershipStatus::get(FALSE)
        ->addSelect('id')
        ->addWhere('label', '=', $label)
        ->execute()
        ->single()['id'];

      $cache[$label] = $id;
    }

    return $cache[$label];
  }

  private static function existsMembershipStatus($label) {
    $membershipStatuses = \Civi\Api4\MembershipStatus::get(FALSE)
      ->addSelect('id')
      ->addWhere('label', '=', $label)
      ->execute()
      ->first();

    return $membershipStatuses ? TRUE : FALSE;
  }

  private static function createMembershipStatus($label) {
    \Civi\Api4\MembershipStatus::create(FALSE)
      ->addValue('label', $label)
      ->addValue('name', str_replace(' ', '_', $label))
      ->addValue('start_event', 'start_date')
      ->addValue('end_event', 'end_date')
      ->addValue('is_current_member', 1)
      ->addValue('is_active', 1)
      ->execute();
  }
}
