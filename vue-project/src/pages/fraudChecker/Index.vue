<template>
    <Layout>
        <Container>
            <div class="max-w-[600px] mx-auto mt-20">
                <div class="bg-red-100 flex rounded-l shadow mb-4">
                    <Input.Native 
                        name="phone"
                        class="flex-1 w-full px-5"
                        placeholder="01xxxxxxxx"
                        v-model="phone"
                    />
                    <Button.Primary
                        @onClick="handleFraudCheck"
                        class="bg-orange-500"
                        :class="phone.length == 11 ? '' : 'opacity-60 pointer-events-none'"
                    >
                        Check Now
                    </Button.Primary>
                </div>
                
                <Card.Native>
                    <h3 
                        v-if="data?.report?.success_rate == '100%'"
                        class="font-bold text-xl mb-4 text-center animate-bounce text-green-600"
                    >
                        🎉 The number has no fraud history! ✅
                    </h3>

                    <Table.Table
                        v-if="data && data?.report?.total_order"
                        class="w-full"
                    >
                        <Table.THead>
                            <Table.Th>Courier Name</Table.Th>
                            <Table.Th class="text-center bg-green-100">Confirm</Table.Th>
                            <Table.Th class="text-center bg-red-100">Cancel</Table.Th>
                            <Table.Th class="text-center bg-sky-100">Success Rate</Table.Th>
                        </Table.Thead>
                        <Table.TBody>
                            <Table.Tr
                                v-for="item in data.report.courier"
                            >
                                <Table.Th>{{ item.title }}</Table.Th>
                                <Table.Th class="text-center bg-green-500 text-white">{{ item.report.confirmed }}</Table.Th>
                                <Table.Th class="text-center bg-red-500 text-white">{{ item.report.cancel }}</Table.Th>
                                <Table.Th class="text-center bg-sky-500 text-white">{{ item.report.success_rate }}</Table.Th>
                            </Table.Tr>
                        </Table.TBody>

                        <Table.THead>
                            <Table.Th>Total</Table.Th>
                            <Table.Th class="bg-green-600 text-white text-center">
                                {{ data.report.confirmed }}
                            </Table.Th>
                            <Table.Th class="bg-red-600 text-white text-center">
                                {{ data.report.cancel }}
                            </Table.Th>
                            <Table.Th class="bg-sky-600 text-white text-center">
                                {{ data.report.success_rate }}
                            </Table.Th>
                        </Table.Thead> 
                    </Table.Table>

                    <div
                        v-if="!data"
                        class="py-10 font-bold text-xl mb-4 text-center text-red-600"
                    >
                        <fraudCheckImg
                            class="mx-auto max-w-[300px] mb-10"
                        />
                        <h3 class="animate-bounce">
                            🫣 Search for fraud using a phone number.
                        </h3>
                    </div>

                    <div
                        v-if="data && data?.report?.total_order == 0"
                    >
                        <fraudCheckImg
                            class="mx-auto max-w-[300px] mb-10"
                        />
                        <h3 class="font-bold text-xl mb-4 text-center">
                            🎉 The number has no data! ✅
                        </h3>
                    </div>
                </Card.Native>
            </div>
        </Container>
    </Layout>
</template>

<script setup lang="ts">
    import { Layout, Container } from '@layout'
    import { Table, Input, Button, Card } from '@components'
    import { checkCustomer } from '@/remoteApi'
    import { ref } from 'vue'
    import fraudCheckImg from './fraudCheckImg.vue'

    const phone = ref('')
    const data  = ref()
    const handleFraudCheck = async (btn) => {
        if(!phone.value || phone.value.length !== 11){
            alert("Please enter a valid phone number !")
            return
        }
        try {
            btn.isLoading = true
            const payload = {
                phone: [{
                    id: 1,
                    phone: phone.value
                }]
            }
            const response = await checkCustomer(payload)
            data.value = response[0]
        } finally {
            btn.isLoading = false
        }
    }
</script>