(() => {
  return {
    props: {
      meeting: {
        type: String,
        required: true
      },
      users: {
        type: Array,
        required: true
      }
    },
    data(){
      return {
        root: appui.plugins['appui-meeting'] + '/',
        formSource: {
          users: []
        }
      }
    },
    methods: {
      onSuccess(d){
        if (d.success) {
          appui.success(bbn._('Invited'));
        }
        else {
          appui.error();
        }
      }
    }
  }
})();