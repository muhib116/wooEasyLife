import { createOrUpdateWPOption, getWPOption } from "@/api"
import { onMounted, ref } from "vue"

export const useSmsConfig = () => {
    const form = ref({
        admin_phone: '',
        admin_message: '',
        customer_message: ''
    })
    const isLoading = ref(false)
    const alertMessage = ref({
        title: '',
        type: ''
    })

    const personalizations = [
        {
            title: 'Customer name',
            slug: 'customer_name'
        },
        {
            title: 'Customer phone',
            slug: 'customer_phone'
        },
        {
            title: 'Customer email',
            slug: 'customer_email'
        },
        {
            title: 'Customer billing address',
            slug: 'customer_billing_address'
        },
        {
            title: 'Customer shipping address',
            slug: 'customer_shipping_address'
        },
        {
            title: 'Customer success rate',
            slug: 'customer_success_rate'
        },
        {
            title: 'Product name',
            slug: 'product_name'
        },
        {
            title: 'Total amount',
            slug: 'total_amount'
        },
        {
            title: 'Delivery charge',
            slug: 'delivery_charge'
        },
        {
            title: 'Payment method',
            slug: 'payment_method'
        },
        {
            title: 'Product price',
            slug: 'product_price'
        },
        {
            title: 'Product name',
            slug: 'product_name'
        },
        {
            title: 'Admin phone',
            slug: 'admin_phone'
        },
    ]

    const saveSMSConfig = async (btn, data) => {
        const payload = {
            option_name: 'sms_config',
            data
        }
        try {
            btn.isLoading = true
            isLoading.value = true
            createOrUpdateWPOption(payload)
        } finally {
            isLoading.value = false
            btn.isLoading = false
        }
    }

    const loadSMSConfig = async () => {
        const { data } = await getWPOption({
            option_name: 'sms_config'
        })

        if(data){
            form.value = data
        }
    }

    onMounted(async () => {
        try {
            isLoading.value = true
            await loadSMSConfig()
        } finally {
            isLoading.value = true
        }
    })

    return {
        personalizations,
        form,
        saveSMSConfig,
        loadSMSConfig,
    }
}