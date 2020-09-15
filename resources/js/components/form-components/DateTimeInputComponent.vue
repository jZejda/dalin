<template>
    <div>
        <datetime
            type="datetime"
            v-model="input_value"
            :name="input_name"
            :minute-step="15"
            format="dd.MM.yyyy HH:mm:ss"
            input-class="form-input-full">
        </datetime>
    </div>
</template>

<script>

    import Datetime from 'vue-datetime'
    import 'vue-datetime/dist/vue-datetime.css' // You need a specific loader for CSS files

    Vue.use(Datetime);

    export default {
        props: ['date_input_value', 'date_input_name'],
        data() {
            return {
                input_value: this.date_input_value,
                input_name: this.date_input_name,
            }
        },
    }
</script>
