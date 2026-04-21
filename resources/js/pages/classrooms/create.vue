<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
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
  store as classroomsStore,
} from '@/routes/classrooms/index'
import type { BreadcrumbItem } from '@/types'
import { useTranslation } from '@/composables/useTranslation'

const { t } = useTranslation()

const breadcrumbs: BreadcrumbItem[] = [
  { title: t('classrooms'), href: classroomsIndex.url() },
]

interface Teacher {
  id: number
  name: string
}

defineProps<{
  teachers: Teacher[]
}>()

const form = useForm({
  name: '',
  teacher_id: '',
  image: null as File | null,
})

// ✅ Preview ref — no classroom prop needed on create page
const imagePreview = ref<string | null>(null)

const onImageChange = (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0]

  if (file) {
    form.image = file
    imagePreview.value = URL.createObjectURL(file)
  }
}

const submit = () => {
  form.post(classroomsStore.url(), {
    forceFormData: true,
  })
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head :title="t('create_classroom')" />
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">

      <Card>
        <CardHeader class="flex items-center justify-between">
          <CardTitle>{{ t('create_classroom') }}</CardTitle>
          <CardAction>
            <Link :href="classroomsIndex.url()">
              <Button variant="outline">{{ t('go_back') }}</Button>
            </Link>
          </CardAction>
        </CardHeader>

        <CardContent>
          <form @submit.prevent="submit">

            <!-- Class Name -->
            <div class="mt-4">
              <Label for="name">{{ t('class_name') }}</Label>
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
              <Label for="teacher">{{ t('assign_teacher') }}</Label>
              <Select v-model="form.teacher_id">
                <SelectTrigger id="teacher" class="w-full" :aria-invalid="!!form.errors.teacher_id">
                  <SelectValue :placeholder="t('select_teacher')" />
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
              <Label for="image">{{ t('classroom_image') }}</Label>

              <!-- Preview — only show if a file was selected -->
              <div v-if="imagePreview" class="mt-2 mb-3">
                <img
                  :src="imagePreview"
                  class="w-32 h-32 object-cover rounded-lg border"
                  :alt="t('classroom_image_preview')"
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
                {{ t('create_classroom') }}
              </Button>
            </div>

          </form>
        </CardContent>
      </Card>

    </div>
  </AppLayout>
</template>
