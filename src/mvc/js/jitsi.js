// Javascript Document
(() => {
  return {
    data() {
      return {
        jitsi: null
      }
    },
    mounted() {
      const domain = 'meet.thomas.lan';
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
            email: appui.app.user.email,
            name: appui.app.user.name,
            nick: appui.app.user.name.indexOf(' ') ? appui.app.user.name.split(' ')[0] : appui.app.user.name
          }
        }
      };
      if (window.JitsiMeetExternalAPI === undefined) {
        let script = document.createElement('script');
        script.src = 'https://meet.thomas.lan/external_api.js';
        script.onload = () => {
          this.jitsi = new JitsiMeetExternalAPI(domain, o);
        };
        document.head.append(script);
      }
      else {
        this.jitsi = new JitsiMeetExternalAPI(domain, o);
      }
    }
  }
})();