<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Select, SelectContent, SelectItem,
  SelectTrigger, SelectValue,
} from '@/components/ui/select'

const props = defineProps<{
  classroom: { id: number; name: string }
}>()

const page = usePage()

const form = useForm({
  name:   '',
  gender: '',
})

const submit = () => {
  form.post(`/register/classroom/${props.classroom.id}`)
}
</script>

<template>
  <Head title="Student Registration" />

  <div class="min-h-screen bg-slate-50 flex items-center justify-center px-4">
    <div class="w-full max-w-md">

      <!-- Header -->
      <div class="text-center mb-8">
        <div class="w-14 h-14 bg-white border border-slate-200 rounded-2xl
                    flex items-center justify-center mx-auto mb-4 shadow-sm">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
               stroke="#475569" stroke-width="1.8" stroke-linecap="round">
            <circle cx="12" cy="12" r="9"/>
            <polyline points="12 6 12 12 16 14"/>
          </svg>
        </div>
        <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">
          Student Registration
        </h1>
        <p class="text-sm text-slate-500 mt-1">
          Joining
          <span class="font-medium text-slate-700">{{ classroom.name }}</span>
        </p>
      </div>

      <!-- Success state -->
      <div v-if="page.props.flash?.success"
           class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl
                  px-4 py-3 text-sm text-emerald-700 text-center">
        {{ page.props.flash.success }}
      </div>

      <!-- Form card -->
      <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <form @submit.prevent="submit" class="flex flex-col gap-5">

          <div class="flex flex-col gap-1.5">
            <Label for="name" class="text-xs font-medium text-slate-600">
              Full Name
            </Label>
            <Input
              id="name"
              v-model="form.name"
              type="text"
              placeholder="e.g. Sophea Chan"
              class="h-9 text-sm border-slate-200 focus-visible:ring-0
                     focus:border-slate-400"
            />
            <InputError :message="form.errors.name" class="text-xs text-red-500" />
          </div>

          <div class="flex flex-col gap-1.5">
            <Label for="gender" class="text-xs font-medium text-slate-600">
              Gender
            </Label>
            <Select v-model="form.gender">
              <SelectTrigger class="h-9 text-sm border-slate-200 w-full">
                <SelectValue placeholder="Select gender" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="male">Male</SelectItem>
                <SelectItem value="female">Female</SelectItem>
              </SelectContent>
            </Select>
            <InputError :message="form.errors.gender" class="text-xs text-red-500" />
          </div>

          <Button
            type="submit"
            :disabled="form.processing"
            class="w-full bg-slate-900 hover:bg-slate-700 text-white h-9 text-sm mt-1"
          >
            {{ form.processing ? 'Submitting...' : 'Submit Registration' }}
          </Button>

        </form>
      </div>

      <p class="text-center text-xs text-slate-400 mt-6">
        AttendTrack · Attendance Management System
      </p>
    </div>
  </div>
</template>