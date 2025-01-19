import { getAbandonedOrders } from "@/api"
import { onMounted, ref } from "vue"

export const useMissingOrder = () => {
    const isLoading = ref()
    const abandonOrders = ref([])
    const alertMessage = ref({
        title: '',
        type: ''
    })
    const selectedFilter = ref('all')
    const filter = [
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
            slug: "carts-without-customer-details",
            title: "Carts without customer details"
        },
    ]

    const handleFilter = (item, btn) => {
        selectedFilter.value = item.slug
    }

    const loadAbandonedOrder = async () => {
        try {
            isLoading.value = true
            const { data } = await getAbandonedOrders()
            abandonOrders.value = data
        } finally {
            isLoading.value = true

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
        selectedFilter,
        handleFilter
    }
}