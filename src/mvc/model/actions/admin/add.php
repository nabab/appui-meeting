<?php
if ($model->hasData(['server', 'text'], true)) {
  $meeting = new \bbn\Appui\Meeting($model->db);
  if ($idRoom = $meeting->addRoom(
    $model->data['server'],
    $model->data['text'],
    !empty($model->data['id_user']) ? $model->data['id_user'] : null,
    !empty($model->data['id_group']) ? $model->data['id_group'] : null,
  )) {
    if (!empty($model->data['moderators'])) {
      foreach ($model->data['moderators'] as $m) {
        $meeting->addModerator($m, $idRoom);
      }
    }
    if (!empty($model->data['id_user'])) {
      $meeting->addModerator($model->data['id_user'], $idRoom);
    }
    return ['success' => true];
  }
}
return ['success' => false];
