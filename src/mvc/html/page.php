<div class="appui-meeting bbn-overlay">
  <div v-if="ready">
    <div v-if="hasMeet"
         ref="meetContainer"
         class="bbn-overlay"/>
    <div v-else>
      <bbn-button @click="makeMeet">START MEET</bbn-button>
    </div>
  </div>
  <div v-else>
    <div v-if="APILoadError">ERROR</div>
    <div v-else>LOADING</div>
  </div>
</div>