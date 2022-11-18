(() => {
  return {
    data(){
      return {
        currentMeeting: false,
        currentMeetingID: false,
        currentMeetingURL: false,
        currentMeetingExtURL: false,
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
          return bbn.fn.filter(
            this.source.rooms,
            room => (!room.moderators.includes(appui.app.user.id) && !room.invited.includes(appui.app.user.id))
          );
        }
        return [];
      },
      invitedRooms(){
        if (this.source.rooms && this.source.rooms.length) {
          return bbn.fn.filter(this.source.rooms, room => room.invited.includes(appui.app.user.id));
        }
        return [];
      },
    },
    methods: {
      joinMeet(meet){
        this.APILoaded = false;
        this.APILoadError = false;
        bbn.fn.log('mirko', !!meet.id, !!meet[this.source.prefCfg.id_option], !!meet[this.source.prefCfg.text])
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
              width: '100%',
              height: '100%',
              parentNode: this.getRef('meetContainer'),
              userInfo: {
                email: appui.app.user.email,
                displayName: appui.app.user.name
              },
              configOverwrite: {
                requireDisplayName: true,
                defaultRemoteDisplayName: bbn._('External user'),
                defaultLocalDisplayName: bbn._('me')
              },
              interfaceConfigOverwrite: {
                VIDEO_LAYOUT_FIT: 'nocrop'
              }
            };
            if (this.currentToken) {
              opt.jwt = this.currentToken;
            }
            this.currentMeeting = new JitsiMeetExternalAPI(this.currentServer, opt);
            this.currentMeeting.addListener('videoConferenceLeft', this._onVideoConferenceLeft);
            this.currentMeeting.addListener('videoConferenceJoined', this._onVideoConferenceJoined);
            this.currentMeeting.addListener('participantJoined', this._onParticipantJoined);
            this.currentMeeting.addListener('displayNameChange', this._onParticipantJoined);
          });
        }
      },
      isModerator(idRoom){
        return !!bbn.fn.getRow(this.administeredRooms, {[this.source.prefCfg.id]: idRoom});
      },
      inviteUsers(){
        if (this.currentRoomID) {
          let room = bbn.fn.getRow(this.source.rooms, {[this.source.prefCfg.id]: this.currentRoomID});
          if (!!room) {
            this.getPopup().open({
              title: bbn._('Invite users'),
              component: 'appui-meeting-users',
              componentOptions: {
                meeting: this.currentMeetingID,
                users: bbn.fn.filter(appui.app.getActiveUsers(), u => !room.invited.includes(u.value))
              },
              width: 400,
              height: 500
            });
          }
        }
      },
      copyURL(a){
        navigator.clipboard.writeText(this.currentMeetingURL);
      },
      copyExtURL(a){
        navigator.clipboard.writeText(this.currentMeetingExtURL);
      },
      openAdminPage(){
        bbn.fn.link(this.root + 'admin');
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
      _setURL(){
        if (!!this.currentMeetingID) {
          this.currentMeetingURL = bbn.env.root + this.root + 'list/' + this.currentMeetingID;
          bbn.fn.setNavigationVars(this.currentMeetingURL);
        }
        if (!!this.currentServer && !!this.currentRoom) {
          this.currentMeetingExtURL = `https://${this.currentServer}/${this.currentRoom}`;
        }
      },
      _unsetURL(){
        this.currentMeetingURL = false;
        this.currentMeetingExtURL = false;
        bbn.fn.setNavigationVars(this.root + 'list');
      },
      _onVideoConferenceLeft(ev){
        this.currentMeeting.dispose();
        this.post(this.root + 'actions/leaved', {
          idRoom: this.currentRoomID,
          idUser: appui.app.user.id,
          idTmp: this.currentTmp
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
        this._unsetURL();
      },
      _onVideoConferenceJoined(ev){
        this.currentTmp = ev.id;
        this.post(this.root + 'actions/joined', {
          idRoom: this.currentRoomID,
          idUser: appui.app.user.id,
          idTmp: ev.id
        }, d => {
          if (d.success && !!d.idMeeting) {
            this.currentMeetingID = d.idMeeting;
            this._setURL();
          }
          else {
            this._onVideoConferenceLeft();
            appui.error();
          }
        })
      },
      _onParticipantJoined(ev){
        if (!!ev.id && !!ev.formattedDisplayName && !!this.currentRoomID) {
          this.post(this.root + 'actions/name', {
            idMeeting: this.currentMeetingID,
            idTmp: ev.id,
            name: ev.formattedDisplayName
          });
        }
      }
    },
    beforeMount(){
      appui.register('appui-meeting', this);
    },
    mounted(){
      this.$nextTick(() => {
        appui.$on('appui-meeting', (type, data) => {
          if (data.rooms !== undefined) {
            this.source.rooms.splice(0, this.source.rooms.length, ...data.rooms);
          }
          if (data.servers !== undefined) {
            this.source.servers.splice(0, this.source.servers.length, ...data.servers);
          }
        });
        appui.$set(appui.pollerObject, 'appui-meeting', {
          roomsHash: false,
          serversHash: false
        });
        appui.poll();
        this.ready = true;
        if (!!this.source.idMeeting && !!this.source.idRoom) {
          let meet = bbn.fn.getRow(this.source.rooms, {id: this.source.idRoom});
          if (meet && (meet['liveMeeting'] === this.source.idMeeting)) {
            this.joinMeet(meet);
          }
        }
      })
    },
    beforeDestroy(){
      this.$off('appui-meeting');
      appui.unregister('appui-meeting');
    },
    watch: {
      'source.rooms'(newVal){
        if (this.currentMeetingID) {
          let r = bbn.fn.getRow(newVal, {liveMeeting: this.currentMeetingID});
          if (!r) {
            this._onVideoConferenceLeft();
          }
        }
      }
    }
  }
})();