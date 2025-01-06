import { getProducts, validateCoupon } from "@/api"
import { computed, onMounted, ref } from "vue"

export const useCustomOrder = () => {
    const products = ref([])
    const productSearchKey = ref('')
    const couponValidationErrorMessage = ref('')
    const appliedCoupon = ref('')
    const couponDiscount = ref(0);
    const form = ref({
        products: [],
        coupons: []
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

    const handleCouponValidation = async (btn) => {
        if(appliedCoupon.value == '') {
            couponValidationErrorMessage.value = 'Coupon code cannot be empty.'
            return
        }
        try {
            btn.isLoading = true
            const { data } = await validateCoupon({
                coupon_code: appliedCoupon.value
            })

            if(data){
                form.value.coupons.push(data)
                _applyCoupon(data)
                appliedCoupon.value = ''
            }
        } catch({ response }) {
            couponValidationErrorMessage.value = response.data.message
        } finally {
            btn.isLoading = false
        }
    }

    const _applyCoupon = (data: {
        "discount_type": string,
        "amount": number | string,
        "usage_limit": number,
        "usage_count": number,
        "expiry_date": string
    }) => {
        //write code here to apply coupon
        console.log(form.value.coupons, getItemsTotal.value)
        couponDiscount.value = 150
    }

    const getItemsTotal = computed(() => {
        let total_amount = 0
        form.value.products.forEach(item => {
            total_amount += (+item.product.price * +item.quantity)
        })

        return total_amount
    })
    

    onMounted(() => {
        if(!products.value.length){
            loadProducts()
        }
    })
    return {
        form,
        products,
        isLoading,
        getItemsTotal,
        appliedCoupon,
        couponDiscount,
        productSearchKey,
        filteredProducts,
        couponValidationErrorMessage,
        loadProducts,
        handleCouponValidation,
        addProductToForm,
    }
}