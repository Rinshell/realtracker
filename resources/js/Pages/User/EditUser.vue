<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const props = defineProps({
    IsEdit: Boolean,
    user: Object,
    userRole: Array,
    roles: Object,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    role: null,
});

const items = ref(null);

const submit = () => {
    const elems= items.value;
    const role= [];
    elems.forEach(function(elem) {
        if (elem.childNodes[0].checked) role.push(elem.childNodes[1].textContent);
    });
    form.role= role;
    if (props.IsEdit) {
        form.put(route('users.update', props.user.id), {
            preserveScroll: true,
            onSuccess: () => form.reset(),
            onError: () => {
                if (form.errors.password) {
                    form.reset('password', 'password_confirmation');
                    passwordInput.value.focus();
                }
            },
        });
    } else {
        form.post(route('users.store'), {
            preserveScroll: true,
            onSuccess: () => form.reset(),
            onError: () => {
                if (form.errors.password) {
                    form.reset('password', 'password_confirmation');
                    passwordInput.value.focus();
                }
            },
        });
    }
};

onMounted(() => {
    items.value.forEach(function(elem) {
        console.log(elem.childNodes[1].textContent);
    });    
});
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="props.IsEdit ? 'Edit user':'Create new user'" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{props.IsEdit ? 'Edit user':'Create new user'}}</h2>
        </template>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">    

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

                <div v-if="!props.IsEdit" class="mt-4">
                    <InputLabel for="password" value="New Password" />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full"
                        autocomplete="new-password"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div v-if="!props.IsEdit" class="mt-4">
                    <InputLabel for="password_confirmation" value="Confirm Password" />

                    <TextInput
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        autocomplete="new-password"
                    />

                    <InputError :message="form.errors.password_confirmation" class="mt-2" />
                </div>


                <div class="my-4">
                    <fieldset>      
                        <legend>Role</legend>
                        <div class="flex">
                            <div v-for="role in roles" class="px-2" ref="items">
                                <input type="checkbox" :name="role.name" :id="role.id" :value="role.id" :checked="userRole && userRole.includes(role.name)">
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
<!--                    
                    <Link
                        :href="route('users.index')"
                        method="get"
                        as="button"
                        class="ml-3 underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >Cancel
                    </Link>
-->
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
