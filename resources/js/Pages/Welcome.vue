<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    appVersion: String,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <Head title="Welcome" />

    <div
        class="relative flex items-top justify-center min-h-screen bg-white sm:items-center sm:pt-0"
    >
        <div v-if="canLogin" class="fixed top-0 right-0 px-6 py-4 sm:block">
<!--                v-if="$page.props.auth.user" -->
            <Link
                :href="route('products')"
                class="text-sm text-gray-700 dark:text-gray-500 underline"
                >Вход</Link
            >
<!--
            <template v-else>
                <a href="/admin/login" class="text-sm text-gray-700 dark:text-gray-500 underline">Log in</a>
            </template>
-->
        </div>

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-center pt-8 sm:pt-0">
                <img src="chip_128.png">
            </div>

            <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
            </div>

            <div class="flex justify-center mt-4 sm:items-center sm:justify-between">
                <div class="ml-4 text-center text-sm text-gray-500 sm:text-right sm:ml-0">
                    {{ appVersion }}  (Laravel v{{ laravelVersion }} PHP v{{ phpVersion }})
                </div>
            </div>
        </div>
    </div>
</template>
