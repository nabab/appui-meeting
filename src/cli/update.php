<?php
use \bbn\X;
// Get the servers list
$servers = $ctrl->inc->options->getCodes('list', 'meeting', 'appui');
$timeout = 300;
if (!empty($servers)) {
  $meetingCls = new \bbn\Appui\Meeting($ctrl->db);
  $meetingClsCfg = $meetingCls->getClassCfg();
  $meetingTable = $meetingClsCfg['tables']['meetings'];
  $meetingFields = $meetingClsCfg['arch']['meetings'];
  $partsTable = $meetingClsCfg['tables']['participants'];
  $partsFields = $meetingClsCfg['arch']['participants'];
  foreach ($servers as $idServer => $codeServer) {
    $started = 0;
    $closed = 0;
    $leaved = 0;
    $joined = 0;
    // Get the rooms list
    $serverRooms = $ctrl->db->getColumnValues('bbn_users_options', 'id', [
      'id_option' => $idServer
    ]);
    if (!empty($serverRooms)) {
      // Get the meetings list form the server
      $meetings = $meetingCls->getMeetingsFromServer($codeServer);
      foreach ($meetings as $id => $meeting) {
        // Get meeting by tmp id
        if (($m = $meetingCls->getMeetingByTmp($id, [
            'field' => $meetingFields['ended'],
            'operator' => 'isnull'
          ]))
          && ($idMeeting = $m[$meetingFields['id']])
        ) {
          foreach ($meeting['participants'] as $p) {
            // Check and insert the new participants
            if (
              !$ctrl->db->selectOne([
                'table' => $partsTable,
                'fields' => [$ctrl->db->cfn($partsFields['id'], $partsTable)],
                'join' => [[
                  'table' => $meetingTable,
                  'on' => [
                    'conditions' => [[
                      'field' => $ctrl->db->cfn($meetingFields['id'], $meetingTable),
                      'exp' => $ctrl->db->cfn($partsFields['id_meeting'], $partsTable)
                    ]]
                  ]
                ]],
                'where' => [
                  'conditions' => [[
                    'field' => $ctrl->db->cfn($meetingFields['id_tmp'], $meetingTable),
                    'value' => $id
                  ], [
                    'field' => $ctrl->db->cfn($partsFields['id_tmp'], $partsTable),
                    'value' => $p
                  ], [
                    'field' => $ctrl->db->cfn($meetingFields['ended'], $meetingTable),
                    'operator' => 'isnull'
                  ]]
                ]
              ])
              && $ctrl->db->insert($partsTable, [
                $partsFields['id_meeting'] => $idMeeting,
                $partsFields['id_tmp'] => $p,
                $partsFields['joined'] => date('Y-m-d H:i:s')
              ])
            ) {
              $joined++;
            }
          }
        }
      }

      $w = array_map(function($s) use($meetingFields){
        return [
          'field' => $meetingFields['id_room'],
          'value' => $s
        ];
      }, $serverRooms);

      // Get the id_tmp from the server for the new started meetings and set them to the db
      if ($newStarted = $ctrl->db->rselectAll([
        'table' => $meetingTable,
        'fields' => [],
        'where' => [
          'conditions' => [[
            'field' => $meetingFields['id_tmp'],
            'operator' => 'isnull'
          ], [
            'field' => $meetingFields['ended'],
            'operator' => 'isnull'
          ], [
            'logic' => 'OR',
            'conditions' => $w
          ]]
        ]
      ])) {
        foreach ($newStarted as $n) {
          if ($parts = $ctrl->db->rselectAll([
            'table' => $partsTable,
            'fields' => [],
            'where' => [
              'conditions' => [[
                'field' => $partsFields['id_meeting'],
                'value' => $n['id']
              ], [
                'field' => $partsFields['id_tmp'],
                'operator' => 'isnotnull'
              ]]
            ]
          ])) {
            $idMeeting = false;
            foreach ($parts as $part) {
              if (!empty($part[$partsFields['id_tmp']])) {
                foreach ($meetings as $i => $m) {
                  if (in_array($part[$partsFields['id_tmp']], $m['participants'], true)) {
                    $idMeeting = $i;
                    break;
                  }
                }
                if (!empty($idMeeting)) {
                  break;
                }
              }
            }
            if (!empty($idMeeting)) {
              $started += $ctrl->db->update($meetingTable, [
                $meetingFields['id_tmp'] => $idMeeting
              ], [
                $meetingFields['id'] => $n['id']
              ]);
            }
          }
        }
      }

      // Check and set as close the ended meetings
      if ($startedToClose = $ctrl->db->rselectAll([
        'table' => $meetingTable,
        'fields' => [],
        'where' => [
          'conditions' => [[
            'field' => $meetingFields['ended'],
            'operator' => 'isnull'
          ], [
            'logic' => 'OR',
            'conditions' => [[
              'field' => $meetingFields['id_tmp'],
              'operator' => 'isnotnull'
            ], [
              'conditions' => [[
                'field' => $meetingFields['id_tmp'],
                'operator' => 'isnull'
              ], [
                'field' => 'DATE_ADD(' . $meetingFields['started'] . ', INTERVAL ' . $timeout . ' SECOND)',
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
          if (empty($c[$meetingFields['id_tmp']])
            || !array_key_exists($c[$meetingFields['id_tmp']], $meetings)
          ) {
            $date = date('Y-m-d H:i:s');
            if ($ctrl->db->update($meetingTable, [
              $meetingFields['ended'] => $date
            ], [
              $meetingFields['id'] => $c[$meetingFields['id']]
            ])) {
              $closed++;
              $leaved += $ctrl->db->update($partsTable, [
                $partsFields['leaved'] => $date
              ], [
                $partsFields['id_meeting'] => $c['id'],
                $partsFields['leaved'] => null
              ]);
            }
          }
        }
      }

      // Check and close the meetings that have no participants
      if ($newStartedToClose = $ctrl->db->rselectAll([
        'table' => $meetingTable,
        'fields' => [
          $ctrl->db->cfn($meetingFields['id'], $meetingTable),
          $ctrl->db->cfn($meetingFields['id_tmp'], $meetingTable),
          'participants' => 'COUNT(' . $ctrl->db->cfn($partsFields['id'], $partsTable). ')'
        ],
        'join' => [[
          'table' => $partsTable,
          'type' => 'left',
          'on' => [
            'conditions' => [[
              'field' => $ctrl->db->cfn($partsFields['id_meeting'], $partsTable),
              'exp' => $ctrl->db->cfn($meetingFields['id'], $meetingTable)
            ], [
              'field' => $ctrl->db->cfn($partsFields['leaved'], $partsTable),
              'operator' => 'isnull'
            ]]
          ]
        ]],
        'where' => [
          'conditions' => [[
            'field' => $ctrl->db->cfn($meetingFields['id_tmp'], $meetingTable),
            'operator' => 'isnull'
          ], [
            'field' => $ctrl->db->cfn($meetingFields['ended'], $meetingTable),
            'operator' => 'isnull'
          ], [
            'logic' => 'OR',
            'conditions' => $w
          ]]
        ],
        'group_by' => [$ctrl->db->cfn($meetingFields['id'], $meetingTable)]
      ])) {
        foreach ($newStartedToClose as $c) {
          if (empty($c['participants'])) {
            $closed += $ctrl->db->update($meetingTable, [
              $meetingFields['ended'] => date('Y-m-d H:i:s')
            ], [
              $meetingFields['id'] => $c[$meetingFields['id']]
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