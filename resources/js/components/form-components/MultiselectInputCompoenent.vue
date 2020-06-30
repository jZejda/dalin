<template>
    <div>
        <multiselect v-model="value" deselect-label="Can't remove this value" track-by="name" label="name" placeholder="Select one" :options="options" :searchable="false" :allow-empty="false">
            <template slot="singleLabel" slot-scope="{ option }"><strong>{{ option.name }}</strong> is written in<strong>  {{ option.language }}</strong></template>
        </multiselect>
        <pre class="language-json"><code>{{ value  }}</code></pre>
        <input type="text" name="lat">{{ value }}</input>
    </div>
</template>

<script>

    import Multiselect from 'vue-multiselect'
    import 'vue-multiselect/dist/vue-multiselect.min.css'

    // register globally
    Vue.component('multiselect', Multiselect)

    export default {
        props: ['date_input_value', 'date_input_name'],
        data () {
            return {
                value: null,
                options: [
                    { name: 'Vue.js', language: 'JavaScript' },
                    { name: 'Rails', language: 'Ruby' },
                    { name: 'Sinatra', language: 'Ruby' },
                    { name: 'Laravel', language: 'PHP', $isDisabled: true },
                    { name: 'Phoenix', language: 'Elixir' }
                ]
            }
        }
    }
</script>
