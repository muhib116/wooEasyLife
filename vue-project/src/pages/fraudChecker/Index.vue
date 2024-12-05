<template>
    <Layout>
        <Container>
            <FraudData :data="data" class="max-w-[600px] mx-auto mt-20">
                <FraudForm
                    v-model="phone"
                    @onSubmit="handleFraudCheck"
                />
            </FraudData>
        </Container>
    </Layout>
</template>

<script setup lang="ts">
    import { Layout, Container } from '@layout'
    import { checkCustomer } from '@/remoteApi'
    import { ref } from 'vue'
    import FraudData from './FraudData.vue'
    import FraudForm from './FraudForm.vue'

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