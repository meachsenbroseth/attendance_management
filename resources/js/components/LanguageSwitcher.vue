<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { useTranslation } from '@/composables/useTranslation'

const { locale } = useTranslation()

function switchTo(lang: 'en' | 'km') {
    router.post(`/language/${lang}`, {}, {
        preserveScroll: true,
        preserveState: false,
    })
}
</script>

<template>
    <div class="flex items-center gap-1 rounded-lg border border-slate-200 p-1">
        <button
            type="button"
            @click="switchTo('en')"
            :class="[
                'rounded-md px-2.5 py-1 text-xs font-medium leading-normal transition-colors',
                locale === 'en'
                    ? 'bg-slate-900 text-white'
                    : 'text-slate-500 hover:text-slate-800',
            ]"
        >
            EN
        </button>
        <button
            type="button"
            @click="switchTo('km')"
            :class="[
                'rounded-md px-2.5 py-1 text-xs font-medium leading-normal transition-colors',
                locale === 'km'
                    ? 'bg-slate-900 text-white'
                    : 'text-slate-500 hover:text-slate-800',
            ]"
        >
            <span class="font-khmer">ខ្មែរ</span>
        </button>
    </div>
</template>