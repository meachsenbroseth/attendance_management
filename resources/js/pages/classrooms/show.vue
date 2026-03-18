<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import InputError from '@/components/InputError.vue'
import Button from '@/components/ui/button/Button.vue'
import { Card, CardContent, CardHeader, CardTitle, CardAction } from '@/components/ui/card'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import AppLayout from '@/layouts/AppLayout.vue'
import {
  index as classroomsIndex,
  edit as classroomsEdit,
} from '@/routes/classrooms'
import {
  add as classroomsStudentsAdd,
  remove as classroomsStudentsRemove,
} from '@/routes/classrooms/students'
import type { BreadcrumbItem } from '@/types'

// Types
interface Teacher {
  id: number
  name: string
}

interface Student {
  id: number
  name: string
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
  { title: 'Classrooms', href: classroomsIndex() },
  { title: props.classroom.name, href: '#' },
]

// Add student form
const studentForm = useForm({
  name: '',
})

const submitAddStudent = () => {
  studentForm.post(classroomsStudentsAdd.url(props.classroom.id), {
    onSuccess: () => studentForm.reset(),
  })
}

// Remove student
const removeStudent = (studentId: number) => {
  if (confirm('Remove this student from the classroom?')) {
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
            <Link :href="classroomsEdit(classroom.id)">
              <Button variant="outline" size="sm">Edit Classroom</Button>
            </Link>
          </CardAction>
        </CardHeader>

        <CardContent>
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
              <p class="text-muted-foreground">Teacher</p>
              <p class="font-medium">{{ classroom.teacher?.name ?? '—' }}</p>
            </div>
            <div>
              <p class="text-muted-foreground">Total Students</p>
              <p class="font-medium">{{ classroom.students.length }}</p>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Students Card -->
      <Card>
        <CardHeader>
          <CardTitle>Students</CardTitle>
        </CardHeader>

        <CardContent class="flex flex-col gap-6">

          <!-- Add Student Form -->
          <form @submit.prevent="submitAddStudent" class="flex gap-3 items-start border rounded-md p-4 bg-muted/40">
            <div class="flex-1">
              <Label for="student_name">Student Name</Label>
              <Input id="student_name" type="text" placeholder="Full name" v-model="studentForm.name"
                :aria-invalid="!!studentForm.errors.name" />
              <InputError :message="studentForm.errors.name" />
            </div>

            <div class="pt-6">
              <Button type="submit" :disabled="studentForm.processing">
                + Add Student
              </Button>
            </div>
          </form>

          <!-- Students Table -->
          <div class="border rounded-md">
            <Table>
              <TableHeader class="bg-secondary">
                <TableRow>
                  <TableHead class="w-12">#</TableHead>
                  <TableHead>Name</TableHead>
                  <TableHead>Student Code</TableHead>
                  <TableHead class="text-right">Action</TableHead>
                </TableRow>
              </TableHeader>

              <TableBody>
                <TableRow v-if="classroom.students.length === 0">
                  <TableCell colspan="4" class="text-center text-muted-foreground py-10">
                    No students in this classroom yet.
                  </TableCell>
                </TableRow>

                <TableRow v-for="(student, index) in classroom.students" :key="student.id">
                  <TableCell class="text-muted-foreground">{{ index + 1 }}</TableCell>
                  <TableCell class="font-medium">{{ student.name }}</TableCell>
                  <TableCell>
                    <span class="font-mono text-sm bg-muted px-2 py-0.5 rounded">
                      {{ student.student_code }}
                    </span>
                  </TableCell>
                  <TableCell class="text-right">
                    <Button variant="destructive" size="sm" @click="removeStudent(student.id)">
                      Remove
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
</template>
