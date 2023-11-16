<?php

class CRM_Kavaconvertmemberships_MembershipType {
  public const WERKENDE_LEDEN = 17;
  public const MEEWERKEND_1_JR_AFGEST = 18;
  public const CORRESPONDERENDE_LEDEN = 19;

  private $cache = [];

  public static function rename($id, $newName) {
    $results = \Civi\Api4\MembershipType::update(FALSE)
      ->addValue('name', $newName)
      ->addValue('description', $newName)
      ->addWhere('id', '=', $id)
      ->execute();
  }

  public static function createIfNotExists($name) {
    if (!self::existsMembershipType($name)) {
      self::createMembershipType($name);
    }
  }

  public static function get($name) {
    if (empty($cache[$name])) {
      $id = \Civi\Api4\MembershipType::get(FALSE)
        ->addSelect('id')
        ->addWhere('name', '=', $name)
        ->execute()
        ->single()['id'];

      $cache[$name] = $id;
    }

    return $cache[$name];
  }

  private static function existsMembershipType($name) {
    $membershipStatuses = \Civi\Api4\MembershipType::get(FALSE)
      ->addSelect('id')
      ->addWhere('name', '=', $name)
      ->execute()
      ->first();

    return $membershipStatuses ? TRUE : FALSE;
  }

  public static function createMembershipType($name, $relationships = []) {
    $membershipType = \Civi\Api4\MembershipType::create(FALSE)
      ->addValue('name', $name)
      ->addValue('description', $name)
      ->addValue('member_of_contact_id', 6057)
      ->addValue('financial_type_id', 2)
      ->addValue('minimum_fee', 0)
      ->addValue('duration_unit', 'year')
      ->addValue('duration_interval', 1)
      ->addValue('period_type', 'fixed')
      ->addValue('fixed_period_start_day', 101)
      ->addValue('fixed_period_rollover_day', 1231);

    if ($relationships) {
      $ids = [];
      $directions = [];

      foreach ($relationships as $relationshipId => $relationshipDirection) {
        $ids[] = $relationshipId;
        $directions[] = $relationshipDirection;
      }

      $membershipType->addValue('relationship_type_id', $ids);
      $membershipType->addValue('relationship_direction', $directions);
    }

    $membershipType->execute();
  }
}
