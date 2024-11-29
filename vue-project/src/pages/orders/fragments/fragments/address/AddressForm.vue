<template>
    <div class="grid gap-4">
        <div v-if="address && address.type == 'shipping'" class="flex gap-4">
            <Button.Native 
                class="font-light text-blue-500 hover:text-orange-500"
                @click="loadShippingAddress"
            >
                Load shipping address
            </Button.Native>
            <Button.Native 
                class="font-light text-blue-500 hover:text-orange-500"
                @click="copyBillingAddress"
            >
                Copy billing address
            </Button.Native>
        </div>
        <p
            v-else
            class="font-light"
        >
            Customer billing information
        </p>

        <div class="grid grid-cols-2 gap-4">
            <Input.Primary
                label="First name"
                v-model="shippingAddressClone.first_name"
            />
            <Input.Primary
                label="Last name"
                v-model="shippingAddressClone.last_name"
            />
        </div>
        <div>
            <Input.Primary
                label="Company"
                v-model="shippingAddressClone.company"
            />
        </div>
        <div>
            <Input.Primary
                label="Address line 1"
                v-model="shippingAddressClone.address_1"
            />
        </div>
        <div>
            <Input.Primary
                label="Address line 2"
                v-model="shippingAddressClone.address_2"
            />
        </div>
        <div class="grid grid-cols-2 gap-4">
            <Input.Primary
                label="City"
                v-model="shippingAddressClone.city"
            />
            <Input.Primary
                label="State"
                v-model="shippingAddressClone.state"
            />
        </div>
        <div class="grid grid-cols-2 gap-4">
            <Input.Primary
                label="Post code"
                v-model="shippingAddressClone.postcode"
            />
            <Input.Primary
                label="Country"
                v-model="shippingAddressClone.country"
            />
        </div>
        <div v-if="address.type=='billing'" class="grid grid-cols-2 gap-4">
            <Input.Primary
                label="Email"
                v-model="shippingAddressClone.email"
            />
            <Input.Primary
                label="Phone"
                v-model="shippingAddressClone.phone"
            />
        </div>
        <div v-if="address.hasOwnProperty('transaction_id')">
            <Input.Primary
                label="Transaction ID"
                v-model="shippingAddressClone.transaction_id"
            />
        </div>
        <div v-if="address.hasOwnProperty('customer_note')">
            <Textarea.Native
                label="Transaction ID"
                v-model="shippingAddressClone.customer_note"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
    import { Input, Textarea, Button } from '@components'
    import { onMounted, inject, watch } from 'vue'
    import { getPaymentMethods } from '@/api'

    const props = defineProps<{
        address: object,
        billingAddress: object,
    }>()

    const { shippingAddressClone } = inject('useAddress')

    const copyBillingAddress = () => {
        if(!confirm('Copy billing information to shipping information? This will remove any currently entered shipping information.')) {
            return
        }
        if(props.address.type == 'shipping'){
            shippingAddressClone.value = props.billingAddress
            shippingAddressClone.value.type = 'shipping'
        }
    }

    const loadShippingAddress = () => {
        if(!confirm('Load the customer\'s shipping information? This will remove any currently entered shipping information.')) {
            return
        }
        shippingAddressClone.value = props.address
    }

    shippingAddressClone.value = props.address

    onMounted(async () => {
        if(props.address.type == 'shipping') return
        const { data } = await getPaymentMethods()
        console.log(data)
    })
</script>