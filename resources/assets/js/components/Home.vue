<template>
    <div id="home">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <!-- <a class="navbar-brand" >Navbar</a> -->
            <button class="navbar-toggler" type="text" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item" >
                        <a class="nav-link pointer" @click="home" >{{ __('navbar.main') }}</a>
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

                <div class="form-check">
                    <input class="form-check-input" type="radio" value="ru" v-model="language" @click="switchLanguage()">
                    <label class="form-check-label">Русский</label>
                </div>
                <div class="form-check radio">
                  <input class="form-check-input" type="radio" value="en" v-model="language" @click="switchLanguage()">
                  <label class="form-check-label">English</label>
                </div>
            </div>
        </nav>
        <router-view></router-view>

        <!-- <div id="notification" v-show="message_notification" class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>{{this.message.user}}</strong>: {{this.message.message}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div> -->

        {{language}}
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
        axios({
            method: 'get',
            url: '/api/v1/user/current-language',
        })
        .then((response) => {
            this.language = response.data;
            // console.log(response.data);
        });
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
        switchLanguage(){
            axios({
                method: 'get',
                url: '/api/v1/user/language',
                headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                params: { language: this.language }

            })
            .then((response) => {
                // window.location.reload();
            });
        }
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
