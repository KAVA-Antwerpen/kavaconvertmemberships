<?php

class CRM_Kavaconvertmemberships_Main {

  public static function start() {
    CRM_Kavaconvertmemberships_MembershipStatus::createIfNotExists('Pas afgestudeerd');
    CRM_Kavaconvertmemberships_MembershipStatus::createIfNotExists('1 jaar afgestudeerd');

    CRM_Kavaconvertmemberships_MembershipType::rename(CRM_Kavaconvertmemberships_MembershipType::WERKENDE_LEDEN, 'KAVA Werkelijk lid');
    CRM_Kavaconvertmemberships_MembershipType::rename(CRM_Kavaconvertmemberships_MembershipType::TOEGETREDEN_LEDEN, 'KAVA Toegetreden lid');
    CRM_Kavaconvertmemberships_MembershipType::rename(CRM_Kavaconvertmemberships_MembershipType::MEEWERKEND_1_JR_AFGEST, 'KAVA Meewerkend lid');
    CRM_Kavaconvertmemberships_MembershipType::rename(CRM_Kavaconvertmemberships_MembershipType::APOTHEEKTEAMLIDM, 'KAVA Apotheekteamlidmaatschap');
    CRM_Kavaconvertmemberships_MembershipType::rename(CRM_Kavaconvertmemberships_MembershipType::CORRESPONDERENDE_LEDEN, 'KAVA Corresponderend lid');

    CRM_Kavaconvertmemberships_MembershipType::createIfNotExists('KAVA stagiair lid');

    CRM_Kavaconvertmemberships_Membership::convertPasAfgestudeerd();
    CRM_Kavaconvertmemberships_Membership::convert1JaarAfgestudeerd();
    CRM_Kavaconvertmemberships_Membership::convertMeewerkendLid();
    CRM_Kavaconvertmemberships_Membership::convertCorresponderendLid();
    CRM_Kavaconvertmemberships_Membership::convertStagiairs();

    CRM_Core_DAO::executeQuery("update civicrm_value_facturatie_79 set product_261 = '&BV500020' where product_261 = '&BV500006'");
  }
}
