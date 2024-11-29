import axios from "axios"

export const remoteApiBaseURL = 'https://api.wpsalehub.com/api'


// remote function
export const checkCustomer = async (payload: {
    phone: { id: number; phone: string }[]
}) => {
    const { data } = await axios.post(`${remoteApiBaseURL}/fraud-check`, payload)
    return data
}