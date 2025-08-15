<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import {useMainStore} from '@/Stores/MainStore.js';

const mainStore = useMainStore();

const props = defineProps({
    status: Array,
    technos: Array,
});

const form = useForm({});

const proxyStatus = computed(() => props.status.filter(el => el.techno_id === mainStore.curTechnoID));

function destroy(id) {
    if(confirm("Are you sure to delete this status?")){
        form.delete(route('status.destroy', id));
    }
}

</script>


<template>
    <div class="row">
        <div class="col-12">
            <div class="card-body">
                <div class="table-responsive">
                    <table v-if="proxyStatus.length > 0" class="ml-5 table-auto border-collapse border border-slate-400">
                        <thead>
                            <tr class="bg-blue-100">
<!--                                <th class="w-10 border border-slate-300">ID</th> -->
                                <th class="w-auto border border-slate-300">Title</th>
                                <th class="w-28 border border-slate-300">Level</th>
                                <th class="w-28 border border-slate-300">Order</th>
                                <th class="border border-slate-300">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(st,key) in proxyStatus" :key="key">
<!--                                <td class="border border-slate-300 text-center">{{ st.id }}</td>  -->
                                <td class="border border-slate-300 px-3">{{ st.title }}</td>
                                <td class="border border-slate-300 px-3">{{ st.level }}</td>
                                <td class="border border-slate-300 text-center">{{ st.order }}</td>
                                <td class="border flex border-slate-300">
                                    <Link
                                        tabIndex="1"
                                        :href="route('status.edit', st.id)"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                        <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712zM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.933.933l2.685-.8a5.25 5.25 0 002.214-1.32l8.4-8.4z" />
                                        <path d="M5.25 5.25a3 3 0 00-3 3v10.5a3 3 0 003 3h10.5a3 3 0 003-3V13.5a.75.75 0 00-1.5 0v5.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V8.25a1.5 1.5 0 011.5-1.5h5.25a.75.75 0 000-1.5H5.25z" />
                                        </svg>
                                    </Link>

                                    <div
                                        @click="destroy(st.id)"
                                        tabIndex="-1"
                                        class="cursor-pointer"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 fill-red-600">
                                        <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </div>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-else colspan="4" align="center">
                        No status found
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>