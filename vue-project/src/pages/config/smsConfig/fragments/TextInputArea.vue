<template>
    {{ cursorPosition }}
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

        <div
            v-if="showTooltip"
            class="absolute top-0 right-0"
        >
            <!-- :style="tooltipStyle" -->
            ((Muhibbullah))
        </div>

        <Textarea.Native
            class="leading-[25px]"
            :label="label"
            :placeholder="placeholder"
            v-model="modelValue"
            rows="5"
            @input="e => {
                handleInput(e)
                handleCharacterRemove(e, modelValue)
            }"
            @click="init"
        />
    </div>
</template>

<script setup lang="ts">
    import { Textarea, Icon, Card } from '@components'
    import { ref, reactive } from 'vue'

    const toggleDropdown = ref(false)
    const cursorPosition = ref(0)
    const textarea = ref()

    withDefaults(
        defineProps<{
            position?: 'up' | 'down',
            dropdownData: {title: string, slug: string}[],
            label?: string,
            placeholder: string
        }>(), {
            position: 'down'
        }
    )

    const showTooltip = ref<boolean>(false)
    const tooltipStyle = reactive({ top: '0px', left: '0px' })

    const handleInput = (event) => {
        const value = event.target.value
        const cursorPos = event.target.selectionStart
        const lastChar = value.slice(cursorPos - 1, cursorPos)

        // Detect `[$]` pattern
        if (lastChar === '$') {
            showTooltip.value = true
            positionTooltip(cursorPos)
        } else {
            showTooltip.value = false
        }
    }

    const positionTooltip = (cursorPos) => {
        const textareaEl = textarea.value
        const { offsetTop, offsetLeft, scrollTop } = textareaEl
        const rect = textareaEl.getBoundingClientRect()

        // Calculate approximate cursor position in pixels
        const lineHeight = 24 // Adjust based on your textarea's line height
        const charWidth = 8 // Adjust based on your textarea's font size

        const row = Math.floor(cursorPos / textareaEl.cols)
        const col = cursorPos % textareaEl.cols

        tooltipStyle.top = `${rect.top + window.scrollY + row * lineHeight}px`
        tooltipStyle.left = `${rect.left + window.scrollX + col * charWidth}px`
    }

    const selectTooltipItem = (item) => {
        if (!textarea.value) return

        const cursorPos = textarea.value.selectionStart
        const newValue =
        modelValue.value.slice(0, cursorPos - 1) + // Remove `$`
        `$${item.slug}` +
        modelValue.value.slice(cursorPos)

        modelValue.value = newValue
        showTooltip.value = false

        // Update cursor position
        textarea.value.focus()
        textarea.value.setSelectionRange(cursorPos + item.slug.length, cursorPos + item.slug.length)
    }























    const handleDropDownData = (item) => {
        // Ensure the textarea reference exists
        if (!textarea.value) return;

        // Get the current cursor position or selection range
        const start = textarea.value.selectionStart;
        const end = textarea.value.selectionEnd;

        // Insert the selected suggestion into the textarea
        const newValue =
            textarea.value.value.slice(0, start) +
            `$${item.slug}` +
            textarea.value.value.slice(end);

        // Update the modelValue
        textarea.value.value = newValue;

        // Set the cursor position after the inserted text
        const cursorAfterInsertion = start + `$${item.slug}`.length;

        // Focus and set cursor position
        textarea.value.focus();
        console.log(cursorAfterInsertion)
        textarea.value.setSelectionRange(cursorAfterInsertion, cursorAfterInsertion);
    };

    const init = (event) => {
        textarea.value = event.target
        cursorPosition.value = textarea.value.selectionStart; // Get cursor position
    }

    const handleCharacterRemove = (event, text) => {
        const textarea = event.target;
        const cursorPos = textarea.selectionStart; // Get current cursor position
        cursorPosition.value = cursorPos;

        // Find the token pattern starting with $ and connected with underscores
        const regex = /\$\w+/g;
        let match;
        let shouldDelete = false;
        let deleteStart, deleteEnd;

        while ((match = regex.exec(text)) !== null) {
            const start = match.index;
            const end = start + match[0].length;

            // Check if the cursor is in or at the exact start of the match
            if (cursorPos <= end && cursorPos >= start) {
                shouldDelete = true;
                deleteStart = start;
                deleteEnd = end;
                break;
            }
        }

        if (shouldDelete) {
            // Delete the matched token
            const newText = text.slice(0, deleteStart) + text.slice(deleteEnd);
            modelValue.value = newText;

            // Adjust the cursor position relative to the deleted text
            const newCursorPos = deleteStart; // Cursor stays at the start of the deleted token
            textarea.value = newText; // Update the textarea value directly
            textarea.setSelectionRange(newCursorPos, newCursorPos); // Set cursor position
        }
    };


    const modelValue = defineModel()
</script>