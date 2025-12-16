<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';
import type { BreadcrumbItemType } from '@/types';
import { Link } from '@inertiajs/vue3';
import { ArrowLeft, Users2 } from 'lucide-vue-next';

import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';

import NavMain from '@/components/NavMain.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import AppLogo from '@/components/AppLogo.vue';
import admin from '@/routes/admin';

const mainNavItems: NavItem[] = [
    {
        title: 'Usuários',
        href: admin.users.index(),
        icon: Users2,
    },
];


interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="sidebar">
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
      </Sidebar>
        <AppContent variant="sidebar" class="overflow-x-hidden">
            <div
                class="inline-flex w-full items-center justify-between bg-orange-600 px-4 py-2"
            >
                <p class="font-mono text-xs">
                    Área restrita a administradores!
                </p>
                <Button
                    as-child
                    class="bg-white font-mono text-xs text-black hover:bg-gray-200"
                >
                    <Link :href="dashboard()">
                        <ArrowLeft />
                        Sair
                    </Link>
                </Button>
            </div>

            <slot />
        </AppContent>
    </AppShell>
</template>
