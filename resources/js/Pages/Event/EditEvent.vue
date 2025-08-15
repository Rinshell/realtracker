<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Combobox from '@/Components/Combobox.vue';
import TextArea from '@/Components/TextArea.vue';
import TextInput from '@/Components/TextInput.vue';
import { computed, ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
//import { Inertia } from "@inertiajs/inertia";
import Datepicker from '@vuepic/vue-datepicker';
//import { ru } from 'date-fns/locale';
import '@vuepic/vue-datepicker/dist/main.css';
import {useMainStore} from '@/Stores/MainStore.js';

const mainStore = useMainStore();

const props = defineProps({
    IsEdit: Boolean,
    id: Number,
    date: String,
    product_id: Number,
    sn_n: Number,
    sn_m: Number,
    sn_p: Number,
    description: String,
    techno_id: Number,
    status_id: Number,
    active: Boolean,
    file: Object,
});

const date = ref(props.IsEdit ? props.date : new Date());
const format = (date) => {
    console.log("format:"+date);
    form.date= date.toISOString().split("T")[0];
    const day = date.getDate();
    const month = date.getMonth() + 1;
    const year = date.getFullYear();
    return (day > 9 ? day : "0"+day)+"-"+(month > 9 ? month : "0"+month)+"-"+year;
}

const form = useForm({
    date: props.date,
    product_id: props.product_id,
    sn_n: props.sn_n,
    sn_m: props.sn_m,
    sn_p: props.sn_p,
    description: props.description,
    techno_id: props.techno_id,
    status_id: props.status_id,
    active: props.active,
    file: null,
});

function getTechnoNames() {
    const arr= [];
    mainStore.curTechnos?.forEach((el) => { if (el.deleted_at == null) arr.push(el.title); });
    return arr;
}

const proxyTechnoID = computed({
    get() {
        var Index= false;
        var t= mainStore.curTechnos?.find((el, key) =>  {
            if (el.id === form.techno_id)  {
                Index= key;
                return true;
            } else {
                return false;
            }
        });
        return Index;
    },
    set(val) {
        form.techno_id= mainStore.curTechnos[val].id;
    },
});

function getCurStatus()
{
    return mainStore.curStatus?.filter(el => (el.techno_id == form.techno_id && el.deleted_at == null));
}

function getStatusNames() {
    const arr= [];
    const sts= getCurStatus();
    sts?.forEach((el) => { arr.push(el.title); });
    return arr;
}

const proxyStatusID = computed({
    get() {
        const sts= getCurStatus();
        var Index= false;
        var t= sts?.find((el, key) =>  {
            if (el.id === form.status_id)  {
                Index= key;
                return true;
            } else {
                return false;
            }
        });
        return Index;
    },
    set(val) {
        const sts= getCurStatus();
        form.status_id= sts[val].id;
    },
});

var newfile= null;

function FileInput(event)
{
    if (event) {
//        console.log("FileInput:" + event.target.files[0]);
        newfile= event.target.files[0];
    } else {
        console.log("FileInput:" + event);
    }
}
/*
const proxyFile = computed({
    get() {
        console.log("proxyFile get");
        return null;
    },
    set(val) {
        console.log("proxyFile set:" + val);
        form.file= val[0];
    },
});
*/
const submit = () => {
    if (form.description === null) form.description= '';
    if (props.IsEdit) {
        router.post(route('events.update', props.id), {
            _method: 'put',
            date: form.date,
            product_id: form.product_id,
            sn_n: form.sn_n,
            sn_m: form.sn_m,
            sn_p: form.sn_p,
            description: form.description,
            techno_id: form.techno_id,
            status_id: form.status_id,
            active: form.active,
            file: newfile,
        });
    } else {
        form.file= newfile;
        form.post(route('events.store'), {
//            onFinish: () => form.reset('description'),
//            preserveScroll: (page) => Object.keys(page.props.errors).length,
//            preserveScroll: true,
        });
    }
};

</script>

<template>
    <Head :title="props.IsEdit ? 'Edit event':'Create new event'" />
    <AuthenticatedLayout>
        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <form @submit.prevent="submit">
                        <div class="flex flex-col">
                            <div>
                                <InputLabel for="date" value="Дата" />
                                <Datepicker
                                    id="date"
                                    v-model="date"
                                    :format="format"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.date" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="sn_p"  value="Серийный номер" />
                                <div class="flex flex-row">
                                    <TextInput
                                        id="sn_p"
                                        type="text"
                                        class="mt-1 basis-1/4 w-full"
                                        v-model="form.sn_p"
                                        autocomplete="sn_p"
                                    />
                                    <TextInput
                                        id="sn_m"
                                        type="text"
                                        class="mt-1 basis-1/4 w-full"
                                        v-model="form.sn_m"
                                        autocomplete="sn_m"
                                    />
                                    <TextInput
                                        id="sn_n"
                                        type="text"
                                        class="mt-1 basis-1/2 w-full"
                                        v-model="form.sn_n"
                                        required
                                        autocomplete="sn_n"
                                    />
                                </div>
                                <InputError class="mt-2" :message="form.errors.sn_p" />
                                <InputError class="mt-2" :message="form.errors.sn_m" />
                                <InputError class="mt-2" :message="form.errors.sn_n" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="description" value="Описание" />
                                <TextArea
                                    id="description"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.description"
                                    autocomplete="description"
                                    rows="3"
                                />
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="techno_id" value="Техпроцес" />
                                <Combobox 
                                    id="techno_id"
                                    class= "mt-1" 
                                    :items="getTechnoNames()" 
                                    v-model:selected="proxyTechnoID"
                                    required
                                    autocomplete="techno_id"
                                />

                                <InputError class="mt-2" :message="form.errors.techno_id" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="status_id" value="Статус" />
                                <Combobox 
                                    id="status_id"
                                    class= "mt-1" 
                                    :items="getStatusNames()" 
                                    v-model:selected="proxyStatusID"
                                    autocomplete="status_id"
                                />

                                <InputError class="mt-2" :message="form.errors.status_id" />
                            </div>

                            <div class="mt-4">
                                <label v-if="props.IsEdit && props.file">old file: {{props.file}}</label>
                                <input type="file" @input="FileInput($event)" />
                                <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                                    {{ form.progress.percentage }}%
                                </progress>

                                <InputError class="mt-2" :message="form.errors.file" />
                            </div>


                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    {{props.IsEdit ? "Сохранить" : "Добавить"}}
                                </PrimaryButton>
                                <Link
                                    :href="route('product', mainStore.curProductID)"
                                    method="get"
                                    as="button"
                                    class="ml-3 underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >Cancel
                                </Link>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
