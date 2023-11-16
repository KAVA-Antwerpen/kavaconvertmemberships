<?php

class CRM_Kavaconvertmemberships_Main {

  public static function start() {
    CRM_Kavaconvertmemberships_MembershipStatus::createIfNotExists('Pas afgestudeerd');
    CRM_Kavaconvertmemberships_MembershipStatus::createIfNotExists('1 jaar afgestudeerd');

    CRM_Kavaconvertmemberships_MembershipType::rename(CRM_Kavaconvertmemberships_MembershipType::WERKENDE_LEDEN, 'KAVA werkelijk lid');
    CRM_Kavaconvertmemberships_MembershipType::rename(CRM_Kavaconvertmemberships_MembershipType::MEEWERKEND_1_JR_AFGEST, 'KAVA meewerkend lid');
    CRM_Kavaconvertmemberships_MembershipType::rename(CRM_Kavaconvertmemberships_MembershipType::CORRESPONDERENDE_LEDEN, 'KAVA corresponderend lid');

    CRM_Kavaconvertmemberships_MembershipType::createIfNotExists('KAVA toegetreden lid');
    CRM_Kavaconvertmemberships_MembershipType::createIfNotExists('KAVA apotheeklidmaatschap', [
      CRM_Kavaconvertmemberships_RelationshipType::HEEFT_ALS_TITULARIS => 'a_b',
      CRM_Kavaconvertmemberships_RelationshipType::IS_ADJUNCT_APOTHEKER_BIJ => 'b_a',
      CRM_Kavaconvertmemberships_RelationshipType::IS_PLAATSVERVANGEND_APOTHEKER_BIJ => 'b_a',
      CRM_Kavaconvertmemberships_RelationshipType::HEEFT_ALS_CO_TITULARIS => 'a_b',
      CRM_Kavaconvertmemberships_RelationshipType::IS_FARMACEUTISCH_TECHNISCH_ASSISTENT_VAN => 'b_a'
    ]);

    CRM_Kavaconvertmemberships_Membership::convertPasAfgestudeerd();
    CRM_Kavaconvertmemberships_Membership::convert1JaarAfgestudeerd();
    CRM_Kavaconvertmemberships_Membership::convertMeewerkendLid();
  }
}
