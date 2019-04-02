<template>
    <div class="container">

        <div class="messaging">
            <div class="inbox_msg">
                <div class="inbox_people">
                    <div class="headind_srch"  style="z-index: 999999;">
                        <label>Беседа</label>
                        <select multiple="" class="form-control form-control-sm" v-model="userSelect">
                            <option v-for="friend in friends" :value="friend.id" >{{friend.username}}</option>
                        </select>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" placeholder="Название" v-model="chatTitle">
                            <div class="input-group-append">
                                <button @click="createChat" class="btn btn-outline-secondary btn-sm" type="button">Создать</button>
                            </div>
                        </div>
                        <label>Диалог</label>
                        <div class="input-group">
                            <select class="form-control form-control-sm" v-model="dialogSelect">
                                <option v-for="friend in friends" :value="friend" >{{friend.username}}</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" placeholder="Наберите сообщение" v-model="message" :disabled="dialogSelect == null">
                            <div class="input-group-append">
                                <button @click="startDialog" class="btn btn-outline-secondary btn-sm" type="button" >Отправить</button>
                            </div>
                        </div>
                    </div>
                    <div class="inbox_chat">
                        <div class="chat_list unselectable" v-for="chat in chats" v-bind="{ chatSelect: chat }" v-bind:class="{ active_chat: chatSelect.id == chat.id  }" @click="makeActive(chat)" >
                            <div class="chat_people">
                                <div class="chat_ib">
                                    <h5>{{chat.title}}  ({{chat.type}})
                                        <!-- <span class="chat_date">{{chat.created_at}}</span> -->
                                        <span class="badge badge-pill badge-secondary" v-if="chat.pivot.unread_messages!=0">{{chat.pivot.unread_messages}}</span>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mesgs">
                    <div class="msg_history">
                        <div v-for="dataMessage in dataMessages" v-bind:class="{ outgoing_msg: dataMessage.user.id == user.id, incoming_msg: dataMessage.user.id !== user.id }">
                            <div v-bind:class="{ sent_msg: dataMessage.user.id == user.id, received_msg: dataMessage.user.id !== user.id }">
                                <div class="received_withd_msg">
                                    <h6>{{dataMessage.user.username}}</h6>
                                    <p>{{dataMessage.body}}</p>
                                    <span class="time_date"> {{dataMessage.created_at}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="type_msg">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Наберите сообщение" v-model="message" :disabled="chatSelect.id == null">
                    <div class="input-group-append">
                        <button @click="sendMessage" class="btn btn-outline-secondary" type="button" :disabled="chatSelect.id == null">Отправить</button>
                    </div>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-secondary" data-toggle="dropdown" aria-expanded="false" :disabled="chatSelect.type == 'dialog' || chatSelect.id == null">
                            Пригласить
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" v-for="friend in friends" @click="inviteUser(friend.id)">{{friend.username}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        data : function(){
            return {
                chats: [],
                chatTitle: "",
                friends: [],
                user: {},
                dataMessages: [],
                nextPage: "",
                message: "",
                userSelect: [],
                dialogSelect: "",
                chatId: "",
                chatSelect: {},
                isActive: false,
                isLoading: false
            }
        },
        created() {

            axios({
                method: 'get',
                url: '/api/v1/friends/all',
                headers: { 'Authorization': 'Bearer ' + localStorage.access_token }
            })
            .then((response) => {
                this.friends.push(...response.data.friends);
            });

            axios({
                method: 'get',
                url: '/api/v1/user/self',
                headers: { 'Authorization': 'Bearer ' + localStorage.access_token }
            })
            .then((response) => {
                this.user = response.data;
            });

            axios({
                method: 'get',
                url: '/api/v1/chat/all',
                headers: { 'Authorization': 'Bearer ' + localStorage.access_token }
            })
            .then((response) => {
                this.chats.push(...response.data.chats);
            });

        },

        watch: {

            user: function (val, oldVal) {
                Echo.connector.pusher.config.auth.headers['Authorization'] = 'Bearer ' + localStorage.access_token;
                Echo.private('channel.'+this.user.id)
                    .listen('ChatPrivateMessage', ({data}) => {
                    if (data.type == 'chat_invite') {
                        this.chats.push({id: data.data.chat_id, title: data.data.chat_title, type: data.data.chat_type, pivot: {unread_messages: 0}});
                        this.$nextTick(() => {
                            var container = this.$el.querySelector(".inbox_chat");
                            container.scrollTop = container.scrollHeight;
                        });
                    } else if (data.type == 'message') {
                        if (this.chatSelect.id == data.data.chat_id) {
                            this.dataMessages.push({body: data.data.message, user: { user_id: data.data.from_user.id, username: data.data.from_user.username}, created_at: data.data.created_at });
                            this.$nextTick(() => {
                                var container = this.$el.querySelector(".msg_history");
                                container.scrollTop = container.scrollHeight;
                            });
                        } else {
                            this.chats.forEach(function(item, i, arr) {
                                if (item.id == data.data.chat_id) {
                                    item.pivot.unread_messages++;
                                }
                            });
                        }
                    }
                });
            },

            chatSelect: function (val, oldVal) {
                // Echo.private('chat.'+this.chatSelect.id)
                // .listen('ChatMessage', ({data}) => {
                //     if(this.user.id !== data.data.from_user.id){
                //         this.dataMessages.push({body: data.data.message, user: { user_id: data.data.from_user.id, username: data.data.from_user.username}, created_at: data.data.created_at });
                //     }
                //     this.$nextTick(() => {
                //         var container = this.$el.querySelector(".msg_history");
                //         container.scrollTop = container.scrollHeight;
                //     });
                // });
                axios({
                    method: 'post',
                    url: '/api/v1/chat/'+this.chatSelect.id+'/all',
                    headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                })
                .then((response) => {
                    this.nextPage = response.data.messages.next_page_url;
                    this.dataMessages.splice( 0, this.dataMessages.length);
                    this.dataMessages.push(...response.data.messages.data);
                    this.$nextTick(() => {
                        var container = this.$el.querySelector(".msg_history");
                        container.scrollTop = container.scrollHeight;
                    });
                });
                axios({
                    method: 'post',
                    url: '/api/v1/chat/'+this.chatSelect.id+'/reset-messages',
                    headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                })
                .then((response) => {
                    this.chats.forEach(function(item, i, arr) {
                        if (item.id == response.data.chat.id) {
                            item.pivot.unread_messages = 0;
                        }
                    });
                });
            },
        },

        mounted() {

            const container = this.$el.querySelector(".msg_history");
            container.addEventListener('scroll', e => {
                if(container.scrollTop == 0 && this.nextPage !== null){

                    var scroll = container.scrollHeight;
                    this.$nextTick(() => {
                        axios({
                            method: 'post',
                            url: this.nextPage,
                            headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                        })
                        .then((response) => {
                            this.nextPage = response.data.messages.next_page_url;
                            this.dataMessages.unshift(...response.data.messages.data);
                            this.$nextTick(() => {
                                var container = this.$el.querySelector(".msg_history");
                                container.scrollTop = container.scrollHeight - scroll;
                            });
                        });
                    });
                }
            });
        },

        methods: {

            startDialog: function () {
                axios({
                    method: 'post',
                    url: '/api/v1/chat/create-chat',
                    headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                    params: { title: this.dialogSelect.username, users: this.dialogSelect.id, type: 'dialog' }
                })
                .then((response) => {
                    this.$nextTick(() => {
                        axios({
                            method: 'post',
                            url: '/api/v1/chat/'+response.data.chat.id+'/send',
                            headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                            params: { body: this.message }
                        })
                        .then((response) => {
                            this.message = "";
                            this.$nextTick(() => {
                                var container = this.$el.querySelector(".msg_history");
                                container.scrollTop = container.scrollHeight;
                            });
                        });
                    });
                });
            },

            sendMessage: function () {

                axios({
                    method: 'post',
                    url: '/api/v1/chat/'+this.chatSelect.id+'/send',
                    headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                    params: { body: this.message }
                })
                .then((response) => {
                    this.dataMessages.push({body: response.data.data.message, user: { id: this.user.id, username: this.user.username}, created_at: response.data.data.created_at });
                    this.message = "";
                    this.$nextTick(() => {
                        var container = this.$el.querySelector(".msg_history");
                        container.scrollTop = container.scrollHeight;
                    });
                });
            },
            makeActive: function (chat) {
                this.chatSelect = chat;
            },

            createChat: function () {

                axios({
                    method: 'post',
                    url: '/api/v1/chat/create-chat',
                    headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                    params: { title: this.chatTitle, users: this.userSelect, type: 'chat' }
                })
                .then((response) => {
                    this.chatTitle = "";
                    this.userSelect = [];
                });

            },
            inviteUser: function (user) {
                axios({
                    method: 'post',
                    url: '/api/v1/chat/'+this.chatSelect.id+'/invite/'+user,
                    headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                })
                .then((response) => {

                });
            },
            test: function (user) {

            },
        }
    }
</script>

<style>
    /* .unselectable {
        -moz-user-select: none;
        -khtml-user-select: none;
        -webkit-user-select: none;
        user-select: none;
    } */

    /* .dropdown{
        z-index: 100;
    } */

    a.active.focus,
    a.active:focus,
    a.focus,
    a:active.focus,
    a:active:focus,
    a:focus,
    button.active.focus,
    button.active:focus,
    button.focus,
    button:active.focus,
    button:active:focus,
    button:focus,
    .btn.active.focus,
    .btn.active:focus,
    .btn.focus,
    .btn:active.focus,
    .btn:active:focus,
    .btn:focus {
        outline: 0 ;
        outline-color: transparent ;
        outline-width: 0 ;
        outline-style: none ;
        box-shadow: 0 0 0 0 rgba(0,123,255,0);
    }
    label {
        margin: 0;
    }
    /* .container{max-width:1170px; margin:auto;} */
    img{ max-width:100%;}
    .inbox_people {
    background: #f8f8f8 none repeat scroll 0 0;
    float: left;
    overflow: hidden;
    width: 40%; border-right:1px solid #c4c4c4;
    }
    .inbox_msg {
    border: 1px solid #c4c4c4;
    clear: both;
    overflow: hidden;
    }
    .top_spac{ margin: 20px 0 0;}


    .recent_heading {float: left; width:40%;}
    .srch_bar {
    display: inline-block;
    text-align: right;
    width: 60%; padding:
    }
    .headind_srch{ padding:10px 29px 10px 20px; overflow:hidden; border-bottom:1px solid #c4c4c4;}

    .recent_heading h4 {
    color: #05728f;
    font-size: 21px;
    margin: auto;
    }
    .srch_bar input{ border:1px solid #cdcdcd; border-width:0 0 1px 0; width:80%; padding:2px 0 4px 6px; background:none;}
    .srch_bar .input-group-addon button {
    background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
    border: medium none;
    padding: 0;
    color: #707070;
    font-size: 18px;
    }
    .srch_bar .input-group-addon { margin: 0 0 0 -27px;}

    .chat_ib h5{ font-size:15px; color:#464646; margin:0 0 8px 0;}
    .chat_ib h5 span{ font-size:13px; float:right;}
    .chat_ib p{ font-size:14px; color:#989898; margin:auto}
    .chat_img {
    float: left;
    width: 11%;
    }
    .chat_ib {
    float: left;
    padding: 0 0 0 15px;
    width: 88%;
    }

    .chat_people{ overflow:hidden; clear:both;}
    .chat_list {
    border-bottom: 1px solid #c4c4c4;
    margin: 0;
    padding: 18px 16px 10px;
    }
    .inbox_chat { height: 372.7px; overflow-y: scroll;}

    .active_chat{ background:#ebebeb;}

    .incoming_msg_img {
    display: inline-block;
    width: 6%;
    }
    .received_msg {
    display: inline-block;
    padding: 0 0 0 0px;
    vertical-align: top;
    width: 60%;
    }
    .received_withd_msg p {
    background: #ebebeb none repeat scroll 0 0;
    border-radius: 3px;
    color: #646464;
    font-size: 14px;
    margin: 0;
    padding: 5px 10px 5px 12px;
    width: 100%;
    }
    .time_date {
    color: #747474;
    display: block;
    font-size: 12px;
    margin: 8px 0 0;
    }
    .received_withd_msg { width: 100%; margin: 0px;}
    .mesgs {
    float: left;
    padding: 10px 15px 15px 25px;
    width: 60%;
    height: 600px;
    position: relative;
    }

    .sent_msg p {
    background: #05728f none repeat scroll 0 0;
    border-radius: 3px;
    font-size: 14px;
    margin: 0; color:#fff;
    padding: 5px 10px 5px 12px;
    width:100%;
    }
    .outgoing_msg{ overflow:hidden; margin:0 0 13px;}
    .incoming_msg{ overflow:hidden; margin:0 0 13px;}
    .sent_msg {
    float: right;
    width: 60%;
    }
    .input_msg_write input {
    background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
    border: medium none;
    color: #4c4c4c;
    font-size: 15px;
    min-height: 48px;
    width: 100%;
    }

    .type_msg {position: relative; margin-top: 3px;}
    .msg_send_btn {
    background: #05728f none repeat scroll 0 0;
    border: medium none;
    border-radius: 50%;
    color: #fff;
    cursor: pointer;
    font-size: 17px;
    height: 33px;
    position: absolute;
    right: 0;
    top: 11px;
    width: 33px;
    }
    .messaging { padding: 0;}
    .msg_history {
        position: absolute;
    left:25;
    right:10;
    top:15;
    bottom:15;
    overflow-y: scroll;
    }
</style>
