<template>
<div id="home">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" >Navbar</a>
        <button class="navbar-toggler" type="text" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" @click="home">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" @click="chat">Chat</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" @click="privateChat">Private Chat</a>
                </li>
            </ul>
        </div>
    </nav>
    <router-view></router-view>

    <!-- <div id="notification" v-show="message_notification" class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>{{this.message.user}}</strong>: {{this.message.message}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div> -->

    <div id="element" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000">
        <div class="toast-header">
            <strong class="mr-auto">{{this.message.user}}</strong>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            {{this.message.message}}
        </div>
    </div>


</div>
</template>

<script>
import PrivateChatComponent from './PrivateChatComponent.vue';
import ChatComponent from './ChatComponent.vue';

export default {
    data : function(){
        return {
            message_notification: false,
            message: "",
            user: {}
        }
    },

    created() {
        axios({
            method: 'get',
            url: '/api/v1/user/self',
            headers: { 'Authorization': 'Bearer ' + localStorage.access_token }
        })
        .then((response) => {
            // Object.assign(this.user, response.data);
            // this.$set(this.user, 'username', response.data.username);
            // this.$set(this.user, 'id', response.data.id);
            this.user = response.data;
        });
    },

    watch: {
        user: function (val, oldVal) {
            Echo.connector.pusher.config.auth.headers['Authorization'] = 'Bearer ' + localStorage.access_token;
            Echo.private('channel.'+this.user.id)
                .listen('ChatPrivateMessage', ({data}) => {
                    this.message = data;
                    $('#element').toast('show');
                    console.log(this.message.user);
            });
        },
    },

    // mounted() {
    //     window.Echo.channel('test')
    //         .listen('ChatMessage', (e) => {
    //             this.dataMessages.push(e.message)
    //     });
    // },
    // status: '',
    methods: {
        home: function () {
            this.$router.push({ path: '/home' });
        },
        chat: function () {
            this.$router.push({ path: '/chat' });
        },
        privateChat: function () {
            this.$router.push({ path: '/private-chat' });
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
</style>
