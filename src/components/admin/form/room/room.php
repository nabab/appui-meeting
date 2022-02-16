<bbn-form :action="root + 'actions/admin/' + (source.id ? 'edit' : 'add')"
          :source="source"
          :data="{
            server: server,
            type: type
          }"
          class="appui-meeting-admin-form-room"
          @success="onSuccess">
  <div class="bbn-spadded bbn-grid-fields">
    <label class="bbn-label"><?=_('Name')?></label>
    <bbn-input v-model="source[prefCfg.text]"
               :required="true"
               pattern="^[a-zA-Z0-9-_]*$"
               title="<?=_('You can only use letters, numbers, dashes and underscores')?>"/>
    <template v-if="type === 'groups'">
      <label class="bbn-label"><?=_('Group')?></label>
      <div>
        <bbn-dropdown v-model="source[prefCfg.id_group]"
                      :source="groups"
                      :required="true"
                      :sourceText="groupsCfg.group"
                      :sourceValue="groupsCfg.id"/>
      </div>
    </template>
    <template v-if="type !== 'users'">
      <label class="bbn-label"><?=_('Moderators')?></label>
      <div>
        <bbn-multiselect v-model="source.moderators"
                        :source="users"
                        :required="true"/>
      </div>
    </template>
    <template v-if="type === 'users'">
      <label class="bbn-label"><?=_('Moderator')?></label>
      <div>
        <bbn-dropdown v-model="source[prefCfg.id_user]"
                      :source="users"
                      :required="true"/>
      </div>
    </template>
  </div>
</bbn-form>