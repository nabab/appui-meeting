(() => {
  return {
    data(){
      return {
        currentServer: ''
      }
    },
    methods: {
      onServerChanged(server){
        this.currentServer = server;
      },
      addRoom(){
        this.getPopup().open({
          component: 'appui-meeting-admin-form-add',
          title: bbn._('Add a public room'),
          width: 400,
          source: {
            server: this.currentServer,
            text: '',
            moderators: [],
            public: 1
          },
          onOpen: c => {
            c.$on('success', () => {
              this.getRef('table').updateData();
              appui.success();
            });
            c.$on('error', () => {
              appui.error();
            });
          }
        })
      },
      editRoom(row){

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
        return !!val ? dayjs(val).format('DD/MM/YYYY HH:mm') : '';
      },
      renderModerators(row){
        if (row.moderators && row.moderators.length) {
          let r = '<div>';
          bbn.fn.each(row.moderators, (m, i) => {
            r += appui.app.getUserName(m.id) + (!!row.moderators[i+1] ? '<br>' : '');
          });
          return r + '</div>';

        }
        return '';
      },
      renderDuration(row){
        return '';
      }
    }
  };
})();