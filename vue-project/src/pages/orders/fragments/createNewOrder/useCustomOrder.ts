import { getProducts, validateCoupon } from "@/api"
import { computed, onMounted, ref } from "vue"

export const useCustomOrder = () => {
    const products = ref([])
    const productSearchKey = ref('')
    const couponValidationErrorMessage = ref('')
    const appliedCoupon = ref('')
    const couponDiscount = ref(0);
    const form = ref({
        date: new Date().toISOString().split('T')[0],
        created_via: '',
        products: [],
        shippingMethod: {},
        paymentMethod: {},
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
    
        if (existProduct) {
            existProduct.quantity++
            return
        }
    
        form.value.products.push({
            product: item,
            quantity: 1
        })

        calculateCouponDiscountAmount(form.value.coupons)
    }

    const handleCouponValidation = async (btn) => {
        if(appliedCoupon.value == '') {
            couponValidationErrorMessage.value = 'Coupon code cannot be empty.'
            return
        }
        if(form.value.coupons.find(item => item.coupon_code == appliedCoupon.value)) {
            couponValidationErrorMessage.value = 'Coupon code already applied.'
            appliedCoupon.value = ''
            return
        }

        try {
            btn.isLoading = true
            const { data } = await validateCoupon({
                coupon_code: appliedCoupon.value
            })

            if(data){
                form.value.coupons.push(data)
                calculateCouponDiscountAmount(form.value.coupons)
                appliedCoupon.value = ''
            }
        } catch({ response }) {
            couponValidationErrorMessage.value = response?.data?.message
        } finally {
            btn.isLoading = false
        }
    }

    const calculateCouponDiscountAmount = (coupons: {
        "discount_type": string,
        "amount": number | string,
        "usage_limit": number,
        "usage_count": number,
        "expiry_date": string
    }[]) => {
        //write code here to apply coupon
        let totalDiscount = 0;

        coupons.forEach(coupon => {
            const { discount_type, amount, usage_limit, usage_count, expiry_date } = coupon;
            // Apply discount based on type
            if (discount_type === "percent") {
                const discountPercent = parseFloat(amount);
                if (!isNaN(discountPercent)) {
                    totalDiscount += (getItemsTotal.value * discountPercent) / 100;
                } else {
                    console.warn(`Invalid percentage amount for coupon: ${amount}`);
                }
            } else if (discount_type === "fixed_cart") {
                const discountFixed = parseFloat(amount);
                if (!isNaN(discountFixed)) {
                    totalDiscount += Math.min(discountFixed, getItemsTotal.value); // Ensure the discount doesn't exceed the total
                } else {
                    console.warn(`Invalid fixed cart amount for coupon: ${amount}`);
                }
            } else if (discount_type === "fixed_product") {
                // Fixed product discounts typically apply per product. Adjust this logic as needed.
                const discountFixedProduct = parseFloat(amount);
                if (!isNaN(discountFixedProduct)) {
                    totalDiscount += Math.min(discountFixedProduct, getItemsTotal.value); // Ensure the discount doesn't exceed the total
                } else {
                    console.warn(`Invalid fixed product amount for coupon: ${amount}`);
                }
            } else {
                console.warn(`Unsupported discount type: ${discount_type}`);
            }
        });
        // Ensure discount does not exceed the total
        totalDiscount = Math.min(totalDiscount, getItemsTotal.value);
    
        couponDiscount.value = +totalDiscount.toFixed(2)

        return {
            totalDiscount: totalDiscount.toFixed(2),
            discountedTotal: (getItemsTotal.value - totalDiscount).toFixed(2),
        };
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
        calculateCouponDiscountAmount,
    }
}