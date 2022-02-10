(() => {
  return {
    data(){
      return {
        currentMeeting: false,
        currentMeetingID: false,
        currentRoomID: false,
        currentRoom: false,
        currentServer: false,
        currentToken: false,
        currentTmp: false,
        ready: false,
        APILoaded: false,
        APILoadError: false,
        root: appui.plugins['appui-meeting'] + '/'
      }
    },
    computed: {
      administeredRooms(){
        if (this.source.rooms && this.source.rooms.length) {
          return bbn.fn.filter(this.source.rooms, room => room.moderators.includes(appui.app.user.id));
        }
        return [];
      },
      yourRooms(){
        if (this.source.rooms && this.source.rooms.length) {
          return bbn.fn.filter(this.source.rooms, room => !room.moderators.includes(appui.app.user.id));
        }
        return [];
      }
    },
    methods: {
      joinMeet(meet){
        bbn.fn.log('jooooin', meet)
        this.APILoaded = false;
        this.APILoadError = false;
        if (!!meet.id && !!meet[this.source.prefCfg.id_option] && !!meet[this.source.prefCfg.text]) {
          this.currentServer = bbn.fn.getField(this.source.servers, 'code', {value: meet[this.source.prefCfg.id_option]});
          this.currentRoomID = meet.id;
          this.currentRoom = meet[this.source.prefCfg.text];
          this.currentToken = false;
          if (!!this.currentServer) {
            if (meet.moderators.includes(appui.app.user.id)) {
              this.post(this.root + 'actions/token', {
                idRoom: meet.id,
                idUser: appui.app.user.id
              }, d => {
                if (d.success && d.token) {
                  this.currentToken = d.token;
                  this._loadAPI();
                }
              });
            }
            else {
              this._loadAPI();
            }
          }
        }
      },
      makeMeet(){
        if (this.ready
          && !this.currentMeeting
          && !!this.currentServer
          && !!this.currentRoomID
          && !!this.currentRoom
        ) {
          this.currentMeeting = true;
          this.$nextTick(() => {
            let opt = {
              roomName: this.currentRoom,
              confID:'123456',
              width: '100%',
              height: '100%',
              parentNode: this.getRef('meetContainer'),
              userInfo: {
                email: appui.app.user.email,
                displayName: appui.app.user.name
              }
            };
            if (this.currentToken) {
              opt.jwt = this.currentToken;
            }
            this.currentMeeting = new JitsiMeetExternalAPI(this.currentServer, opt);
            this.currentMeeting.addListener('videoConferenceLeft', this._onVideoConferenceLeft);
            this.currentMeeting.addListener('videoConferenceJoined', this._onVideoConferenceJoined);
            //this.currentMeeting.addListener('participantJoined', this._onParticipantJoined);
            this.currentMeeting.addListener('dataChannelOpened', (a, b) => {bbn.fn.log('MIRKO', a, b)});
          });
        }
      },
      _loadAPI(){
        let script = document.getElementById('appui-meeting-api');
        if (script) {
          script.remove();
        }
        script = document.createElement('script');
        script.setAttribute('id', 'appui-meeting-api');
        script.setAttribute('type', 'text/javascript');
        script.setAttribute('src', `https://${this.currentServer}/external_api.js`);
        script.onload = this._onAPILoaded;
        script.onerror = this._onAPILoadError;
        document.head.insertAdjacentElement('beforeend', script);
      },
      _onAPILoaded(ev){
        this.APILoadError = false;
        this.APILoaded = true;
        this.makeMeet()
      },
      _onAPILoadError(ev){
        this.APILoadError = true;
        this.APILoaded = false;
      },
      _onVideoConferenceLeft(ev){
        this.currentMeeting.dispose();
        this.post(this.root + 'actions/leaved', {
          idRoom: this.currentRoomID,
          idUser: appui.app.user.id,
          idTmp: this.currentTmp
        }, d => {
          if (!d.success) {
            appui.error();
          }
        });
        this.post(this.root + 'data/rooms', d => {
          if (d.servers !== undefined) {
            this.source.servers.splice(0, this.source.servers.length, ...d.servers);
          }
          if (d.rooms !== undefined) {
            this.source.rooms.splice(0, this.source.rooms.length, ...d.rooms);
          }
        });
        this.currentMeeting = false;
        this.currentMeetingID = false;
        this.currentServer = false;
        this.currentRoomID = false;
        this.currentRoom = false;
        this.currentTmp = false;
      },
      _onVideoConferenceJoined(ev){
        bbn.fn.log('JOINED', ev);
        this.currentTmp = ev.id;
        this.post(this.root + 'actions/joined', {
          idRoom: this.currentRoomID,
          idUser: appui.app.user.id,
          idTmp: ev.id
        }, d => {
          if (!d.success) {
            this._onVideoConferenceLeft();
            appui.error();
          }
        })
      }
    },
    mounted(){
      appui.$on('appui-meeting', (type, data) => {
        if (data.rooms !== undefined) {
          this.source.rooms.splice(0, this.source.rooms.length, ...data.rooms);
        }
        if (data.servers !== undefined) {
          this.source.servers.splice(0, this.source.servers.length, ...data.servers);
        }
      });
      appui.poll({
        'appui-meeting': {
          roomsHash: false,
          serversHash: false
        }
      });
      this.ready = true;
    }
  }
})();