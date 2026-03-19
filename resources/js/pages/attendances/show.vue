<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3'  
import { computed } from 'vue'
import Button from '@/components/ui/button/Button.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import {
  Select, SelectContent, SelectItem,
  SelectTrigger, SelectValue,
} from '@/components/ui/select'
import {
  Table, TableBody, TableCell,
  TableHead, TableHeader, TableRow,
} from '@/components/ui/table'
import AppLayout from '@/layouts/AppLayout.vue'
import { index as attendancesIndex, store as attendancesStore } from '@/routes/attendances'
import type { BreadcrumbItem, PageProps } from '@/types'

interface Student {
  id: number
  name: string
  student_code: string
  status: 'present' | 'absent' | 'permission'
}

interface Classroom {
  id: number
  name: string
}

const props = defineProps<{
  classroom: Classroom
  students: Student[]
  date: string
  alreadyMarked: boolean
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Attendances', href: attendancesIndex.url() },
  { title: props.classroom.name, href: '#' },
]

const form = useForm({
  date: props.date, 
  attendances: props.students.map((student) => ({
    student_id: student.id,
    status: student.status,
  })),
})

const success = computed(() => usePage<PageProps>().props.flash?.success)

const submit = () => {
  form.post(attendancesStore.url(props.classroom.id))
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head :title="`Attendance – ${classroom.name}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4">
      <Card>
        <CardHeader class="flex items-center justify-between">
          <CardTitle>{{ classroom.name }} — Attendance</CardTitle>

          <!-- ✅ Show date as read-only text, no input -->
          <span class="text-sm text-muted-foreground">📅 {{ date }}</span>
        </CardHeader>

        <CardContent>

          <!-- Success message -->
          <div v-if="success"
            class="mb-4 flex items-center gap-2 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            ✅ {{ success }}
          </div>

          <!-- Already marked warning -->
          <div v-if="alreadyMarked"
            class="mb-4 flex items-center gap-2 rounded-md border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-700">
            ⚠️ Attendance for this date has already been recorded.
          </div>

          <div class="border rounded-md">
            <Table>
              <TableHeader class="bg-secondary">
                <TableRow>
                  <TableHead class="w-12">#</TableHead>
                  <TableHead>Student</TableHead>
                  <TableHead>Code</TableHead>
                  <TableHead>Status</TableHead>
                </TableRow>
              </TableHeader>

              <TableBody>
                <TableRow v-if="students.length === 0">
                  <TableCell colspan="4" class="text-center text-muted-foreground py-10">
                    No students in this classroom.
                  </TableCell>
                </TableRow>

                <TableRow v-for="(student, index) in students" :key="student.id">
                  <TableCell class="text-muted-foreground">{{ index + 1 }}</TableCell>
                  <TableCell class="font-medium">{{ student.name }}</TableCell>
                  <TableCell>
                    <span class="font-mono text-sm bg-muted px-2 py-0.5 rounded">
                      {{ student.student_code }}
                    </span>
                  </TableCell>
                  <TableCell>
                    <Select v-model="form.attendances[index].status" :disabled="alreadyMarked">
                      <SelectTrigger class="w-32" :disabled="alreadyMarked">
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="present">✅ Present</SelectItem>
                        <SelectItem value="absent">❌ Absent</SelectItem>
                        <SelectItem value="permission">📝 Permission</SelectItem>
                      </SelectContent>
                    </Select>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>

          <!-- Save -->
          <div class="flex justify-end mt-6">
            <Button size="lg" @click="submit" :disabled="form.processing || alreadyMarked">
              Save Attendance
            </Button>
          </div>

        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>