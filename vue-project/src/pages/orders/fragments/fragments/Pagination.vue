<template>
    <div class="flex justify-between px-4 mt-2 mb-2">
        <div>
            <Input.Native
                placeholder="Search customer"
                class="text-base border px-2 py-1 rounded-sm"
            />
        </div>

        <div class="flex items-center space-x-2 text-sm justify-end">
            <div class="flex items-center gap-2">
                Per page
                <Input.Native
                    type="number"
                    class="border px-2 pr-1 py-1 w-14"
                    v-model="orderFilter.per_page"
                />
            </div>

            <!-- Total Items -->
            <span>{{ totalRecords }} items</span>
            
            <!-- Pagination Buttons -->
            <div class="flex items-center space-x-1 ml-4">
                <!-- First Page Button -->
                <button class="px-2 py-1 text-gray-400 bg-gray-100 border rounded-sm cursor-not-allowed" disabled>
                    «
                </button>
                
                <!-- Previous Page Button -->
                <button class="px-2 py-1 text-gray-400 bg-gray-100 border rounded-sm cursor-not-allowed" disabled>
                    ‹
                </button>
                
                <!-- Current Page -->
                <span
                    class="px-2 py-1 border border-gray-300 rounded-sm"
                >
                    {{ orderFilter.page }}
                </span>
                
                <!-- Total Pages -->
                <span class="text-gray-500">
                    of {{ orderFilter.per_page ? Math.round(totalRecords / orderFilter.per_page) : '' }}
                </span>
                
                <!-- Next Page Button -->
                <button 
                    class="px-2 py-1 text-blue-600 bg-blue-100 border border-blue-300 rounded-sm hover:bg-blue-200"
                    @click="() => {
                        if(orderFilter.page < Math.round(totalRecords / orderFilter.per_page)){
                            orderFilter.page ++
                        }
                    }"
                >
                    ›
                </button>
                
                <!-- Last Page Button -->
                <button 
                    class="px-2 py-1 text-blue-600 bg-blue-100 border border-blue-300 rounded-sm hover:bg-blue-200"
                    @click="() => {
                        orderFilter.page = Math.round(totalRecords / orderFilter.per_page)
                    }"
                >
                    »
                </button>
            </div>
        </div>   
    </div>   
</template>

<script setup lang="ts">
    import { Input } from '@components'
    import { inject } from 'vue'

    const { 
        totalRecords,
        orderFilter
    } = inject('useOrders')
</script>