<?php
$meeting = new \bbn\Appui\Meeting($model->db);
return [
  'servers' => $model->inc->options->textValueOptions($model->inc->options->fromCode('list', 'meeting', 'appui')),
  'rooms' => $meeting->getAllRooms($model->inc->user->getId(), $model->inc->user->getIdGroup())
];
