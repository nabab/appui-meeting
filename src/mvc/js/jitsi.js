// Javascript Document
(() => {
  return {
    data() {
      return {
        jitsi: null
      }
    },
    mounted() {
      if (this.source.domain) {
        const o = {
          roomName: 'BBNSolutions',
          height: '100%',
          width: '100%',
          parentNode: this.getRef('container'),
          interfaceConfigOverwrite: {
            SHOW_JITSI_WATERMARK: false,
            SHOW_WATERMARK_FOR_GUESTS: false,
            JITSI_WATERMARK_LINK: '',
            userInfo: {
              email: appui.user.email,
              name: appui.user.name,
              nick: appui.user.name.indexOf(' ') ? appui.user.name.split(' ')[0] : appui.user.name
            }
          }
        };
        if (window.JitsiMeetExternalAPI === undefined) {
          let script = document.createElement('script');
          script.src = 'https://' + this.source.domain + '/external_api.js';
          script.onload = () => {
            this.jitsi = new JitsiMeetExternalAPI(this.source.domain, o);
          };
          document.head.append(script);
        }
        else {
          this.jitsi = new JitsiMeetExternalAPI(this.source.domain, o);
        }
      }
    }
  }
})();