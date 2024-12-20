import { createOrUpdateWPOption, createSMS, getWoocommerceStatuses, getWPOption } from "@/api"
import { onMounted, ref } from "vue"
import List from './List.vue'
import Create from './Create.vue'

export const useSmsConfig = () => {
    const isLoading = ref(false)
    const wooStatuses = ref([])
    const alertMessage = ref<{
        message: string
        type: "success" | "danger" | "warning" | "info" | ''
    }>({
        message: '',
        type: 'danger'
    })
    const hasUnsavedData = ref(false)
    const activeTab = ref('create')

    const tabs = ref([
        {
            title: 'List',
            slug: 'list'
        },
        {
            title: 'Create',
            slug: 'create'
        },
    ])
    const components = ref({
        list: List,
        create: Create
    })

    const defaultFormData = {
        status: '',
        message: '',
        phone_number: '',
        message_for: '',
        is_active: true
    }
    const messageFor = [
        {
            title: 'Admin',
            slug: 'admin'
        },
        {
            title: 'Customer',
            slug: 'customer'
        }
    ]
    const tabChange = (slug: string) => {
        activeTab.value = slug
        form.value = {...defaultFormData}
    }

    const form = ref({...defaultFormData})

    const personalizations = [
        {
            title: 'Site name',
            slug: 'site_name'
        },
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
    
    const handleCreateSMS = async (btn, payload) => {
        try {
            isLoading.value = true
            btn.isLoading = true
            const res = await createSMS(payload)
            if(res.status == "success"){
                alertMessage.value.message = res.message
                alertMessage.value.type = 'success'
            }

            setTimeout(() => {
                alertMessage.value = {
                    message: '',
                    type: ''
                }
            }, 4000)

        } finally {
            isLoading.value = false
            btn.isLoading = false
        }
    }

    const updateSMS = (btn) => {

    }
    
    const deleteSMS = (btn) => {

    }
    
    const loadSMS = () => {

    }
    
    const loadWooStatuses = async () => {
        try {
            isLoading.value = true
            const { data } = await getWoocommerceStatuses()
            wooStatuses.value = data
        } finally {
            isLoading.value = false
        }
    }

    onMounted(async () => {
        try {
            isLoading.value = true
            await loadSMS()
        } finally {
            isLoading.value = false
        }
    })

    return {
        tabs,
        components,
        tabChange,
        personalizations,
        form,
        messageFor,
        alertMessage,
        isLoading,
        activeTab,
        hasUnsavedData,
        handleCreateSMS,
        deleteSMS,
        updateSMS,
        wooStatuses,
        loadWooStatuses
    }
}