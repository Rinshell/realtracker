<script setup>
    defineProps({
        name: {
            type: String,
            default: '',
        },
        description: {
            type: String,
            default: '',
        },
        img: {
            type: String,
            default: '',
        },
    });
</script>

<template>
    <div class="flex font-sans">
        <div class="flex-none w-48 relative">
            <img :src="img" alt="" class="absolute inset-0 w-full h-full object-cover" loading="lazy" />
        </div>
        <div class="flex-auto p-6">
            <div class="flex flex-wrap">
                <h1 class="flex-auto text-lg font-semibold text-slate-900">
                   {{name}}
                </h1>
            </div>
            <p class="text-sm text-slate-700">
                {{description}}
            </p>
        </div>
    </div>
</template>
