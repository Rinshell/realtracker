<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import EditEvent from './EditEvent.vue';
import {useMainStore} from '@/Stores/MainStore.js';

const mainStore = useMainStore();

const props = defineProps({
    sn: 0,
});

</script>

<template>
    <EditEvent
        :IsEdit="false"
        :id="0"
        :date="null"
        :product_id="mainStore.curProductID"
        :sn_n= "props.sn % 10000"
        :sn_m= "Math.floor((props.sn % 10000000) / 10000)"
        :sn_p= "Math.floor(props.sn  / 10000000)"
        :file= "null"
        description=""
        :techno_id="0"
        :status_id="0"
        :active="true"
    >
    </EditEvent>
</template>
