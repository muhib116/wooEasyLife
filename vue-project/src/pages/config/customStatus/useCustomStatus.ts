import { onMounted, ref } from "vue"
import List from "./fragments/List.vue"
import Create from "./fragments/Create.vue"
import { getCustomStatusList } from "@/api"

export const useCustomStatus = () => {
    const isLoading = ref(false)
    const hasUnsavedData = ref(false)
    const activeTab = ref('list')
    const statusList = ref<{
        label: string,
        color: string,
        description: string
    }>({})

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

    const tabChange = (slug: string) => {
        activeTab.value = slug
    }

    const handleCustomStatusList = async () => {
        try {
            isLoading.value = true
            const { data } = await getCustomStatusList()
            statusList.value = data
        } finally{
            isLoading.value = false
        }
    }

    onMounted(() => {
        handleCustomStatusList()
    })


    return {
        tabs,
        activeTab,
        isLoading,
        statusList,
        components,
        hasUnsavedData,
        tabChange,
        handleCustomStatusList,
    }
}