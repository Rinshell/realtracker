<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:selected']);

const props = defineProps({
    selected: {
        type: [Number, Boolean],
        default: false,
    },
    items: Array,
});

const proxySelected = computed({
    get() {
        return props.selected;
    },

    set(val) {
        emit('update:selected', val);
    },
});
</script>

<template>
        <select v-model="proxySelected">
            <option v-for="(item, key) in items" :value="key" :key="key">{{item}}</option>
        </select>
</template>
