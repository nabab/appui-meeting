<?php
if ($model->hasData(['idMeeting', 'users'], true)) {
  $meeting = new \bbn\Appui\Meeting($model->db);
  $invited = 0;
  foreach ($model->data['users'] as $user) {
    if ($meeting->inviteUser($model->data['idMeeting'], $user)) {
      $invited++;
    }
  }
  return [
    'success' => count($model->data['users']) === $invited
  ];
}
return ['success' => false];