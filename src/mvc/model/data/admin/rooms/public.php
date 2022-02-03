<?php
if (!empty($model->data['data']['server'])) {
  $meeting = new \bbn\Appui\Meeting($model->db);
  $prefCfg = $model->inc->pref->getClassCfg();
  $prefFields = $prefCfg['arch']['user_options'];
  $grid = new \bbn\Appui\Grid($model->db, $model->data, [
    'table' => $prefCfg['table'],
    'fields' => [
      $prefFields['id'],
      $prefFields['text'],
      $prefFields['public'],
      $prefFields['id_user'],
      $prefFields['id_group'],
      'created' => 'JSON_UNQUOTE(JSON_EXTRACT('.$prefFields['cfg'].', "$.created"))',
      'last_use' => 'JSON_UNQUOTE(JSON_EXTRACT('.$prefFields['cfg'].', "$.last_use"))',
      'last_duration' => 'JSON_UNQUOTE(JSON_EXTRACT('.$prefFields['cfg'].', "$.last_duration"))',
    ],
    'filters' => [
      'conditions' => [[
        'field' => $prefFields['id_option'],
        'value' => $model->data['data']['server']
      ], [
        'field' => $prefFields['public'],
        'value' => 1
      ]]
    ],
    'order' => [[
      'field' => $prefFields['text'],
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