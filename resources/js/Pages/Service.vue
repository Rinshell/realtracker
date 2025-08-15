<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import ProductCard from '@/Components/ProductCard.vue';
    import TechnosTable from '@/Pages/Service/TechnosTable.vue';
    import StatusTable from '@/Pages/Service/StatusTable.vue';
    import { useForm } from '@inertiajs/vue3';
    import Combobox from '@/Components/Combobox.vue';
    import { computed, onMounted } from 'vue';
    import {useMainStore} from '@/Stores/MainStore.js';

    const props= defineProps({
        products: Array,
        technos: Array,
        status: Array,
    });

    const form = useForm({});

    const mainStore = useMainStore();
    
    function destroy(id) {
        if (confirm("Are you sure you want to Delete")) {
            form.delete(route('products.destroy', id));
        }
    }    

    function getNames(items) {
        const arr= [];
        items.forEach((el) => { arr.push(el.title); });
        return arr;
    }

    const proxyProdIdx = computed({
        get() {
            return props.products.findIndex(function isIDEquals(el) { return el.id === mainStore.curProductID });
        },
        set(val) {
            if (props.products[val].id != mainStore.curProductID) mainStore.curTechnoID= false;
            mainStore.curProductID= props.products[val].id;
        },
    });

    function getTechnoTitle(id) {
        var t= props.technos.find(el => el.id === id);
        return t ? t.title : "техпроцесс не выбран";
    }

//    const getProductName = computed(() => proxyProdIdx.value === false ? "none" : (props.products[proxyProdIdx.value].title + ':' + getProductID.value));
    
    onMounted(() => {
        if (mainStore.curProductID === false) proxyProdIdx.value= 0;
        console.log("Service onMounted curProductID:" + mainStore.curProductID);
    });

</script>

<template>
    <Head title="Service" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">Service</h2>
        </template>

        <div class="py-12">
            <div class="p-4 mx-auto bg-white rounded-lg max-w-7xl">
                <h2 class="font-semibold text-xl text-gray-800 mb-3 leading-tight">Продукция</h2>

                <div class="ml-8 mb-6">
                    <Link
                        class="focus:outline-none"
                        :href="route('products.create')"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                        <path fill-rule="evenodd" d="M12 3.75a.75.75 0 01.75.75v6.75h6.75a.75.75 0 010 1.5h-6.75v6.75a.75.75 0 01-1.5 0v-6.75H4.5a.75.75 0 010-1.5h6.75V4.5a.75.75 0 01.75-.75z" clip-rule="evenodd" />
                        </svg>
                    </Link>
                </div>


                <div v-if="products != null" class="max-w-7xl mx-auto py-2 sm:px-6 lg:px-8">
                    <div v-for="p in products" class="flex justify-between h-30 px-2 py-2 overflow-hidden shadow-sm sm:rounded-lg">
                        <ProductCard :name="p.title" :description="p.description" :img="'/storage/'+p.path" />
                        <div class="flex flex-col justify-around">
                            <Link
                                tabIndex="1"
                                class="w-20 px-4 py-2 "
                                :href="route('products.edit', p.id)"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712zM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.933.933l2.685-.8a5.25 5.25 0 002.214-1.32l8.4-8.4z" />
                                <path d="M5.25 5.25a3 3 0 00-3 3v10.5a3 3 0 003 3h10.5a3 3 0 003-3V13.5a.75.75 0 00-1.5 0v5.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V8.25a1.5 1.5 0 011.5-1.5h5.25a.75.75 0 000-1.5H5.25z" />
                                </svg>
                            </Link>

                            <button
                                @click="destroy(p.id)"
                                tabIndex="-1"
                                type="button"
                                class="w-20 px-4 py-2"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 fill-red-600">
                                <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>

                        </div>
                    </div>
                </div>
            </div>


            <div class="p-4 my-8 mx-auto bg-white rounded-lg max-w-7xl">
                <h2 class="font-semibold text-xl text-gray-800 mb-3 leading-tight">Техпроцесс</h2>

                <div class="pt-12 ml-5">
                    <h3 class="font-semibold text-lg text-gray-800 mb-3 leading-tight">Выбор продукта</h3>
                    <Combobox class= "ml-2" v-model:selected="proxyProdIdx" :items="getNames(products)" />
                </div>


                <div class="py-12 flex">

                    <div class="h-fit">
                        <h3 class="font-semibold text-lg text-gray-800 m-4 leading-tight">Технологический процесс</h3>
                        <div class="ml-8 mb-6">
                            <Link
                                class="focus:outline-none"
                                :href="route('technos.create')"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                <path fill-rule="evenodd" d="M12 3.75a.75.75 0 01.75.75v6.75h6.75a.75.75 0 010 1.5h-6.75v6.75a.75.75 0 01-1.5 0v-6.75H4.5a.75.75 0 010-1.5h6.75V4.5a.75.75 0 01.75-.75z" clip-rule="evenodd" />
                                </svg>
                            </Link>
                        </div>
                        <TechnosTable :technos="technos" :products="products"></TechnosTable>
                    </div>

                    <div class="h-fit">
                        <h3 class="font-semibold text-lg text-gray-800 m-4 leading-tight">Статус техпроцесса - {{getTechnoTitle(mainStore.curTechnoID)}}</h3>
                        <div class="ml-8 mb-6" v-if="mainStore.curTechnoID">
                            <Link
                                class="focus:outline-none"
                                :href="route('status.create')"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                <path fill-rule="evenodd" d="M12 3.75a.75.75 0 01.75.75v6.75h6.75a.75.75 0 010 1.5h-6.75v6.75a.75.75 0 01-1.5 0v-6.75H4.5a.75.75 0 010-1.5h6.75V4.5a.75.75 0 01.75-.75z" clip-rule="evenodd" />
                                </svg>
                            </Link>
                        </div>
                        <StatusTable :status="status" :technos="technos"></StatusTable>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
