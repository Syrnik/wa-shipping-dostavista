<template>
    <section v-show="isActive"
             class="tab-content"
             style="border: 1px solid #ddd"
             :id="computedId"
    ><div class="block"><slot /></div>
    </section>
</template>

<script>
    export default {
        props: {
            id: { default: null },
            name: { required: true },
            prefix: { default: '' },
            suffix: { default: '' },
            isDisabled:{ default: false },
        },
        data: () => ({
            isActive: false,
            isVisible: true,
        }),
        computed: {
            header() {
                return this.prefix + this.name + this.suffix;
            },
            computedId() {
                return this.id ? this.id : this.name.toLowerCase().replace(/ /g, '-');
            },
            hash() {
                if (this.isDisabled) {
                    return '#';
                }
                return '#' + this.computedId;
            },
        },
    };
</script>