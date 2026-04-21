// resources/js/composables/useTranslation.ts
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import type { PageProps } from '@/types'

export function useTranslation() {
    const page = usePage<PageProps>()

    const locale = computed(() => page.props.locale ?? 'en')
    const translations = computed<Record<string, string>>(() => page.props.translations ?? {})

    function t(key: string, replacements?: Record<string, string>): string {
        let translation = translations.value[key] ?? key

        if (replacements) {
            Object.entries(replacements).forEach(([k, v]) => {
                translation = translation.replace(`:${k}`, v)
            })
        }

        return translation
    }

    return { t, locale }
}