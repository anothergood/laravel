<template>
    <div id="home">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <button class="navbar-toggler" type="text" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item" >
                        <a class="nav-link pointer" @click="home" >Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link pointer" @click="chat">Публичный чат</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link pointer" @click="privateChat">Приватный чат</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link pointer" @click="posts">Посты</a>
                    </li>
                </ul>
            </div>
        </nav>
        <router-view></router-view>
    </div>
</template>

<script>
import PrivateChatComponent from './PrivateChatComponent.vue';
import ChatComponent from './ChatComponent.vue';
// import PostComponent from './PostComponent.vue';

export default {
    data : function(){
        return {
            message_notification: false,
            language: "",
            message: "",
            from_user: "",
            user: {},
            system: ""
        }
    },

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
            })
            .catch((error) => {
                this.$router.push({ path: '/login' })
            });
        };

    },

    watch: {
        user: function (val, oldVal) {
            Echo.connector.pusher.config.auth.headers['Authorization'] = 'Bearer ' + localStorage.access_token;
            Echo.private('channel.'+this.user.id)
            .listen('ChatPrivateMessage', ({data}) => {

            });
        },
    },
    methods: {
        home: function () {
            this.$router.push({ path: '/home' });
        },
        chat: function () {
            this.$router.push({ path: '/chat' });
        },
        privateChat: function () {
            this.$router.push({ path: '/private-chat' });
        },
        posts: function () {
            this.$router.push({ path: '/posts' });
        },
    }
}
</script>

<style>
.toast {
    position:fixed;
    bottom:50%;
    right:5px;
    min-width:200px;
    z-index: 10;
}
.pointer{
    cursor: pointer;
}
.language{
    float: right;
}
.radio {
    margin-left: 5px;
}
</style>
