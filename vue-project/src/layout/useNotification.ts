import { checkHasNewOrder } from "@/api";
import { inject, onMounted, ref } from "vue";
import NotificationSound from "@/assets/notification-sound.wav";

export const useNotification = () => {
  const { getOrders, loadOrderStatusList } = inject("useOrders", {});
  const hasNewOrder = ref(false);

  const checkNewOrderStatus = async () => {
    const { data } = await checkHasNewOrder();
    if (data.has_new_orders) {
      hasNewOrder.value = true;
      const audio = new Audio(NotificationSound); // Use relative path
      audio.play();
      loadOrderStatusList();
      getOrders(false);

      setTimeout(() => {
        hasNewOrder.value = false;
        setTimeout(checkNewOrderStatus, 8000);
      }, 4000);
    } else {
      setTimeout(checkNewOrderStatus, 8000);
    }
  };
  onMounted(checkNewOrderStatus);

  return {
    hasNewOrder,
  };
};
