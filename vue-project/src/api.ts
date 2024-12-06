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
    const { data } = await axios.get(`${localApiBaseURL}/orders`)
    return data
}
export const updateAddress = async (payload) => {
    return await axios.post(`${localApiBaseURL}/update-address/${payload.order_id}`, payload)
}



export const getOrderStatistics = async (payload: {
    startDate: string,
    endDate: string
}) => {
    const { data } = await axios.get(`${localApiBaseURL}/order-stats`, { params:payload })
    return data
}


// custom order status
export const createCustomStatus = async (payload) => {
    const { data } = await axios.post(`${localApiBaseURL}/statuses`, payload)
    return data
}
export const updateCustomStatus = async (payload, id) => {
    const { data } = await axios.put(`${localApiBaseURL}/statuses/${id}`, payload)
    return data
}
export const getCustomStatusList = async () => {
    const { data } = await axios.get(`${localApiBaseURL}/statuses`)
    return data
}
export const deleteCustomStatus = async (id:string) => {
    const { data } = await axios.delete(`${localApiBaseURL}/statuses/${id}`)
    return data
}