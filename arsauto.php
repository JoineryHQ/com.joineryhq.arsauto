<?php

require_once 'arsauto.civix.php';

use CRM_Arsauto_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function arsauto_civicrm_config(&$config): void {
  _arsauto_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function arsauto_civicrm_install(): void {
  _arsauto_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function arsauto_civicrm_enable(): void {
  _arsauto_civix_civicrm_enable();
}

function arsauto_civicrm_triggerInfo(&$info, $tableName) {
  $customFieldAttributes = CRM_Arsauto_Utils::getDistCustomFieldAttributes('ARS_Contact_Attributes_Calculated_', 'Region_District');

  if (empty($customFieldAttributes['tableName']) || empty($customFieldAttributes['columnName'])) {
    // No such custom field found; do nothing and return.
    return;
  }

  $sourceTable = 'civicrm_address';

  $sqlParams = [
    '1' => [$customFieldAttributes['tableName'], 'MysqlColumnNameOrAlias'],
    '2' => [$customFieldAttributes['columnName'], 'MysqlColumnNameOrAlias'],
    '3' => [$sourceTable, 'MysqlColumnNameOrAlias'],
  ];
  $sqlPattern = "
    REPLACE INTO %1 (entity_id, %2)
    SELECT * FROM (
      SELECT contact_id, district_code
      FROM
      %3 a
        LEFT JOIN civicrm_arsauto_lookup al ON
          al.state_province_id = a.state_province_id
          and al.postal_code in ('*', LEFT(a.postal_code, 5))
      WHERE a.contact_id = NEW.contact_id
        and a.is_primary
    ) as regionlist;
  ";
  $sql = CRM_Core_DAO::composeQuery($sqlPattern, $sqlParams);

  $info[] = array(
    'table' => $sourceTable,
    'when' => 'AFTER',
    'event' => 'INSERT',
    'sql' => $sql,
  );
  $info[] = array(
    'table' => $sourceTable,
    'when' => 'AFTER',
    'event' => 'UPDATE',
    'sql' => $sql,
  );
  // For delete, we reference OLD.contact_id instead of NEW.contact_id
  $sql = str_replace('NEW.contact_id', 'OLD.contact_id', $sql);
  $info[] = array(
    'table' => $sourceTable,
    'when' => 'AFTER',
    'event' => 'DELETE',
    'sql' => $sql,
  );
}
