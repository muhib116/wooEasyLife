import { ref } from "vue"
import { updateAddress } from '@/api'

export const useAddress = () => {
    const shippingAddressClone = ref()

    const updateBillingAddress = async (address) => {
        const {
            order_id,
            first_name,
            last_name,
            company,
            address_1,
            address_2,
            city,
            state,
            postcode,
            country,
            email,
            phone,
            transaction_id
        } = address

        const payload = {
            order_id,
            billing: {
                first_name,
                last_name,
                company,
                address_1,
                address_2,
                city,
                state,
                postcode,
                country,
                email,
                phone,
                transaction_id,
            }
        }

        return await updateAddress(payload)
    }

    const updateShippingAddress = async (address) => {
        const {
            order_id,
            first_name,
            last_name,
            company,
            address_1,
            address_2,
            city,
            state,
            postcode,
            country,
            customer_note,
        } = address
        const payload = {
            order_id,
            shipping: {
                first_name,
                last_name,
                company,
                address_1,
                address_2,
                city,
                state,
                postcode,
                country,
                customer_note,
            }
        }

        return await updateAddress(payload)
    }

    const handleAddressEdit = async (address) => {
        if (address.type == 'billing') {
            return await updateBillingAddress(address)
        }

        if (address.type == 'shipping') {
            return await updateShippingAddress(shippingAddressClone.value)
        }
    }

    return {
        updateBillingAddress,
        updateShippingAddress,
        shippingAddressClone,
        handleAddressEdit,
    }
}