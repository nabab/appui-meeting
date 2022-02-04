<?php
$ctrl
  //->setUrl(APPUI_MEETING_ROOT . 'page')
  ->setIcon('nf nf-fa-video_camera')
  ->addData([
    'meetURL' => BBN_MEET_DOMAIN,
    'meetUserToken' => $meetUserToken
  ])
  ->combo(_('MEETING'), true);
  