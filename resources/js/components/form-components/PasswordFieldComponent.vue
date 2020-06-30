<template>
    <div class="flex justify-between">
        <div class="flex justify-start">
            <password
                    v-model="password"
                    :toggle="true"
                    :placeholder="'Vlož silné heslo'"
                    :defaultClass="'form-input-full'"
                    :badge="false"
                    @score="showScore"
                    @feedback="showFeedback"
            />
        </div>
    </div>
</template>

<script>

    import Password from 'vue-password-strength-meter'


    export default {
        components: {Password},
        data: () => ({
            password: null,
        })
    }
</script>