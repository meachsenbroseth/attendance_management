<script setup lang="ts">
	import { Head, Link, router } from '@inertiajs/vue3';
	import { destroy } from '@/actions/App/Http/Controllers/UserController';
	import Button from '@/components/ui/button/Button.vue';
	import {
	    Table,
	    TableBody,
	    TableCell,
	    TableHead,
	    TableHeader,
	    TableRow,
	} from '@/components/ui/table';
	import AppLayout from '@/layouts/AppLayout.vue';
	import { create as usersCreate, edit, index as usersIndex } from '@/routes/users';
	import type { BreadcrumbItem } from '@/types';

	const breadcrumbs: BreadcrumbItem[] = [
	    {
	        title: 'Users',
	        href: usersIndex(),
	    },
	];

interface User {
  id: number
  name: string
  email: string
  role: string
}

interface PaginationLink {
  url: string | null
  label: string
  active: boolean
}

	interface Paginated<T> {
	  data: T[]
	  links: PaginationLink[]
	}

const deleteUser = (id: number) => {
  if (confirm('Are you sure you want to delete this user?')) {
    // Wayfinder actions return an object with { url, method }
    router.visit(destroy(id));
  }
}

const editUser = (id: number) => {
  router.visit(edit(id));
}

defineProps<{
  users: Paginated<User>
}>()

</script>

<template>

  <Head title="User" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Users</h1>

        <Button as-child>
          <Link :href="usersCreate()">Add User</Link>
        </Button>
      </div>
      <div class="border rounded-md">
        <Table class="w-full">
          <TableHeader class=" bg-secondary">
            <TableRow>
              <TableHead>Id</TableHead>
              <TableHead>Name</TableHead>
              <TableHead>Email</TableHead>
              <TableHead>Role</TableHead>
              <TableHead class="text-right">Action</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="user in users.data" :key="user.id">
              <TableCell>{{ user.id }}</TableCell>
              <TableCell>{{ user.name }}</TableCell>
              <TableCell>{{ user.email }}</TableCell>
              <TableCell>{{ user.role }}</TableCell>

              <TableCell class="text-right">
                <div class="flex justify-end gap-2">
                  <Button variant="outline" size="sm" @click="editUser(user.id)">Edit</Button>
                  <Button variant="destructive" size="sm" @click="deleteUser(user.id)">
                    Delete
                  </Button>
                </div>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
	      <div class="mt-4 flex justify-end gap-1">
	        <Link
	            v-for="(link, i) in users.links"
	            :key="i"
	            :href="link.url || ''"
	            class="px-3 py-1 border rounded text-sm"
	            :class="[
	                link.active
	                    ? 'bg-primary text-primary-foreground'
	                    : 'bg-background hover:bg-muted',
	                !link.url && 'opacity-50 pointer-events-none',
	            ]"
	        >
	            <span v-html="link.label" />
	        </Link>
	      </div>
	    </div>
	  </AppLayout>
	</template>
