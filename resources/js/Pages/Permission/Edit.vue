<script setup>
import EditPermission from './EditPermission.vue';

const props = defineProps({
    permission: Object
});
</script>

<template>
    <EditPermission
        :IsEdit="true"
        :id="props.permission.id"
        :name="props.permission.name"
    >
    </EditPermission>
</template>