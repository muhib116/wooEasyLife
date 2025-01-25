import { checkHasNewOrder } from "@/api";
import { inject, onMounted, ref } from "vue";

export const useNotification = () => {
  const { getOrders, loadOrderStatusList } = inject("useOrders", {});
  const hasNewOrder = ref(false);

  let timeoutId;
  const checkNewOrderStatus = async () => 
  {
    clearTimeout(timeoutId)
    const { data } = await checkHasNewOrder();
    if (data.has_new_orders) 
    {
      const audio = new Audio(import.meta.env.DEV ? '/notification-sound.wav' : window?.wooLifeChanger?.dist_url+'/notification-sound.wav'); // Use relative path
      audio.play();
      hasNewOrder.value = true;
      loadOrderStatusList();
      getOrders(false);

      timeoutId = setTimeout(() => {
        hasNewOrder.value = false;
        setTimeout(checkNewOrderStatus, 8000);
      }, 4000);
    } else {
      timeoutId = setTimeout(checkNewOrderStatus, 8000);
    }
  };
  onMounted(checkNewOrderStatus);

  return {
    hasNewOrder,
  };
};
