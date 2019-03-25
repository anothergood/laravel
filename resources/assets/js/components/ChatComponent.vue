<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="form-group mb-3">
                    <textarea rows="6" readonly="" class="form-control">{{dataMessages.join('\n')}}</textarea>
                </div>
                <div class="input-group mb-3">
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
                message: "",
            }
        },
        mounted() {
            window.Echo.channel('test')
                .listen('ChatMessage', (e) => {
                    this.dataMessages.push(e.message)
            });
        },
        methods: {
            sendMessage: function () {

                axios({
                    method: 'get',
                    url: '/api/v1/chat/send-message',
                    params: { message: this.message }
                })
                .then((response) => {
                    this.message = "";
                    this.dataMessage = "";
                });
            }
        }
    }
</script>
