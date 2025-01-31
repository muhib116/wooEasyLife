import { checkHasNewOrder } from "@/api";
import { inject, onMounted, ref } from "vue";

export const useNotification = () => {
  const { getOrders, loadOrderStatusList } = inject("useOrders", {});
  const hasNewOrder = ref(false);

  let timeoutId;
  const notificationSound = new Audio(
    import.meta.env.DEV
      ? "/notification-sound.wav"
      : window?.wooEasyLife?.dist_url + "/notification-sound.wav"
  );
  
  const checkNewOrderStatus = async () => {
    try {
      const { data } = await checkHasNewOrder();
      if (data?.has_new_orders) {
        notificationSound.play();
        hasNewOrder.value = true;
        loadOrderStatusList();
        getOrders(false);
  
        timeoutId = setTimeout(() => {
          hasNewOrder.value = false;
          scheduleNextCheck(8000);
        }, 4000);
      } else {
        scheduleNextCheck(8000);
      }
    } catch (error) {
      console.error("Error checking new order status:", error);
      scheduleNextCheck(8000);
    }
  };
  
  const scheduleNextCheck = (delay) => {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(checkNewOrderStatus, delay);
  };
  
  // Start tracking orders
  onMounted(checkNewOrderStatus);

  return {
    hasNewOrder,
  };
};
