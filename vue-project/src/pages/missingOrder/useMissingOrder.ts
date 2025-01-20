import { getAbandonedOrders, updateAbandonedOrderStatus } from "@/api"
import { computed, onMounted, ref } from "vue"

export const useMissingOrder = () => {
    const selectedFilter = ref('dashboard')
    const isLoading = ref()
    const abandonOrders = ref([])
    const filteredAbandonOrders = computed(() => {
        let filteredOrders = []
        switch(selectedFilter.value){
            case 'all': filteredOrders = abandonOrders.value
            break;

            case 'registered-user': filteredOrders = abandonOrders.value.filter(item => item.status == 'abandoned' && item.is_repeat_customer == 1)
            break;
            
            case 'guest-user': filteredOrders = abandonOrders.value.filter(item => item.status == 'abandoned' && item.is_repeat_customer == 0)
            break;
            
            case 'recovered-order': filteredOrders = abandonOrders.value.filter(item => item.status == 'recovered')
            break;

            case 'carts-without-customer-details': filteredOrders = abandonOrders.value.map((item) => item.customer_phone)
            break;

        }
        return filteredOrders
    })
    const alertMessage = ref({
        title: '',
        type: ''
    })
    const filter = [
        {
            slug: "dashboard",
            title: "Dashboard"
        },
        {
            slug: "all",
            title: "All"
        },
        {
            slug: "registered-user",
            title: "Registered User"
        },
        {
            slug: "guest-user",
            title: "Guest User"
        },
        {
            slug: "recovered-order",
            title: "Recovered order"
        }
    ]

    const handleFilter = (item) => {
        selectedFilter.value = item.slug
    }

    const loadAbandonedOrder = async () => {
        try {
            isLoading.value = true
            const { data } = await getAbandonedOrders()
            abandonOrders.value = data
        } finally {
            isLoading.value = false

        }
    }

    const markAsRecovered = async (item, btn: { isLoading: boolean }) => {
        if(!confirm('Are you sure to make it recovered!')) return
        item.status = 'recovered'

        handleUpdate(item, btn)
    }
    const markAsAbandoned = async (item, btn: { isLoading: boolean }) => {
        if(!confirm('Are you sure to make it abandoned!')) return
        item.status = 'abandoned'

        handleUpdate(item, btn)
    }

    const handleUpdate = async (item, btn) => {
        try {
            isLoading.value = true
            btn.isLoading = true
            const { message, status } = await updateAbandonedOrderStatus(item.id, item)
            alertMessage.value.type = status == 'success' ? 'success' : 'warning'
            alertMessage.value.title = message

            await loadAbandonedOrder()
        } catch({response}) {
            alertMessage.value = {
                title: response?.data?.message,
                type: 'danger'
            }
        } finally {
            isLoading.value = false
            btn.isLoading = false

            setTimeout(() => {
                alertMessage.value = {
                    title: '',
                    type: ''
                }
            }, 5000)
        }
    }

    onMounted(() => {
        loadAbandonedOrder()
    })

    return {
        filter,
        isLoading,
        alertMessage,
        abandonOrders,
        filteredAbandonOrders,
        selectedFilter,
        handleFilter,
        markAsRecovered,
        markAsAbandoned
    }
}