import { checkHasNewOrder } from "@/api"
import { inject, onMounted, ref } from "vue"

export const useNotification = () => {
    const { getOrders, loadOrderStatusList } = inject('useOrders', {})
    const hasNewOrder = ref(false)
    const audio = new Audio('/notification-sound.wav'); // Use relative path
    

    const checkNewOrderStatus = async () => {
        const { data } = await checkHasNewOrder()
        if(data.has_new_orders){
            hasNewOrder.value = true
            audio.play();
            loadOrderStatusList();
            getOrders(false);

            setTimeout(() => {
                hasNewOrder.value = false
                setTimeout(checkNewOrderStatus, 8000)
            }, 4000)
        }else {
            setTimeout(checkNewOrderStatus, 8000)
        }
    }
    onMounted(checkNewOrderStatus)

    return {
        hasNewOrder
    }
}