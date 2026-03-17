<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'
import { index as usersIndex, update as usersUpdate } from '@/routes/users'

import { Card, CardAction, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import Button from '@/components/ui/button/Button.vue'
import Label from '@/components/ui/label/Label.vue'
import Input from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

interface User {
  id: number
  name: string
  email: string
  role: 'admin' | 'teacher' | 'student'
}

const props = defineProps<{
  user: User
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Users', href: usersIndex() },
]

const roles = ['admin', 'teacher', 'student'] as const

// ⭐ Prefill form with existing user data
const form = useForm({
  name: props.user.name,
  email: props.user.email,
  password: '',
  role: props.user.role,
})

// ⭐ Submit UPDATE request
const submit = () => {
  form.put(usersUpdate.url(props.user.id))
}
</script>

<template>

  <Head title="Edit User" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">

      <Card>
        <CardHeader class="flex items-center justify-between">
          <CardTitle>Edit User</CardTitle>

          <CardAction>
            <Link :href="usersIndex()">
              <Button>Go back</Button>
            </Link>
          </CardAction>
        </CardHeader>

        <CardContent>
          <form @submit.prevent="submit">

            <!-- Name -->
            <div class="mt-4">
              <Label for="name">Name</Label>
              <Input id="name" type="text" v-model="form.name" />
              <InputError :message="form.errors.name" />
            </div>

            <!-- Email -->
            <div class="mt-4">
              <Label for="email">Email</Label>
              <Input id="email" type="email" v-model="form.email" />
              <InputError :message="form.errors.email" />
            </div>

            <!-- Password -->
            <div class="mt-4">
              <Label for="password">Password (leave blank to keep)</Label>
              <Input id="password" type="password" v-model="form.password" />
              <InputError :message="form.errors.password" />
            </div>

            <!-- Role -->
            <div class="mt-6">
              <Label for="role">Role</Label>

              <Select v-model="form.role">
                <SelectTrigger id="role" class="w-full">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem v-for="role in roles" :key="role" :value="role">
                    {{ role }}
                  </SelectItem>
                </SelectContent>
              </Select>

              <InputError :message="form.errors.role" />
            </div>

            <!-- Submit -->
            <div class="flex justify-end">
              <Button size="lg" type="submit" :disabled="form.processing">
                Update
              </Button>
            </div>

          </form>
        </CardContent>
      </Card>

    </div>
  </AppLayout>
</template>
