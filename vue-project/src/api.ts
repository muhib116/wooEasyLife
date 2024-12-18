import axios from "axios"

export let baseUrl = ''
if(location.hostname == 'localhost'){
    baseUrl = import.meta.env.DEV ? 'http://localhost:8080/wordpress' : location.origin + '/wordpress'
}else {
    baseUrl = location.origin
}

export const localApiBaseURL = `${baseUrl}/wp-json/wooeasylife/v1`


// functions for order list
export const getPaymentMethods = async () => {
    return await axios.get(`${localApiBaseURL}/payment-methods`)
}
export const getOrderList = async (payload) => {
    const { data } = await axios.get(`${localApiBaseURL}/orders`, {
        params: payload
    })
    return data
}
export const getOrderStatusListWithCounts = async () => {
    const { data } = await axios.get(`${localApiBaseURL}/status-with-counts`)
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
export const getOrderStatuses = async () => {
    const { data } = await axios.get(`${localApiBaseURL}/status-with-counts`)
    return data
}
// functions for order list





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


// wp_options table CRUD start
export const createOrUpdateWPOption = async (payload: {
    option_name: string,
    data: object
}) => {
    const { data } = await axios.post(`${localApiBaseURL}/wp-option`, payload)
    return data
}

export const createOrUpdateWPOptionItem = async (payload: {
    option_name: string,
    key: string,
    value: string
}) => {
    const { data } = await axios.post(`${localApiBaseURL}/wp-option-item`, null, {
        params: payload
    })
        return data
}

export const getWPOption = async (payload: {option_name: string}) => {
    const { data } = await axios.get(`${localApiBaseURL}/wp-option`, {
        params: payload
    })
    return data
}

export const getWPOptionItem = async (payload: {
    option_name: string
    key: string
}) => {
    const { data } = await axios.get(`${localApiBaseURL}/wp-option-item`, {
        params: payload
    })
    return data
}

export const deleteWPOption = async (payload: {option_name: string}) => {
    const { data } = await axios.delete(`${localApiBaseURL}/wp-option`, {
        params: payload
    })
    return data
}
// wp_options table CRUD end


// sms config CRUD start

export const getWoocommerceStatuses = async () => {
    const { data } = await axios.get(`${localApiBaseURL}/woo-statuses`)
    return data
}
// sms config CRUD end