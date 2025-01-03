<template>
    <Container>
        <template #header>
            <Header />
            <ClientInfo />
        </template>
        
        <template #table>
            <TableHeader />
            <TableBody />
            <TableFooter />
        </template>

        <template #footer>
            <Footer />
        </template>
    </Container>
</template>

<script setup lang="ts">
    import Container from './fragments/Container.vue'
    import Header from './fragments/Header.vue'
    import ClientInfo from './fragments/ClientInfo.vue'
    import TableHeader from './fragments/TableHeader.vue'
    import TableBody from './fragments/TableBody.vue'
    import TableFooter from './fragments/TableFooter.vue'
    import Footer from './fragments/Footer.vue'
</script>