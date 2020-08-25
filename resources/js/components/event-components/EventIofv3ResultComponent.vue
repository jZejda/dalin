<template>
    <!-- Content -->
    <div class="flex-1 flex flex-row">

        <div class="w-full">

            <div class="px-6 py-1 items-center bg-gray-200 border-b">

            </div>

            <div class="px-6 py-1 items-center bg-gray-100">

                <table class="table-fixed w-full" v-for="(legs, legIndex) in data.resultData">

                    <thead>
                    <tr>
                        <th class="w-2/12 px-4 py-2 text-left">{{ legIndex }}</th>
                        <th class="w-4/12 px-4 py-2 text-left">regc</th>
                        <th class="w-4/12 px-4 py-2 text-left">jméno</th>
                        <th class="w-2/12 px-4 py-2 text-left">oddíl</th>
                        <th class="w-2/12 px-4 py-2 text-left">cas</th>
                        <th class="w-2/12 px-4 py-2 text-left">ztrata</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(personResult, personEntryId) in legs.personCourseData">
                            <td class="px-4 py-2 bg-white">{{ personResult.personResultPosition}} </td>
                            <td class="px-4 py-2 bg-white">{{ personResult.personId}}</td>
                            <td class="px-4 py-2 bg-white">{{ personResult.familyName}} {{ personResult.givenName}}</td>
                            <td class="px-4 py-2 bg-white">{{ personResult.personOrgShortName}}</td>
                            <td class="px-4 py-2 bg-white">{{ personResult.personResultTime | secondToMinutes }}</td>
                            <td class="px-4 py-2 bg-white">{{ personResult.personResultTimeBehind | secondToMinutesLoss}}</td>
                        </tr>
                    </tbody>

                </table>

            </div>

        </div>
    </div>
</template>

<script>

    import moment from 'moment';

    export default {
        props: [],
        data() {
            return {
                data: [],
                test:'',
                onlyLegs:['D35', 'H35'],

            }
        },
        mounted() {
            this.readEventIofv3Result();
        },
        methods: {
            readEventIofv3Result(){
                axios.get('/admin/link/oevent/eventIofv3Result')
                    .then(response => {
                        this.data = response.data.sorted_data;
                    });
            },
        },
        computed: {
          onlySpecificLegs: function (){
              let personResultData = this.data.resultData;

              var result = [];
              for(var i=0; i<this.onlyLegs.length; i++) {
                  var index = this.onlyLegs[i];
                  result.push(personResultData[index]);
              }

              return result;
          }
        },
        filters: {
            secondToMinutes: function(value) {
                return (Math.round(((value / 60) + Number.EPSILON) * 100) / 100).toFixed(2);
            },
            secondToMinutesLoss: function (value) {
                if (value == 0 ){
                    return '';
                } else {
                    return '+ '+(Math.round(((value / 60) + Number.EPSILON) * 100) / 100).toFixed(2)
                }
            }
        }
    };


</script>

<style>
    table {
        border-collapse:separate;
        border-spacing: 0 4px;
    }
</style>



