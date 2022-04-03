<bbn-form :action="root + 'actions/invite'"
          :source="formSource"
          :data="{
            idMeeting: meeting
          }"
          @success="onSuccess">
  <div class="bbn-padded">
    <bbn-multiselect :source="users"
                     v-model="formSource.users"
                     class="bbn-overlay"/>
  </div>
</bbn-form>