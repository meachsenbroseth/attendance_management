<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'
import Button from '@/components/ui/button/Button.vue'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow
} from '@/components/ui/table'
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'
import {
  index as classroomsIndex,
  create as classroomsCreate,
  edit as classroomsEdit,
  destroy as classroomsDestroy,
  show as classroomsShow
} from '@/routes/classrooms/index'

import type { BreadcrumbItem } from '@/types'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: dashboard() },
  { title: 'Classrooms', href: classroomsIndex() }
]

// Types
interface Classroom {
  id: number
  name: string
  teacher?: { name: string }
}

interface Paginated<T> {
  data: T[]
  links: any[]
}

// Props from Laravel
const props = defineProps<{
  classrooms: Paginated<Classroom>
}>()

const classroomsSortedById = computed(() => {
  return [...props.classrooms.data].sort((a, b) => a.id - b.id)
})

// Actions
const editClassroom = (id: number) => {
  router.visit(classroomsEdit(id))
}

const deleteClassroom = (id: number) => {
  if (confirm('Delete this classroom?')) {
    router.visit(classroomsDestroy(id))
  }
}
</script>

<template>

  <Head title="Classrooms" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">

      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Classrooms</h1>

        <Button as-child>
          <Link v-if="$page.props.auth.user.role === 'admin'" :href="classroomsCreate()">Add Classroom</Link>
        </Button>
      </div>

      <!-- Table -->
      <div class="border rounded-md">
        <Table class="w-full">
          <TableHeader class="bg-secondary">
            <TableRow>
              <TableHead>Id</TableHead>
              <TableHead>Name</TableHead>
              <TableHead>Teacher</TableHead>
              <TableHead class="text-right">Action</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody>
            <TableRow v-for="classroom in classroomsSortedById" :key="classroom.id"
              class="hover:bg-muted/50 transition">
              <TableCell class="font-medium text-muted-foreground">
                #{{ classroom.id }}
              </TableCell>

              <TableCell class="font-semibold">
                {{ classroom.name }}
              </TableCell>

              <TableCell>
                <span class="text-muted-foreground">
                  {{ classroom.teacher?.name ?? 'No teacher' }}
                </span>
              </TableCell>

              <TableCell class="text-right">
                <div class="flex justify-end items-center gap-2">

                  <!-- 👁 View (Primary) -->
                  <Button size="sm" @click="router.visit(classroomsShow(classroom.id))">
                    View
                  </Button>

                  <!-- ✏️ Edit (only admin or owner) -->
                  <Button
                    v-if="$page.props.auth.user.role === 'admin'"
                    variant="outline" size="sm" @click="editClassroom(classroom.id)">
                    Edit
                  </Button>

                  <!-- 🗑 Delete (admin only) -->
                  <Button v-if="$page.props.auth.user.role === 'admin'" variant="destructive" size="sm"
                    @click="deleteClassroom(classroom.id)">
                    Delete
                  </Button>

                </div>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      <!-- Pagination -->
      <div class="mt-4 flex justify-end gap-1">
        <Link v-for="(link, i) in classrooms.links" :key="i" :href="link.url || ''"
          class="px-3 py-1 border rounded text-sm" :class="[
            link.active
              ? 'bg-primary text-primary-foreground'
              : 'bg-background hover:bg-muted',
            !link.url && 'opacity-50 pointer-events-none'
          ]">
          <span v-html="link.label" />
        </Link>
      </div>

    </div>
  </AppLayout>
</template>
