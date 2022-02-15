<?php
$meet = false;
if ($ctrl->hasArguments() && \bbn\Str::isUid($ctrl->arguments[0])) {
  $meeting = new \bbn\Appui\Meeting($ctrl->db);
  $meetingCfg = $meeting->getClassCfg();
  $meetingFields = $meetingCfg['arch']['meetings'];
  if ($meeting->isMeeting($ctrl->arguments[0])
    && ($meet = $meeting->getMeeting($ctrl->arguments[0]))
  ) {
    $ctrl->addData([
      'idMeeting' => $meet[$meetingFields['id']],
      'idRoom' => $meet[$meetingFields['id_room']]
    ]);
  }
}
$ctrl
  ->setIcon('nf nf-fa-video_camera')
  ->setUrl(APPUI_MEETING_ROOT . 'list')
  ->combo(_('Meeting'), true);
