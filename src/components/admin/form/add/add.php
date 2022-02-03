<bbn-form :action="root + 'actions/admin/add'"
          :source="source"
          class="appui-meeting-admin-form-add"
          @success="onSuccess">
  <div class="bbn-spadded bbn-grid-fields">
    <label class="bbn-label"><?=_('Name')?></label>
    <bbn-input v-model="source.text"
               :required="true"/>
    <template v-if="source.id_group !== undefined">
      <label class="bbn-label"><?=_('Group')?></label>
      <div>
        <bbn-dropdown v-model="source.id_group"
                      :source="groups"
                      :required="true"/>
      </div>
    </template>
    <template v-if="source.moderators !== undefined">
      <label class="bbn-label"><?=_('Moderators')?></label>
      <div>
        <bbn-multiselect v-model="source.moderators"
                        :source="users"
                        :required="true"/>
      </div>
    </template>
    <template v-if="source.id_user !== undefined">
      <label class="bbn-label"><?=_('Moderator')?></label>
      <div>
        <bbn-dropdown v-model="source.id_user"
                      :source="users"
                      :required="true"/>
      </div>
    </template>
  </div>
</bbn-form>