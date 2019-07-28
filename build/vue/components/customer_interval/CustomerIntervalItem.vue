<template>
    <tr>
        <td class="nowrap">с <input type="text"
                     class="short numerical"
                     :name="addns('from', ns)"
                     v-model="value.from"> : <input type="text"
                                                    class="short numerical"
                                                    :name="addns('from_m', ns)"
                                                    v-model="value.from_m"></td>
        <td class="nowrap">до <input
                type="text"
                class="short numerical"
                :name="addns('to', ns)"
                v-model="value.to"> : <input type="text"
                                               class="short numerical"
                                               :name="addns('to_m', ns)"
                                               v-model="value.to_m"></td>
        <td v-for="v in [1,2,3,4,5,6,7]"><input type="checkbox" :name="addns('', addns('day', ns))" :value="v" v-model="value.day"></td>
        <td v-if="holiday"><input type="checkbox" :name="addns('holiday', ns)" v-model="value.holiday"></td>
        <td v-if="workday"><input type="checkbox" :name="addns('workday', ns)" v-model="value.workday"></td>
        <td><a class="inline-link"
               @click.prevent="$emit('delete')"><i class="icon16 delete"></i></a></td>
    </tr>
</template>

<script>
    import AddNs from '../wa-namespace'

    export default {
        props: {
            ns: {type: String, default: ''},
            holiday: {type: Boolean, default: true},
            workday: {type: Boolean, default: true},
            value: {
                type: Object, default() {
                    return {
                        from: '10',
                        from_m: '00',
                        to: '12',
                        to_m: '00',
                        day: [1, 2, 3, 4, 5],
                        workday: false,
                        holiday: false
                    };
                }
            }
        },
        mixins: [AddNs]
    }
</script>
