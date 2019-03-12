<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <textarea roes="6" readonly="" class="form-control">{{dataMessages.join('/n')}}</textarea>
            </div>
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Наберите сообщение" v-model="message">
                <div class="input-group-append">
                    <button @click="sendMessage" class="btn btn-outline-secondary" type="button">Отправить</button>
                </div>
            </div>
        </div>


        <!-- <div class="row justify-content-center">
          <div class="container" style="padding-top: 20px; width: 50%">
            <div class="row">
              <div class="col-md-12"><textarea readonly="" class="form-control" rows="20" style="resize:vertical;">{{dataMessages.join('/n')}}</textarea></div>
            </div>
            <div class="row" style="padding-top: 10px;">
              <div class="col-md-10"><input type="text" class="form-control" placeholder="Текст сообщения" v-model="message"></div>
              <div class="col-md-2"><button @click="sendMessage" type="submit" class="btn btn-success">Отправить</button></div>
            </div>
          </div>
        </div> -->
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }

    // export default {
    //     data : function(){
    //         return {
    //               dataMessages: [],
    //               message: "",
    //         }
    //     },
    //     mounted() {
    //         var socket = io.connect ('http:192.168.1.125');
    //
    //         socket.on("news-action:App\\Events\\ChatMessage", function(data){
    //             this.dataMessages.push(data.message)
    //         }.bind(this));
    //     },
    //     methods: {
    //         sendMessage: function () {
    //
    //             axios({
    //                 method: 'get',
    //                 url: '/start/send-message',
    //                 params: { message: this.message }
    //             })
    //             .then((response) => {
    //                 this.message = "";
    //             });
    //         }
    //     }
    // }
</script>
