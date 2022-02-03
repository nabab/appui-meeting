(() => {
  return {
    data(){
      return {
        hasMeet: false,
        domain: this.source.meetURL ? this.source.meetURL.replace(/https{0,1}:\/\//i, '').replace(/\/{0,1}$/, '') : '',
        currentMeet: false,
        ready: false,
        APILoadError: false
      }
    },
    methods: {
      onAPILoaded(ev){
        this.APILoadError = false;
        this.ready = true;
      },
      onAPILoadError(ev){
        this.APILoadError = true;
        this.ready = false;
      },
      makeMeet(){
        if (this.ready && this.domain && !this.hasMeet) {
          this.hasMeet = true;
          this.$nextTick(() => {
            let opt = {
              roomName: 'testing',
              width: '100%',
              height: '100%',
              parentNode: this.getRef('meetContainer'),
              jwt: this.source.meetUserToken,
              onload: this.onMeetLoad,
              userInfo: {
                email: appui.app.user.email,
                displayName: appui.app.user.name
              }
            };
            this.currentMeet = new JitsiMeetExternalAPI(this.domain, opt);
            this.currentMeet.addListener('videoConferenceLeft', this.onVideoConferenceLeft);
          });
        }
      },
      onMeetLoad(ev){
        bbn.fn.log('meet loaded', ev);
      },
      onVideoConferenceLeft(ev){
        bbn.fn.log('OUT', ev);
        this.hasMeet = false;
      }
    },
    mounted(){
      if (window.JitsiMeetExternalAPI === undefined) {
        let script = document.createElement('script');
        script.setAttribute('type', 'text/javascript');
        script.setAttribute('src', `${(!this.source.meetURL.startsWith('https://') ? 'https://' : '') + this.source.meetURL}/external_api.js`);
        script.onload = this.onAPILoaded;
        script.onerror = this.onAPILoadError;
        document.head.insertAdjacentElement('beforeend', script);
      }
    }
  }
})();