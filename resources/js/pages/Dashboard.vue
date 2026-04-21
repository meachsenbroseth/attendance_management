<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { GraduationCap, BookOpen, School, CheckCircle, UserX, CalendarDays, TrendingUp } from 'lucide-vue-next'  // ✅ add missing icons
import AppLayout from '@/layouts/AppLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { dashboard } from '@/routes'
import type { BreadcrumbItem, PageProps } from '@/types'
import { useTranslation } from '@/composables/useTranslation'

const { t } = useTranslation()

const breadcrumbs: BreadcrumbItem[] = [
  { title: t('dashboard'), href: dashboard() },
]

const page = usePage<PageProps>()
const userName = computed(() => page.props.auth.user.name)

const greeting = computed(() => {
  const hour = new Date().getHours()
  if (hour < 12) return t('good_morning')
  if (hour < 18) return t('good_afternoon')
  return t('good_evening')
})

const currentDate = computed(() =>
  new Date().toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
)

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
  role: 'admin' | 'teacher'
  classroomCount?: number
  stats?: Stats
  todayClassAttendance?: ClassAttendance[]
  weeklyTrend?: WeeklyTrend[]
  bottomStats?: BottomStats
}>()

// ✅ Fixed — if guard outside the array
const statCards = computed(() => {
  if (!props.stats) return []
  return [
    {
      label: t('total_teachers'),
      value: props.stats.totalTeachers.toLocaleString(),
      icon: GraduationCap,
      bg: 'bg-blue-100',
      color: 'text-blue-500',
    },
    {
      label: t('total_students'),
      value: props.stats.totalStudents.toLocaleString(),
      icon: BookOpen,
      bg: 'bg-green-100',
      color: 'text-green-500',
    },
    {
      label: t('total_classrooms'),
      value: props.stats.totalClassrooms.toLocaleString(),
      icon: School,
      bg: 'bg-orange-100',
      color: 'text-orange-500',
    },
    {
      label: t('attendance_rate'),
      value: `${props.stats.todayRate}%`,
      icon: CheckCircle,
      bg: 'bg-teal-100',
      color: 'text-teal-500',
    },
  ]
})
</script>

<template>
  <Head :title="t('dashboard')" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 flex flex-col gap-6">

      <!-- ✅ Teacher view -->
      <template v-if="role === 'teacher'">
        <div class="rounded-xl bg-accent/40 border px-6 py-8">
          <p class="text-xs font-semibold uppercase tracking-widest text-primary mb-1">
            {{ t('teacher_portal') }}
          </p>
          <h1 class="text-3xl font-bold text-foreground">
            {{ greeting }}, {{ userName }}
          </h1>
          <p class="text-muted-foreground mt-1">
            {{ t('classes_scheduled_today', { count: String(classroomCount ?? 0) }) }}
          </p>
          <p class="text-xs text-muted-foreground mt-3">{{ currentDate }}</p>
        </div>
      </template>

      <!-- ✅ Admin view -->
      <template v-else>

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

          <Card>
            <CardHeader class="pb-2">
              <CardTitle class="flex items-center gap-2 text-sm font-semibold">
                <CalendarDays class="w-4 h-4 text-muted-foreground" />
                {{ t('today_class_attendance') }}
              </CardTitle>
            </CardHeader>
            <CardContent class="flex flex-col gap-4">
              <div v-if="!todayClassAttendance?.length" class="text-muted-foreground text-sm text-center py-6">
                {{ t('no_attendance_today') }}
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

          <Card>
            <CardHeader class="pb-2">
              <CardTitle class="flex items-center gap-2 text-sm font-semibold">
                <TrendingUp class="w-4 h-4 text-muted-foreground" />
                {{ t('weekly_attendance_trend') }}
              </CardTitle>
            </CardHeader>
            <CardContent class="flex flex-col gap-4">
              <div v-if="!weeklyTrend?.length" class="text-muted-foreground text-sm text-center py-6">
                {{ t('no_data_this_week') }}
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
                <p class="text-2xl font-bold">{{ bottomStats?.studentsPresent.toLocaleString() }}</p>
                <p class="text-xs text-muted-foreground">{{ t('students_present_today') }}</p>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent class="flex items-center gap-4 p-5">
              <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <UserX class="text-red-500 w-5 h-5" />
              </div>
              <div>
                <p class="text-2xl font-bold">{{ bottomStats?.studentsAbsent.toLocaleString() }}</p>
                <p class="text-xs text-muted-foreground">{{ t('students_absent_today') }}</p>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent class="flex items-center gap-4 p-5">
              <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                <GraduationCap class="text-blue-500 w-5 h-5" />
              </div>
              <div>
                <p class="text-2xl font-bold">{{ bottomStats?.teachersActive.toLocaleString() }}</p>
                <p class="text-xs text-muted-foreground">{{ t('teachers_active_today') }}</p>
              </div>
            </CardContent>
          </Card>
        </div>

      </template>
    </div>
  </AppLayout>
</template>