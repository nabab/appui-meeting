<bbn-router :autoload="true"
            :nav="true"
            :master="true"
            class="appui-meeting-admin">
  <bbns-container url="public"
                  component="appui-meeting-admin-rooms"
                  :options="{
                    type: 'public'
                  }"
                  :load="false"
                  title="<?= _('Public rooms') ?>"
                  :fixed="true"/>
  <bbns-container url="private"
                  component="appui-meeting-admin-rooms"
                  :options="{
                    type: 'users'
                  }"
                  :load="false"
                  title="<?= _('Private users rooms') ?>"
                  :fixed="true"/>
  <bbns-container url="groups"
                  component="appui-meeting-admin-rooms"
                  :options="{
                    type: 'groups'
                  }"
                  :load="false"
                  title="<?= _('Private groups rooms') ?>"
                  :fixed="true"/>
</bbn-router>