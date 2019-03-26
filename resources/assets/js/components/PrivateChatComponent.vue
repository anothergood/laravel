<template>
    <div class="container">

        <div class="messaging">
            <div class="inbox_msg">
                <div class="inbox_people">

                    <div class="inbox_chat">
                        <div class="chat_list" v-for="friend in friends" v-bind="{ userSelect: friend.id }" v-bind:class="{ active_chat: isActive }" @click="makeActive(friend)" >
                            <div class="chat_people">
                                <div class="chat_ib">
                                    <h5>{{friend.username}}
                                        <!-- <span class="chat_date">Dec 25</span> -->
                                    </h5>
                                    <!-- <p>Test, which is a new approach to have all solutions astrology under one roof.</p> -->
                                </div>
                            </div>
                        </div>
                        <p>User id: {{userSelect}}</p>
                    </div>
                </div>
                <div class="mesgs">
                    <div class="msg_history">

                        <div class="incoming_msg dataMessage" v-for="dataMessage in dataMessages">
                            <div class="received_msg">
                                <div class="received_withd_msg">
                                    <h6>{{dataMessage.from_user_username}}</h6>
                                    <p>{{dataMessage.body}}</p>
                                    <p>{{dataMessage.id}}</p>
                                    <!-- <span class="time_date"> 11:01 AM    |    June 9</span> -->
                                </div>
                            </div>
                        </div>

                        <!-- <div class="incoming_msg">
                            <div class="received_msg">
                                <div class="received_withd_msg">
                                    <h6>dcvxcv</h6>
                                    <p>Test which is a new approach to have all solutions</p>
                                    <span class="time_date"> 11:01 AM    |    June 9</span>
                                </div>
                            </div>
                        </div>
                        <div class="outgoing_msg">
                            <div class="sent_msg">
                                <h6>dcvxcv</h6>
                                <p>Test which is a new approach to have all solutions</p>
                                <span class="time_date"> 11:01 AM    |    June 9</span>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
            <div class="type_msg" v-if="userSelect">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Наберите сообщение" v-model="message">
                    <div class="input-group-append">
                        <button @click="sendMessage" class="btn btn-outline-secondary" type="button">Отправить</button>
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
                dataMessages: [],
                nextPage: "",
                message: "",
                friends: [],
                user: {},
                userSelect: "",
                isActive: false
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

        },

        watch: {
            user: function (val, oldVal) {
                Echo.connector.pusher.config.auth.headers['Authorization'] = 'Bearer ' + localStorage.access_token;
                Echo.private('channel.'+this.user.id)
                .listen('ChatPrivateMessage', ({data}) => {
                    if(this.userSelect == data.from_user_id){
                        this.dataMessages.push({body: data.message, from_user_username: data.from_user_username});
                    }
                    this.$nextTick(() => {
                        var container = this.$el.querySelector(".msg_history");
                        container.scrollTop = container.scrollHeight;
                    });
                });
            },
            userSelect: function (val, oldVal) {
                axios({
                    method: 'post',
                    url: '/api/v1/messages/'+this.userSelect+'/all',
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
            },
        },

        mounted() {
            const container = this.$el.querySelector(".msg_history");
            container.addEventListener('scroll', e => {
                if(container.scrollTop == 0 && this.nextPage !== null){
                    this.$nextTick(() => {
                        axios({
                            method: 'post',
                            url: this.nextPage,
                            headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                            params: { user_id: this.userSelect },
                        })
                        .then((response) => {
                            this.nextPage = response.data.messages.next_page_url;
                            this.dataMessages.unshift(...response.data.messages.data);
                            this.$nextTick(() => {
                                var container = this.$el.querySelector(".msg_history");     //??????????????
                                // container.scrollTop = container.scrollHeight;            //??????????????
                            });
                        });
                    });
                }
            });
        },

        methods: {
            sendMessage: function () {
                axios({
                    method: 'post',
                    url: '/api/v1/chat/send-private-message/'+this.userSelect,
                    headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                    params: { message: this.message, from_user_id: this.user.id, from_user_username: this.user.username }
                })
                .then((response) => {
                    this.dataMessages.push({body: this.message, from_user_username: this.user.username});
                    this.message = "";
                    this.$nextTick(() => {
                        var container = this.$el.querySelector(".msg_history");
                        container.scrollTop = container.scrollHeight;
                    });
                });
                // axios({
                //     method: 'post',
                //     url: '/api/v1/messages/'+this.userSelect+'/send',
                //     headers: { 'Authorization': 'Bearer ' + localStorage.access_token },
                //     params: { body: this.message }
                // });
            },
            makeActive: function (friend) {
                this.userSelect = friend.id;
            },
        }
    }
</script>


<style>
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
    .inbox_chat { height: 550px; overflow-y: scroll;}

    .active_chat{ background:#ebebeb;}

    .incoming_msg_img {
    display: inline-block;
    width: 6%;
    }
    .received_msg {
    display: inline-block;
    padding: 0 0 0 0px;
    vertical-align: top;
    width: 92%;
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
    .received_withd_msg { width: 57%; margin-bottom: 6px;}
    .mesgs {
    float: left;
    padding: 15px 15px 0 25px;
    width: 60%;
    }

    .sent_msg p {
    background: #05728f none repeat scroll 0 0;
    border-radius: 3px;
    font-size: 14px;
    margin: 0; color:#fff;
    padding: 5px 10px 5px 12px;
    width:100%;
    }
    .outgoing_msg{ overflow:hidden; margin:26px 0 26px;}
    .sent_msg {
    float: right;
    width: 46%;
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
    .messaging { padding: 0 0 50px 0;}
    .msg_history {
    height: 516px;
    overflow-y: scroll;
    }
</style>
