<?php
$meeting = new \bbn\Appui\Meeting($model->db);
$opt =& $model->inc->options;
$idUser = $model->inc->user->getId();
$idGroup = $model->inc->user->getIdGroup();
return [[
  'id' => 'appui-meeting-0',
  'frequency' => 5,
  'function' => function(array $data) use($meeting, $opt, $idUser, $idGroup){
    $res = [
      'success' => true,
      'data' => []
    ];
    if (isset($data['data'])) {
      $servers = $opt->textValueOptions($opt->fromCode('list', 'meeting', 'appui'));
      $serversHash = md5(json_encode($servers));
      if (isset($data['data']['serversHash'])
        && ($serversHash !== $data['data']['serversHash'])
      ) {
        $res['data'] = [
          'servers' => $servers,
          'serviceWorkers' => [
            'serversHash' => $serversHash
          ]
        ];
      }
      $rooms = $meeting->getAllRooms($idUser, $idGroup);
      $roomsHash = md5(json_encode($rooms));
      if (isset($data['data']['roomsHash'])
        && ($roomsHash !== $data['data']['roomsHash'])
      ) {
        $res['data']['rooms'] = $rooms;
        if (!isset($res['data']['serviceWorkers'])) {
          $res['data']['serviceWorkers'] = [];
        }
        $res['data']['serviceWorkers']['roomsHash'] = $roomsHash;
      }
    }
    return $res;
  }
]];