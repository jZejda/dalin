<template>
    <!-- Content -->
    <div class="flex-1 flex flex-row">

        <div class="w-full">

            <div class="py-1 items-center">

            </div>

            <div class="px-6 items-center">
            <p class="pt-4 text-2xl">{{ data.event.name }} - výsledky</p>

                <div v-for="(legs, legIndex) in data.resultData" class="bg-white text-sm lg:text-base">

                    <p class="px-2 text-2xl border-t-4 border-green-500 mt-4">{{ legs.classCourseData.courseName}}</p>
                    <p class="px-2">převýšení: {{ legs.classCourseData.courseClimb}}, délka: {{ legs.classCourseData.courseLenght}} kontrol: {{ legs.classCourseData.courseNumControls}}</p>
                    <table class="table-fixed w-full mb-12">

                        <thead>
                        <tr class="bg-gray-400 text-sm lg:text-base">
                            <th class="w-1/12 px-2 py-1 text-left"></th>
                            <th class="w-2/12 px-2 text-left">reg.č.</th>
                            <th class="w-5/12 lg:w-4/12 px-2 text-left">jméno</th>
                            <th class="hidden lg:block lg:visible lg:w-4/12 px-2 text-left">oddíl</th>
                            <th class="w-2/12 lg:w-1/12 px-2 text-left">čas</th>
                            <th class="w-2/12 lg:w-1/12 px-2 text-left"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(personResult, personEntryId) in legs.personCourseData" class="text-sm lg:text-base">
                            <td class="px-2 m-b-2 border-b-2 border-gray-400">{{ personResult.personResultPosition}}</td>
                            <td class="px-2 border-b-2 border-gray-400">{{ personResult.personId}}</td>
                            <td class="px-2 border-b-2 border-gray-400">{{ personResult.familyName}} {{ personResult.givenName}}</td>
                            <td class="hidden lg:block px-2 border-b-2 border-gray-400">{{ personResult.personOrgLongname}}</td>
                            <td class="px-2 border-b-2 border-gray-400">{{ personResult.personResultTime | secondToMinutes }}</td>
                            <td class="px-2 border-b-2 border-gray-400 text-gray-600">{{ personResult.personResultTimeBehind | secondToMinutesLoss}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    import moment from 'moment';

    export default {
        props: ['result_id'],
        data() {
            return {
                data: [],
                test:'',
                onlyLegs:['H35'],

            }
        },
        mounted() {
            this.readEventIofv3Result();
        },
        methods: {
            readEventIofv3Result(){
                //axios.get('/admin/link/oevent/eventIofv3Result')
                axios.get('/app-api/event-ifoxml-result/'+this.result_id)
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
                    return '+'+(Math.round(((value / 60) + Number.EPSILON) * 100) / 100).toFixed(2)
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



