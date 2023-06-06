<template>
  <div>
    <navigation></navigation>
    <div class="container-fluid">
      <router-view></router-view>
    </div>
    <footer>
      <span>Simple CRM</span>
    </footer>
  </div>
</template>

<script>
import Navigation from './Navigation.vue';

export default {
  data() {
    return {}
  },
  components: {
    Navigation
  }
};
</script>