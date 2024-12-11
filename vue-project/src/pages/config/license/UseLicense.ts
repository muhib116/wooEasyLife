import { getWPOption } from "@/api"
import { onMounted, ref } from "vue"

export const useLicense = () => {
    const licenseKey = ref("")
    const isLoading = ref(false)

    const loadLicenseKey = async () => {
        const { data } = await getWPOption({option_name: 'license_key'})
        licenseKey.value = data
    }

    onMounted(async () => {
        try {
            isLoading.value = true
            await loadLicenseKey()
        } finally {
            isLoading.value = false
        }
    })
    return {
        isLoading,
        licenseKey,
        loadLicenseKey,
    }
}