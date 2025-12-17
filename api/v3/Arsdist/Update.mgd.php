<?php

// This file declares a managed database record of type "Job".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
return [
  [
    'name' => 'Cron:Arsdist.Update',
    'entity' => 'Job',
    'params' => [
      'version' => 3,
      'name' => 'Call Arsdist.Update API',
      'description' => 'Call Arsdist.Update API',
      'run_frequency' => 'Always',
      'api_entity' => 'Arsdist',
      'api_action' => 'Update',
      'parameters' => 'runInNonProductionEnvironment = 1',
      'is_active' => 1, // HERE?
    ],
  ],
];