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
          return bbn.fn.filter(appui.getActiveUsers(), u => u[this.usersCfg.id_group] === this.source[gf]);
        }
        return appui.getActiveUsers();
      },
      groups(){
        return appui.groups;
      }
    },
    methods: {
      onSuccess(d){
        if (d.success) {
          this.$emit('success');
        }
        else {
          this.$emit('error');
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