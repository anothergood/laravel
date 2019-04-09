<template>
  <div id="main">
    <div class="content">
      <router-view ></router-view>
    </div>
  </div>
</template>

<script>
    import Header from './Header.vue';

    export default {
        data : function(){
            return {
                user: {}
            }
        },
            name: 'main-app',
            components: {Header},
            created() {
                if (!localStorage.access_token){
                    this.$router.push({ path: '/login' });
                } else {
                    axios({
                        method: 'get',
                        url: '/api/v1/user/self',
                        headers: { 'Authorization': 'Bearer ' + localStorage.access_token }
                    })
                    .then((response) => {
                        this.user = response.data;
                        // this.$router.push({ path: '/home' });
                    })
                    .catch((error) => {
                        this.$router.push({ path: '/login' })
                    });
                    // console.log(response);
                }
            }
    }
</script>
