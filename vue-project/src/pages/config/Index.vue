<template>
    <Layout>
        <Container>
            <Card.Native
                class="h-[calc(100svh-120px)]"
            >
                <div class="border-b pb-3">
                    <h3 class="text-2xl font-semibold">Settings</h3>
                </div>
                <div class="flex h-full">
                    <LeftSideMenu
                        class="w-[255px] border-r h-full pr-5 py-5"
                    />
                    <div class="p-5">
                        <RouterView />
                    </div>
                </div>
            </Card.Native>
        </Container>
    </Layout>
</template>
<script setup lang="ts">
    import { Layout, Container } from '@layout'
    import { Card } from '@components'
    import LeftSideMenu from './fragments/LeftSideMenu.vue'
</script>