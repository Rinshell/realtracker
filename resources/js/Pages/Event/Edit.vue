<script setup>
import EditEvent from './EditEvent.vue';

const props = defineProps({
    event: Object,
});

</script>

<template>
    <EditEvent
        :IsEdit="true"
        :id="props.event.id"
        :date="props.event.date"
        :product_id="props.event.product_id"
        :sn_n="props.event.sn_n"
        :sn_m="props.event.sn_m"
        :sn_p="props.event.sn_p"
        :description="props.event.description"
        :techno_id="props.event.techno_id"
        :status_id="props.event.status_id"
        :file="null"
        :active="props.event.active==1"
    >
    </EditEvent>
</template>