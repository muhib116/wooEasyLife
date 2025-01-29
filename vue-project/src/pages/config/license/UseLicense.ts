import { createOrUpdateWPOptionItem, getWPOptionItem } from "@/api"
import { getUser } from "@/remoteApi"
import { onMounted, ref } from "vue"

const licenseKey = ref(localStorage.getItem('license_key'))
const isValidLicenseKey = ref(true)
const licenseAlertMessage = ref({
    type: '',
    title: ''
})

export const useLicense = (mountable: boolean = true) => {
    const isLoading = ref(false)

    const loadLicenseKey = async () => 
    {
        if(!licenseKey.value){
            const { data } = await getWPOptionItem({option_name: 'license', key: 'key'})
            licenseKey.value = data.value
            localStorage.setItem('license_key', data.value)
        }

        licenseKey.value && await getUser() //this function calling to check authentication, read inside the code

        return licenseKey.value
    }

    const ActivateLicense = async (btn: {isLoading: boolean}, shouldDisabled: boolean = false) => {
        try {
            if(shouldDisabled && confirm('Are you sure to deactivate license?')){
                licenseKey.value = ''
                isValidLicenseKey.value = false
                localStorage.removeItem('license_key')
            }

            isLoading.value = true
            btn.isLoading = true
            await createOrUpdateWPOptionItem({
                option_name: 'license',
                key: 'key',
                value: licenseKey.value?.trim() || ''
            })
            if(licenseKey.value) {
                await loadLicenseKey()
            }
        } finally {
            isLoading.value = false
            btn.isLoading = false
        }
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
        isValidLicenseKey,
        licenseAlertMessage,
        loadLicenseKey,
        ActivateLicense
    }
}