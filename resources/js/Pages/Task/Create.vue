<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    date: '',
    sn: '',
    fn: '',
    description: '',
    task: '',
    status: '',
    next: '',
});

const submit = () => {
    form.post(route('store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Create new product" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="date" value="Дата" />

                <TextInput
                    id="date"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.date"
                    required
                    autofocus
                    autocomplete="date"
                />

                <InputError class="mt-2" :message="form.errors.date" />
            </div>

            <div class="mt-4">
                <InputLabel for="sn" value="Серийный номер" />

                <TextInput
                    id="sn"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.sn"
                    required
                    autocomplete="sn"
                />

                <InputError class="mt-2" :message="form.errors.sn" />
            </div>

            <div class="mt-4">
                <InputLabel for="fn" value="Заводской номер" />

                <TextInput
                    id="fn"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.fn"
                    autocomplete="fn"
                />

                <InputError class="mt-2" :message="form.errors.fn" />
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
                <InputLabel for="task" value="Задача" />

                <TextInput
                    id="task"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.task"
                    required
                    autocomplete="task"
                />

                <InputError class="mt-2" :message="form.errors.task" />
            </div>

            <div class="mt-4">
                <InputLabel for="status" value="Статус" />

                <TextInput
                    id="status"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.status"
                    autocomplete="status"
                />

                <InputError class="mt-2" :message="form.errors.status" />
            </div>

            <div class="mt-4">
                <InputLabel for="next" value="Следующий процесс" />

                <TextInput
                    id="next"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.next"
                    autocomplete="next"
                />

                <InputError class="mt-2" :message="form.errors.next" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Добавить
                </PrimaryButton>
                <SecondaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Назад
                </SecondaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
