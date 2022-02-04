<?php
$userCfg = $model->inc->user->getClassCfg();
$prefCfg = $model->inc->pref->getClassCfg();
return [
  'servers' => $model->inc->options->textValueOptions($model->inc->options->fromCode('list', 'meeting', 'appui')),
  'usersCfg' => $userCfg['arch']['users'],
  'groupsCfg' => $userCfg['arch']['groups'],
  'prefCfg' => $prefCfg['arch']['user_options']
];
