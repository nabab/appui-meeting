<?php
if ($model->hasData('idMeeting', true)) {
  $meeting = new \bbn\Appui\Meeting($model->db);
  $meetingFields = $meeting->getClassCfg()['arch']['meetings'];
  $prefFields = $model->inc->pref->getClassCfg()['arch']['user_options'];
  if (($m = $meeting->getMeeting($model->data['idMeeting']))
    && ($r = $meeting->getRoom($m[$meetingFields['id_room']]))
  ) {
    $logs = $meeting->getParticipantsLogs($model->data['idMeeting']);
    \array_unshift($logs, [
      'moment' => $m[$meetingFields['started']],
      'text' => sprintf(_('Meeting starts on "%s" room'), $r[$prefFields['text']])
    ]);
    if (!empty($m[$meetingFields['ended']])) {
      $logs[] = [
        'moment' => $m[$meetingFields['ended']],
        'text' => _('End of meeting')
      ];
    }
    $logs = array_map(function($log){
      $log['moment'] = date('d/m/Y H:i:s', strtotime($log['moment']));
      return $log;
    }, $logs);
    $file = $model->userTmpPath($model->inc->user->getId(), 'appui-meeting') .
      '/logs/' . $r[$prefFields['text']] . '_' . date('d-m-Y_H_i', strtotime($m[$meetingFields['started']])) .
      '__' . date('d-m-Y_H_i_s') . '.xlsx';
    if (\bbn\X::toExcel($logs, $file, true, [
      'fields' => [[
        'title' => _('DATE AND TIME'),
        'type' => 'datetime'
      ], [
        'title' => _('ACTION'),
        'type' => 'string'
      ]]
    ])) {
      return [
        'success' => true,
        'file' => $file
      ];
    }
  }
}
return [
  'success' => false
];
