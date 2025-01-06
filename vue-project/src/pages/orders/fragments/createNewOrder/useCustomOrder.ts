import { getProducts } from "@/api"
import { computed, onMounted, ref } from "vue"

export const useCustomOrder = () => {
    const products = ref([])
    const productSearchKey = ref('')
    const form = ref({
        products: []
    })
    const filteredProducts = computed(() => {
        if(productSearchKey.value){
            return products.value.filter(item => {
                const searchKey = productSearchKey.value?.toLowerCase();
                return item.name?.toLowerCase().includes(searchKey) || item.id?.toString().toLowerCase().includes(searchKey);
            });            
        }

        return products.value
    })

    const isLoading = ref(false)
    const loadProducts = async () => {
        try {
            isLoading.value = true
            const { data } = await getProducts()
            products.value = data
        } finally {
            isLoading.value = false
        }
    }

    const addProductToForm = (item) => {
        // Check existence of product
        const existProduct = form.value.products.find(productItem => {
            return productItem.product.id === item.id
        })
    
        console.log(item, { existProduct })
    
        if (existProduct) {
            existProduct.quantity++
            return
        }
    
        form.value.products.push({
            product: item,
            quantity: 1
        })
    }
    

    onMounted(() => {
        if(!products.value.length){
            loadProducts()
        }
    })
    return {
        form,
        products,
        isLoading,
        filteredProducts,
        productSearchKey,
        loadProducts,
        addProductToForm,
    }
}