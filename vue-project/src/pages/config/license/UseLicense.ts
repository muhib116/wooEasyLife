import { createOrUpdateWPOptionItem, getWPOptionItem } from "@/api"
import { getUser } from "@/remoteApi"
import { onMounted, ref } from "vue"
import { useRouter } from 'vue-router'

const licenseKey = ref('')
const isValidLicenseKey = ref(true)

export const useLicense = (mountable: boolean = true) => {
    const router = useRouter()
    const isLoading = ref(false)
    const alertMessage = ref({
        message: '',
        type: ''
    })

    const loadLicenseKey = async () => {
        const { data } = await getWPOptionItem({option_name: 'license', key: 'key'})
        licenseKey.value = data.value

        await getUser() //this function calling to check authentication, read inside the code

        return licenseKey.value
    }

    const ActivateLicense = async (btn: {isLoading: boolean}, shouldDisabled: boolean = false) => {
        try {
            if(shouldDisabled){
                licenseKey.value = ''
            }

            isLoading.value = true
            btn.isLoading = true
            await createOrUpdateWPOptionItem({
                option_name: 'license',
                key: 'key',
                value: licenseKey.value.trim()
            })
            await loadLicenseKey()

            alertMessage.value = {
                message: licenseKey.value.trim() != '' ? 'Your license activated!' : 'License removed!',
                type: licenseKey.value.trim() != '' ? 'success' : 'danger'
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

    if(mountable){
        onMounted(async () => {
            if(!licenseKey.value) return
            try {
                isLoading.value = true
                await loadLicenseKey()
            } finally {
                isLoading.value = false
            }
        })
    }
    return {
        isLoading,
        licenseKey,
        alertMessage,
        isValidLicenseKey,
        loadLicenseKey,
        ActivateLicense,
        deactivateLicense,
    }
}