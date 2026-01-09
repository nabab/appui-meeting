(() => {
  return {
    props: {
      type: {
        type: String,
        required: true,
        validator: t => ['public', 'users', 'groups'].includes(t)
      }
    },
    data(){
      return {
        currentServer: '',
        ready: false
      }
    },
    methods: {
      onServerChanged(server){
        this.currentServer = server;
        this.$nextTick(() => {
          if (this.ready) {
            let table = this.getRef('table');
            if (table && !table.isLoading && !!table.isLoaded) {
              table.updateData();
            }
          }
          else {
            this.ready = true;
          }
        });
      },
      addRoom(){
        let source = {
          text: ''
        };
        switch (this.type) {
          case 'public':
            source.public = 1;
            source.moderators = [];
            break;
          case 'users':
            source.id_user = '';
            break;
          case 'groups':
            source.id_group = '';
            source.moderators = [];
            break;
        }
        this.getPopup({
          component: 'appui-meeting-admin-form-room',
          label: bbn._('Add a public room'),
          width: 400,
          componentOptions: {
            source: source,
            server: this.currentServer,
            type: this.type
          },
          componentEvents: {
            success: () => {
              this.getRef('table').updateData();
              appui.success();
            },
            error: () => {
              appui.error();
            }
          }
        })
      },
      editRoom(row){
        this.getPopup({
          component: 'appui-meeting-admin-form-room',
          label: bbn._('Add a public room'),
          width: 400,
          componentOptions: {
            source: row,
            server: this.currentServer,
            type: this.type
          },
          componentEvents: {
            success: () => {
              this.getRef('table').updateData();
              appui.success();
            },
            error: () => {
              appui.error();
            }
          }
        })
      },
      deleteRoom(row){
        if (row.id) {
          this.confirm(bbn._('Are you sure you want to delete this room?'), () => {
            this.post(this.root + 'actions/admin/delete', {id: row.id}, d => {
              if (d.success) {
                this.getRef('table').updateData();
                appui.success();
              }
            })
          })
        }
      },
      renderDate(row, col, idx, val){
        return !!val ? bbn.dt(val).format('DD/MM/YYYY HH:mm') : '';
      },
      renderModerators(row){
        if (row.moderators && row.moderators.length) {
          let r = '<div>';
          bbn.fn.each(row.moderators, (m, i) => {
            r += appui.getUserName(m) + (!!row.moderators[i+1] ? '<br>' : '');
          });
          return r + '</div>';

        }
        return '';
      },
      renderGroup(row){
        if (row[this.prefCfg['id_group']]) {
          return bbn.fn.getField(appui.groups, this.groupsCfg.group, {[this.groupsCfg.id]: row[this.prefCfg['id_group']]});
        }
        return '';
      }
    },
    mounted(){
      if (this.servers.length) {
        this.onServerChanged(this.servers[0].value);
      }
    }
  };
})();