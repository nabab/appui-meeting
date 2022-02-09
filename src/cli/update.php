<?php
use \bbn\X;
$servers = $ctrl->inc->options->getCodes('list', 'meeting', 'appui');
if (!empty($servers)) {
  $started = 0;
  $closed = 0;
  $leaved = 0;
  $joined = 0;
  foreach ($servers as $idServer => $codeServer) {
    $serverRooms = $ctrl->db->getColumnValues('bbn_users_options', 'id', [
      'id_option' => $idServer
    ]);
    if (!empty($serverRooms)) {
      $url = 'http://' . $codeServer . ':8080/colibri/conferences';
      if ($tmpMeetings = X::curl($url, null, [])) {
        $tmpMeetings = json_decode($tmpMeetings, true);
        $meetings = [];
        if (!empty($tmpMeetings)) {
          foreach ($tmpMeetings as $meet) {
            if ($m = X::curl($url . '/' . $meet['id'], null, [])) {
              $m = json_decode($m, true);
              $meetings[$m['id']] = [
                'id' => $m['id'],
                'participants' => array_map(function($p){
                  return $p['endpoint'];
                }, $m['contents'][0]['sctpconnections'])
              ];
            }
          }
        }
        $w = array_map(function($s){
          return [
            'field' => 'id_room',
            'value' => $s
          ];
        }, $serverRooms);
        if ($newStarted = $ctrl->db->rselectAll([
          'table' => 'bbn_meeting',
          'fields' => [],
          'where' => [
            'conditions' => [[
              'field' => 'id_tmp',
              'operator' => 'isnull'
            ], [
              'field' => 'ended',
              'operator' => 'isnull'
            ], [
              'logic' => 'OR',
              'conditions' => $w
            ]]
          ]
        ])) {
          foreach ($newStarted as $n) {
            if ($parts = $ctrl->db->rselectAll('bbn_meeting_participants', [], [
              'id_meeting' => $n['id']
            ])) {
              $idMeeting = false;
              foreach ($parts as $part) {
                foreach ($meetings as $i => $m) {
                  if (in_array($part['id_tmp'], $m['participants'], true)) {
                    $idMeeting = $i;
                    break;
                  }
                }
                if (!empty($idMeeting)) {
                  break;
                }
              }
              if (!empty($idMeeting)) {
                $started += $ctrl->db->update('bbn_meeting', [
                  'id_tmp' => $idMeeting
                ], [
                  'id' => $n['id']
                ]);
              }
            }
          }
        }
        if ($startedToClose = $ctrl->db->rselectAll([
          'table' => 'bbn_meeting',
          'fields' => [],
          'where' => [
            'conditions' => [[
              'field' => 'id_tmp',
              'operator' => 'isnotnull'
            ], [
              'field' => 'ended',
              'operator' => 'isnull'
            ], [
              'logic' => 'OR',
              'conditions' => $w
            ]]
          ]
        ])) {
          foreach ($startedToClose as $c) {
            if (!array_key_exists($c['id_tmp'], $meetings)) {
              $date = date('Y-m-d H:i:s');
              if ($ctrl->db->update('bbn_meeting', [
                'ended' => $date
              ], [
                'id' => $c['id']
              ])) {
                $closed++;
                $leaved += $ctrl->db->update('bbn_meeting_participants', [
                  'leaved' => $date
                ], [
                  'id_meeting' => $c['id'],
                  'leaved' => null
                ]);
              }
            }
          }
        }
        foreach ($meetings as $id => $meeting) {
          if ($idMeeting = $ctrl->db->selectOne([
            'table' => 'bbn_meeting',
            'fields' => ['id'],
            'where' => [
              'conditions' => [[
                'field' => 'id_tmp',
                'value' => $id
              ], [
                'field' => 'ended',
                'operator' => 'isnull'
              ]]
            ]
          ])) {
            foreach ($meeting['participants'] as $p) {
              if (
                !$ctrl->db->selectOne([
                  'table' => 'bbn_meeting_participants',
                  'fields' => ['bbn_meeting_participants.id'],
                  'join' => [[
                    'table' => 'bbn_meeting',
                    'on' => [
                      'conditions' => [[
                        'field' => 'bbn_meeting.id',
                        'exp' => 'bbn_meeting_participants.id_meeting'
                      ]]
                    ]
                  ]],
                  'where' => [
                    'conditions' => [[
                      'field' => 'bbn_meeting.id_tmp',
                      'value' => $id
                    ], [
                      'field' => 'bbn_meeting_participants.id_tmp',
                      'value' => $p
                    ], [
                      'field' => 'bbn_meeting.ended',
                      'operator' => 'isnull'
                    ]]
                  ]
                ])
                && $ctrl->db->insert('bbn_meeting_participants', [
                  'id_meeting' => $idMeeting,
                  'id_tmp' => $p,
                  'joined' => date('Y-m-d H:i:s')
                ])
              ) {
                $joined++;
              }
            }
          }
        }
        if ($started || $closed || $joined || $leaved) {
          echo _('Started') . ": $started" . PHP_EOL;
          echo _('Ended') . ": $closed" . PHP_EOL;
          echo _('Joined') . ": $joined" . PHP_EOL;
          echo _('Leaved') . ": $leaved" . PHP_EOL;
        }
      }
    }
  }
}