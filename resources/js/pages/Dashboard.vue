<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
  Users,
  GraduationCap,
  BookOpen,
  School,
  CheckCircle,
  UserX,
  TrendingUp,
  CalendarDays
} from 'lucide-vue-next'
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';
import Card from '@/components/ui/card/Card.vue';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: dashboard(),
  },
];
interface ClassAttendance {
  name: string
  present: number
  total: number
  rate: number
}

interface WeeklyTrend {
  day: string
  rate: number
}

interface Stats {
  totalUsers: number
  totalTeachers: number
  totalStudents: number
  totalClassrooms: number
  todayRate: number
}

interface BottomStats {
  studentsPresent: number
  studentsAbsent: number
  teachersActive: number
}

const props = defineProps<{
  stats: Stats
  todayClassAttendance: ClassAttendance[]
  weeklyTrend: WeeklyTrend[]
  bottomStats: BottomStats
}>()

const statCards = computed(() => [
  {
    label: 'Total Teachers',
    value: props.stats.totalTeachers.toLocaleString(),
    icon: GraduationCap,
    bg: 'bg-blue-100',
    color: 'text-blue-500',
  },
  {
    label: 'Total Students',
    value: props.stats.totalStudents.toLocaleString(),
    icon: BookOpen,
    bg: 'bg-green-100',
    color: 'text-green-500',
  },
  {
    label: 'Total Classrooms',
    value: props.stats.totalClassrooms.toLocaleString(),
    icon: School,
    bg: 'bg-orange-100',
    color: 'text-orange-500',
  },
  {
    label: "Today's Attendance Rate",
    value: `${props.stats.todayRate}%`,
    icon: CheckCircle,
    bg: 'bg-teal-100',
    color: 'text-teal-500',
  },
])
</script>
<template>

  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 flex flex-col gap-6">

      <!-- Top Stats -->
      <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        <Card v-for="item in statCards" :key="item.label">
          <CardContent class="flex items-center justify-between p-5">
            <div class="flex flex-col gap-1">
              <p class="text-xs text-muted-foreground">{{ item.label }}</p>
              <p class="text-3xl font-bold">{{ item.value }}</p>
            </div>
            <div :class="`w-11 h-11 rounded-full ${item.bg} flex items-center justify-center shrink-0`">
              <component :is="item.icon" :class="`w-5 h-5 ${item.color}`" />
            </div>
          </CardContent>
        </Card>
      </div>
      <!-- Middle Charts -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Today's Class Attendance -->
        <Card class="p-5">
          <CardHeader class="pb-2">
            <CardTitle class="flex items-center gap-2 text-sm font-semibold">
              <component :is="CalendarDays" class="w-4 h-4 text-muted-foreground" />
              Today's Class Attendance
            </CardTitle>
          </CardHeader>
          <CardContent class="flex flex-col gap-4">
            <div v-if="todayClassAttendance.length === 0" class="text-muted-foreground text-sm text-center py-6">
              No attendance recorded today.
            </div>
            <div v-for="item in todayClassAttendance" :key="item.name" class="flex flex-col gap-2">
              <div class="flex items-center justify-between text-sm">
                <span class="font-medium">{{ item.name }}</span>
                <span class="text-muted-foreground text-xs">
                  {{ item.present }}/{{ item.total }} ({{ item.rate }}%)
                </span>
              </div>
              <div class="w-full bg-muted rounded-full h-2">
                <div class="h-2 rounded-full bg-primary transition-all duration-500"
                  :style="{ width: `${item.rate}%` }" />
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Weekly Attendance Trend -->
        <Card class="p-5">
          <CardHeader class="pb-2">
            <CardTitle class="flex items-center gap-2 text-sm font-semibold">
              <component :is="TrendingUp" class="w-4 h-4 text-muted-foreground" />
              Weekly Attendance Trend
            </CardTitle>
          </CardHeader>
          <CardContent class="flex flex-col gap-4">
            <div v-if="weeklyTrend.length === 0" class="text-muted-foreground text-sm text-center py-6">
              No data this week.
            </div>
            <div v-for="item in weeklyTrend" :key="item.day" class="flex flex-col gap-2">
              <div class="flex items-center justify-between text-sm">
                <span class="font-medium">{{ item.day }}</span>
                <span class="text-muted-foreground text-xs">{{ item.rate }}%</span>
              </div>
              <div class="w-full bg-muted rounded-full h-2">
                <div class="h-2 rounded-full bg-primary transition-all duration-500"
                  :style="{ width: `${item.rate}%` }" />
              </div>
            </div>
          </CardContent>
        </Card>

      </div>

      <!-- Bottom Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <Card>
          <CardContent class="flex items-center gap-4 p-5">
            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center shrink-0">
              <CheckCircle class="text-green-500 w-5 h-5" />
            </div>
            <div>
              <p class="text-2xl font-bold">{{ bottomStats.studentsPresent.toLocaleString() }}</p>
              <p class="text-xs text-muted-foreground">Students Present Today</p>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent class="flex items-center gap-4 p-5">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center shrink-0">
              <UserX class="text-red-500 w-5 h-5" />
            </div>
            <div>
              <p class="text-2xl font-bold">{{ bottomStats.studentsAbsent.toLocaleString() }}</p>
              <p class="text-xs text-muted-foreground">Students Absent Today</p>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent class="flex items-center gap-4 p-5">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
              <GraduationCap class="text-blue-500 w-5 h-5" />
            </div>
            <div>
              <p class="text-2xl font-bold">{{ bottomStats.teachersActive.toLocaleString() }}</p>
              <p class="text-xs text-muted-foreground">Teachers Active Today</p>
            </div>
          </CardContent>
        </Card>

      </div>

    </div>
  </AppLayout>
</template>
