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
        return this.admin?.root;
      },
      servers(){
        return this.admin?.source?.servers;
      },
      usersCfg(){
        return this.admin?.source?.usersCfg;
      },
      groupsCfg(){
        return this.admin?.source?.groupsCfg;
      },
      prefCfg(){
        return this.admin?.source?.prefCfg;
      }
    },
    created(){
      this._admin = appui.getRegistered('appui-meeting-admin');
    }
  }];
  bbn.cp.addPrefix('appui-meeting-admin-', null, mixins);
  return {
    data(){
      return {
        root: appui.plugins['appui-meeting'] + '/'
      }
    },
    created(){
      appui.register('appui-meeting-admin', this);
    },
    beforeDestroy(){
      appui.unregister('appui-meeting-admin');
    }
  };
})();