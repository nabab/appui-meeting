<div class="appui-meeting-room bbn-padded bbn-bordered-bottom bbn-flex-width">
  <div v-text="source[prefCfg.text]"
       class="bbn-flex-fill bbn-vmiddle bbn-hpadded"/>
  <div class="bbn-middle bbn-upper bbn-hpadded bbn-b appui-meeting-room-live"
       v-if="!!source.live"><?=_('LIVE')?></div>
  <div class="bbn-hpadded">
    <div class="bbn-upper bbn-xs"
         :title="moderators">
      <?=_('Administrators')?>
    </div>
    <div class="bbn-middle"
         :title="moderators">
      <i class="nf nf-fa-user_secret bbn-lg bbn-right-sspace"/>
      <span v-text="source.moderators.length"/>
    </div>
  </div>
  <div class="bbn-hpadded">
    <div class="bbn-upper bbn-xs"
         :title="participants">
      <?=_('Participants')?>
    </div>
    <div class="bbn-middle"
         :title="participants">
      <i class="nf nf-fa-users bbn-lg bbn-right-sspace"/>
      <span v-text="source.participants.length"/>
    </div>
  </div>
  <div :class="['bbn-hpadded', 'bbn-middle', 'bbn-upper', 'bbn-b', 'bbn-p', {
         'bbn-secondary-text-alt': !!administered,
         'bbn-tertiary-text-alt': !administered
       }]"
       @click="joinMeet">
    <span v-if="!administered || !!source.participants.length"><?=_('JOIN')?></span>
    <span v-else><?=_('START')?></span>
  </div>
  <bbn-context v-if="!!administered"
               class="bbn-middle bbn-p"
               :source="contextItems"
               tag="div">
    <i class="nf nf-mdi-dots_vertical"/>
  </bbn-context>
</div>