<?php
if ($model->hasData('idRoom', true)) {
  $meeting =new \bbn\Appui\Meeting($model->db);
  if ($idMeeting = $meeting->getStartedMeeting($model->data['idRoom'])) {
    return [
      'success' => $meeting->stopMeeting($idMeeting)
    ];
  }
}
return ['success' => false];
