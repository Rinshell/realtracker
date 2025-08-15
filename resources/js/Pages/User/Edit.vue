<script setup>
import EditUser from './EditUser.vue';

const props = defineProps({
    user: Object,
    userRole: Array,
    roles: Object,
});
</script>

<template>
    <EditUser
        :IsEdit="true"
        :user="props.user"
        :userRole="props.userRole"
        :roles="props.roles"
    >
    </EditUser>
</template>