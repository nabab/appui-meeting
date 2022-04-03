<?php
if (!empty($model->data['data']['server'])
  && !empty($model->data['data']['type'])
) {
  $meeting = new \bbn\Appui\Meeting($model->db);
  $meetingCfg = $meeting->getClassCfg();
  $meetingTable = $meetingCfg['tables']['meetings'];
  $meetingFields = $meetingCfg['arch']['meetings'];
  $prefCfg = $model->inc->pref->getClassCfg();
  $prefTable = $prefCfg['table'];
  $prefFields = $prefCfg['arch']['user_options'];
  $filters = [
    'conditions' => [[
      'field' => $model->db->cfn($prefFields['id_option'], $prefTable),
      'value' => $model->data['data']['server']
    ], [
      'field' => $model->db->cfn($prefFields['id_alias'], $prefTable),
      'operator' => 'isnull'
    ]]
  ];
  switch ($model->data['data']['type']) {
    case 'public':
      $filters['conditions'][] = [
        'field' => $model->db->cfn($prefFields['public'], $prefTable),
        'value' => 1
      ];
      break;
    case 'users':
      $filters['conditions'][] = [
        'field' => $model->db->cfn($prefFields['id_user'], $prefTable),
        'operator' => 'isnotnull'
      ];
      break;
    case 'groups':
      $filters['conditions'][] = [
        'field' => $model->db->cfn($prefFields['id_group'], $prefTable),
        'operator' => 'isnotnull'
      ];
      break;
  }
  $s = $model->db->cfn($meetingFields['started'], $meetingTable);
  $e = $model->db->cfn($meetingFields['ended'], $meetingTable);
  $grid = new \bbn\Appui\Grid($model->db, $model->data, [
    'table' => $prefTable,
    'fields' => [
      $model->db->cfn($prefFields['id'], $prefTable),
      $model->db->cfn($prefFields['text'], $prefTable),
      $model->db->cfn($prefFields['public'], $prefTable),
      $model->db->cfn($prefFields['id_user'], $prefTable),
      $model->db->cfn($prefFields['id_group'], $prefTable),
      'created' => 'JSON_UNQUOTE(JSON_EXTRACT(' . $model->db->cfn($prefFields['cfg'], $prefTable) . ', "$.created"))',
      'last_use' => 'MAX(' . $s . ')',
      'last_duration' => 'SEC_TO_TIME(TIMESTAMPDIFF(SECOND,' . $s . ', ' . $e . '))',
    ],
    'join' => [[
      'table' => $meetingTable,
      'type' => 'left',
      'on' => [
        'conditions' => [[
          'field' => $model->db->cfn($prefFields['id'], $prefTable),
          'exp' => $model->db->cfn($meetingFields['id_room'], $meetingTable)
        ]]
      ]
    ]],
    'filters' => $filters,
    'group_by' => [$model->db->cfn($prefFields['id'], $prefTable)],
    'order' => [[
      'field' => $model->db->cfn($prefFields['text'], $prefTable),
      'dir' => 'ASC'
    ]]
  ]);
  $data = $grid->getDataTable();
  if (!empty($data['data'])) {
    foreach ($data['data'] as $i => $d) {
      $data['data'][$i]['moderators'] = $meeting->getModerators($d[$prefFields['id']]);
    }
  }
  return $data;
}
