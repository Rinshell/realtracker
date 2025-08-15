<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link, useForm, router } from '@inertiajs/vue3';
    import EventsTable from '@/Pages/Event/EventsTable.vue';
    import ProductCard from '@/Components/ProductCard.vue';
    import { ref, computed, onBeforeMount, onMounted, onBeforeUnmount, onUnmounted, nextTick } from 'vue';
    import {useMainStore} from '@/Stores/MainStore.js';

    const mainStore = useMainStore();

    const props= defineProps({
        product: Object,
        tables: Object,
        technos: Array,
        status: Array,
        enableEdit: Boolean,
        enableAdmin: Boolean
    });

    const searchText= ref('');
    const searchTextProxy= ref('');
    const searchSN= ref('');
    const searchSNProxy= ref('');
    const searchFN= ref('');
    const searchFNProxy= ref('');

    function saveFilter()
    {
        var pf= {
                text: searchText.value,
                sn: searchSN.value,
                fn: searchFN.value,
            };
        mainStore.ProductFilter[props.product.id]= pf;
        console.log("saveFilter id:" +props.product.id+" sn:" + mainStore.ProductFilter[props.product.id].sn);
    }

    function getFilter()
    {
        console.log("getFilter product.id:" + props.product.id);
        const pf= mainStore.ProductFilter[props.product.id];
        if (pf) {
            console.log("getFilter sn:" + pf.sn);
            searchText.value = pf.text;
            searchSN.value   = pf.sn;
            searchFN.value   = pf.fn;
            searchTextProxy.value= searchText.value;
            searchSNProxy.value  = searchSN.value;
            searchFNProxy.value  = searchFN.value;
        }
        console.log("getFilter prod id:" + props.product.id + ' sn:' + searchSN.value);
    }

    var factoryNumberID= null;

    const editEnableSN= ref(mainStore.ProductEditEnableSN);

    function swEdit(sn) {
        if (editEnableSN.value != sn) {
            editEnableSN.value= sn;
        } else {
            editEnableSN.value= -1;
        }
        mainStore.ProductEditEnableSN= editEnableSN.value;
    };
    
    function getEditable(sn) {
        return editEnableSN.value === sn;
    };

    function getFNumber(sn) {
        var fn= 's/n:'+sn;
        for (var key in props.tables) {
            if (props.tables[key][0].sn_n == sn) {
                props.tables[key].forEach((el) => {
                    if (el.techno_id == factoryNumberID && el.description) fn= el.description;
                })
            }
        }
        return fn;
    }

    function getLastSN(events) {
        const el= events[events.length-1];
        return el.sn_p * 10000000 + el.sn_m * 10000 + el.sn_n
    }

    const update= _.debounce((e) => {
        if (searchTextProxy.value != searchText.value) searchTextProxy.value= searchText.value;
        if (searchSNProxy.value   != searchSN.value  ) searchSNProxy.value  = searchSN.value;
        if (searchFNProxy.value   != searchFN.value  ) searchFNProxy.value  = searchFN.value;
    }, 500)

    function onReset(idx) {
        if (idx == 1) {
            searchText.value= ''; 
            searchTextProxy.value= ''; 
        }
        if (idx == 2) {
            searchSN.value= ''; 
            searchSNProxy.value= ''; 
        }
        if (idx == 3) {
            searchFN.value= ''; 
            searchFNProxy.value= ''; 
        }
    }

    const searchTable = computed(() => {
        console.log("searchTable");
        if (!searchTextProxy.value && !searchSNProxy.value && !searchFNProxy.value) {
            saveFilter();
            return props.tables;
        }

        var objsn= {};
        if (searchSNProxy.value) {
            for (var key in props.tables) {
                if (props.tables[key][0].sn_n == parseInt(searchSNProxy.value)) {
                    objsn[key]= props.tables[key];
                    break;
                }
            }
        } else {
            objsn= props.tables;
        }
        var objfn= {};
        if (searchFNProxy.value) {
            for (var key in objsn) {
                var fn= getFNumber(objsn[key][0].sn_n);
                if (fn.includes(searchFNProxy.value)) {
                    if (!(key in objfn)) {
                        objfn[key]= objsn[key];
                    }
                }
            }
        } else {
            objfn= objsn;
        }   

        var objtx= {};
        if (searchTextProxy.value) {
            for (var key in objfn) {
                const a= objfn[key].every(el => {
                    if (el.description && el.description.toLowerCase().includes(searchTextProxy.value.toLowerCase())) {
                        if (!(key in objtx)) {
                            objtx[key]= objfn[key];
                            return false;
                        }
                    }
                    return true;
                });
            }
        } else {
            objtx= objfn;
        }
        saveFilter();
        return objtx;
    })

    onMounted(() => {
        mainStore.curProductID= props.product.id;
        mainStore.curTechnos= props.technos;
        mainStore.curStatus= props.status;
        setTimeout(() => window.scrollTo(0, mainStore.ProductScroll), 300);

        var t= props.technos.find(function (el) {
            return el.title == 'установка заводского номера';
        });
        factoryNumberID= t ? t.id : null;
    });

    onBeforeMount(() => {
        getFilter();
    });

    onBeforeUnmount(() => {
        mainStore.ProductScroll= window.top.scrollY;
    });

    onUnmounted(() => {
    });

</script>

<style>
    .cursor-pointer {
        cursor: pointer;
    }
</style>

<template>
    <Head :title="product.title" />

    <AuthenticatedLayout :enableAdmin="enableAdmin">
        <template #header>
            <div class="flex justify-between font-semibold text-xl text-gray-800 leading-tight">
                <ProductCard :name="product.title" :description="product.description" :img="'/storage/'+product.path" />
                <Link v-if="enableEdit"
                    class="px-3 py-2 mr-2 focus:outline-none"
                    :href="route('events.create')"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                    <path fill-rule="evenodd" d="M12 3.75a.75.75 0 01.75.75v6.75h6.75a.75.75 0 010 1.5h-6.75v6.75a.75.75 0 01-1.5 0v-6.75H4.5a.75.75 0 010-1.5h6.75V4.5a.75.75 0 01.75-.75z" clip-rule="evenodd" />
                    </svg>
                </Link>
            </div>
        </template>
        
        <div class="sticky top-0 w-full mx-auto sm:px-6 lg:px-8 py-1 bg-white overflow-hidden shadow-sm sm:rounded-lg">

            <div class="flex flex-column gap-[14px] w-full">
                <div class="relative w-[250px]">
                    <input :class="['w-full h-[30px] rounded-md pr-[20px] pl-[30px]', {'bg-green-100': searchText}]" type="text" placeholder="Поиск по тексту ..." v-model="searchText" @input="update">
                    <svg class="absolute left-[10px] top-[50%] translate-y-[-50%]" width="13" height="14" viewBox="0 0 13 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.7 13.3L9.1 9.7C9.9 8.8 10.4 7.6 10.4 6.2C10.4 3.4 8.1 1.1 5.3 1.1C2.5 1.1 0.2 3.4 0.2 6.2C0.2 9 2.5 11.3 5.3 11.3C6.7 11.3 8 10.8 8.9 10L12.5 13.6C12.6 13.7 12.7 13.7 12.8 13.7C12.9 13.7 13 13.7 13.1 13.6C13.2 13.5 13.2 13.4 13.2 13.3C13.1 13.2 13.1 13.1 13 13.1C12.9 13.1 12.8 13.2 12.7 13.3ZM5.3 10.4C3 10.4 1.1 8.5 1.1 6.2C1.1 3.9 3 2 5.3 2C7.6 2 9.5 3.9 9.5 6.2C9.5 8.5 7.6 10.4 5.3 10.4Z" fill="#696958"/>
                    </svg>
                    <div v-if="searchText" class="absolute right-[0] top-[0] w-[30px] h-full" @click="onReset(1)">
                        <svg class="absolute right-[5px] top-[50%] translate-y-[-50%]" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </div>
                </div>
                <div class="relative w-[250px]">
                    <input :class="['w-full h-[30px] rounded-md pr-[10px] pl-[30px]', {'bg-green-100': searchSN}]" type="text" placeholder="По серийному номеру ..." v-model.number="searchSN" @input="update">
                    <svg class="absolute left-[10px] top-[50%] translate-y-[-50%]" width="13" height="14" viewBox="0 0 13 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.7 13.3L9.1 9.7C9.9 8.8 10.4 7.6 10.4 6.2C10.4 3.4 8.1 1.1 5.3 1.1C2.5 1.1 0.2 3.4 0.2 6.2C0.2 9 2.5 11.3 5.3 11.3C6.7 11.3 8 10.8 8.9 10L12.5 13.6C12.6 13.7 12.7 13.7 12.8 13.7C12.9 13.7 13 13.7 13.1 13.6C13.2 13.5 13.2 13.4 13.2 13.3C13.1 13.2 13.1 13.1 13 13.1C12.9 13.1 12.8 13.2 12.7 13.3ZM5.3 10.4C3 10.4 1.1 8.5 1.1 6.2C1.1 3.9 3 2 5.3 2C7.6 2 9.5 3.9 9.5 6.2C9.5 8.5 7.6 10.4 5.3 10.4Z" fill="#696958"/>
                    </svg>
                    <div v-if="searchSN" class="absolute right-[0] top-[0] w-[30px] h-full" @click="onReset(2)">
                        <svg class="absolute right-[5px] top-[50%] translate-y-[-50%]" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </div>
                </div>
                <div class="relative w-[250px]">
                    <input :class="['w-full h-[30px] rounded-md pr-[10px] pl-[30px]', {'bg-green-100': searchFN}]" type="text" placeholder="По заводскому номеру ..." v-model="searchFN" @input="update">
                    <svg class="absolute left-[10px] top-[50%] translate-y-[-50%]" width="13" height="14" viewBox="0 0 13 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.7 13.3L9.1 9.7C9.9 8.8 10.4 7.6 10.4 6.2C10.4 3.4 8.1 1.1 5.3 1.1C2.5 1.1 0.2 3.4 0.2 6.2C0.2 9 2.5 11.3 5.3 11.3C6.7 11.3 8 10.8 8.9 10L12.5 13.6C12.6 13.7 12.7 13.7 12.8 13.7C12.9 13.7 13 13.7 13.1 13.6C13.2 13.5 13.2 13.4 13.2 13.3C13.1 13.2 13.1 13.1 13 13.1C12.9 13.1 12.8 13.2 12.7 13.3ZM5.3 10.4C3 10.4 1.1 8.5 1.1 6.2C1.1 3.9 3 2 5.3 2C7.6 2 9.5 3.9 9.5 6.2C9.5 8.5 7.6 10.4 5.3 10.4Z" fill="#696958"/>
                    </svg>
                    <div v-if="searchFN" class="absolute right-[0] top-[0] w-[30px] h-full" @click="onReset(3)">
                        <svg class="absolute right-[5px] top-[50%] translate-y-[-50%]" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl bg-white py-12 pb-12 mx-auto sm:px-6 lg:px-8 sm:rounded-lg">
            <div v-for="(events, sn) in searchTable" class="overflow-hidden shadow-sm sm:rounded-lg" :key="sn">
                <div class="flex justify-between items-center pt-5">
                    <h3 class="font-semibold text-lg text-gray-800 m-4 leading-tight">{{getFNumber(sn)}}</h3>
                    <div v-if="enableEdit" class="flex py-2 mr-2">
                        <Link
                            class="px-3 text-4xl text-black rounded-md focus:outline-none"
                            :href="route('events.create', getLastSN(events))"
                            as="button"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                            <path fill-rule="evenodd" d="M12 3.75a.75.75 0 01.75.75v6.75h6.75a.75.75 0 010 1.5h-6.75v6.75a.75.75 0 01-1.5 0v-6.75H4.5a.75.75 0 010-1.5h6.75V4.5a.75.75 0 01.75-.75z" clip-rule="evenodd" />
                            </svg>
                        </Link>
                        <div
                            class="px-3 text-4xl text-black rounded-md focus:outline-none cursor-pointer"
                            @click="swEdit(sn)"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6" :class="{'fill-green-600': getEditable(sn)}">
                            <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712zM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.933.933l2.685-.8a5.25 5.25 0 002.214-1.32l8.4-8.4z" />
                            <path d="M5.25 5.25a3 3 0 00-3 3v10.5a3 3 0 003 3h10.5a3 3 0 003-3V13.5a.75.75 0 00-1.5 0v5.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V8.25a1.5 1.5 0 011.5-1.5h5.25a.75.75 0 000-1.5H5.25z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <EventsTable 
                    :product_id="props.product.id"
                    :events="events" 
                    :technos="technos"
                    :status="status"
                    :editEnable="getEditable(sn)"
                >
                </EventsTable>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
