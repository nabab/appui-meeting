<div class="appui-meeting-admin-toolbar bbn-flex-width bbn-header bbn-spadded">
  <div class="bbn-flex-fill">
    <bbn-button icon="nf nf-fa-plus"
                @click="$emit('addroom')"><?= _('Add') ?></bbn-button>
  </div>
  <div class="bbn-vmiddle">
    <span class="bbn-right-sspace"><?= _('Server') ?>:</span>
    <bbn-dropdown :source="servers"
                  v-model="server"/>
  </div>
</div>