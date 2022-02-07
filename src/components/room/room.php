<div class="appui-meeting-room bbn-padded bbn-bordered-bottom bbn-flex-width">
  <div v-text="source[prefCfg.text]"
       class="bbn-flex-fill bbn-vmiddle bbn-hpadded"/>
  <div :class="['bbn-middle', 'bbn-upper', 'bbn-hpadded', 'bbn-b', {
        'appui-meeting-room-live': !administered,
        'appui-meeting-room-live-admin': !!administered
       }]"
       v-if="!!source.live"><?=_('LIVE')?></div>
  <div class="bbn-hpadded">
    <div class="bbn-upper bbn-xs"
         :title="moderators"><?=_('Administrators')?></div>
    <div class="bbn-middle"
         :title="moderators">
      <i class="nf nf-fa-user_secret bbn-lg bbn-right-sspace"/>
      <span v-text="source.moderators.length"/>
    </div>
  </div>
  <div class="bbn-hpadded">
    <div class="bbn-upper bbn-xs"><?=_('Participants')?></div>
    <div class="bbn-middle">
      <i class="nf nf-fa-users bbn-lg bbn-right-sspace"/>
      <span v-text="source.participants.length"/>
    </div>
  </div>
  <div class="bbn-hpadded bbn-middle bbn-upper bbn-primary-text-alt bbn-b"
       @click="joinMeet"><?=_('JOIN')?></div>
</div>