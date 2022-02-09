<div class="appui-meeting bbn-overlay">
  <div v-if="currentMeeting"
        ref="meetContainer"
        class="bbn-overlay">
  </div>
  <div v-else
        class="bbn-overlay bbn-middle bbn-xlpadded bbn-alt-background">
    <div class="bbn-100 bbn-alt-background">
      <bbn-splitter orientation="auto">
        <bbn-pane class="bbn-radius bbn-bordered appui-meeting-adminroom">
          <div class="bbn-flex-height">
            <div class="bbn-header bbn-b bbn-upper bbn-padded bbn-radius-top bbn-no-border bbn-secondary-text-alt">
              <?=_('Administered rooms')?>
            </div>
            <div class="bbn-flex-fill">
              <bbn-list :source="administeredRooms"
                        component="appui-meeting-room"
                        :componentOptions="{
                          usersCfg: source.usersCfg,
                          groupsCfg: source.groupsCfg,
                          prefCfg: source.prefCfg,
                          participantsCfg: source.participantsCfg
                        }"
                        :alternate-background="true"
                        @joinMeet="joinMeet"/>
            </div>
          </div>
        </bbn-pane>
        <bbn-pane class="bbn-radius bbn-bordered appui-meeting-yourroom">
          <div class="bbn-flex-height">
            <div class="bbn-header bbn-b bbn-upper bbn-padded bbn-radius-top bbn-no-borderm bbn-tertiary-text-alt">
              <?=_('Your rooms')?>
            </div>
            <div class="bbn-flex-fill">
              <bbn-list :source="yourRooms"
                        component="appui-meeting-room"
                        :componentOptions="{
                          usersCfg: source.usersCfg,
                          groupsCfg: source.groupsCfg,
                          prefCfg: source.prefCfg,
                          participantsCfg: source.participantsCfg
                        }"
                        :alternate-background="true"
                        @joinMeet="joinMeet"/>
            </div>
          </div>
        </bbn-pane>
      </bbn-splitter>
    </div>
  </div>
</div>