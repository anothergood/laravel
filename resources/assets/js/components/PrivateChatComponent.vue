<template>
    <div class="container">

        <div class="messaging">
            <div class="inbox_msg">
                <div class="inbox_people">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link" id="nav-chats-tab" data-toggle="tab" href="#nav-chats" role="tab" aria-controls="nav-chats" aria-selected="true" v-bind:class="{ active: tabSelect == 1 }" @click="tabActive(1)">Chats</a>
                            <a class="nav-item nav-link" id="nav-dialog-tab" data-toggle="tab" href="#nav-dialog" role="tab" aria-controls="nav-dialog" aria-selected="false"  v-bind:class="{ active: tabSelect == 2 }"  @click="tabActive(2)">Диалог</a>
                            <a class="nav-item nav-link" id="nav-chat-tab" data-toggle="tab" href="#nav-chat" role="tab" aria-controls="nav-chat" aria-selected="false" v-bind:class="{ active: tabSelect == 3 }" @click="tabActive(3)">Беседа</a>
                            <a class="nav-item nav-link" id="nav-invite-tab" data-toggle="tab" href="#nav-invite" role="tab" aria-controls="nav-invite" aria-selected="false" v-bind:class="{ disabled: chatSelect.id == null || chatSelect.type == 'dialog', active: tabSelect == 4}" @click="tabActive(4)">Пригласить</a>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade" id="nav-chats" role="tabpanel" aria-labelledby="nav-chats-tab"  v-bind:class="{ active: tabSelect == 1, show: tabSelect == 1 }">
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
                        <div class="tab-pane fade" id="nav-dialog" role="tabpanel" aria-labelledby="nav-dialog-tab" v-bind:class="{ active: tabSelect == 2, show: tabSelect == 2 }">
                            <div class="inbox_dialogs">
                                <div class="chat_list unselectable" v-for="without_dialog in without_dialogs" v-bind="{ dialogSelect: without_dialog }" v-bind:class="{ active_chat: dialogSelect.id == without_dialog.id  }" @click="makeActiveFriend(without_dialog)"  data-toggle="modal" data-target="#exampleModal">
                                    <div class="chat_people">
                                        <div class="chat_ib">
                                            <h5>{{without_dialog.username}}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade parrent" id="nav-chat" role="tabpanel" aria-labelledby="nav-chat-tab" v-bind:class="{ active: tabSelect == 3, show: tabSelect == 3 }">
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
                            <button id="create_button" data-toggle="modal" data-target="#chatModal" class="btn btn-secondary btn-sm" type="button">Создать</button>
                        </div>
                        <div class="tab-pane fade" id="nav-invite" role="tabpanel" aria-labelledby="nav-invite-tab" v-bind:class="{ active: tabSelect == 4, show: tabSelect == 4 }">
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
                    <div class="loading" v-if="isLoading"></div>
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
                </div>
            </div>
        </div>

        <!-- Modal диалог -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Отправить сообщение</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" placeholder="Наберите сообщение" v-model="dialog_message">
                            <div class="input-group-append">
                                <button @click="startDialog" class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Отправить</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal беседа -->
        <div class="modal fade" id="chatModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Создать беседу</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" placeholder="Название" v-model="chatTitle">
                            <div class="input-group-append">
                                <button @click="createChat" class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Создать</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
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
                dialog_message: "",
                userSelect: [],
                dialogSelect: "",
                tabSelect: 1,
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
                this.nextPageChat = response.data.next_page_url;
                this.friends.push(...response.data.data);
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
                this.nextPageChats = response.data.next_page_url;
                this.chats.push(...response.data.data);
            });

            axios({
                method: 'get',
                url: '/api/v1/friends/without-dialog',
                headers: { 'Authorization': 'Bearer ' + localStorage.access_token }
            })
            .then((response) => {
                this.nextPageDialog = response.data.next_page_url;
                this.without_dialogs.push(...response.data.data);
            });

        },

        watch: {

            user: function (val, oldVal) {
                Echo.connector.pusher.config.auth.headers['Authorization'] = 'Bearer ' + localStorage.access_token;
                Echo.private('channel.'+this.user.id)
                    .listen('ChatPrivateMessage', ({data}) => {
                    if (data.type == 'chat_invite') {
                        this.chats.push({id: data.data.id, title: data.data.title, type: data.data.type, pivot: {unread_messages: 0}});
                        if (data.data.type == 'dialog'){
                            axios({
                                method: 'get',
                                url: '/api/v1/friends/without-dialog',
                                headers: { 'Authorization': 'Bearer ' + localStorage.access_token }
                            })
                            .then((response) => {
                                this.nextPageDialog = response.data.next_page_url;
                                this.without_dialogs.splice( 0, this.without_dialogs.length);
                                this.without_dialogs.push(...response.data.data);
                            });
                        }
                        this.$nextTick(() => {
                            var container = this.$el.querySelector(".inbox_chat");
                            container.scrollTop = container.scrollHeight;
                        });
                    }
                    else if (data.type == 'message') {
                        if (this.chatSelect.id == data.data.message.chat.id) {
                            this.dataMessages.push({body: data.data.message.body, user: { user_id: data.data.message.user.id, username: data.data.message.user.username}, created_at: data.data.message.created_at });
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
                                    if (item.id == response.data.id) {
                                        item.pivot.unread_messages = 0;
                                    }
                                });
                            });
                        } else {
                            this.chats.forEach(function(item, i, arr) {
                                if (item.id == data.data.message.chat.id) {
                                    item.pivot.unread_messages++;
                                }
                            });
                        }
                    }
                    else if (data.type == 'new_invited') {
                        if(this.chatSelect.id == data.data.id){
                            axios({
                                method: 'post',
                                url: '/api/v1/chat/'+this.chatSelect.id+'/invite-chat-list',
                                headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                            })
                            .then((response) => {
                                this.nextPageInviteList = response.data.next_page_url;
                                this.invite_chat_users.splice( 0, this.invite_chat_users.length);
                                this.invite_chat_users.push(...response.data.data);
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
                    this.nextPageMessages = response.data.next_page_url;
                    this.dataMessages.splice( 0, this.dataMessages.length);
                    this.dataMessages.push(...response.data.data);
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
                        if (item.id == response.data.id) {
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
                    // this.isLoading = true;
                    axios({
                        method: 'post',
                        url: this.nextPageMessages,
                        headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                    })
                    .then((response) => {
                        this.nextPageMessages = response.data.next_page_url;
                        this.dataMessages.unshift(...response.data.data);
                        this.$nextTick(() => {
                            var container = this.$el.querySelector(".msg_history");
                            container.scrollTop = container.scrollHeight - scroll;
                        });
                    });
                }
            });
            const chats_container = this.$el.querySelector(".inbox_chats");
            chats_container.addEventListener('scroll', e => {
                if(Math.round(chats_container.scrollTop + chats_container.clientHeight) == chats_container.scrollHeight && this.nextPageChats !== null){
                    axios({
                        method: 'get',
                        url: this.nextPageChats,
                        headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                    })
                    .then((response) => {
                        this.$nextTick(() => {
                            this.nextPageChats = response.data.next_page_url;
                            this.chats.push(...response.data.data);
                        });
                    });
                }
            });
            const dialog_container = this.$el.querySelector(".inbox_dialogs");
            dialog_container.addEventListener('scroll', e => {
                if (Math.round(dialog_container.scrollTop + dialog_container.clientHeight) >= dialog_container.scrollHeight && this.nextPageDialog !== null){
                    axios({
                        method: 'get',
                        url: this.nextPageDialog,
                        headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                    })
                    .then((response) => {
                        this.nextPageDialog = response.data.next_page_url;
                        this.without_dialogs.push(...response.data.data);
                    });
                }
            });
            const chat_container = this.$el.querySelector(".inbox_chat");
            chat_container.addEventListener('scroll', e => {
                if(Math.round(chat_container.scrollTop + chat_container.clientHeight) >= chat_container.scrollHeight && this.nextPageChat !== null){
                    axios({
                        method: 'get',
                        url: this.nextPageChat,
                        headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                    })
                    .then((response) => {
                        this.nextPageChat = response.data.next_page_url;
                        this.friends.push(...response.data.data);
                    });
                }
            });
            const invite_container = this.$el.querySelector(".inbox_invite");
            invite_container.addEventListener('scroll', e => {
                if(Math.round(invite_container.scrollTop + invite_container.clientHeight) >= invite_container.scrollHeight && this.nextPageInviteList !== null){
                    axios({
                        method: 'post',
                        url: this.nextPageInviteList,
                        headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                    })
                    .then((response) => {
                        this.nextPageInviteList = response.data.next_page_url;
                        this.invite_chat_users.push(...response.data.data);
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
                    this.chats.push({id: response.data.id, title: response.data.title, type: response.data.type, pivot: {unread_messages: 0}});
                    this.chatSelect = response.data;
                    this.tabSelect = 1;
                    this.$nextTick(() => {
                        axios({
                            method: 'post',
                            url: '/api/v1/chat/'+response.data.id+'/send',
                            headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                            params: { body: this.dialog_message }
                        })
                        .then((response) => {
                            this.dataMessages.push({body: response.data.data.message.body, user: { id: this.user.id, username: this.user.username}, created_at: response.data.data.message.created_at });
                            this.dialog_message = "";
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
                    this.dataMessages.push({body: response.data.data.message.body, user: { id: this.user.id, username: this.user.username}, created_at: response.data.data.message.created_at });
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
                if (this.userSelect.indexOf(friend) !== -1) {
                    this.userSelect.splice( this.userSelect.indexOf(friend), 1);
                } else {
                    this.userSelect.push(friend);
                }
            },
            tabActive: function (tab) {
                this.tabSelect = tab;
                if (tab == 4) {
                    axios({
                        method: 'post',
                        url: '/api/v1/chat/'+this.chatSelect.id+'/invite-chat-list',
                        headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                    })
                    .then((response) => {
                        this.nextPageInviteList = response.data.next_page_url;
                        this.invite_chat_users.splice( 0, this.invite_chat_users.length);
                        this.invite_chat_users.push(...response.data.data);
                    });
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
                    this.chats.push({id: response.data.id, title: response.data.title, type: response.data.type, pivot: {unread_messages: 0}});
                    this.chatTitle = "";
                    this.userSelect = [];
                    this.chatSelect = response.data;
                    this.tabSelect = 1;
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
            // inviteUserList: function () {
            //     axios({
            //         method: 'post',
            //         url: '/api/v1/chat/'+this.chatSelect.id+'/invite-chat-list',
            //         headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
            //     })
            //     .then((response) => {
            //         this.nextPageInviteList = response.data.next_page_url;
            //         this.invite_chat_users.splice( 0, this.invite_chat_users.length);
            //         this.invite_chat_users.push(...response.data.data);
            //     });
            // },
        }
    }
</script>

<style>
    .parrent{
        position:relative;
    }
    #create_button{
        position:absolute;
        right:35px;
        bottom: 20px;
    }



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
    height: 570px;
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
	cursor: pointer;
    border-bottom: 1px solid #c4c4c4;
    margin: 0;
    padding: 18px 16px 10px;
    }
    .inbox_chats {padding: 0; height: 530px; overflow-y: scroll;}
    .inbox_dialogs {padding: 0; height: 530px; overflow-y: scroll;}
    .inbox_chat {padding: 0; height: 530px; overflow-y: scroll;}
    .inbox_invite {padding: 0; height: 530px; overflow-y: scroll;}

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
    height: 570px;
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


    .loading {
        z-index:10;
        margin-left: auto;
        margin-right: auto;
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      padding: 20px;  /* если задать в %, то будет рассчитываться от ширины родителя */
      background:
       linear-gradient(rgba(0,0,0,1) 30%, transparent 30%, transparent 70%, rgba(0,0,0,.4) 70%),
       linear-gradient(to left, rgba(0,0,0,.2) 30%, transparent 30%, transparent 70%, rgba(0,0,0,.8) 70%);
      background-repeat: no-repeat;
      background-size: 10% 100%, 100% 10%;
      background-position: 50% 0%, 0 50%;
      -webkit-animation: loading .7s infinite steps(8);
      animation: loading .7s infinite steps(8);
    }
    .loading:after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image:
       linear-gradient(rgba(0,0,0,.1) 30%, transparent 30%, transparent 70%, rgba(0,0,0,.5) 70%),
       linear-gradient(to left, rgba(0,0,0,.3) 30%, transparent 30%, transparent 70%, rgba(0,0,0,.9) 70%);
      background-repeat: no-repeat;
      background-size: 10% 100%, 100% 10%;
      background-position: 50% 0%, 0 50%;
      -webkit-transform: rotate(45deg);
      transform: rotate(45deg);
    }
    @-webkit-keyframes loading {
      100% {-webkit-transform: rotate(1turn);}
    }
    @keyframes loading {
      100% {transform: rotate(1turn);}
    }

</style>
