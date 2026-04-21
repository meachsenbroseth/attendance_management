<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, NotebookPen, UniversityIcon, User2Icon } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem, PageProps } from '@/types';
import LanguageSwitcher from './LanguageSwitcher.vue';
import { useTranslation } from '@/composables/useTranslation';

const page = usePage<PageProps>();
const role = computed(() => page.props.auth.user.role); // 'admin' | 'teacher'
const { t } = useTranslation();

const mainNavItems = computed(() => {
    const allNavItems: (NavItem & { roles?: string[] })[] = [
        {
            title: t('dashboard'),
            href: dashboard(),
            icon: LayoutGrid,
        },
        {
            title: t('classrooms'),
            href: '/classrooms',
            icon: UniversityIcon,
            roles: ['admin', 'teacher'],
        },
        {
            title: t('attendances'),
            href: '/attendances',
            icon: NotebookPen,
            roles: ['admin', 'teacher'],
        },
        {
            title: t('users'),
            href: '/users',
            icon: User2Icon,
            roles: ['admin'],
        },
    ];

    return allNavItems.filter((item) => !item.roles || item.roles.includes(role.value));
});

const footerNavItems: NavItem[] = [
    // {
    //     title: 'Repository',
    //     href: 'https://github.com/laravel/vue-starter-kit',
    //     icon: FolderGit2,
    // },
    // {
    //     title: 'Documentation',
    //     href: 'https://laravel.com/docs/starter-kits#vue',
    //     icon: BookOpen,
    // },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <div class="px-2 pb-1">
                <LanguageSwitcher />
            </div>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
