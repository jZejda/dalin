<template>
    <!-- Content -->
    <div class="flex-1 flex flex-row">

        <div class="w-full">

            <div class="py-1 items-center">

            </div>

            <div class="px-6 items-center">
            <p class="pt-4 text-2xl">{{ data.event.name }} - výsledky</p>

                <div class="flex justify-between text-sm lg:text-base">
                    <button @click="sh_filters = !sh_filters" class='relative bg-green-500 text-white px-2 rounded overflow-visible'>
                        Fitr kategorie
                        <div v-if="onlyLegs.length != 0" class="absolute top-0 right-0 -mt-3 -mr-3 rounded-full h-4 w-4 flex bg-red-500 items-center justify-center text-xs">{{ onlyLegs.length }}</div>
                    </button>
                    <span v-show="oevent_url != ''">
                        <a v-bind:href="oevent_url" class="rounded-md px-2 border border-gray-400">Web závodu</a>
                    </span>
                    <button v-on:click="readEventIofv3Result" class="rounded-md px-2 border border-gray-400">Aktualizuj výsledky</button>
                </div>


                <div v-show="sh_filters" class="flex flex-wrap mt-2">
                    <span v-for="category in computedCategory" class="text-sm lg:text-base mr-1 mb-2">
                    <span v-if="onlyLegs.includes(category)" >
                        <span v-on:click="legFilterToggle(category)" class="rounded-md px-1 border border-gray-300 bg-green-500 text-gray-100">{{ category }}</span>
                    </span>
                    <span v-else>
                        <span v-on:click="legFilterToggle(category)" class="rounded-md px-1 border border-gray-400">{{ category }}</span>
                    </span>
                </span>
                </div>


                <!-- data.resultData -->
                <div v-for="(legs, legIndex) in onlySpecificLegs" class="bg-white text-sm lg:text-base">

                    <p class="px-2 text-2xl border-t-4 border-green-500 mt-2">{{ legs.classCourseData.courseName}}</p>
                    <p class="px-2">převýšení: {{ legs.classCourseData.courseClimb}} m | vzdálenost: {{ legs.classCourseData.courseLenght | toKmLenght}} km | kontrol: {{ legs.classCourseData.courseNumControls}}</p>
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
        props: ['result_id', 'oevent_url'],
        data() {
            return {
                data: [],
                onlyLegs:[],
                sh_filters:false,
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
            legFilterToggle: function (category) {
                // add/remove catogory

                if(this.onlyLegs.includes(category)){
                    const index = this.onlyLegs.indexOf(category);
                    if (index > -1) {
                        this.onlyLegs.splice(index, 1);
                    }
                } else {
                    this.onlyLegs.push(category);
                }

            }
        },
        computed: {

            buttonCategory: function(category){

                let filteredCategory = [];
                return filteredCategory.push(category);
            },

            computedCategory: function() {
                // create array Category from result data
                let resultCategory = this.data.resultData;
                //ES6 Object.keys
                return Object.keys(resultCategory);
            },

            onlySpecificLegs: function (){

                let personResultData = this.data.resultData;

                let filteredResult = [];

                if(this.onlyLegs == 0) {
                    filteredResult = personResultData;
                } else {
                    var result = [];

                    for(var i=0; i<this.onlyLegs.length; i++) {
                        var index = this.onlyLegs[i];
                        filteredResult.push(personResultData[index]);
                  }
                }

              return filteredResult;
          }
        },
        filters: {

            toKmLenght: function(value) {
                if (!value) return ''
                value = value.toString()
                return value / 1000
            },
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



