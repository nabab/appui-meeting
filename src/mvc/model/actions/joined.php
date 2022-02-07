<?php
if ($model->hasData(['idUser', 'idRoom'], true)) {
  $meeting = new \bbn\Appui\Meeting($model->db);
  return [
    'success' => $meeting->setJoined($model->data['idUser'], $model->data['idRoom'])
  ];
}
return ['success' => false];
