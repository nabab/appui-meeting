<?php
if ($model->hasData(['server', 'text'], true)) {
  $meeting = new \bbn\Appui\Meeting($model->db);
  $moderators = [];
  if (!empty($model->data['moderators'])) {
    array_push($moderators, ...$model->data['moderators']);
  }
  if (!empty($model->data['id_user'])) {
    $moderators[] = $model->data['id_user'];
  }
  return [
    'success' => $meeting->addRoom(
      $model->data['server'],
      $model->data['text'],
      !empty($model->data['id_user']) ? $model->data['id_user'] : null,
      !empty($model->data['id_group']) ? $model->data['id_group'] : null,
      $moderators
    )
  ];
}
return ['success' => false];
