<div class="appui-meeting-admin-public bbn-overlay bbn-flex-height">
  <appui-meeting-admin-toolbar @serverChanged="onServerChanged"
                               @addRoom="addRoom"/>
  <div class="bbn-flex-fill">
    <bbn-table :source="root + 'data/admin/rooms/public'"
              @serverChanged="onServerChanged"
              :data="{
                server: currentServer
              }"
              :pageable="true"
              ref="table"
              v-if="currentServer">
      <bbns-column field="text"
                   title="<?=_('Name')?>"/>
      <bbns-column field="created"
                   title="<?=_('Created')?>"
                   :render="renderDate"
                   :width="120"/>
      <bbns-column field="moderators"
                   title="<?=_('Moderators')?>"
                   :render="renderModerators"/>
      <bbns-column field="last_use"
                   title="<?=_('Last use')?>"
                   :render="renderDate"
                   :width="120"/>
      <bbns-column field="last_duration"
                   title="<?=_('Last duration')?>"
                   :render="renderDuration"
                   :width="120"/>
      <bbns-column :buttons="[{
                     text: _('Edit'),
                     icon: 'nf nf-fa-edit',
                     action: editRoom,
                     notext: true
                   }, {
                     text: _('Delete'),
                     icon: 'nf nf-fa-trash',
                     action: deleteRoom,
                     notext: true
                   }]"
                   cls="bbn-c"
                   :width="80"/>
    </bbn-table>
  </div>
</div>