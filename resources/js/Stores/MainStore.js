import {defineStore} from 'pinia';
import { ref } from 'vue';

export const useMainStore = defineStore('mainStore', () => {
    const Products           = ref(null)
    const curProductID          = ref(false)
    const curTechnoID           = ref(false)
    const curTechnos            = ref(null)
    const curStatus             = ref(null)
    const ProductEditEnableSN   = ref(-1)
    const ProductScroll         = ref(0)
    const ProductFilter         = ref([])
    const permissions           = ref([])

    return { Products, curProductID, curTechnoID, curTechnos, curStatus, 
             ProductEditEnableSN, ProductScroll, ProductFilter, permissions        }
})
