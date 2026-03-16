<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'
import { index as usersIndex } from '@/routes/users'

import { Card, CardAction, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import Button from '@/components/ui/button/Button.vue'
import Label from '@/components/ui/label/Label.vue'
import Input from '@/components/ui/input/Input.vue'
import Checkbox from '@/components/ui/checkbox/Checkbox.vue'
import InputError from '@/components/InputError.vue'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Users', href: usersIndex() },
]

// Example roles (replace with props from backend)
const roles = ['admin', 'teacher', 'student']

const form = useForm({
  name: '',
  email: '',
  password: '',
  roles: [] as string[],
})

type CheckedState = boolean | 'indeterminate'

const toggleRole = (role: string, checked: CheckedState): void => {
  if (checked === true) {
    if (!form.roles.includes(role)) {
      form.roles.push(role)
    }

    return
  }

  form.roles = form.roles.filter((existingRole) => existingRole !== role)
}

const submit = () => {
  form.post('/users')
}
</script>

<template>
  <Head title="Create User" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">

      <Card>
        <CardHeader class="flex items-center justify-between">
          <CardTitle>Create User</CardTitle>

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
              <Input
                id="name"
                type="text"
                v-model="form.name"
                :aria-invalid="!!form.errors.name"
              />
              <InputError :message="form.errors.name" />
            </div>

            <!-- Email -->
            <div class="mt-4">
              <Label for="email">Email</Label>
              <Input
                id="email"
                type="email"
                v-model="form.email"
                :aria-invalid="!!form.errors.email"
              />
              <InputError :message="form.errors.email" />
            </div>

            <!-- Password -->
            <div class="mt-4">
              <Label for="password">Password</Label>
              <Input
                id="password"
                type="password"
                v-model="form.password"
                :aria-invalid="!!form.errors.password"
              />
              <InputError :message="form.errors.password" />
            </div>

            <!-- Roles -->
            <div class="mt-6">
              <Label>Select Role</Label>

              <div class="my-4 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
                <div
                  v-for="role in roles"
                  :key="role"
                  class="flex items-center gap-3"
                >
                  <Checkbox
                    :id="role"
                    :checked="form.roles.includes(role)"
                    @update:checked="toggleRole(role, $event)"
                  />
                  <Label :for="role">{{ role }}</Label>
                </div>
              </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end">
              <Button size="lg" type="submit" :disabled="form.processing">
                Create
              </Button>
            </div>

          </form>
        </CardContent>
      </Card>

    </div>
  </AppLayout>
</template>
