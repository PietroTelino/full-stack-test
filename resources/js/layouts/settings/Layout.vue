<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Profile',
        href: route('profile.edit'),
        current: route().current('profile.edit'),
    },
    {
        title: 'Password',
        href: route('user-password.edit'),
        current: route().current('user-password.edit'),
    },
    {
        title: 'Two-Factor Auth',
        href: route('two-factor.show'),
        current: route().current('two-factor.show'),
    },
    {
        title: 'Appearance',
        href: route('appearance.edit'),
        current: route().current('appearance.edit'),
    },
    {
      title: 'Team',
      href: route('teams.show', usePage().props.auth.user.current_team_id),
      current: route().current('teams.*'),
    }
];
</script>

<template>
    <div class="px-4 py-6">
        <Heading
            title="Settings"
            description="Manage your profile and account settings"
        />

        <div class="flex flex-col lg:flex-row lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-y-1 space-x-0">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="item.href"
                        variant="ghost"
                        :class="[
                            'w-full justify-start',
                            { 'bg-muted': item.current },
                        ]"
                        as-child
                    >
                        <Link :href="item.href">
                            <component :is="item.icon" class="h-4 w-4" />
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 lg:hidden" />

            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
