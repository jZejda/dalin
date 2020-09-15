<template>
    <div :class="color" class="text-white rounded-full h-10 w-10 flex items-center justify-center">{{ name }}</div>
</template>

<script>
    export default {
        props: ['name','color','picture'],
    }
</script>
