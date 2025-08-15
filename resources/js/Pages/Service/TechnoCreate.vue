<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {useMainStore} from '@/Stores/MainStore.js';

const mainStore = useMainStore();

const form = useForm({
    product_id: mainStore.curProductID,
    title: '',
    order: 0,
});

const submit = () => {
    form.post(route('technos.store'), {
        onFinish: () => form.reset('title', 'order', 'product_id'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Create new techno" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="title" value="Техпроцесс" />

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
                <InputLabel for="order" value="Порядок" />

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
<!---
            <div class="mt-4">
                <InputLabel for="product_id" value="Продукт" />

                <TextInput
                    id="product_id"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.product_id"
                    required
                    autocomplete="product_id"
                />

                <InputError class="mt-2" :message="form.errors.product_id" />
            </div>
-->
            <div class="flex items-center justify-end mt-4">
                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Добавить
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
    </GuestLayout>
</template>
