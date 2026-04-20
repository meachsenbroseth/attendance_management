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

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <Head title="Sign in" />

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2 bg-white">

        <!-- ── LEFT PANEL ─────────────────────────── -->
        <div class="hidden lg:flex flex-col justify-between p-10 border-r border-slate-200 relative overflow-hidden bg-slate-50">

            <!-- dot grid -->
            <div class="absolute inset-0 opacity-60"
                 style="background-image: radial-gradient(circle, #cbd5e1 1px, transparent 1px); background-size: 24px 24px;" />

            <!-- Brand -->
            <div class="relative z-10 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white border border-slate-200 shadow-sm flex items-center justify-center">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                         stroke="#475569" stroke-width="1.8" stroke-linecap="round">
                        <circle cx="12" cy="12" r="9"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-800 tracking-tight">AttendTrack</p>
                    <p class="text-xs text-slate-400">Attendance Management</p>
                </div>
            </div>

            <!-- Illustration card -->
            <div class="relative z-10 flex items-center justify-center py-4">
                <div class="relative w-full max-w-xs">

                    <!-- Floating badge -->
                    <div class="absolute -top-4 -right-3 bg-white border border-slate-200 rounded-xl px-3 py-2 shadow-md z-10">
                        <p class="text-xs text-slate-400">Classroom</p>
                        <p class="text-sm font-semibold text-slate-800">Grade 10-A</p>
                    </div>

                    <!-- Main card -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-lg">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-xs font-medium text-slate-400 uppercase tracking-widest">
                                Today's Attendance
                            </p>
                            <span class="flex items-center gap-1.5 text-xs text-emerald-600 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
                                Live
                            </span>
                        </div>

                        <!-- Rows -->
                        <div class="space-y-2">
                            <div v-for="(row, i) in [
                                { name: 'Sophea Chan',  time: '08:01 AM', status: 'present' },
                                { name: 'Dara Nget',    time: '08:14 AM', status: 'present' },
                                { name: 'Maly Pich',    time: '08:32 AM', status: 'late'    },
                                { name: 'Virak Heng',   time: '—',        status: 'absent'  },
                            ]" :key="i"
                               class="flex items-center gap-3 p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0"
                                     :class="{
                                         'bg-blue-100 text-blue-600':    row.status === 'present',
                                         'bg-amber-100 text-amber-600':  row.status === 'late',
                                         'bg-slate-100 text-slate-400':  row.status === 'absent',
                                     }">
                                    {{ row.name.split(' ').map(n => n[0]).join('') }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-slate-700 truncate">{{ row.name }}</p>
                                    <p class="text-xs text-slate-400">{{ row.time }}</p>
                                </div>
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                                      :class="{
                                          'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200':  row.status === 'present',
                                          'bg-amber-50 text-amber-600 ring-1 ring-amber-200':        row.status === 'late',
                                          'bg-slate-100 text-slate-400 ring-1 ring-slate-200':       row.status === 'absent',
                                      }">
                                    {{ row.status }}
                                </span>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-3 gap-2 text-center">
                            <div>
                                <p class="text-base font-bold text-emerald-600">24</p>
                                <p class="text-xs text-slate-400">Present</p>
                            </div>
                            <div>
                                <p class="text-base font-bold text-amber-500">3</p>
                                <p class="text-xs text-slate-400">Late</p>
                            </div>
                            <div>
                                <p class="text-base font-bold text-slate-400">2</p>
                                <p class="text-xs text-slate-400">Absent</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Headline + stats -->
            <div class="relative z-10 space-y-5">
                <div>
                    <h2 class="text-2xl font-semibold text-slate-800 leading-snug tracking-tight">
                        Manage attendance<br>with precision.
                    </h2>
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed">
                        Track check-ins, monitor classrooms,<br>
                        and export reports — all in one place.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
                        <div class="flex items-center gap-2 text-base font-semibold text-slate-800">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
                            Live
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Real-time sync</p>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
                        <p class="text-base font-semibold text-slate-800">100%</p>
                        <p class="text-xs text-slate-400 mt-1">Audit ready</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── RIGHT PANEL ────────────────────────── -->
        <div class="flex items-center justify-center px-6 py-12 bg-white">
            <div class="w-full max-w-sm">

                <!-- Mobile brand -->
                <div class="flex items-center gap-2 mb-8 lg:hidden">
                    <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-center">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                             stroke="#475569" stroke-width="1.8" stroke-linecap="round">
                            <circle cx="12" cy="12" r="9"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-slate-800">AttendTrack</span>
                </div>

                <!-- Heading -->
                <div class="mb-7">
                    <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">Sign in</h1>
                    <p class="text-sm text-slate-500 mt-1">Enter your credentials to continue</p>
                </div>

                <!-- Status flash -->
                <div v-if="status"
                     class="mb-5 rounded-lg bg-emerald-50 border border-emerald-200 px-3 py-2.5 text-center text-xs text-emerald-700">
                    {{ status }}
                </div>

                <Form
                    v-bind="store.form()"
                    :reset-on-success="['password']"
                    v-slot="{ errors, processing }"
                    class="flex flex-col gap-5"
                >
                    <!-- Email -->
                    <div class="flex flex-col gap-1.5">
                        <Label for="email" class="text-xs font-medium text-slate-600">
                            Email
                        </Label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="email"
                            placeholder="you@school.edu"
                            class="bg-white border-slate-200 text-slate-900 placeholder:text-slate-400
                                   focus:border-slate-400 focus-visible:ring-0 h-9 text-sm shadow-sm"
                        />
                        <InputError :message="errors.email" class="text-xs text-red-500" />
                    </div>

                    <!-- Password -->
                    <div class="flex flex-col gap-1.5">
                        <div class="flex items-center justify-between">
                            <Label for="password" class="text-xs font-medium text-slate-600">
                                Password
                            </Label>
                            <TextLink
                                v-if="canResetPassword"
                                :href="request()"
                                :tabindex="5"
                                class="text-xs text-slate-400 hover:text-slate-700 transition-colors"
                            >
                                Forgot password?
                            </TextLink>
                        </div>
                        <PasswordInput
                            id="password"
                            name="password"
                            required
                            :tabindex="2"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="bg-white border-slate-200 text-slate-900 placeholder:text-slate-400
                                   focus:border-slate-400 focus-visible:ring-0 h-9 text-sm shadow-sm"
                        />
                        <InputError :message="errors.password" class="text-xs text-red-500" />
                    </div>

                    <!-- Remember me -->
                    <div class="flex items-center gap-2.5">
                        <Checkbox
                            id="remember"
                            name="remember"
                            :tabindex="3"
                            class="border-slate-300 data-[state=checked]:bg-slate-800
                                   data-[state=checked]:border-slate-800 data-[state=checked]:text-white"
                        />
                        <Label for="remember" class="text-sm text-slate-500 font-normal cursor-pointer">
                            Remember me for 30 days
                        </Label>
                    </div>

                    <!-- Submit -->
                    <Button
                        type="submit"
                        :tabindex="4"
                        :disabled="processing"
                        data-test="login-button"
                        class="w-full bg-slate-900 hover:bg-slate-700 text-white font-medium
                               h-9 text-sm transition-colors gap-2 shadow-sm"
                    >
                        <Spinner v-if="processing" class="w-4 h-4" />
                        <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                            <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/>
                            <line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>
                        {{ processing ? 'Signing in...' : 'Sign in' }}
                    </Button>
                </Form>
            </div>
        </div>
    </div>
</template>