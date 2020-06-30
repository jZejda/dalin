<template>
    <div>
        <button @click="toggleModalDelete">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
        </button>

        <!-- Modal Update Task -->
        <div v-if="modalDelete" @click.self="toggleModalDelete" class="modal-remove bg-smoke">
            <div
                class="fixed shadow-inner max-w-md md:relative pin-b pin-x align-top m-auto justify-end md:justify-center p-8 bg-white md:rounded w-full md:h-auto md:shadow flex flex-col">

                <h2 class="text-4xl text-center font-hairline md:leading-loose text-grey md:mt-8 mb-4">Budeme Mazat</h2>
                <p class="text-xl leading-normal mb-8 text-center">
                    Objekt {{ del_name }}
                </p>
                <p class="background-purple-50">Toto je modal 2</p>

                <div class="inline-flex justify-center">
                    <button
                        class="bg-grey-lighter flex-1 border-b-2 md:flex-none border-green ml-2 hover:bg-green-lightest text-gray-700 font-bold py-4 px-6 rounded">
                        Upravit Kategorii
                    </button>
                    <button
                        class="bg-grey-lighter flex-1 md:flex-none border-b-2 border-red ml-2 hover:bg-red-500 text-gray-700 font-bold py-4 px-6 rounded">
                        Zavřít
                    </button>
                </div>
                <span @click="toggleModalDelete" class="absolute pin-t pin-r pt-4 px-4">
                    <svg class="h-12 w-12 text-gray-700 hover:text-black" role="button"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <title>Close</title>
                        <path
                            d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                    </svg>
                </span>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: ['id', 'del_name'],
    data() {
        return {
            name: 'Jmeno',
            modalDelete: false,
        }
    },
    methods: {
        toggleModalDelete() {
            this.modalDelete = !this.modalDelete
        },
    }
}

</script>
