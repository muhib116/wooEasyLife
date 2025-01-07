<template>
    <Card.Native>
        Top Selling Product
        <Table.Table>
            <Table.THead>
                <Table.Th>Product</Table.Th>
                <Table.Th>Sold</Table.Th>
                <Table.Th>Stock</Table.Th>
                <Table.Th>Stock Status</Table.Th>
            </Table.THead>
            <Table.TBody>
                <Table.Tr
                    v-for="product in topSellingProducts"
                    :key="product.product_id"
                >
                    <Table.Td>
                        <div class="flex gap-2 items-center">
                            <img
                                :src="product.image"
                                class="size-10 object-cover block"
                            />
                            {{ product.product_name }}
                        </div>
                    </Table.Td>
                    <Table.Td>
                        {{ product.total_sold }}
                    </Table.Td>
                    <Table.Td>
                        {{ product.stock_quantity }}
                    </Table.Td>
                    <Table.Td>
                        {{ product.stock_status }}
                    </Table.Td>
                </Table.Tr>
            </Table.TBody>
        </Table.Table>
    </Card.Native>
</template>

<script setup lang="ts">
    import { Card, Table } from '@components'
    import { useTopSellingProduct } from './UseTopSellingProduct'

    const {
        topSellingProducts
    } = useTopSellingProduct()
</script>