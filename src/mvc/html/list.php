<div class="appui-meeting bbn-overlay">
  <div v-if="currentMeeting"
        class="bbn-overlay bbn-flex-height">
    <div v-if="currentMeetingID && currentRoomID && isModerator(currentRoomID)"
         class="bbn-padded bbn-header bbn-flex-width appui-meeting-toolbar">
      <bbn-button @click="inviteUsers"
                  icon="nf nf-fa-user_plus">
        <?= _('Invite user(s)') ?>
      </bbn-button>
      <div class="bbn-flex-fill bbn-r">
        <template v-if="currentMeetingURL">
          <span><?= _('URL') ?>: </span>
          <bbn-input :value="currentMeetingURL"
                     :readonly="true"
                     class="appui-meeting-toolbar-url bbn-right-space"
                     button-right="nf nf-mdi-content_copy"
                     @clickrightbutton="copyURL"/>
        </template>
        <template v-if="currentMeetingExtURL">
          <span><?= _('External URL') ?>: </span>
          <bbn-input :value="currentMeetingExtURL"
                     :readonly="true"
                     class="appui-meeting-toolbar-url"
                     button-right="nf nf-mdi-content_copy"
                     @clickrightbutton="copyExtURL"/>
        </template>
      </div>
    </div>
    <div class="bbn-flex-fill"
         ref="meetContainer">
    </div>
  </div>
  <div v-else
        class="bbn-overlay bbn-middle bbn-xlpadded bbn-alt-background">
    <div v-if="source.isAdmin"
         class="bbn-top-right bbn-top-sspace bbn-right-sspace">
      <bbn-button class="appui-meeting-admin-btn"
                  icon="nf nf-fae-tools bbn-xxl"
                  :notext="true"
                  text="<?= _('Open administration page') ?>"
                  @click="openAdminPage"/>
    </div>
    <div class="bbn-100 bbn-alt-background">
      <bbn-splitter orientation="auto">
        <bbn-pane v-if="administeredRooms.length"
                  class="bbn-radius bbn-bordered appui-meeting-adminroom">
          <div class="bbn-flex-height">
            <div class="bbn-header bbn-b bbn-upper bbn-padded bbn-radius-top bbn-no-border bbn-secondary-text-alt bbn-lg">
              <?= _('Administered rooms') ?>
            </div>
            <div class="bbn-flex-fill">
              <bbn-scroll>
                <bbn-list :source="administeredRooms"
                          component="appui-meeting-room"
                          :alternate-background="true"
                          @joinmeet="joinMeet"/>
              </bbn-scroll>
            </div>
          </div>
        </bbn-pane>
        <bbn-pane class="bbn-radius bbn-bordered appui-meeting-yourroom">
          <div class="bbn-flex-height">
            <div class="bbn-header bbn-b bbn-upper bbn-padded bbn-radius-top bbn-no-border bbn-tertiary-text-alt bbn-lg">
              <span v-if="invitedRooms.length"><?= _('Invited') ?></span>
              <span v-else><?= _('Your rooms') ?></span>
            </div>
            <div class="bbn-flex-fill">
              <bbn-scroll>
                <div v-if="invitedRooms.length">
                  <bbn-list :source="invitedRooms"
                            component="appui-meeting-room"
                            :alternate-background="true"
                            @joinmeet="joinMeet"/>
                  <div class="bbn-header bbn-b bbn-upper bbn-padded bbn-radius-top bbn-no-border bbn-tertiary-text-alt bbn-lg">
                    <?= _('Your rooms') ?>
                  </div>
                </div>
                <bbn-list :source="yourRooms"
                          component="appui-meeting-room"
                          :alternate-background="true"
                          @joinmeet="joinMeet"/>
              </bbn-scroll>
            </div>
          </div>
        </bbn-pane>
      </bbn-splitter>
    </div>
  </div>
</div>