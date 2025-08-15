<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
   users: Object,
});

const form = useForm({});

function destroy(id) {
    if(confirm("Are you sure to delete this user?")){
        form.delete(route('users.destroy', id));
    }
}

</script>

<template>
    <AuthenticatedLayout>
        <Head title="Users" />

        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Users</h2>
                <div class="flex py-2 mr-2">
                    <Link
                        class="px-3 text-4xl text-black rounded-md focus:outline-none"
                        :href="route('users.create')"
                        as="button"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                        <path fill-rule="evenodd" d="M12 3.75a.75.75 0 01.75.75v6.75h6.75a.75.75 0 010 1.5h-6.75v6.75a.75.75 0 01-1.5 0v-6.75H4.5a.75.75 0 010-1.5h6.75V4.5a.75.75 0 01.75-.75z" clip-rule="evenodd" />
                        </svg>
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                     <table class="table-auto border-collapse border-slate-400">
                        <thead>
                        <tr class="bg-blue-100">
                            <th class="border border-slate-300">#</th>
                            <th class="border border-slate-300">Name</th>
                            <th class="border border-slate-300">Email</th>
                            <th class="border border-slate-300">Roles</th>
                            <th class="border border-slate-300" colspan="2">Action</th>    
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users">
                                <td class="border border-slate-300 px-3">{{ user.id }}</td>
                                <td class="border border-slate-300 px-3">{{ user.name }}</td>
                                <td class="border border-slate-300 px-3">{{ user.email }}</td>
                                <td class="border border-slate-300 px-3">
                                    <span v-for="role in user.roles" class="badge bg-primary">{{ role.name }}<br /></span>
                                </td>
                                <td class="border border-slate-300 px-3">
                                    <Link
                                        tabIndex="1"
                                        class="mx-1"
                                        :href="route('users.edit', user)"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                        <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712zM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.933.933l2.685-.8a5.25 5.25 0 002.214-1.32l8.4-8.4z" />
                                        <path d="M5.25 5.25a3 3 0 00-3 3v10.5a3 3 0 003 3h10.5a3 3 0 003-3V13.5a.75.75 0 00-1.5 0v5.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V8.25a1.5 1.5 0 011.5-1.5h5.25a.75.75 0 000-1.5H5.25z" />
                                        </svg>
                                    </Link>
                                </td>
                                <td class="border border-slate-300 px-3">
                                    <div
                                        @click="destroy(user.id)"
                                        class="mx-1 cursor-pointer"
                                        tabIndex="-1"
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
    </AuthenticatedLayout>
</template>
