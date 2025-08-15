<script setup>
import EditUser from './EditUser.vue';
const props = defineProps({
    roles: Object,
});
</script>

<template>
    <EditUser
        :IsEdit="false"
        user=""
        :userRole="null"
        :roles="props.roles"
    >
    </EditUser>
</template>