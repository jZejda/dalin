<template>
    <div class="w-full">
        <textarea class="form-input-full" id="editorial" name="editorial"
                  v-on:keyup="countdown"
                  v-model="message"
                  placeholder="úvodník novinky">
        </textarea>
        <p class='text-right text-grey-dark' v-bind:class="{'text-red-dark': hasError }">{{remainingCount}}</p>
    </div>
</template>

<script>

    export default {
        props: ['message'],
        data() {
            return {
                maxCount: 220,
                remainingCount: 220,
               // message: '',
                hasError: false
            }
        },
        methods: {
            countdown: function() {
                this.remainingCount = this.maxCount - this.message.length;
                this.hasError = this.remainingCount < 0;
            }
        }
    }

</script>