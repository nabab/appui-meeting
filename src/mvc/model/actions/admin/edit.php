<?php
$prefCfg = $model->inc->pref->getClassCfg();
$prefFields = $prefCfg['arch']['user_options'];
if ($model->hasData([$prefFields['id'], $prefFields['text'], 'type'], true)) {
  $meeting = new \bbn\Appui\Meeting($model->db);
  if ($model->data['type'] === 'users') {
    if (empty($model->data[$prefFields['id_user']])) {
      return ['success' => false];
    }
    $model->data['moderators'] = [$model->data[$prefFields['id_user']]];
  }
  return [
    'success' => $meeting->editRoom(
      $model->data[$prefFields['id']],
      $model->data[$prefFields['text']],
      $model->data[$prefFields['id_user']],
      $model->data[$prefFields['id_group']],
      $model->data['moderators']
    )
  ];
}
return ['success' => false];
