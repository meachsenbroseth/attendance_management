<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import InputError from '@/components/InputError.vue'
import Button from '@/components/ui/button/Button.vue'
import { Card, CardContent, CardHeader, CardTitle, CardAction } from '@/components/ui/card'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import {
  Select, SelectContent, SelectItem,
  SelectTrigger, SelectValue,
} from '@/components/ui/select'
import {
  Table, TableBody, TableCell,
  TableHead, TableHeader, TableRow,
} from '@/components/ui/table'
import AppLayout from '@/layouts/AppLayout.vue'
import {
  index as classroomsIndex,
  edit as classroomsEdit,
  index,
} from '@/routes/classrooms'
import {
  add as classroomsStudentsAdd,
  remove as classroomsStudentsRemove,
} from '@/routes/classrooms/students'
import type { BreadcrumbItem } from '@/types'
import { useTranslation } from '@/composables/useTranslation'

const { t } = useTranslation()

interface Teacher {
  id: number
  name: string
}

interface Student {
  id: number
  name: string
  gender: string
  student_code: string
}

interface Classroom {
  id: number
  name: string
  teacher?: Teacher
  students: Student[]
}

const props = defineProps<{
  classroom: Classroom
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: t('classrooms'), href: classroomsIndex() },
  { title: props.classroom.name, href: '#' },
]

// ✅ QR modal state
const showQr = ref(false)
const qrSrc = ref<string | null>(null)
const qrLoading = ref(false)

const openQr = async () => {
  showQr.value = true
  if (qrSrc.value) return

  qrLoading.value = true

  try {
    const res = await fetch(`/classrooms/${props.classroom.id}/qrcode`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    })

    if (!res.ok) throw new Error(t('failed_load_qr'))

    const data = await res.json()
    qrSrc.value = data.svg
  } catch (error) {
    console.error(error)
  } finally {
    qrLoading.value = false
  }
}

// Add student form
const studentForm = useForm({
  name: '',
  gender: '',
})

const submitAddStudent = () => {
  studentForm.post(classroomsStudentsAdd.url(props.classroom.id), {
    onSuccess: () => studentForm.reset(),
  })
}

// Remove student
const removeStudent = (studentId: number) => {
  if (confirm(t('confirm_remove'))) {
    router.delete(classroomsStudentsRemove.url(studentId))
  }
}
</script>

<template>

  <Head :title="classroom.name" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 p-4">

      <!-- Classroom Info Card -->
      <Card>
        <CardHeader class="flex items-center justify-between">
          <CardTitle>{{ classroom.name }}</CardTitle>
          <CardAction>
            <div class="flex gap-2">
              <Link :href="index()">
                <Button size="sm">{{ t('back') }}</Button>
              </Link>
              <Link v-if="$page.props.auth.user.role === 'admin'" :href="classroomsEdit(classroom.id)">
                <Button variant="outline" size="sm">{{ t('edit_classroom') }}</Button>
              </Link>
            </div>
          </CardAction>
        </CardHeader>

        <CardContent>
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
              <p class="text-muted-foreground">{{ t('teacher') }}</p>
              <p class="font-medium">{{ classroom.teacher?.name ?? '—' }}</p>
            </div>
            <div>
              <p class="text-muted-foreground">{{ t('total_students') }}</p>
              <p class="font-medium">{{ classroom.students.length }}</p>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Students Card -->
      <Card>
        <CardHeader class="flex items-center justify-between">
          <CardTitle>{{ t('students') }}</CardTitle>
          <CardAction>
            <!-- ✅ QR button in the right place -->
            <Button variant="outline" size="sm" @click="openQr">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round" class="mr-1.5">
                <rect x="3" y="3" width="7" height="7" />
                <rect x="14" y="3" width="7" height="7" />
                <rect x="3" y="14" width="7" height="7" />
                <rect x="14" y="14" width="4" height="4" />
              </svg>
              {{ t('qr_code') }}
            </Button>
          </CardAction>
        </CardHeader>

        <CardContent class="flex flex-col gap-6">

          <!-- Add Student Form -->
          <form @submit.prevent="submitAddStudent" class="border rounded-md p-4 bg-muted/40">
            <div class="grid grid-cols-3 gap-3 items-end w-full">
              <div class="flex-1">
                <Label for="student_name">{{ t('student_name') }}</Label>
                <Input id="student_name" type="text" :placeholder="t('full_name')" v-model="studentForm.name"
                  :aria-invalid="!!studentForm.errors.name" />
                <InputError :message="studentForm.errors.name" />
              </div>

              <div class="flex-1">
                <Label for="gender">{{ t('gender') }}</Label>
                <Select v-model="studentForm.gender">
                  <SelectTrigger id="gender" class="w-full" :aria-invalid="!!studentForm.errors.gender">
                    <SelectValue :placeholder="t('select_gender')" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="male">{{ t('male') }}</SelectItem>
                    <SelectItem value="female">{{ t('female') }}</SelectItem>
                  </SelectContent>
                </Select>
                <InputError :message="studentForm.errors.gender" />
              </div>

              <div class="pt-6">
                <Button type="submit" :disabled="studentForm.processing">
                  + {{ t('add_student') }}
                </Button>
              </div>
            </div>
          </form>

          <!-- Students Table -->
          <div class="border rounded-md">
            <Table>
              <TableHeader class="bg-secondary">
                <TableRow>
                  <TableHead class="w-12">#</TableHead>
                  <TableHead>{{ t('name') }}</TableHead>
                  <TableHead>{{ t('gender') }}</TableHead>
                  <TableHead>{{ t('student_code') }}</TableHead>
                  <TableHead class="text-right">{{ t('action') }}</TableHead>
                </TableRow>
              </TableHeader>

              <TableBody>
                <TableRow v-if="classroom.students.length === 0">
                  <TableCell colspan="5" class="text-center text-muted-foreground py-10">
                    {{ t('no_students') }}
                  </TableCell>
                </TableRow>

                <TableRow v-for="(student, idx) in classroom.students" :key="student.id">
                  <TableCell class="text-muted-foreground">{{ idx + 1 }}</TableCell>
                  <TableCell class="font-medium">{{ student.name }}</TableCell>
                  <TableCell class="capitalize">{{ student.gender }}</TableCell>
                  <TableCell>
                    <span class="font-mono text-sm bg-muted px-2 py-0.5 rounded">
                      {{ student.student_code }}
                    </span>
                  </TableCell>
                  <TableCell class="text-right">
                    <Button variant="destructive" size="sm" @click="removeStudent(student.id)">
                      {{ t('remove') }}
                    </Button>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>

        </CardContent>
      </Card>

    </div>
  </AppLayout>

  <!-- ✅ QR Modal — outside AppLayout so it overlays everything -->
  <Teleport to="body">
    <div v-if="showQr" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
      @click.self="showQr = false">
      <div class="bg-white rounded-2xl p-8 shadow-2xl flex flex-col items-center gap-4 max-w-xs w-full mx-4">

        <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="1.8"
            stroke-linecap="round">
            <rect x="3" y="3" width="7" height="7" />
            <rect x="14" y="3" width="7" height="7" />
            <rect x="3" y="14" width="7" height="7" />
            <rect x="14" y="14" width="4" height="4" />
          </svg>
        </div>

        <div class="text-center">
          <h2 class="text-lg font-semibold text-slate-800">{{ t('scan_to_register') }}</h2>
          <p class="text-sm text-slate-500 mt-1">
            {{ t('scan_join') }}
            <span class="font-medium text-slate-700">{{ classroom.name }}</span>
          </p>
        </div>

        <!-- ✅ Loading state -->
        <div v-if="qrLoading" class="w-52 h-52 rounded-xl border border-slate-200 bg-slate-50
                        flex items-center justify-center">
          <svg class="animate-spin w-6 h-6 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2">
            <circle cx="12" cy="12" r="10" stroke-opacity="0.25" />
            <path d="M12 2a10 10 0 0110 10" stroke-linecap="round" />
          </svg>
        </div>

        <!-- ✅ QR loads from base64 data URL — no auth issue -->
        <img v-else-if="qrSrc" :src="qrSrc" :alt="t('qr_code')" class="w-52 h-52 rounded-xl border border-slate-200" />

        <div class="flex flex-col gap-2 w-full">
          <!-- <a v-if="qrSrc" :href="qrSrc" :download="`qrcode-${classroom.name.replace(/\s+/g, '-').toLowerCase()}.svg`"
            class="w-full">
            <Button class="w-full" variant="outline">
              Download QR
            </Button>
          </a> -->

          <Button class="w-full" variant="ghost" @click="showQr = false">
            {{ t('close') }}
          </Button>
        </div>
      </div>
    </div>
  </Teleport>
</template>