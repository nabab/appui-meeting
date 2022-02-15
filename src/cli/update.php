<?php
use \bbn\X;
$servers = $ctrl->inc->options->getCodes('list', 'meeting', 'appui');
$timeout = 300;
if (!empty($servers)) {
  foreach ($servers as $idServer => $codeServer) {
    $started = 0;
    $closed = 0;
    $leaved = 0;
    $joined = 0;
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
              $parts = [];
              if (!empty($m['contents'])) {
                $conn = array_values(array_filter($m['contents'], function($c){
                  return array_key_exists('sctpconnections', $c);
                }));
                $parts = !empty($conn) && !empty($conn[0]['sctpconnections']) ?
                  array_map(function($p){
                    return $p['endpoint'];
                  }, $conn[0]['sctpconnections']) :
                  [];
              }
              $meetings[$m['id']] = [
                'id' => $m['id'],
                'participants' => $parts
              ];
            }
          }
        }

        foreach ($meetings as $id => $meeting) {
          if ($idMeeting = $ctrl->db->selectOne([
            'table' => 'bbn_meetings',
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
                  'table' => 'bbn_meetings_participants',
                  'fields' => ['bbn_meetings_participants.id'],
                  'join' => [[
                    'table' => 'bbn_meetings',
                    'on' => [
                      'conditions' => [[
                        'field' => 'bbn_meetings.id',
                        'exp' => 'bbn_meetings_participants.id_meeting'
                      ]]
                    ]
                  ]],
                  'where' => [
                    'conditions' => [[
                      'field' => 'bbn_meetings.id_tmp',
                      'value' => $id
                    ], [
                      'field' => 'bbn_meetings_participants.id_tmp',
                      'value' => $p
                    ], [
                      'field' => 'bbn_meetings.ended',
                      'operator' => 'isnull'
                    ]]
                  ]
                ])
                && $ctrl->db->insert('bbn_meetings_participants', [
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

        $w = array_map(function($s){
          return [
            'field' => 'id_room',
            'value' => $s
          ];
        }, $serverRooms);

        if ($newStarted = $ctrl->db->rselectAll([
          'table' => 'bbn_meetings',
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
            if ($parts = $ctrl->db->rselectAll('bbn_meetings_participants', [], [
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
                $started += $ctrl->db->update('bbn_meetings', [
                  'id_tmp' => $idMeeting
                ], [
                  'id' => $n['id']
                ]);
              }
            }
          }
        }

        if ($startedToClose = $ctrl->db->rselectAll([
          'table' => 'bbn_meetings',
          'fields' => [],
          'where' => [
            'conditions' => [[
              'field' => 'ended',
              'operator' => 'isnull'
            ], [
              'logic' => 'OR',
              'conditions' => [[
                'field' => 'id_tmp',
                'operator' => 'isnotnull'
              ], [
                'conditions' => [[
                  'field' => 'id_tmp',
                  'operator' => 'isnull'
                ], [
                  'field' => 'DATE_ADD(started, INTERVAL ' . $timeout . ' SECOND)',
                  'operator' => '<',
                  'value' => date('Y-m-d H:i:s')
                ]]
              ]]
            ], [
              'logic' => 'OR',
              'conditions' => $w
            ]]
          ]
        ])) {
          foreach ($startedToClose as $c) {
            if (!array_key_exists($c['id_tmp'], $meetings)) {
              $date = date('Y-m-d H:i:s');
              if ($ctrl->db->update('bbn_meetings', [
                'ended' => $date
              ], [
                'id' => $c['id']
              ])) {
                $closed++;
                $leaved += $ctrl->db->update('bbn_meetings_participants', [
                  'leaved' => $date
                ], [
                  'id_meeting' => $c['id'],
                  'leaved' => null
                ]);
              }
            }
          }
        }

        if ($newStartedToClose = $ctrl->db->rselectAll([
          'table' => 'bbn_meetings',
          'fields' => [
            'bbn_meetings.id',
            'bbn_meetings.id_tmp',
            'participants' => 'COUNT(bbn_meetings_participants.id)'
          ],
          'join' => [[
            'table' => 'bbn_meetings_participants',
            'type' => 'left',
            'on' => [
              'conditions' => [[
                'field' => 'bbn_meetings_participants.id_meeting',
                'exp' => 'bbn_meetings.id'
              ], [
                'field' => 'bbn_meetings_participants.leaved',
                'operator' => 'isnull'
              ]]
            ]
          ]],
          'where' => [
            'conditions' => [[
              'field' => 'bbn_meetings.id_tmp',
              'operator' => 'isnull'
            ], [
              'field' => 'bbn_meetings.ended',
              'operator' => 'isnull'
            ], [
              'logic' => 'OR',
              'conditions' => $w
            ]]
          ],
          'group_by' => ['bbn_meetings.id']
        ])) {
          foreach ($newStartedToClose as $c) {
            if (empty($c['participants'])) {
              $closed += $ctrl->db->update('bbn_meetings', [
                'ended' => date('Y-m-d H:i:s')
              ], [
                'id' => $c['id']
              ]);
            }
          }
        }

        if ($started || $closed || $joined || $leaved) {
          echo '--' . $codeServer . '--' . PHP_EOL;
          echo _('Started') . ": $started" . PHP_EOL;
          echo _('Ended') . ": $closed" . PHP_EOL;
          echo _('Joined') . ": $joined" . PHP_EOL;
          echo _('Leaved') . ": $leaved" . PHP_EOL . PHP_EOL;
        }
      }
    }
  }
}