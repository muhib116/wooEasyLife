import axios from "axios"

export let baseUrl = ''
if(location.hostname == 'localhost'){
    baseUrl = import.meta.env.DEV ? 'http://localhost:8080/test' : location.origin + '/test'
}else {
    baseUrl = location.origin
}

export const localApiBaseURL = `${baseUrl}/wp-json/wooeasylife/v1`

// local function
export const getPaymentMethods = async () => {
    return await axios.get(`${localApiBaseURL}/payment-methods`)
}
export const getOrderList = async () => {
    return await axios.get(`${localApiBaseURL}/orders`)
}
export const updateAddress = async (payload) => {
    return await axios.post(`${localApiBaseURL}/update-address/${payload.order_id}`, payload)
}