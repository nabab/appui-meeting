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
      }
    },
    methods: {
      joinMeet(){
        this.closest('bbn-list').$emit('joinMeet', this.source);
      }
    }
  }
})();