<?php
if ($model->hasData(['idRoom', 'idUser'], true)) {
  $meeting = new \bbn\Appui\Meeting($model->db);
  if ($meeting->checkModeratorToken($model->data['idUser'], $model->data['idRoom'])
    && ($token = $meeting->getModeratorToken($model->data['idUser'], $model->data['idRoom']))
  ) {
    return [
      'success' => true,
      'token' => $token
    ];
  }
}
return ['success' => false];
