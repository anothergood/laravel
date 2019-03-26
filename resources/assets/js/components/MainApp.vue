<template>
  <div id="main">
    <div class="content">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
    import Header from './Header.vue';

    export default {
            name: 'main-app',
            components: {Header},
            mounted() {
                if (!localStorage.access_token){
                    this.$router.push({ path: '/login' });
                } else {
                    this.$router.push({ path: '/home' });
                }
            }
    }
</script>
