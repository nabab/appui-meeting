(() => {
  return {
    computed: {
      users(){
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
    }
  }
})();