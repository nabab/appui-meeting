<?php
$userCfg = $model->inc->user->getClassCfg();
$prefCfg = $model->inc->pref->getClassCfg();
$meeting = new \bbn\Appui\Meeting($model->db);
$meetingCfg = $meeting->getClassCfg();
return \bbn\X::mergeArrays([
  'idMeeting' => $model->data['idMeeting'] ?? false,
  'idRoom' => $model->data['idRoom'] ?? false,
  'usersCfg' => $userCfg['arch']['users'],
  'groupsCfg' => $userCfg['arch']['groups'],
  'prefCfg' => $prefCfg['arch']['user_options'],
  'meetingCfg' => $meetingCfg['arch']['meetings'],
  'participantsCfg' => $meetingCfg['arch']['participants']
], $model->getModel(APPUI_MEETING_ROOT . 'data/rooms'));