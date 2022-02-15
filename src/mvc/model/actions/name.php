<?php
if ($model->hasData(['idMeeting', 'idTmp', 'name'], true)) {
  $meeting = new \bbn\Appui\Meeting($model->db);
  return [
    'success' => $meeting->setParticipantName($model->data['idMeeting'], $model->data['idTmp'], $model->data['name'])
  ];
}
return ['success' => false];
