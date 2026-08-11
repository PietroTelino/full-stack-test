<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const form = useForm({
    name: '',
});

const createTeam = () => {
    form.post(route('teams.store'), {
        errorBag: 'createTeam',
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="space-y-6">
        <form @submit.prevent="createTeam" class="space-y-6">
            <div class="grid gap-2">
                <Label>Team Owner</Label>

                <div class="flex items-center mt-2">
                    <img
                        class="object-cover w-12 h-12 rounded-full"
                        :src="$page.props.auth.user.profile_photo_url"
                        :alt="$page.props.auth.user.name"
                    />

                    <div class="ms-4 leading-tight">
                        <div>{{ $page.props.auth.user.name }}</div>
                        <div class="text-sm text-muted-foreground">
                            {{ $page.props.auth.user.email }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="name">Team Name</Label>
                <Input id="name" v-model="form.name" type="text" autofocus />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">Create Team</Button>
            </div>
        </form>
    </div>
</template>
