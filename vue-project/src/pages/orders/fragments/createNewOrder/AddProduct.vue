<template>

    <div class="space-y-4">
        <div
            v-for="(item, index) in form.products"
            :key="index"
            class="flex items-center justify-between p-4 border rounded-sm"
        >
            <div class="flex gap-4 items-center">
                <img
                    :src="item.product.image"
                    class="size-8"
                />
                <p>
                    #{{ item.product.id }} {{ item.product.name }}
                </p>
            </div>

            <div class="flex gap-2 items-center">
                <Input.Native
                    label="quantity"
                    type="number"
                    v-model="item.quantity"
                    class="bg-transparent border w-10 pl-1"
                />

                <Button.Native class="hover:text-red-500" title="Remove product">
                    <Icon name="PhX" />
                </Button.Native>
            </div>
        </div>
    </div>



    <div v-click-outside="() => toggleProductList = false" >
        <Button.Primary
            @click="toggleProductList = true"
        >
            Add Product
        </Button.Primary>
        <div v-if="toggleProductList" class="border border-gray-200 p-4 bg-gray-50 z-40 w-full mt-1 relative">
            <Button.Native 
                class="absolute right-0 top-0 p-1 bg-red-500 text-white z-20"
                @click="toggleProductList = false"
            >
                <Icon name="PhX" />
            </Button.Native>
            <Input.Primary
                placeholder="Search Product"
                v-model="productSearchKey"
                type="search"
                wrapperClass="!mr-3"
            />

            <div class="max-h-[100px] overflow-auto mt-4 [&>div+div]:border-t">
                <div
                    v-for="item in filteredProducts"
                    :key="item.id"
                    class="flex gap-4 items-center justify-between p-2"
                    @click="addProductToForm(item)"
                >
                    <div class="flex gap-4 items-center">
                        <img
                            :src="item.image"
                            class="size-8"
                        />
                        <p>
                            #{{ item.id }} {{ item.name }}
                        </p>
                    </div>

                    <Button.Primary
                        class="px-1 py-0 !bg-green-500 !font-light text-sm"
                        title="Add Product"
                    >
                        Add
                    </Button.Primary>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { Input, Button, Icon } from '@components'
    import { inject, ref } from 'vue'

    const toggleProductList = ref(false)
    const {
        form,
        filteredProducts,
        productSearchKey,
        addProductToForm
    } = inject('useCustomOrder')
</script>