import { getUser } from "@/remoteApi"
import { ref } from "vue"

export const userData = ref()
export const licenseKey = ref(localStorage.getItem('license_key'))
export const isValidLicenseKey = ref(true)
export const licenseAlertMessage = ref({
    type: '',
    title: ''
})

export const setUserData = (data) => {
    userData.value = data
}

export const loadUserData = async () => {
    const data = await getUser() //this function calling to check authentication, read inside the code
    setUserData(data)
}

export const useServiceProvider = () => 
{
    return {
        userData,
        licenseKey,
        isValidLicenseKey,
        licenseAlertMessage,
        setUserData,
        loadUserData,
    }
}