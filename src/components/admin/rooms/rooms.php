<div class="appui-meeting-admin-public bbn-overlay bbn-flex-height">
  <appui-meeting-admin-toolbar @serverchanged="onServerChanged"
                               @addroom="addRoom"/>
  <div class="bbn-flex-fill">
    <bbn-table :source="root + 'data/admin/rooms'"
              @serverchanged="onServerChanged"
              :data="{
                server: currentServer,
                type: type
              }"
              :pageable="true"
              ref="table"
              v-if="ready">
      <bbns-column :field="prefCfg.text"
                   label="<?= _('Name') ?>"/>
      <bbns-column :field="prefCfg.id_group"
                   label="<?= _('Group') ?>"
                   v-if="type === 'groups'"
                   :render="renderGroup"
                   :width="180"/>
      <bbns-column field="created"
                   label="<?= _('Created') ?>"
                   :render="renderDate"
                   :width="120"
                   cls="bbn-c"/>
      <bbns-column field="moderators"
                   label="<?= _('Moderators') ?>"
                   :render="renderModerators"/>
      <bbns-column field="last_use"
                   label="<?= _('Last use') ?>"
                   :render="renderDate"
                   :width="120"
                   cls="bbn-c"/>
      <bbns-column field="last_duration"
                   label="<?= _('Last duration') ?>"
                   :width="120"
                   cls="bbn-c"/>
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