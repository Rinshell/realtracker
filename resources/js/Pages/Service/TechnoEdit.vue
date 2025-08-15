<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    techno: Object,
});

const form = useForm({
    title: props.techno.title,
    order: props.techno.order,
    product_id: props.techno.product_id,
});

const submit = () => {
    form.put(route('technos.update', props.techno.id));
};

</script>

<template>
    <Head title="Techno edit" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit techno
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">

                        <div className="flex items-center justify-between mb-6">
                            <Link
                                className="px-6 py-2 text-white bg-blue-500 rounded-md focus:outline-none"
                                :href="route('service')"
                            >
                                Back
                            </Link>
                        </div>

                        <form name="createForm" @submit.prevent="submit">
                                <div className="flex flex-col">
                                    <div>
                                        <InputLabel for="title" value="Название" />

                                        <TextInput
                                            id="title"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.title"
                                            required
                                            autofocus
                                            autocomplete="title"
                                        />

                                        <InputError class="mt-2" :message="form.errors.title" />
                                    </div>

                                    <div class="mt-4">
                                        <InputLabel for="order" value="Описание" />

                                        <TextInput
                                            id="order"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.order"
                                            required
                                            autocomplete="order"
                                        />

                                        <InputError class="mt-2" :message="form.errors.order" />
                                    </div>

                                </div>
  
                                <div className="mt-4">
                                    <button
                                        type="submit"
                                        className="px-6 py-2 font-bold text-white bg-green-500 rounded"
                                    >
                                        Save
                                    </button>
                                </div>
                            </form>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>