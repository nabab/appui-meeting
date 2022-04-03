(() => {
  return {
    props: {
      source: {
        type: String,
        required: true
      }
    },
    data(){
      return {
        meeting: appui.getRegistered('appui-meeting'),
        root: appui.plugins['appui-meeting'] + '/'
      }
    },
    computed: {
      usersCfg(){
        return this.meeting ? this.meeting.source.usersCfg : {}
      },
      prefCfg(){
        return this.meeting ? this.meeting.source.prefCfg : {}
      },
      meetingCfg(){
        return this.meeting ? this.meeting.source.meetingCfg : {}
      },
      participantsCfg(){
        return this.meeting ? this.meeting.source.participantsCfg : {}
      }
    },
    methods: {
      renderDate(row, col, idx, val){
        return !!val ? dayjs(val).format('DD/MM/YYYY HH:mm') : '';
      },
      renderParts(row){
        let res = [];
        if (row.participants && row.participants.length) {
          bbn.fn.each(row.participants, p => {
            if (!!p[this.participantsCfg.id_user]) {
              res.push(appui.app.getUserName(p[this.participantsCfg.id_user]));
            }
            else if (!!p[this.participantsCfg.name] && p[this.participantsCfg.name].length) {
              res.push(p[this.participantsCfg.name]);
            }
          });
        }
        return res.sort().join(', ');
      },
      openDetails(row, col, idx){
        let table = this.getRef('table');
        table.toggleExpanded(table.items[idx].index);
      },
      exportExcel(row){
        if (row[this.meetingCfg.id]) {
          this.postOut(this.root + 'actions/logs', {idMeeting: row[this.meetingCfg.id]});
        }
      }
    },
    components: {
      expander: {
        template: `
          <bbn-table :source="source.logs"
                     :pageable="false"
                     :scrollable="false"
                     :titles="false">
            <bbns-column field="moment"
                         :render="renderMoment"
                         :width="120"/>
            <bbns-column field="text"/>
          </bbn-table>
        `,
        props: {
          source: {
            type: Object,
            required: true
          }
        },
        methods: {
          renderMoment(row, col, idx, val){
            return !!val ? dayjs(val).format('DD/MM/YYYY HH:mm') : '';
          }
        }
      }
    }
  }
})();