<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const props = defineProps({
    IsEdit: Boolean,
    id: Number,
    name: String,
    rolePermissions: Array,
    permissions: Object,
});

const form = useForm({
    name: props.name,
    permission: null,
});

const items = ref(null);

const submit = () => {
    const elems= items.value;
    const perm= [];
    elems.forEach(function(elem) {
        if (elem.childNodes[0].firstChild.checked) perm.push(elem.childNodes[1].firstChild.textContent);
    });
    form.permission= perm;
    if (props.IsEdit) {
        form.put(route('roles.update', props.id), {
            onFinish: () => form.reset('name'),
        });
    } else {
        form.post(route('roles.store'), {
            onFinish: () => form.reset('name'),
        });
    }
};


onMounted(() => {
/*    items.value.forEach(function(elem) {
        console.log(elem.childNodes[1].firstChild.data);
    });    */
});
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="props.IsEdit ? 'Edit role':'Create new role'" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{props.IsEdit ? 'Edit role':'Create new role'}}</h2>
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

                <InputLabel for="permissions">Assign Permissions</InputLabel>

                <table class="table-auto border-collapse border-slate-400">
                    <thead>
                        <th class="border border-slate-300"><input type="checkbox" name="all_permission"></th>
                        <th class="border border-slate-300">Name</th>
                        <th class="border border-slate-300">Guard</th> 
                    </thead>

                    <tr v-for="permission in permissions" ref="items">
                        <td class="border border-slate-300 px-3">
                            <input type="checkbox"
                            :checked="rolePermissions && rolePermissions.includes(permission.name)">
                        </td>
                        <td class="border border-slate-300 px-3">{{ permission.name }}</td>
                        <td class="border border-slate-300 px-3">{{ permission.guard_name }}</td>
                    </tr>
                </table>


                <div class="flex items-center justify-end mt-4">
                    <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Сохранить
                    </PrimaryButton>
                    <Link
                        :href="route('roles.index')"
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
