<template>
    <div 
        class="relative" 
    >
        <div 
            v-click-outside="()=>toggleDropdown = false"
        >
            <button
                class="absolute right-2 opacity-60 hover:opacity-100 top-8 z-20"
                @click="toggleDropdown = true"
                title="Add dynamic data."
            >
                <Icon
                    name="PhDatabase"
                    size="20"
                />
            </button>
            
            <Card.Native
                v-if="toggleDropdown"
                class="absolute right-0 z-30 px-0 h-[250px] border"
                :class="position == 'up' ? 'bottom-full' : 'top-4'"
            >
                <button
                    class="absolute right-2 top-2 hover:text-red-500"
                    @click="toggleDropdown = false"
                >
                    <Icon 
                        name="PhX"
                        size="20"
                    />
                </button>
                <div class="h-full overflow-y-auto pt-4 [&>*+*]:border-t">
                    <div
                        v-for="(item, index) in dropdownData"
                        :key="index"
                        class="hover:bg-gray-100 py-2 font-light px-4 cursor-pointer"
                        @click="() => {
                            handleDropDownData(item)
                            toggleDropdown = false
                        }"
                    >
                        {{ item.title }}
                    </div>
                </div>
            </Card.Native>
        </div>

        <Textarea.Native
            label="Customer Message"
            placeholder="Write customer message"
            v-model="modelValue"
            @input="e => handleCharacterRemove(e, modelValue)"
        />
    </div>
</template>

<script setup lang="ts">
    import { Textarea, Icon, Card } from '@components'
    import { ref } from 'vue'

    const toggleDropdown = ref(false)

    withDefaults(
        defineProps<{
            position?: 'up' | 'down',
            dropdownData: {title: string, slug: string}[]
        }>(), {
            position: 'down'
        }
    )

    const handleDropDownData = (item) => {
        modelValue.value += `$${item.slug}`
    }

    const handleCharacterRemove = (event, text) => {
        const textarea = event.target
        const cursorPosition = textarea.selectionStart; // Get cursor position
        // Find the token pattern starting with $ and connected with underscores
        const regex = /\$\w+/g;
        let match;
        let shouldDelete = false;
        let deleteStart, deleteEnd;
        while ((match = regex.exec(text)) !== null) {
            const start = match.index;
            const end = start + match[0].length;
      
            // Check if the cursor is in the range of the match
            if (cursorPosition <= end && cursorPosition > start) {
              shouldDelete = true;
              deleteStart = start;
              deleteEnd = end;
              break;
            }
        }

        if (shouldDelete) {
            // Delete the matched token
            const newText = text.slice(0, deleteStart) + text.slice(deleteEnd);
            textarea.value = newText;
      
            // Adjust cursor position
            textarea.setSelectionRange(deleteStart, deleteStart);
        }
    }

    const modelValue = defineModel()
</script>