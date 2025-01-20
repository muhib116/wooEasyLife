import { getAbandonedOrders } from "@/api"
import { computed, onMounted, ref } from "vue"

export const useMissingOrder = () => {
    const selectedFilter = ref('all')
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

            case 'carts-without-customer-details': filteredOrders = abandonOrders.value
            break;

        }
        console.log(filteredOrders)
        return filteredOrders
    })
    const alertMessage = ref({
        title: '',
        type: ''
    })
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
            slug: "recovered-order",
            title: "Recovered order"
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
            isLoading.value = false

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
        handleFilter
    }
}