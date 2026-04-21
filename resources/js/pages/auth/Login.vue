<script setup lang="ts">
import { Head, Form } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { useTranslation } from '@/composables/useTranslation';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();

const { t } = useTranslation();
</script>

<template>
    <Head :title="t('sign_in')" />

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2 bg-white font-sans selection:bg-indigo-100 selection:text-indigo-900">

        <div class="hidden lg:flex flex-col justify-between p-10 relative overflow-hidden bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800">
        </div>

        <div class="flex items-center justify-center px-6 py-12 relative bg-white">
            <div class="w-full max-w-[380px] relative z-10">

                <div class="flex items-center gap-2.5 mb-10 lg:hidden">
                    <div class="w-9 h-9 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                             stroke="#475569" stroke-width="2" stroke-linecap="round">
                            <circle cx="12" cy="12" r="9"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <span class="text-base font-bold text-slate-900 tracking-tight">AttendTrack</span>
                </div>

                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">{{ t('sign_in') }}</h1>
                    <p class="text-[15px] text-slate-500 mt-2">{{ t('credentials') }}</p>
                </div>

                <div v-if="status"
                     class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 flex items-center gap-3 text-sm font-medium text-emerald-700">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    {{ status }}
                </div>

                <Form
                    v-bind="store.form()"
                    :reset-on-success="['password']"
                    v-slot="{ errors, processing }"
                    class="flex flex-col gap-5"
                >
                    <div class="flex flex-col gap-2">
                        <Label for="email" class="text-[13px] font-semibold text-slate-700">
                            {{ t('email') }}
                        </Label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="email"
                            placeholder=""
                            class="bg-white border-slate-300 text-slate-900 placeholder:text-slate-400
                                   focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 
                                   h-10 text-[15px] rounded-lg shadow-sm transition-all duration-200"
                        />
                        <InputError :message="errors.email" class="text-[13px] font-medium text-red-500 mt-0.5" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <Label for="password" class="text-[13px] font-semibold text-slate-700">
                                {{ t('password') }}
                            </Label>
                            <TextLink
                                v-if="canResetPassword"
                                :href="request()"
                                :tabindex="5"
                                class="text-[13px] font-semibold text-indigo-600 hover:text-indigo-700 transition-colors"
                            >
                                {{ t('forgot_password') }}
                            </TextLink>
                        </div>
                        <PasswordInput
                            id="password"
                            name="password"
                            required
                            :tabindex="2"
                            autocomplete="current-password"
                            class="bg-white border-slate-300 text-slate-900 placeholder:text-slate-400
                                   focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 
                                   h-10 text-[15px] rounded-lg shadow-sm transition-all duration-200"
                        />
                        <InputError :message="errors.password" class="text-[13px] font-medium text-red-500 mt-0.5" />
                    </div>

                    <div class="flex items-center gap-3 mt-1">
                        <Checkbox
                            id="remember"
                            name="remember"
                            :tabindex="3"
                            class="border-slate-300 rounded-[4px] data-[state=checked]:bg-indigo-600 
                                   data-[state=checked]:border-indigo-600 text-white transition-all shadow-sm"
                        />
                        <Label for="remember" class="text-[14px] text-slate-600 font-medium cursor-pointer select-none">
                            {{ t('remember_me') }}
                        </Label>
                    </div>

                    <Button
                        type="submit"
                        :tabindex="4"
                        :disabled="processing"
                        data-test="login-button"
                        class="w-full mt-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold
                               h-10 text-[15px] rounded-lg shadow-md hover:shadow-lg 
                               transition-all duration-200 gap-2 disabled:opacity-70"
                    >
                        <Spinner v-if="processing" class="w-4 h-4 text-white" />
                        <svg v-else width="15" height="15" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/>
                            <line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>
                        {{ processing ? t('signing_in') : t('sign_in') }}
                    </Button>
                </Form>
            </div>
        </div>
    </div>
</template>