<?php

try {
  CRM_Kavaconvertmemberships_Membership::convertPasAfgestudeerd(31299);
}
  catch (Exception $e) {
  echo "\nFile: " . $e->getFile();
  echo "\nLine: " . $e->getLine();
  echo "\nMessage: " . $e->getMessage();
  echo "\n";
  print_r($e->getTraceAsString());
  echo "\n";
}
