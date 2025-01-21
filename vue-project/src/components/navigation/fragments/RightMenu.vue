<template>
  <div class="flex">
    <ul class="flex items-center">
      <li>
        <button
          v-if="isValidLicenseKey"
          class="pointer-events-none bg-[#f0fdf4] border border-green-200 px-3 py-1 text-green-600 hover:text-green-600 rounded cursor-pointer font-normal mr-4"
        >
          Valid License
        </button>
        <Button.Native
          v-else
          class="bg-[#fdf0f0] border border-red-200 px-3 py-1 text-red-600 hover:text-red-600 rounded cursor-pointer font-normal mr-4"
          :to="{
            name: 'license'
          }"
        >
          Activate License
        </Button.Native>
      </li>
    </ul>

    <div class="border-r border-gray-200 py-2.5 mr-3"></div>

    <ul class="flex gap-3">
      <li>
        <RouterLink
          :to="{
            name: 'license',
          }"
          class="text-gray-400 hover:text-orange-500 py-4 inline-flex"
          exact-active-class="text-orange-500"
        >
          <Icon name="PhGearSix" size="28" />
        </RouterLink>
      </li>
      <li>
        <a href="#" class="text-gray-400 hover:text-gray-500 py-4 inline-flex">
          <Icon name="PhPlayCircle" size="28" />
        </a>
      </li>
      <li>
        <a href="#" class="text-gray-400 hover:text-gray-500 py-4 inline-flex">
          <Icon name="PhQuestion" size="28" />
        </a>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { Icon, Button } from "@components";
import { useLicense } from "@/pages/config/license/UseLicense";

const { isValidLicenseKey } = useLicense();
</script>
