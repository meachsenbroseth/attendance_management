<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as attendancesIndex, show as attendancesShow } from '@/routes/attendances'
import type { BreadcrumbItem } from '@/types';
// import CardContent from '@/components/ui/card/CardContent.vue';


interface Classroom {
  id: number
  name: string
  image: string | null
}

defineProps<{
  classrooms: Classroom[]
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Attendances', href: attendancesIndex.url() },
]
</script>


<template>

  <Head title="Attendances" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4">

      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">My Classrooms</h1>
      </div>

      <div v-if="classrooms.length === 0" class="text-muted-foreground text-center py-20">
        You have no classrooms assigned.
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <Card v-for="classroom in classrooms" :key="classroom.id"
          class="cursor-pointer hover:shadow-md transition-shadow overflow-hidden p-0"
          @click="router.visit(attendancesShow.url(classroom.id))">
          <!-- Image -->
          <img v-if="classroom.image" :src="`/storage/${classroom.image}`" :alt="classroom.name"
            class="w-full h-44 object-cover" />
          <!-- Fallback -->
          <div v-else class="w-full h-44 bg-muted flex items-center justify-center">
            <span class="text-muted-foreground text-sm">No image</span>
          </div>

          <!-- Title -->
          <div class="px-4 py-3">
            <p class="text-base font-semibold text-center">{{ classroom.name }}</p>
          </div>
        </Card>
      </div>

    </div>
  </AppLayout>
</template>
