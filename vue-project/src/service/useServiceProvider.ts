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


export const router = ref()
export const route = ref()
export const redirectToLicensePage = () => {
    if(route.value.name == 'license') return
    router.value && router.value?.push({
        name: 'license'
    })
}

export const useServiceProvider = () => 
{
    return {
        route,
        router,
        userData,
        licenseKey,
        isValidLicenseKey,
        licenseAlertMessage,
        setUserData,
        loadUserData,
        redirectToLicensePage,
    }
}