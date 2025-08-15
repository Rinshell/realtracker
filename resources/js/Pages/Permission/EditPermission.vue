<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    IsEdit: Boolean,
    id: Number,
    name: String,
});

const form = useForm({
    name: props.name,
});

const submit = () => {
    if (props.IsEdit) {
        form.put(route('permissions.update', props.id), {
            onFinish: () => form.reset('name'),
        });
    } else {
        form.post(route('permissions.store'), {
            onFinish: () => form.reset('name'),
        });
    }
};

</script>

<template>
    <AuthenticatedLayout>
        <Head :title="props.IsEdit ? 'Edit permissions':'Create new permissions'" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{props.IsEdit ? 'Edit permissions':'Create new permissions'}}</h2>
        </template>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="name" value="Название" />

                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                    />

                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Сохранить
                    </PrimaryButton>
                    <Link
                        :href="route('permissions.index')"
                        method="get"
                        as="button"
                        class="ml-3 underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >Cancel
                    </Link>
                </div>
            </form>
<!--

            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="name" value="Name" />

                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                    />

                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="mt-4">
                    <InputLabel for="email" value="Email" />

                    <TextInput
                        id="email"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        required
                        autocomplete="email"
                    />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="my-4">
                    <fieldset>      
                        <legend>Role</legend>
                        <div class="flex">
                            <div v-for="role in roles" class="px-2">
                                <input type="checkbox" :name="role.name" :id="role.id" :value="role.id" :checked="userRole.includes(role.name)">
                                <label :for="role.id" class="pl-2">{{role.name}}</label>
                            </div>
                        </div>
                    </fieldset>                        
                    <InputError class="mt-2" :message="form.errors.role" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Добавить
                    </PrimaryButton>
                    <Link
                        :href="route('users.index')"
                        method="get"
                        as="button"
                        class="ml-3 underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >Cancel
                    </Link>
                </div>
            </form>
-->
        </div>
    </AuthenticatedLayout>
</template>
