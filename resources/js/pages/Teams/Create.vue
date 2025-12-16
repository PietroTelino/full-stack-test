<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import CreateTeamForm from '@/pages/Teams/Partials/CreateTeamForm.vue';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Create Team',
        href: route('teams.create'),
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Create Team" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    title="Create Team"
                    description="Create a new team to collaborate with others"
                />

                <CreateTeamForm />
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
