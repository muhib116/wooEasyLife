<template>
  <DashboardCard
    title="Sales Target"
    :showDateFilter="false"
    @dateChange="() => {}"
  >
    <template #before-filter>
      <Button.Outline
        class="border-orange-500 bg-orange-50 text-orange-500"
        title="Make a sales target"
        @onClick="toggleModal = true"
      >
        <Icon name="PhPlus" weight="bold" />
      </Button.Outline>
    </template>

    <div class="grid grid-cols-[1fr_1fr_2fr] gap-4">
      <Card title="Daily" :chartData="chartData.daily" />
      <Card title="Monthly" :chartData="chartData.monthly" />
      <Card
        title="Date wise"
        :chartData="chartData.dateWise"
        hideTargetAchieve
      />
    </div>
  </DashboardCard>

  <Modal
    v-model="toggleModal"
    @close="toggleModal = false"
    title="Set your sales target"
    class="max-w-[550px] w-full"
    hideFooter
  >
    <SalesTargetForm
        @close="toggleModal = false"
    />
  </Modal>
</template>

<script setup lang="ts">
import { Loader, Button, Icon, Modal } from "@components";
import DashboardCard from "../DashboardCard.vue";
import { provide, ref } from "vue";
import SalesTargetForm from "./SalesTargetForm.vue";
import Card from "./Card.vue";
import { useSalesTarget } from "./useSalesTarget";

const _useSalesTarget = useSalesTarget();
const {
  isLoading,
  endDate,
  salesTargetData,
  dailyTargetAmount,
  chartData,
  saveSalesTarget,
  loadSalesTargetData,
} = _useSalesTarget;

const toggleModal = ref(false);
provide("useSalesTarget", _useSalesTarget);
</script>
