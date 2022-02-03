<bbn-router :autoload="true"
            :nav="true"
            :master="true"
            class="appui-meeting-admin">
  <bbns-container url="public"
                  component="appui-meeting-admin-public"
                  :load="false"
                  title="<?=_('Public rooms')?>"
                  :static="true"/>
  <bbns-container url="private"
                  component="appui-meeting-admin-private"
                  :load="false"
                  title="<?=_('Private users rooms')?>"
                  :static="true"/>
  <bbns-container url="groups"
                  component="appui-meeting-admin-groups"
                  :load="false"
                  title="<?=_('Private groups rooms')?>"
                  :static="true"/>
</bbn-router>