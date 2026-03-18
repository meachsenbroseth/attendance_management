<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, toRefs } from 'vue'
import InputError from '@/components/InputError.vue'
import Button from '@/components/ui/button/Button.vue'
import { Card, CardAction, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import AppLayout from '@/layouts/AppLayout.vue'
import {
  index as classroomsIndex,
  update as classroomsUpdate,
} from '@/routes/classrooms/index'
import type { BreadcrumbItem } from '@/types'

interface Teacher {
  id: number
  name: string
}

interface Classroom {
  id: number
  name: string
  teacher_id: number
  image: string | null  // ✅ add image
}

const props = defineProps<{
  classroom: Classroom
  teachers: Teacher[]
}>()

const { classroom, teachers } = toRefs(props)

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Classrooms', href: classroomsIndex.url() },
  { title: classroom.value.name, href: '#' },
]

const form = useForm({
  name: classroom.value.name,
  teacher_id: String(classroom.value.teacher_id),  // ✅ String for Select
  image: null as File | null,
})

// ✅ Preview: starts as null, shows existing image from storage if no new file chosen
const imagePreview = ref<string | null>(null)

const onImageChange = (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0]

  if (file) {
    form.image = file
    imagePreview.value = URL.createObjectURL(file)
  }
}

// ✅ Update route is POST to support multipart uploads
const submit = () => {
  form.post(classroomsUpdate.url(classroom.value.id), {
    forceFormData: true,
  })
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Edit Classroom" />
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">

      <Card>
        <CardHeader class="flex items-center justify-between">
          <CardTitle>Edit Classroom</CardTitle>
          <CardAction>
            <Link :href="classroomsIndex.url()">
              <Button variant="outline">Go back</Button>
            </Link>
          </CardAction>
        </CardHeader>

        <CardContent>
          <form @submit.prevent="submit">

            <!-- Class Name -->
            <div class="mt-4">
              <Label for="name">Class Name</Label>
              <Input
                id="name"
                type="text"
                v-model="form.name"
                :aria-invalid="!!form.errors.name"
              />
              <InputError :message="form.errors.name" />
            </div>

            <!-- Teacher -->
            <div class="mt-6">
              <Label for="teacher">Assign Teacher</Label>
              <Select v-model="form.teacher_id">
                <SelectTrigger id="teacher" class="w-full" :aria-invalid="!!form.errors.teacher_id">
                  <SelectValue placeholder="Select teacher" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem
                    v-for="teacher in teachers"
                    :key="teacher.id"
                    :value="String(teacher.id)"
                  >
                    {{ teacher.name }}
                  </SelectItem>
                </SelectContent>
              </Select>
              <InputError :message="form.errors.teacher_id" />
            </div>

            <!-- Image Upload -->
            <div class="mt-6">
              <Label for="image">Classroom Image</Label>

              <!-- Show new preview OR existing image from storage -->
              <div v-if="imagePreview || classroom.image" class="mt-2 mb-3">
                <img
                  :src="imagePreview ?? `/storage/${classroom.image}`"
                  class="w-32 h-32 object-cover rounded-lg border"
                  alt="Classroom image"
                />
              </div>

              <Input
                id="image"
                type="file"
                accept="image/*"
                @change="onImageChange"
                :aria-invalid="!!form.errors.image"
              />
              <InputError :message="form.errors.image" />
            </div>

            <!-- Submit -->
            <div class="flex justify-end mt-6">
              <Button size="lg" type="submit" :disabled="form.processing">
                Update Classroom
              </Button>
            </div>

          </form>
        </CardContent>
      </Card>

    </div>
  </AppLayout>
</template>
