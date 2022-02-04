<div class="appui-meeting bbn-overlay">
  <div v-if="ready"
       class="bbn-overlay">
    <div v-if="hasMeet"
         ref="meetContainer"
         class="bbn-overlay"/>
    <div v-else
         class="bbn-overlay bbn-middle bbn-xlpadded bbn-alt-background">
      <div class="bbn-100 bbn-alt-background">
        <bbn-splitter orientation="auto">
          <bbn-pane class="bbn-radius bbn-bordered appui-meeting-adminroom">
            <div class="bbn-flex-height">
              <div class="bbn-header bbn-b bbn-upper bbn-padded bbn-radius-top bbn-no-border">
                <?=_('Administered rooms')?>
              </div>
              <div class="bbn-flex-fill">
                <bbn-list/>
              </div>
            </div>
          </bbn-pane>
          <bbn-pane class="bbn-radius bbn-bordered appui-meeting-yourroom">
            <div class="bbn-flex-height">
              <div class="bbn-header bbn-b bbn-upper bbn-padded bbn-radius-top bbn-no-border">
                <?=_('Your rooms')?>
              </div>
              <div class="bbn-flex-fill">
                <bbn-list/>
              </div>
            </div>
          </bbn-pane>
        </bbn-splitter>
      </div>
    </div>
  </div>
  <div v-else>
    <div v-if="APILoadError">ERROR</div>
    <div v-else>LOADING</div>
  </div>
</div>