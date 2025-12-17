<?php
use CRM_Arsauto_ExtensionUtil as E;

class CRM_Arsauto_Utils {

  /**
   * For a given custom-group/custom-field pairing (by name), get the actual
   * db table name and column name.
   *
   * @param String $customGroupName
   * @param String $customFieldName
   * @return Array ['columnName' => 'example_column_name', 'tableName' => 'example_table_name']
   */
  public static function getDistCustomFieldAttributes($customGroupName, $customFieldName) {
    \Civi::$statics[__METHOD__] = [];
    $staticKey = "{$customGroupName}|{$customFieldName}";
    if (empty(\Civi::$statics[__METHOD__][$staticKey])) {
      $customField = \Civi\Api4\CustomField::get()
        ->setCheckPermissions(FALSE)
        ->addWhere('custom_group_id:name', '=', $customGroupName)
        ->addWhere('name', '=', $customFieldName)
        ->setLimit(1)
        ->addChain('custom_group', \Civi\Api4\CustomGroup::get()
          ->setCheckPermissions(FALSE)
          ->setUseCache(TRUE)
          ->addWhere('id', '=', '$custom_group_id'),
        0)
        ->execute()
        ->first();
      $columnName = $customField['column_name'] ?? NULL;
      $tableName = $customField['custom_group']['table_name'] ?? NULL;
      if (!empty($tableName) && !empty($columnName)) {
        \Civi::$statics[__METHOD__][$staticKey] = [
          'columnName' => $columnName,
          'tableName' => $tableName,
        ];
      }
    }
    return \Civi::$statics[__METHOD__][$staticKey] ?? [];
  }

}
