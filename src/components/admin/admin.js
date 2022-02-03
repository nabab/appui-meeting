(() => {
  let mixins = [{
    data(){
      return {
        _admin: null
      };
    },
    computed:{
      admin(){
        return this._admin;
      },
      root(){
        return this.admin.root;
      },
      servers(){
        return this.admin.source.servers;
      }
    },
    beforeMount(){
      this._admin = this.closest('appui-meeting-admin');
    }
  }];
  bbn.vue.addPrefix('appui-meeting-admin-', (tag, resolve, reject) => {
    return bbn.vue.queueComponent(
      tag,
      'components/admin/' + bbn.fn.replaceAll('-', '/', tag).substr('appui-meeting-admin-'.length),
      mixins,
      resolve,
      reject
    );
  });
  return {
    data(){
      return {
        root: appui.plugins['appui-meeting'] + '/'
      }
    }
  };
})();