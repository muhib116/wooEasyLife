import { createOrUpdateWPOption, createOrUpdateWPOptionItem, getWPOption, getWPOptionItem } from "@/api"
import { onMounted, ref } from "vue"

export const useLicense = () => {
    const licenseKey = ref('')
    const isLoading = ref(false)

    const loadLicenseKey = async () => {
        const { data } = await getWPOptionItem({option_name: 'license', key: 'key'})
        licenseKey.value = data.value
    }

    const ActivateLicense = async (btn) => {
        try {
            isLoading.value = true
            btn.isLoading = true
            await createOrUpdateWPOptionItem({
                option_name: 'license',
                key: 'key',
                value: licenseKey.value
            })
        } finally {
            isLoading.value = false
            btn.isLoading = false
        }
    }

    const deactivateLicense = async (btn) => {
        if(!confirm('Are you sure to deactivate your license?')) return
        try {
            isLoading.value = true
            btn.isLoading = true
            await createOrUpdateWPOptionItem({
                option_name: 'license',
                key: 'key',
                value: ''
            })
            licenseKey.value = ''
        } finally {
            isLoading.value = false
            btn.isLoading = false
        }
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
        deactivateLicense,
        ActivateLicense
    }
}