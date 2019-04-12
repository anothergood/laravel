<template>

    <div class="container">
        <div class="chats">
            <div class="inbox_post">
                <div class="posts">
                    <div class="chat_list unselectable" v-for="post in posts"  >
                        <!-- v-bind="{ postSelect: post }" v-bind:class="{ active_post: postSelect.id == post.id  }" @click="makeActiveChat(post)" -->
                        <div class="post_ib">
                            <h5>{{post.title}}</h5>
                            <p>{{post.body}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        // props: ['user'],
        data : function(){
            return {
                user: {},
                nextPagePosts: '',
                posts: []
            }
        },
        created() {
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
                url: '/api/v1/posts/my',
                headers: { 'Authorization': 'Bearer ' + localStorage.access_token, 'hl': 'en' }
            })
            .then((response) => {
                this.nextPagePosts = response.data.next_page_url;
                this.posts.push(...response.data.data);
                console.log(response.data);
            });

        },

        watch: {

        },

        // mounted() {
        //     this.$nextTick(() => {
        //         console.log(this.users);
        //     });
        // },


        methods: {

        }
    }
</script>

<style>
    .chats {
        padding: 0;
    }
    .inbox_post {
        position: relative;
        height: 600px;
        border: 1px solid #c4c4c4;
        clear: both;
        overflow: hidden;
    }
    .posts {
        /* position: absolute;
        left:25;
        right:10;
        top:15;
        bottom:15; */
        height: 600px;
        overflow-y: scroll;
    }
    .chat_list {
        /* cursor: pointer; */
        border-bottom: 1px solid #c4c4c4;
        margin: 0;
        padding: 10px 10px 10px;
    }
    .post_ib h5{ font-size:15px; color:#464646; margin:0 0 8px 0;}
    .post_ib h5 span{ font-size:13px; float:right;}
    .post_ib p{ font-size:14px; color:#989898; margin:auto}
</style>
