(() => {
  return {
    props: {
      source: {
        type: Object,
        required: true
      }
    },
    data(){
      return {
        meeting: appui.getRegistered('appui-meeting')
      }
    },
    computed: {
      usersCfg(){
        return this.meeting ? this.meeting.source.usersCfg : {}
      },
      groupsCfg(){
        return this.meeting ? this.meeting.source.groupsCfg : {}
      },
      prefCfg(){
        return this.meeting ? this.meeting.source.prefCfg : {}
      },
      participantsCfg(){
        return this.meeting ? this.meeting.source.participantsCfg : {}
      },
      administered(){
        return this.source
          && this.source.moderators
          && this.source.moderators.includes(appui.app.user.id);
      },
      moderators(){
        let ret = '';
        if (this.source.moderators && this.source.moderators.length) {
          ret = bbn.fn.map(this.source.moderators, m => appui.app.getUserName(m)).sort().join("\n");
        }
        return ret;
      },
      participants(){
        let ret = '';
        if (this.source.participants && this.source.participants.length) {
          let i = 1;
          ret = bbn.fn.map(this.source.participants, m => {
            if (!!m[this.participantsCfg.id_user]) {
              return appui.app.getUserName(m[this.participantsCfg.id_user])
            }
            if (!!m[this.participantsCfg.name]) {
              return m[this.participantsCfg.name];
            }
            return bbn._('External user') + (i > 1 ? i : '');
          }).sort().join("\n");
        }
        return ret;
      },
      contextItems(){
        let ret = [];
        if (!!this.source.live && (!this.source.participants || !this.source.participants.length)) {
          ret.push({
            text: bbn._('Stop meet'),
            icon: 'nf nf-oct-stop',
            action: this.stopMeet
          });
        }
        ret.push({
          text: bbn._('View reports'),
          icon: 'nf nf-fa-history',
          action: this.openReports
        })
        return ret;
      },
      lastUse(){
        if (!!this.source.last) {
          return dayjs(this.source.last).format('DD/MM/YYYY HH:mm');
        }
        return '';
      }
    },
    methods: {
      joinMeet(){
        this.closest('bbn-list').$emit('joinMeet', this.source);
      },
      stopMeet(){
        if (this.source[this.prefCfg.id]) {
          this.post(this.root + 'actions/stop', {idRoom: this.source[this.prefCfg.id]}, d => {
            if (d.success) {
              appui.success();
            }
            else {
              appui.error();
            }
          })
        }
      },
      openReports(){
        this.getPopup().open({
          title: bbn._('Logs') + ' - ' + this.source[this.prefCfg.text],
          component: 'appui-meeting-logs',
          source: this.source[this.prefCfg.id],
          width: '90%',
          height: '90%'
        });
      }
    }
  }
})();