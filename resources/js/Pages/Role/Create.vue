<script setup>
import EditRole from './EditRole.vue';

const props = defineProps({
    permissions: Object,
});
</script>

<template>
    <EditRole
        :IsEdit="flse"
        name=""
        :permissions="props.permissions"
    >
    </EditRole>
</template>