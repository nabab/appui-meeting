(() => {
  return {
    data(){
      return {
        server: ''
      }
    },
    mounted(){
      if (this.servers && this.servers.length) {
        this.server = this.servers[0].value;
      }
    },
    watch: {
      server(newVal){
        this.$emit('serverChanged', newVal);
      }
    }
  }
})();