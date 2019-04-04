<template>
    <div class="container">

        <div class="messaging">
            <div class="inbox_msg">
                <div class="inbox_people">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-chats-tab" data-toggle="tab" href="#nav-chats" role="tab" aria-controls="nav-chats" aria-selected="true">Chats</a>
                            <a class="nav-item nav-link" id="nav-dialog-tab" data-toggle="tab" href="#nav-dialog" role="tab" aria-controls="nav-dialog" aria-selected="false">Диалог</a>
                            <a class="nav-item nav-link" id="nav-chat-tab" data-toggle="tab" href="#nav-chat" role="tab" aria-controls="nav-chat" aria-selected="false">Беседа</a>
                            <a class="nav-item nav-link" id="nav-invite-tab" data-toggle="tab" href="#nav-invite" role="tab" aria-controls="nav-invite" aria-selected="false" v-bind:class="{ disabled: chatSelect.id == null || chatSelect.type == 'dialog' }" @click="inviteUserList">Пригласить</a>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-chats" role="tabpanel" aria-labelledby="nav-chats-tab">
                            <div class="inbox_chats">
                                <div class="chat_list unselectable" v-for="chat in chats" v-bind="{ chatSelect: chat }" v-bind:class="{ active_chat: chatSelect.id == chat.id  }" @click="makeActiveChat(chat)" >
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
                        <div class="tab-pane fade" id="nav-dialog" role="tabpanel" aria-labelledby="nav-dialog-tab">
                            <div class="inbox_dialogs">
                                <div class="chat_list unselectable" v-for="without_dialog in without_dialogs" v-bind="{ dialogSelect: without_dialog }" v-bind:class="{ active_chat: dialogSelect.id == without_dialog.id  }" @click="makeActiveFriend(without_dialog)" >
                                    <div class="chat_people">
                                        <div class="chat_ib">
                                            <h5>{{without_dialog.username}}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm" placeholder="Наберите сообщение" v-model="message" :disabled="dialogSelect == null">
                                <div class="input-group-append">
                                    <button @click="startDialog" class="btn btn-outline-secondary btn-sm" type="button" >Отправить</button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-chat" role="tabpanel" aria-labelledby="nav-chat-tab">
                            <!-- <label>Беседа</label> -->
                            <div class="inbox_chat">
                                <div class="chat_list unselectable" v-for="friend in friends" v-bind:class="{ active_chat: userSelect.indexOf(friend.id) !== -1 }" @click="makeActiveFriends(friend.id)" >
                                    <div class="chat_people">
                                        <div class="chat_ib">
                                            <h5>{{friend.username}}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm" placeholder="Название" v-model="chatTitle">
                                <div class="input-group-append">
                                    <button @click="createChat" class="btn btn-outline-secondary btn-sm" type="button">Создать</button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-invite" role="tabpanel" aria-labelledby="nav-invite-tab">
                            <!-- <label>Пригласить</label> -->
                            <div class="inbox_invite">
                                <div class="chat_list unselectable" v-for="invite_chat_user in invite_chat_users" @click="inviteUser(invite_chat_user)" >
                                    <!-- v-bind:class="{ active_chat: userSelect.indexOf(friend.id) !== -1 }" @click="makeActiveFriends(friend.id)"  -->
                                    <div class="chat_people">
                                        <div class="chat_ib">
                                            <h5>{{invite_chat_user.username}}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mesgs">
                    <div class="msg_history">
                        <!-- <div class="loading" v-if="isLoading">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="loader">Loading...</div>
                                </div>
                            </div>
                        </div> -->
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
                without_dialogs: [],
                invite_chat_users: [],
                user: {},
                dataMessages: [],
                nextPageMessages: "",
                nextPageChats: "",
                nextPageDialog: "",
                nextPageChat: "",
                nextPageInviteList: "",
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
                this.nextPageChat = response.data.friends.next_page_url;
                this.friends.push(...response.data.friends.data);
                // this.friends.push(...response.data.friends);
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
                this.nextPageChats = response.data.chats.next_page_url;
                this.chats.push(...response.data.chats.data);
            });

            axios({
                method: 'get',
                url: '/api/v1/friends/without-dialog',
                headers: { 'Authorization': 'Bearer ' + localStorage.access_token }
            })
            .then((response) => {
                this.nextPageDialog = response.data.without_dialogs.next_page_url;
                this.without_dialogs.push(...response.data.without_dialogs.data);
                // this.without_dialogs.push(...response.data.without_dialogs);
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
                    }
                    else if (data.type == 'message') {
                        if (this.chatSelect.id == data.data.chat_id) {
                            this.dataMessages.push({body: data.data.message, user: { user_id: data.data.from_user.id, username: data.data.from_user.username}, created_at: data.data.created_at });
                            this.$nextTick(() => {
                                var container = this.$el.querySelector(".msg_history");
                                container.scrollTop = container.scrollHeight;
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
                axios({
                    method: 'post',
                    url: '/api/v1/chat/'+this.chatSelect.id+'/all',
                    headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                })
                .then((response) => {
                    this.nextPageMessages = response.data.messages.next_page_url;
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
                if(container.scrollTop == 0 && this.nextPageMessages !== null){
                    var scroll = container.scrollHeight;
                    this.isLoading = true;
                    axios({
                        method: 'post',
                        url: this.nextPageMessages,
                        headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                    })
                    .then((response) => {
                        this.nextPageMessages = response.data.messages.next_page_url;
                        this.dataMessages.unshift(...response.data.messages.data);
                        this.$nextTick(() => {
                            var container = this.$el.querySelector(".msg_history");
                            container.scrollTop = container.scrollHeight - scroll;
                        });
                    });
                    this.isLoading = false;
                }
            });
            const chats_container = this.$el.querySelector(".inbox_chats");
            chats_container.addEventListener('scroll', e => {
                if((chats_container.scrollHeight == chats_container.scrollTop + chats_container.clientHeight || chats_container.scrollHeight == chats_container.clientHeight) && this.nextPageChats !== null){
                    axios({
                        method: 'get',
                        url: this.nextPageChats,
                        headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                    })
                    .then((response) => {
                        this.nextPageChats = response.data.chats.next_page_url;
                        this.chats.push(...response.data.chats.data);
                    });
                }
            });
            const dialog_container = this.$el.querySelector(".inbox_dialogs");
            dialog_container.addEventListener('scroll', e => {
                if(dialog_container.scrollHeight == dialog_container.scrollTop + dialog_container.clientHeight && this.nextPageDialog !== null){
                    axios({
                        method: 'get',
                        url: this.nextPageDialog,
                        headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                    })
                    .then((response) => {
                        this.nextPageDialog = response.data.without_dialogs.next_page_url;
                        this.without_dialogs.push(...response.data.without_dialogs.data);
                    });
                }
            });
            const chat_container = this.$el.querySelector(".inbox_chat");
            chat_container.addEventListener('scroll', e => {
                if(chat_container.scrollHeight == chat_container.scrollTop + chat_container.clientHeight && this.nextPageChat !== null){
                    axios({
                        method: 'get',
                        url: this.nextPageChat,
                        headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                    })
                    .then((response) => {
                        this.nextPageChat = response.data.friends.next_page_url;
                        this.friends.push(...response.data.friends.data);
                    });
                }
            });
            const invite_container = this.$el.querySelector(".inbox_invite");
            invite_container.addEventListener('scroll', e => {
                if(invite_container.scrollHeight == invite_container.scrollTop + invite_container.clientHeight && this.nextPageInviteList !== null){
                    axios({
                        method: 'post',
                        url: this.nextPageInviteList,
                        headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                    })
                    .then((response) => {
                        this.nextPageInviteList = response.data.invite_chat_list.next_page_url;
                        this.invite_chat_users.push(...response.data.invite_chat_list.data);
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
                            this.without_dialogs.splice( this.without_dialogs.indexOf(this.dialogSelect), 1);
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
            makeActiveChat: function (chat) {
                this.chatSelect = chat;
            },
            makeActiveFriend: function (friend) {
                this.dialogSelect = friend;
            },
            makeActiveFriends: function (friend) {
                // console.log(this.userSelect.indexOf(friend) !== -1);
                if (this.userSelect.indexOf(friend) !== -1) {
                    this.userSelect.splice( this.userSelect.indexOf(friend), 1);
                } else {
                    this.userSelect.push(friend);
                }
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
                    url: '/api/v1/chat/'+this.chatSelect.id+'/invite/'+user.id,
                    headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                })
                .then((response) => {
                    this.invite_chat_users.splice( this.invite_chat_users.indexOf(user), 1);
                });
            },
            inviteUserList: function () {
                axios({
                    method: 'post',
                    url: '/api/v1/chat/'+this.chatSelect.id+'/invite-chat-list',
                    headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                })
                .then((response) => {
                    this.nextPageInviteList = response.data.invite_chat_list.next_page_url;
                    this.invite_chat_users.push(...response.data.invite_chat_list.data);
                });
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
    height: 600px;
    position: relative;
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
    .inbox_chats {padding: 0; height: 530.7px; overflow-y: scroll;}
    .inbox_dialogs {padding: 0; height: 530.7px; overflow-y: scroll;}
    .inbox_chat {padding: 0; height: 530.7px; overflow-y: scroll;}
    .inbox_invite {padding: 0; height: 530.7px; overflow-y: scroll;}

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


    .loader:before,
    .loader:after {
    border-radius: 50%;
    }
    .loader:before,
    .loader:after {
    position: absolute;
    content: '';
    }
    .loader:before {
    width: 5.2em;
    height: 10.2em;
    background: #1abc9c;
    border-radius: 10.2em 0 0 10.2em;
    top: -0.1em;
    left: -0.1em;
    -webkit-transform-origin: 5.2em 5.1em;
    transform-origin: 5.2em 5.1em;
    -webkit-animation: load2 2s infinite ease 1.5s;
    animation: load2 2s infinite ease 1.5s;
    }
    .loader {
    font-size: 11px;
    text-indent: -99999em;
    margin: 55px auto;
    position: relative;
    width: 10em;
    height: 10em;
    box-shadow: inset 0 0 0 1em #fff;
    -webkit-transform: translateZ(0);
    -ms-transform: translateZ(0);
    transform: translateZ(0);
    }
    .loader:after {
    width: 5.2em;
    height: 10.2em;
    background: #1abc9c;
    border-radius: 0 10.2em 10.2em 0;
    top: -0.1em;
    left: 5.1em;
    -webkit-transform-origin: 0px 5.1em;
    transform-origin: 0px 5.1em;
    -webkit-animation: load2 2s infinite ease;
    animation: load2 2s infinite ease;
    }
    @-webkit-keyframes load2 {
    0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
    }
    100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
    }
    }
    @keyframes load2 {
    0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
    }
    100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
    }
    }

</style>
