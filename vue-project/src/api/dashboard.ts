import axios from "axios"
import {
    localApiBaseURL
} from './init'
import { getOrderList } from "@/api"



export const getRecentOrders = async (limit?: number) => {
    const { data } = await getOrderList({per_page: limit})
    return data
}
export const getTopSellingProduct = async (limit?: number) => {
    const { data } = await axios.get(`${localApiBaseURL}/top-selling-products?limit=${limit}`)
    return data
}
export const getSalesProgressData = async (startDate?:string, endDate?:string) => {
    const { data } = await axios.get(`${localApiBaseURL}/sales-progress`)
    return data
}