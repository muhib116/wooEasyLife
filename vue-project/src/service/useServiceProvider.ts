import { ref } from "vue"

export const userData = ref()

export const useServiceProvider = () => 
{
    const setUserData = (data) => {
        userData.value = data
    }

    return {
        userData,
        setUserData
    }
}