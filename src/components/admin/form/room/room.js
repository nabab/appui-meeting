(() => {
  return {
    props: {
      source: {
        type: Object,
        required: true
      },
      server: {
        type: String,
        required: true
      },
      type: {
        type: String,
        required: true,
        validator: t => ['public', 'users', 'groups'].includes(t)
      }
    },
    computed: {
      users(){
        let gf = this.prefCfg.id_group;
        if ((this.type === 'groups')) {
          if (!this.source[gf] || !this.source[gf].length) {
            return [];
          }
          return bbn.fn.filter(appui.app.getActiveUsers(), u => u[this.usersCfg.id_group] === this.source[gf]);
        }
        return appui.app.getActiveUsers();
      },
      groups(){
        return appui.app.groups;
      }
    },
    methods: {
      onSuccess(d){
        let floater = this.closest('bbn-floater');
        if (bbn.fn.isVue(floater)) {
          if (d.success) {
            floater.$emit('success');
          }
          else {
            floater.$emit('error');
          }
        }
      }
    },
    watch: {
      'source.id_group'(newVal){
        if (this.type === 'groups') {
          this.source.moderators.splice(0);
        }
      }
    }
  }
})();