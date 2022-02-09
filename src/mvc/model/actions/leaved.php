<?php
if ($model->hasData(['idRoom', 'idUser', 'idTmp'], true)) {
  $meeting = new \bbn\Appui\Meeting($model->db);
  if ($idMeeting = $meeting->getStartedMeeting($model->data['idRoom'])) {
    return [
      'success' => $meeting->setLeaved(
        $idMeeting,
        $model->data['idTmp'],
        $model->data['idUser']
      )
    ];
  }
}
return ['success' => false];
