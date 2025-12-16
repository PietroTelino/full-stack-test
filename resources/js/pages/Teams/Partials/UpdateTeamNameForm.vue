<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps([ 'team', 'permissions']);

const form = useForm({
    name: props.team.name,
});

const updateTeamName = () => {
    form.put(route('teams.update', props.team), {
        errorBag: 'updateTeamName',
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="space-y-6">
        <form @submit.prevent="updateTeamName" class="space-y-6">
            <!-- Team Owner Information -->
            <div class="grid gap-2">
                <Label>{{ $t('Team Owner :teamName', { teamName: props.team.name }) }}</Label>

                <div class="flex items-center mt-2">
                    <img
                        class="w-12 h-12 rounded-full object-cover"
                        :src="team.owner.profile_photo_url"
                        :alt="team.owner.name"
                    />

                    <div class="ms-4 leading-tight">
                        <div>{{ team.owner.name }}</div>
                        <div class="text-sm text-muted-foreground">
                            {{ team.owner.email }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Name -->
            <div class="grid gap-2">
                <Label for="name">{{ $t('Team Name') }}</Label>

                <Input
                    id="name"
                    v-model="form.name"
                    type="text"
                    :disabled="!permissions.canUpdateTeam"
                />

                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <div v-if="permissions.canUpdateTeam" class="flex items-center gap-4">
                <Button :disabled="form.processing">
                    {{ $t('Save') }}
                </Button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-show="form.recentlySuccessful" class="text-sm text-muted-foreground">
                        {{ $t('Saved.') }}
                    </p>
                </Transition>
            </div>
        </form>
    </div>
</template>
