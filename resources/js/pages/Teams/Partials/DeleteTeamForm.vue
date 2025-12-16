<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

const props = defineProps({
    team: Object,
});

const confirmingTeamDeletion = ref(false);
const form = useForm({});

const confirmTeamDeletion = () => {
    confirmingTeamDeletion.value = true;
};

const deleteTeam = () => {
    form.delete(route('teams.destroy', props.team), {
        errorBag: 'deleteTeam',
    });
};
</script>

<template>
    <div class="space-y-6">
        <HeadingSmall
            title="Delete Team"
            description="Permanently delete this team"
        />
        <div
            class="space-y-4 rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10"
        >
            <div class="relative space-y-0.5 text-red-600 dark:text-red-100">
                <p class="font-medium">Warning</p>
                <p class="text-sm">
                    Once a team is deleted, all of its resources and data will be permanently deleted. Before deleting this team, please download any data or information regarding this team that you wish to retain.
                </p>
            </div>

            <Dialog :open="confirmingTeamDeletion" @update:open="confirmingTeamDeletion = $event">
                <DialogTrigger as-child>
                    <Button variant="destructive">Delete Team</Button>
                </DialogTrigger>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Delete Team</DialogTitle>
                        <DialogDescription>
                            Are you sure you want to delete this team? Once a team is deleted, all of its resources and data will be permanently deleted.
                        </DialogDescription>
                    </DialogHeader>

                    <DialogFooter class="gap-2">
                        <DialogClose as-child>
                            <Button variant="secondary">Cancel</Button>
                        </DialogClose>

                        <Button
                            variant="destructive"
                            :disabled="form.processing"
                            @click="deleteTeam"
                        >
                            Delete Team
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </div>
</template>
