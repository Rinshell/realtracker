<script setup>
import EditRole from './EditRole.vue';

const props = defineProps({
    role: Object,
    rolePermissions: Array,
    permissions: Object,
});
</script>

<template>
    <EditRole
        :IsEdit="true"
        :id="props.role.id"
        :name="props.role.name"
        :rolePermissions="props.rolePermissions"
        :permissions="props.permissions"
    >
    </EditRole>
</template>