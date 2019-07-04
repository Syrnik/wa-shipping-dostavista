<template>
    <div class="field">
        <div class="name" v-if="name.length" v-html="name"></div>
        <slot/>
    </div>
</template>

<script>
    export default {
        name: "wa-field",
        props: {name: {default: ''}}
    }
</script>
