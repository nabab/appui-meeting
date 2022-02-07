<?php
$userCfg = $model->inc->user->getClassCfg();
$prefCfg = $model->inc->pref->getClassCfg();
return \bbn\X::mergeArrays([
  'usersCfg' => $userCfg['arch']['users'],
  'groupsCfg' => $userCfg['arch']['groups'],
  'prefCfg' => $prefCfg['arch']['user_options']
], $model->getModel(APPUI_MEETING_ROOT . 'data/rooms'));