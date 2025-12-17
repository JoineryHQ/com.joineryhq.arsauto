<?php
use CRM_Arsauto_ExtensionUtil as E;

/**
 * Arsauto.Update API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_arsauto_Update_spec(&$spec) {
}

/**
 * Arsauto.Update API
 *
 * @param array $params
 *
 * @return array
 *   API result descriptor
 *
 * @see civicrm_api3_create_success
 *
 * @throws CRM_Core_Exception
 */
function civicrm_api3_arsauto_Update($params) {
  $limit = 5000;

  $customFieldAttributes = CRM_Arsauto_Utils::getDistCustomFieldAttributes('ARS_Contact_Attributes_Calculated_', 'Region_District');

  $sqlParams = [
    '1' => [$customFieldAttributes['tableName'], 'MysqlColumnNameOrAlias'],
    '2' => [$customFieldAttributes['columnName'], 'MysqlColumnNameOrAlias'],
    '3' => [$limit, 'Integer'],
  ];
  $sqlPattern = "
    CREATE TEMPORARY TABLE tmp_valid_contact (
      id INT UNSIGNED NOT NULL PRIMARY KEY
    ) ENGINE=MEMORY
    AS
    SELECT distinct c.id FROM civicrm_contact c
    INNER join civicrm_address a on a.contact_id = c.id and a.is_primary
    LEFT JOIN %1 t ON t.entity_id = c.id
    WHERE t.entity_id IS NULL
    LIMIT %3
  ";
  CRM_Core_DAO::executeQuery($sqlPattern, $sqlParams);

  $sqlPattern = "
    INSERT INTO %1 (entity_id, %2)
      SELECT contact_id, district_code
      FROM
      civicrm_address a
        INNER JOIN tmp_valid_contact t ON
          t.id = a.contact_id
        LEFT JOIN civicrm_arsauto_lookup al ON
          al.state_province_id = a.state_province_id
          and al.postal_code in ('*', LEFT(a.postal_code, 5))
      WHERE a.is_primary
      GROUP BY a.contact_id
  ";
  $dao = CRM_Core_DAO::executeQuery($sqlPattern, $sqlParams);
  $affectedRows = $dao->affectedRows();

  $returnValues = ['Row count' => $affectedRows];
  return civicrm_api3_create_success($returnValues, $params, 'Arsauto', 'Update');
}
