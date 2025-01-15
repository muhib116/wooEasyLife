import { checkHasNewOrder } from "@/api"
import { inject, onMounted, ref } from "vue"

export const useNotification = () => {
    const { getOrders, loadOrderStatusList } = inject('useOrders', {})
    const hasNewOrder = ref(false)
    const audio = new Audio('/notification-sound.wav'); // Use relative path
    

    onMounted(() => {
        setInterval(async () => {
            const { data } = await checkHasNewOrder()
            if(data.has_new_orders){
                hasNewOrder.value = true
                audio.play();
                loadOrderStatusList();
                getOrders(false);
    
                setTimeout(() => {
                    hasNewOrder.value = false
                }, 4000)
            }
        }, 10000)
    })

    return {
        hasNewOrder
    }
}