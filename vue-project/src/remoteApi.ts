import axios from "axios"

export const remoteApiBaseURL = 'https://api.wpsalehub.com/api'
const headers = {
  headers: {
    Authorization: "Bearer Kod30eDnI1EFG9vaf9gBPsSwaD3IkklCIATZoSYz9cf733bd"
  }
}


// remote function
export const checkCustomer = async (payload: {
    phone: { id: number; phone: string }[]
}) => {
    const { data } = await axios.post(`${remoteApiBaseURL}/fraud-check`, payload)
    return data
}


// courier start
export const getCourierCompanies = async () => {
    const { data } = await axios.post(`${remoteApiBaseURL}/courier/list`, null, headers)
    return data
}

export const saveCourierConfig = async (payload: {
    title: "steadfast" | "paperfly" | "steadfast" | "redx",
    api_key: 'string',
    secret_key: 'string'
}) => {
    const { data } = await axios.post(`${remoteApiBaseURL}/courier/save-configuration`, payload, headers)
    return data
}

export const getCourierConfig = async () => {
  const { data } = await axios.post(`${remoteApiBaseURL}/courier/get-configuration`, null, headers)
  return data
}

// courier end


// sms integration start
export const sendSMS = async (payload: {
  phone: string,
  content: string
}) => {
  const { data } = await axios.post(`${remoteApiBaseURL}/sms/send`, payload, headers)
  return data
}
// sms integration end