<?php
if ($model->hasData('data', true) && !empty($model->data['data']['idRoom'])) {
  $meeting = new \bbn\Appui\Meeting($model->db);
  $meetingCfg = $meeting->getClassCfg();
  $meetingTable = $meetingCfg['tables']['meetings'];
  $meetingFields = $meetingCfg['arch']['meetings'];
  $partsTable = $meetingCfg['tables']['participants'];
  $partsFields = $meetingCfg['arch']['participants'];
  $prefFields = $model->inc->pref->getClassCfg()['arch']['user_options'];
  $grid = new \bbn\Appui\Grid($model->db, $model->data, [
    'table' => $meetingTable,
    'fields' => array_map(function($f) use($model, $meetingTable){
        return $model->db->cfn($f, $meetingTable);
      }, $meetingFields),
    'join' => [[
      'table' => $partsTable,
      'on' => [
        'conditions' => [[
          'field' => $model->db->cfn($partsFields['id_meeting'], $partsTable),
          'exp' => $model->db->cfn($meetingFields['id'], $meetingTable)
        ]]
      ]
    ]],
    'filters' => [
      'conditions' => [[
        'field' => $model->db->cfn($meetingFields['id_room'], $meetingTable),
        'value' => $model->data['data']['idRoom']
      ]]
    ],
    'order' => [[
      'field' => $model->db->cfn($meetingFields['started'], $meetingTable),
      'dir' => 'DESC'
    ]],
    'group_by' => [$meetingFields['id']]
  ]);
  if ($grid->check()) {
    $data = $grid->getDataTable();
    if (!empty($data['data'])) {
      foreach ($data['data'] as $i => $d) {
        $data['data'][$i]['participants'] = $model->db->rselectAll([
          'table' => $partsTable,
          'fields' => [
            $partsFields['id_user'],
            $partsFields['name']
          ],
          'where' => [
            'conditions' => [[
              'field' => $partsFields['id_meeting'],
              'value' => $d[$meetingFields['id']]
            ], [
              'field' => $partsFields['joined'],
              'operator' => 'isnotnull'
            ], [
              'logic' => 'OR',
              'conditions' => [[
                'field' => $partsFields['id_user'],
                'operator' => 'isnotnull'
              ], [
                'field' => $partsFields['name'],
                'operator' => 'isnotnull'
              ]]
            ]]
          ],
          'group_by' => [$partsFields['id_user'], $partsFields['name']]
        ]);
        $logs = [];
        if ($r = $meeting->getRoom($d[$meetingFields['id_room']])) {
          $logs = $meeting->getParticipantsLogs($d[$meetingFields['id']]);
          \array_unshift($logs, [
            'moment' => $d[$meetingFields['started']],
            'text' => sprintf(_('Meeting starts on "%s" room'), $r[$prefFields['text']])
          ]);
          if (!empty($d[$meetingFields['ended']])) {
            $logs[] = [
              'moment' => $d[$meetingFields['ended']],
              'text' => _('End of meeting')
            ];
          }
        }
        $data['data'][$i]['logs'] = $logs;
      }
    }
  }
  return $data;
}
return [];
