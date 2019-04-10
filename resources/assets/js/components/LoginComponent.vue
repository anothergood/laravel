<template>
  <div class="container jumbotron " style="padding: 20px;margin-top:10px;width:100%">
      <div class="form-group">
        <div class="col-md"><label>Введите почту</label></div>
        <div class="col-md"><input class="form-control" type="email" name="email" v-model="email" placeholder="Введите почту"></div>
      </div>
      <div class="form-group">
        <div class="col-md"><label>Введите пароль</label></div>
        <div class="col-sm"><input class="form-control" type="password" name="password" v-model="password" placeholder="Введите пароль"></div>
      </div>
      <div class="form-group">
        <div class="col-md"><button @click="login" type="button" class="btn btn-outline-secondary">Отправить</button></div>
      </div>
      <div class="form-group" v-if="error">
        <div class="col-md"><p class="error">Неверный адрес почты или пароль!</p></div>
      </div>
  </div>
</template>

<script>
    export default {
        data : function(){
            return {
                email: "",
                password: "",
                error: false
            }
        },
        methods: {
            login: function () {
                axios({
                    method: 'post',
                    url: '/api/v1/user/login',
                    params: { email: this.email, password: this.password }
                })
                .then((response) => {
                    if(response.status === 200) {
                        localStorage.setItem("access_token", response.data.token.access_token);
                        this.$router.push({ path: '/private-chat' });
                    }
                })
                .catch((error) => {
                    this.error = true;
                });
            },
        }
    }
</script>

<style scope>
.error {
    text-align: center;
    color: red;
}
</style>
