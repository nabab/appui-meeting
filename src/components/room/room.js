(() => {
  return {
    props: {
      source: {
        type: Object,
        required: true
      },
      usersCfg: {
        type: Object,
        required: true
      },
      groupsCfg: {
        type: Object,
        required: true
      },
      prefCfg: {
        type: Object,
        required: true
      },
      participantsCfg: {
        type: Object,
        required: true
      }
    },
    computed: {
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
            if (!!m[this.partecipantsCfg.id_user]) {
              return appui.app.getUserName(m[this.partecipantsCfg.id_user])
            }
            if (!!m[this.partecipantsCfg.name]) {
              return m[this.partecipantsCfg.name];
            }
            return bbn._('External user') + (i > 1 ? i : '');
          }).sort().join("\n");
        }
        return ret;
      },
      contextItems(){
        let ret = [];
        if (this.source.participants && this.source.participants.length) {
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
      }
    },
    methods: {
      joinMeet(){
        this.closest('bbn-list').$emit('joinMeet', this.source);
      },
      stopMeet(){

      },
      openReports(){

      }
    }
  }
})();