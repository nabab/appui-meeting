<?php
$success = false;
if ($model->hasData('id', true)) {
  $meeting = new \bbn\Appui\Meeting($model->db);
  $success = $meeting->removeRoom($model->data['id']);
}
return ['success' => $success];
