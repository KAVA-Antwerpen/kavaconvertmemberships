<?php

try {
  if (empty($argv[1])) {
    die("Missing argument: contact ID");
  }
  $contactId = $argv[1];

  CRM_Kavaconvertmemberships_MembershipType::createIfNotExists('KAVA stagiair lid');

  CRM_Kavaconvertmemberships_Membership::convertPasAfgestudeerd($contactId);
  CRM_Kavaconvertmemberships_Membership::convert1JaarAfgestudeerd($contactId);
  CRM_Kavaconvertmemberships_Membership::convertMeewerkendLid($contactId);
  CRM_Kavaconvertmemberships_Membership::convertCorresponderendLid($contactId);
  CRM_Kavaconvertmemberships_Membership::convertStagiairs($contactId);
}
catch (Exception $e) {
  echo "\nFile: " . $e->getFile();
  echo "\nLine: " . $e->getLine();
  echo "\nMessage: " . $e->getMessage();
  echo "\n";
  print_r($e->getTraceAsString());
  echo "\n";
}
