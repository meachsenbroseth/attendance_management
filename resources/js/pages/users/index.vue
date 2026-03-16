<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { create as usersCreate, index as usersIndex } from '@/routes/users';

import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow
} from '@/components/ui/table';
import Button from '@/components/ui/button/Button.vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Users',
    href: usersIndex(),
  },
];
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
            <TableRow>
              <TableCell>1</TableCell>
              <TableCell>tom</TableCell>
              <TableCell>tom@gmail.com</TableCell>
              <TableCell>teacher</TableCell>
              <TableCell class="text-right">
                <div class="flex justify-end gap-2">
                  <Button variant="outline" size="sm">Edit</Button>

                  <Button variant="destructive" size="sm">
                    Delete
                  </Button>
                </div>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </div>
  </AppLayout>
</template>
