<div class="appui-meeting-logs bbn-overlay">
  <bbn-table :source="root + 'data/logs'"
             :data="{
               idRoom: source
             }"
             :pageable="true"
             :expander="$options.components.expander"
             ref="table">
    <bbns-column title="<?= _('Started') ?>"
                 :field="meetingCfg.started"
                 :render="renderDate"
                 :width="120"/>
    <bbns-column title="<?= _('Ended') ?>"
                 :field="meetingCfg.ended"
                 :render="renderDate"
                 :width="120"/>
    <bbns-column title="<?= _('Participants') ?>"
                 field="participants"
                 :render="renderParts"/>
    <bbns-column :buttons="[{
                   text: _('Details'),
                   icon: 'nf nf-fa-history',
                   action: openDetails,
                  
                 }, {
                   text: _('Excel'),
                   icon: 'nf nf-fa-file_excel_o',
                   action: exportExcel
                 }]"
                 :width="200"
                 cls="bbn-c"/>
  </bbn-table>
</div>