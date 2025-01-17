import axios from "axios"
import { createSMSHistory, getWPOption } from "./api"
import { computed, ref } from "vue"

export const licenseKey = ref('');


// load license key start--------
export const loadLicenseKey = async () => {
  const { data } = await getWPOption({ option_name: 'license' })
  licenseKey.value = data.key
}
// load license key end----------


export const remoteApiBaseURL = 'https://api.wpsalehub.com/api'
const headers = computed(() => ({
  headers: {
    Authorization: 'Bearer ' + licenseKey.value
  }
}))


// remote function
export const checkFraudCustomer = async (payload: {
  phone: { id: number; phone: string }[]
}) => {
  const { data } = await axios.post(`${remoteApiBaseURL}/fraud-check`, payload, headers.value)
  return data
}


// courier start
export const getCourierCompanies = async () => {
  const { data } = await axios.post(`${remoteApiBaseURL}/courier/list`, null, headers.value)
  return data
}

export const saveCourierConfig = async (payload: {
  title: "steadfast" | "paperfly" | "steadfast" | "redx",
  logo: 'string',
  api_key: 'string',
  secret_key: 'string'
  is_active: boolean
}) => {
  const { data } = await axios.post(`${remoteApiBaseURL}/courier/save-configuration`, payload, headers.value)
  return data
}

export const getCourierConfig = async () => {
  const { data } = await axios.post(`${remoteApiBaseURL}/courier/get-configuration`, null, headers.value)
  return data
}

export const steadfastBulkOrderCreate = async (payload: {
  orders: {
    invoice: number | string
    recipient_name: string
    recipient_phone: string
    recipient_address: string
    cod_amount: number | string
  }[]
}) => {
  const { data } = await axios.post(`${remoteApiBaseURL}/steadfast/create-bulk-order`, payload, headers.value)
  return data
}
// courier end


// sms integration start
export const sendSMS = async (payload: {
  phone: string,
  content: string,
  status?: string
}) => {
  const { data } = await axios.post(`${remoteApiBaseURL}/sms/send`, payload, headers.value)
  await createSMSHistory({
    phone_number: payload.phone,
    message: payload.content,
    status: data.data.response_code == 202 ? payload.status || '' : 'failed',
    error_message: data.data.error_message
  })
  return data
}
// sms integration end



export const checkCourierStatus = async (partnerName: string, consignmentId: string) => {
  const { data } = await axios.post(`${remoteApiBaseURL}/${partnerName.toLowerCase()}/check-status`, {
    consignment_id: consignmentId
  }, headers.value)
  return data
}


export const checkCourierBalance = async () => {
  const { data } = await axios.get(`${remoteApiBaseURL}/check-courier-balance`, headers.value)
  return data
}