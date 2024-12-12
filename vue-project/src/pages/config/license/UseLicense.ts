import { createOrUpdateWPOption, createOrUpdateWPOptionItem, getWPOption, getWPOptionItem } from "@/api"
import { onMounted, ref } from "vue"

export const useLicense = () => {
    const licenseKey = ref('')
    const isLoading = ref(false)
    const alertMessage = ref({
        message: '',
        type: ''
    })

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
            alertMessage.value = {
                message: 'Your license successfully activated!',
                type: 'success'
            }
        } finally {
            isLoading.value = false
            btn.isLoading = false
        }

        setTimeout(() => {
            alertMessage.value = {
                message: '',
                type: ''
            }
        }, 4000)
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
            alertMessage.value = {
                message: 'Your license deactivated!',
                type: 'success'
            }
        } finally {
            isLoading.value = false
            btn.isLoading = false
        }
        setTimeout(() => {
            alertMessage.value = {
                message: '',
                type: ''
            }
        }, 4000)
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
        ActivateLicense,
        alertMessage
    }
}