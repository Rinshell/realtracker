<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    product_id: Number,
    events: Array,
    technos: Array,
    status: Array,
    editEnable: false,
});

const form = useForm({});

function destroy(id) {
    if(confirm("Are you sure to delete this event?")){
        form.delete(route('events.destroy', id), {
            onFinish: () => {
                    const event = new Event('inertia:deleteEvent');
                    document.dispatchEvent(event);
                },
            preserveScroll: true,
        });
    }
}

function getSN(event) {
    var sn = "0000" + event.sn_n;
    var sm = "000"  + event.sn_m;
    var sp = "000"  + event.sn_p;
    var n= '';
    if (event.sn_p >= 0) n+= sp.substring(sp.length-3) + '-';
    if (event.sn_m >= 0) n+= sm.substring(sm.length-3) + '-';
    return  n + sn.substring(sn.length-4);
}

function getTechno(event) {
    const tch= props.technos.find(el => el.id === event.techno_id);
    return tch.title;
}

function getStatus(event) {
    const sts= props.status.find(el => el.id === event.status_id);
    return sts ? sts.level : null;
}

</script>


<template>
    <div class="row">
        <div class="col-12">
            <div class="card-body">
                <div class="table-responsive">
                    <table v-if="events.length > 0" class="table-auto w-full border-collapse border border-slate-400">
                        <thead>
                            <tr class="bg-blue-100">
                                <th class="w-32 border border-slate-300">Date</th>
                                <th class="w-36 border border-slate-300">S/N</th>
                                <th class="border border-slate-300">Techno</th>
                                <th class="border border-slate-300">Title</th>
                                <th class="border border-slate-300">Files</th>
                                <th v-if="editEnable" class="border border-slate-300">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(event,key) in events" :key="key"
                                :class="{'bg-green-200' : (getStatus(event) == 'ok'),
                                         'bg-yellow-200': (getStatus(event) == 'warning'),
                                         'bg-red-200'   : (getStatus(event) == 'failure')
                                        }"
                            >
                                <td class="border border-slate-300 px-3">{{ event.date == '1990-01-01' ? '' : event.date }}</td>
                                <td class="border border-slate-300 px-3">{{ getSN(event) }}</td>
                                <td class="border border-slate-300 px-3">{{ getTechno(event) }}</td>
                                <td class="border border-slate-300 px-3" v-html="event.description"></td>
                                <td class="border border-slate-300 px-3">
                                    <a v-for="file in event.files" :href="'/storage/'+file.path">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                            <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625zM7.5 15a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5A.75.75 0 017.5 15zm.75 2.25a.75.75 0 000 1.5H12a.75.75 0 000-1.5H8.25z" clip-rule="evenodd" />
                                            <path d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z" />
                                        </svg>
                                    </a>
                                </td>
                                <td v-if="editEnable" class="border flex border-slate-300">
                                    <Link
                                        tabIndex="1"
                                        :href="route('events.edit', event.id)"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                        <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712zM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.933.933l2.685-.8a5.25 5.25 0 002.214-1.32l8.4-8.4z" />
                                        <path d="M5.25 5.25a3 3 0 00-3 3v10.5a3 3 0 003 3h10.5a3 3 0 003-3V13.5a.75.75 0 00-1.5 0v5.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V8.25a1.5 1.5 0 011.5-1.5h5.25a.75.75 0 000-1.5H5.25z" />
                                        </svg>
                                    </Link>

                                    <div
                                        @click="destroy(event.id)"
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
                </div>
            </div>
        </div>
    </div>
</template>