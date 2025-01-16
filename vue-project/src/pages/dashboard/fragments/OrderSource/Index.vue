<template>
  <DashboardCard
    title="Order Source"
    :Key="chartKey"
    @dateChange="loadOrderSourceData"
  >
    <Loader
      :active="isLoading"
      class="bg-white/90 rounded-full p-[2px] absolute inset-1/2 -translate-x-1/2"
    />
    <div class="-ml-3 -mr-3 h-[320px]">
      <Chart.Native :chartData="chartData" width="100%" height="100%" />
    </div>
  </DashboardCard>
</template>

<script setup lang="ts">
import { Chart, Loader } from "@components";
import { computed } from "vue";
import { useOrderSource } from "./useOrderSource.js";
import DashboardCard from "../DashboardCard.vue";

const { chartKey, isLoading, orderSourceData, loadOrderSourceData } =
  useOrderSource();

const chartData = computed(() => {
  return {
    type: "polarArea",
    options: {
      xaxis: {
        categories: orderSourceData.value?.categories || [],
      },
      colors: ["#39c1a0"],
    },
    series: orderSourceData.value?.series?.length
      ? orderSourceData.value?.series[0].data
      : [],
  };
});
</script>
