<div class="appui-meeting-room bbn-padding bbn-border-bottom bbn-flex-width">
  <div class="bbn-flex-fill bbn-vmiddle bbn-hpadding">
    <div class="bbn-l">
      <div v-text="source[prefCfg.text]"
           :class="{
             'bbn-b': true,
             'bbn-secondary-text-alt': !!administered,
             'bbn-tertiary-text-alt': !administered
           }"/>
      <div v-if="lastUse"
           class="bbn-s bbn-top-sspace">
        <i class="nf nf-md-calendar_clock"
           title="<?= _('Last use') ?>"/>
        <span v-text="lastUse"></span>
      </div>
    </div>
  </div>
  <div class="bbn-middle bbn-upper bbn-hpadding bbn-b appui-meeting-room-live"
       v-if="!!source.live"><?= _('LIVE') ?></div>
  <div class="bbn-hpadding">
    <div class="bbn-upper bbn-xs"
         :title="moderators">
      <?= _('Administrators') ?>
    </div>
    <div class="bbn-middle"
         :title="moderators">
      <i class="nf nf-fa-user_secret bbn-lg bbn-right-sspace"/>
      <span v-text="source.moderators.length"/>
    </div>
  </div>
  <div class="bbn-hpadding">
    <div class="bbn-upper bbn-xs"
         :title="participants">
      <?= _('Participants') ?>
    </div>
    <div class="bbn-middle"
         :title="participants">
      <i class="nf nf-fa-users bbn-lg bbn-right-sspace"/>
      <span v-text="source.participants.length"/>
    </div>
  </div>
  <div :class="['bbn-hpadding', 'bbn-middle', 'bbn-upper', 'bbn-b', 'bbn-p', {
         'bbn-secondary-text-alt': !!administered,
         'bbn-tertiary-text-alt': !administered
       }]"
       @click="joinMeet">
    <span v-if="!administered || !!source.participants.length"><?= _('JOIN') ?></span>
    <span v-else><?= _('START') ?></span>
  </div>
  <bbn-context v-if="!!administered"
               class="bbn-middle bbn-p"
               :source="contextItems"
               tag="div">
    <i class="nf nf-md-dots_vertical"/>
  </bbn-context>
</div>