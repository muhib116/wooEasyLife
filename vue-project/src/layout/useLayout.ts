import { getWPOption } from "@/api"
import { ref } from "vue"


const configData = ref()
export const useLayout = () => {
    const loadConfig = async () => {
        if(configData.value) return
        const { data } = await getWPOption({ option_name: 'config' })
        configData.value = data
    }

    return {
        loadConfig,
        configData
    }
}