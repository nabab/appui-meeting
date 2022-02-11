<?php
if ($model->hasData(['idRoom', 'idUser', 'idTmp'], true)) {
  $meeting = new \bbn\Appui\Meeting($model->db);
  return [
    'success' => $meeting->setJoined($model->data['idRoom'], $model->data['idTmp'], $model->data['idUser']),
    'idMeeting' => $meeting->getStartedMeeting($model->data['idRoom'])
  ];
}
return ['success' => false];
