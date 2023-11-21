<?php

class CRM_Kavaconvertmemberships_Membership {

  public static function convertPasAfgestudeerd($contactId = NULL) {
    $currentYear = date('Y');

    // afgestudeerden op basis van relatie ophalen
    $relationships = \Civi\Api4\Relationship::get(FALSE)
      ->addWhere('relationship_type_id', '=', CRM_Kavaconvertmemberships_RelationshipType::IS_AFGESTUDEERD_LID)
      ->addWhere('start_date', '>=', "$currentYear-01-01")
      ->addWhere('end_date', '<=', "$currentYear-12-31")
      ->addWhere('is_active', '=', 1);

    if ($contactId) {
      $relationships = $relationships->addWhere('contact_id_a', '=', $contactId);
    }

    $relationships = $relationships->execute();

    foreach ($relationships as $relationship) {
      // verwijder klaargezette lidm. 1 jaar afgestudeerd
      \Civi\Api4\Membership::delete(FALSE)
        ->addWhere('contact_id', '=', $relationship['contact_id_a'])
        ->addWhere('membership_type_id', '=', CRM_Kavaconvertmemberships_MembershipType::MEEWERKEND_1_JR_AFGEST)
        ->execute();

      // kijk of er een werkend lidm. is
      $membership = \Civi\Api4\Membership::get(FALSE)
        ->addWhere('membership_type_id', '=', CRM_Kavaconvertmemberships_MembershipType::WERKENDE_LEDEN)
        ->addWhere('contact_id', '=', $relationship['contact_id_a'])
        ->execute()
        ->first();
      if ($membership) {
        // pas de status aan en datums aan
        \Civi\Api4\Membership::update(FALSE)
          ->addValue('status_id', CRM_Kavaconvertmemberships_MembershipStatus::get('Pas afgestudeerd'))
          ->addValue('join_date', $relationship['start_date'])
          ->addValue('start_date', $relationship['start_date'])
          ->addValue('is_override', TRUE)
          ->addValue('Facturatie.Gratis_', 1)
          ->addValue('Facturatie.Product', NULL)
          ->addWhere('id', '=', $membership['id'])
          ->execute();
      }
      else {
        // maak een nieuwe aan
        \Civi\Api4\Membership::create(FALSE)
          ->addValue('contact_id', $relationship['contact_id_a'])
          ->addValue('membership_type_id', CRM_Kavaconvertmemberships_MembershipType::WERKENDE_LEDEN)
          ->addValue('join_date', $relationship['start_date'])
          ->addValue('start_date', $relationship['start_date'])
          ->addValue('is_override', TRUE)
          ->addValue('status_id', CRM_Kavaconvertmemberships_MembershipStatus::get('Pas afgestudeerd'))
          ->addValue('end_date', '3000-01-01')
          ->addValue('Facturatie.Gratis_', 1)
          ->execute();
      }
    }
  }

  public static function convert1JaarAfgestudeerd($contactId = NULL) {
    $currentYear = date('Y');

    // afgestudeerden op basis van relatie ophalen
    $relationships = \Civi\Api4\Relationship::get(FALSE)
      ->addWhere('relationship_type_id', '=', CRM_Kavaconvertmemberships_RelationshipType::IS_1_JAAR_AFGESTUDEERD_LID)
      ->addWhere('start_date', '>=', "$currentYear-01-01")
      ->addWhere('end_date', '<=', "$currentYear-12-31")
      ->addWhere('is_active', '=', 1);

    if ($contactId) {
      $relationships = $relationships->addWhere('contact_id_a', '=', $contactId);
    }

    $relationships = $relationships->execute();

    foreach ($relationships as $relationship) {
      // kijk of er een lidm. 1 jaar afgestudeerd is
      $mem1year = \Civi\Api4\Membership::get(FALSE)
        ->addWhere('contact_id', '=', $relationship['contact_id_a'])
        ->addWhere('membership_type_id', '=', CRM_Kavaconvertmemberships_MembershipType::MEEWERKEND_1_JR_AFGEST)
        ->execute()
        ->first();
      if ($mem1year) {
        $betaler = $mem1year['Facturatie.Betaler'];

        \Civi\Api4\Membership::delete(FALSE)
          ->addWhere('id', '=', $mem1year['id'])
          ->execute();
      }
      else {
        $betaler = NULL;
      }

      // kijk of er een werkend lidm. is
      $membership = \Civi\Api4\Membership::get(FALSE)
        ->addWhere('membership_type_id', '=', CRM_Kavaconvertmemberships_MembershipType::WERKENDE_LEDEN)
        ->addWhere('contact_id', '=', $relationship['contact_id_a'])
        ->execute()
        ->first();
      if ($membership) {
        // pas de status aan en datums aan
        \Civi\Api4\Membership::update(FALSE)
          ->addValue('status_id', CRM_Kavaconvertmemberships_MembershipStatus::get('1 jaar afgestudeerd'))
          ->addValue('join_date', $relationship['start_date'])
          ->addValue('start_date', $relationship['start_date'])
          ->addValue('is_override', TRUE)
          ->addValue('Facturatie.Gratis_', 0)
          ->addValue('Facturatie.Betaler', $betaler)
          ->addValue('Facturatie.Product', '&BV500021')
          ->addWhere('id', '=', $membership['id'])
          ->execute();
      }
      else {
        // maak een nieuwe aan
        \Civi\Api4\Membership::create(FALSE)
          ->addValue('contact_id', $relationship['contact_id_a'])
          ->addValue('membership_type_id', CRM_Kavaconvertmemberships_MembershipType::WERKENDE_LEDEN)
          ->addValue('status_id', CRM_Kavaconvertmemberships_MembershipStatus::get('1 jaar afgestudeerd'))
          ->addValue('join_date', $relationship['start_date'])
          ->addValue('start_date', $relationship['start_date'])
          ->addValue('end_date', '3000-01-01')
          ->addValue('is_override', TRUE)
          ->addValue('Facturatie.Gratis_', 0)
          ->addValue('Facturatie.Product', '&BV500021')
          ->execute();
      }
    }
  }

  public static function convertMeewerkendLid($contactId = NULL) {
    $currentYear = (int)date('Y');
    $nextYear = $currentYear + 1;

    // meerwerkende leden (we gaan ervan uit dat convert1JaarAfgestudeerd() al uitgevoerd werd
    $memberships = \Civi\Api4\Membership::get(FALSE)
      ->addWhere('membership_type_id', '=', CRM_Kavaconvertmemberships_MembershipType::MEEWERKEND_1_JR_AFGEST)
      ->addWhere('end_date', '>', "$currentYear-12-31");

    if ($contactId) {
      $memberships = $memberships->addWhere('contact_id', '=', $contactId);
    }

    $memberships = $memberships->execute();

    // eindig op einde van het jaar en creeer nieuw lidm.
    foreach ($memberships as $membership) {
      \Civi\Api4\Membership::update(FALSE)
        ->addValue('end_date', "$currentYear-12-31")
        ->addWhere('id', '=', $membership['id'])
        ->execute();

      \Civi\Api4\Membership::create(FALSE)
        ->addValue('contact_id', $membership['contact_id'])
        ->addValue('membership_type_id', CRM_Kavaconvertmemberships_MembershipType::get('KAVA werkelijk lid'))
        ->addValue('status_id', CRM_Kavaconvertmemberships_MembershipStatus::get('Actief'))
        ->addValue('join_date', $membership['join_date'])
        ->addValue('start_date', "$nextYear-01-01")
        ->addValue('end_date', '3000-01-01')
        ->addValue('Facturatie.Betaler', $membership['Facturatie.Betaler'])
        ->addValue('Facturatie.Gratis_', $membership['Facturatie.Gratis_'])
        ->addValue('Facturatie.Product', $membership['Facturatie.Product'])
        ->execute();
    }
  }
}
