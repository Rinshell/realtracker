<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link, useForm } from '@inertiajs/vue3';
    
    const props= defineProps({
        permissions: Object,
    });

    const form = useForm({});

    function destroy(id) {
        if(confirm("Are you sure to delete this permissions?")){
            form.delete(route('permissions.destroy', id));
        }
    }

</script>

<template>
    <AuthenticatedLayout>
        <Head title="Permissions" />

        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Permissions</h2>
                <div class="flex py-2 mr-2">
                    <Link
                        class="px-3 text-4xl text-black rounded-md focus:outline-none"
                        :href="route('permissions.create')"
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
                            <th class="table-auto border border-collapse border-slate-400">Name</th>
                            <th class="table-auto border border-collapse border-slate-400">Guard</th> 
                            <th class="table-auto border border-collapse border-slate-400" colspan="3" width="1%">Action</th> 
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-for="permission in permissions">
                                <td class="border border-slate-300 px-3">{{ permission.name }}</td>
                                <td class="border border-slate-300 px-3">{{ permission.guard_name }}</td>
                                <td class="border border-slate-300 px-3">
                                    <Link
                                        tabIndex="1"
                                        class="mx-1"
                                        :href="route('permissions.edit', permission.id)"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                        <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712zM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.933.933l2.685-.8a5.25 5.25 0 002.214-1.32l8.4-8.4z" />
                                        <path d="M5.25 5.25a3 3 0 00-3 3v10.5a3 3 0 003 3h10.5a3 3 0 003-3V13.5a.75.75 0 00-1.5 0v5.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V8.25a1.5 1.5 0 011.5-1.5h5.25a.75.75 0 000-1.5H5.25z" />
                                        </svg>
                                    </Link>
                                </td>
                                <td class="border border-slate-300 px-3">
                                    <div
                                        @click="destroy(permission.id)"
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
