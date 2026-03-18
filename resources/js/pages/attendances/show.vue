<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import {
  Table, TableBody, TableCell,
  TableHead, TableHeader, TableRow,
} from '@/components/ui/table'
import Input from '@/components/ui/input/Input.vue'
import {
  Select, SelectContent, SelectItem,
  SelectTrigger, SelectValue,
} from '@/components/ui/select'
import { index as attendancesIndex, store as attendancesStore } from '@/routes/attendances'
import type { BreadcrumbItem } from '@/types'

interface Student {
  id: number
  name: string
  student_code: string
  status: 'present' | 'absent' | 'late'
}

interface Classroom {
  id: number
  name: string
}

const props = defineProps<{
  classroom: Classroom
  students: Student[]
  date: string
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Attendances', href: attendancesIndex.url() },
  { title: props.classroom.name, href: '#' },
]

const form = useForm({
  date: props.date,
  attendances: props.students.map(s => ({
    student_id: s.id,
    status: s.status,
  })),
})

const submit = () => {
  form.post(attendancesStore.url(props.classroom.id))
}

// Status badge color
const statusClass = (status: string) => ({
  'present': 'bg-green-100 text-green-700',
  'absent':  'bg-red-100 text-red-700',
  'late':    'bg-yellow-100 text-yellow-700',
}[status] ?? '')
</script>

<template>
  <Head :title="`Attendance – ${classroom.name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 p-4">

      <Card>
        <CardHeader class="flex items-center justify-between">
          <CardTitle>{{ classroom.name }} — Attendance</CardTitle>

          <!-- Date picker -->
          <Input
            type="date"
            class="w-48"
            v-model="form.date"
          />
        </CardHeader>

        <CardContent>
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

                <TableRow
                  v-for="(student, index) in students"
                  :key="student.id"
                >
                  <TableCell class="text-muted-foreground">{{ index + 1 }}</TableCell>
                  <TableCell class="font-medium">{{ student.name }}</TableCell>
                  <TableCell>
                    <span class="font-mono text-sm bg-muted px-2 py-0.5 rounded">
                      {{ student.student_code }}
                    </span>
                  </TableCell>
                  <TableCell>
                    <Select v-model="form.attendances[index].status">
                      <SelectTrigger class="w-32">
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="present">✅ Present</SelectItem>
                        <SelectItem value="absent">❌ Absent</SelectItem>
                        <SelectItem value="late">⏰ Late</SelectItem>
                      </SelectContent>
                    </Select>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>

          <!-- Save -->
          <div class="flex justify-end mt-6">
            <Button size="lg" @click="submit" :disabled="form.processing">
              Save Attendance
            </Button>
          </div>
        </CardContent>
      </Card>

    </div>
  </AppLayout>
</template>