<template>
    <div class="grid grid-cols-2 gap-4">
        <div v-if="shippingMethods">
            <Select.Primary
                label="Shipping Method"
                :options="shippingMethods.map(item => {
                    return {
                        ...item,
                        title: `${item.zone_name}-${item.method_title}-(${item.shipping_cost})`
                    }
                })"
                returnType="object"
                itemValue="title"
                itemKey="method_id"
                v-model="form.shippingMethod"
            />
        </div>
        <div v-if="paymentMethods">
            <Select.Primary
                label="Payment Method"
                :options="paymentMethods"
                returnType="object"
                v-model="form.paymentMethod"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
    import { Select } from '@components'
    import { ref, onMounted, inject } from 'vue'
    import { getPaymentMethods, getShippingMethods } from '@/api'
    
    const shippingMethods = ref(null)
    const paymentMethods  = ref(null)
    
    const {
        form
    } = inject('useCustomOrder')

    onMounted(async () => {
        const { data:_shippingMethods } = await getShippingMethods();
        shippingMethods.value = _shippingMethods
        
        const { data:_paymentMethods } = await getPaymentMethods();
        paymentMethods.value = _paymentMethods
    })
</script>