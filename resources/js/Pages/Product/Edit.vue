<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';


const props = defineProps({
    product: Object,
});

const form = useForm({
    title: props.product.title,
    description: props.product.description,
});

var newfile= null;

function FileInput(event)
{
    if (event) {
        newfile= event.target.files[0];
    }
}

const submit = () => {
    form.put(route('products.update', props.product.id));
    router.post(route('products.update', props.product.id), {
        _method: 'put',
        title: form.title,
        description: form.description,
        file: newfile,
    });
};

</script>

<template>
    <Head title="Product edit" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit product
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">

                        <form name="editForm" @submit.prevent="submit">
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
                                        <InputLabel for="description" value="Примечание" />

                                        <TextInput
                                            id="description"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.description"
                                            required
                                            autocomplete="description"
                                        />

                                        <InputError class="mt-2" :message="form.errors.description" />
                                    </div>

                                    <div class="mt-4">
                                        <InputLabel value="Картинка" />
                                        <input type="file" @input="FileInput($event)" />
                                        <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                                            {{ form.progress.percentage }}%
                                        </progress>
                                        <InputError class="mt-2" :message="form.errors.file" />
                                    </div>
                                </div>
  
                                <div class="flex items-center justify-end mt-4">
                                    <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                        Сохранить
                                    </PrimaryButton>
                                    <Link
                                        :href="route('service')"
                                        method="get"
                                        as="button"
                                        class="ml-3 underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        >Cancel
                                    </Link>
                                </div>

                            </form>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>