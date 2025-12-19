<script setup lang="ts">
import { ref } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

const props = defineProps({
    team: Object,
    availableRoles: Array,
    userPermissions: Object,
});

const page = usePage();

const addTeamMemberForm = useForm({
    email: '',
    role: null,
});

const updateRoleForm = useForm({
    role: null,
});

const leaveTeamForm = useForm({});
const removeTeamMemberForm = useForm({});

const currentlyManagingRole = ref(false);
const managingRoleFor = ref(null);
const confirmingLeavingTeam = ref(false);
const teamMemberBeingRemoved = ref(null);

const addTeamMember = () => {
    addTeamMemberForm.post(route('team-members.store', props.team), {
        errorBag: 'addTeamMember',
        preserveScroll: true,
        onSuccess: () => addTeamMemberForm.reset(),
    });
};

const cancelTeamInvitation = (invitation) => {
    router.delete(route('team-invitations.destroy', invitation), {
        preserveScroll: true,
    });
};

const manageRole = (teamMember) => {
    managingRoleFor.value = teamMember;
    updateRoleForm.role = teamMember.membership.role;
    currentlyManagingRole.value = true;
};

const updateRole = () => {
    updateRoleForm.put(route('team-members.update', [props.team, managingRoleFor.value]), {
        preserveScroll: true,
        onSuccess: () => (currentlyManagingRole.value = false),
    });
};

const confirmLeavingTeam = () => {
    confirmingLeavingTeam.value = true;
};

const leaveTeam = () => {
    leaveTeamForm.delete(route('team-members.destroy', [props.team, page.props.auth.user]));
};

const confirmTeamMemberRemoval = (teamMember) => {
    teamMemberBeingRemoved.value = teamMember;
};

const removeTeamMember = () => {
    removeTeamMemberForm.delete(route('team-members.destroy', [props.team, teamMemberBeingRemoved.value]), {
        errorBag: 'removeTeamMember',
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (teamMemberBeingRemoved.value = null),
    });
};

const displayableRole = (role) => {
    return props.availableRoles.find((r) => r.key === role)?.name;
};
</script>

<template>
    <div class="space-y-6">
        <div v-if="userPermissions.canAddTeamMembers" class="space-y-6">
            <HeadingSmall
                :title="$t('Add Team Member')"
                :description="$t('Add a new team member to your team, allowing them to collaborate with you.')"
            />

            <form @submit.prevent="addTeamMember" class="space-y-6">
                <p class="text-sm text-muted-foreground">
                    {{
                        $t('Please provide the email address of the person you would like to add to this team.')
                    }}
                </p>

                <!-- Member Email -->
                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        v-model="addTeamMemberForm.email"
                        type="email"
                    />
                    <InputError :message="addTeamMemberForm.errors.email" class="mt-2" />
                </div>

                <!-- Role -->
                <div v-if="availableRoles.length > 0" class="grid gap-2">
                    <Label for="roles">Departamento</Label>
                    <InputError :message="addTeamMemberForm.errors.role" class="mt-2" />

                    <div class="relative z-0 border border-gray-200 rounded-lg cursor-pointer">
                        <button
                            v-for="(role, i) in availableRoles"
                            :key="role.key"
                            type="button"
                            class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-accent-500 focus:ring-2 focus:ring-accent-500"
                            :class="{
                                'border-t border-gray-200 focus:border-none rounded-t-none': i > 0,
                                'rounded-b-none': i != Object.keys(availableRoles).length - 1,
                            }"
                            @click="addTeamMemberForm.role = role.key"
                        >
                            <div
                                :class="{
                                    'opacity-50': addTeamMemberForm.role && addTeamMemberForm.role != role.key,
                                }"
                            >
                                <!-- Role Name -->
                                <div class="flex items-center">
                                    <div
                                        class="text-sm text-gray-600"
                                        :class="{ 'font-semibold': addTeamMemberForm.role == role.key }"
                                    >
                                        {{ role.name }}
                                    </div>

                                    <svg
                                        v-if="addTeamMemberForm.role == role.key"
                                        class="ms-2 h-5 w-5 text-green-400"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                </div>

                                <!-- Role Description -->
                                <div class="mt-2 text-xs text-gray-600 text-start">
                                    {{ role.description }}
                                </div>
                            </div>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <Button
                        :disabled="addTeamMemberForm.processing"
                    >
                        {{ $t('Add') }}
                    </Button>

                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p v-show="addTeamMemberForm.recentlySuccessful" class="text-sm text-muted-foreground">
                            {{ $t('Added.') }}
                        </p>
                    </Transition>
                </div>
            </form>
        </div>

        <div v-if="team.team_invitations.length > 0 && userPermissions.canAddTeamMembers" class="space-y-6">
            <HeadingSmall
                :title="$t('Pending Team Invitations')"
                :description="$t(
                    'These people have been invited to your team and have been sent an invitation email. They may join the team by accepting the email invitation.',
                )"
            />

            <div class="space-y-4">
                <div
                    v-for="invitation in team.team_invitations"
                    :key="invitation.id"
                    class="flex items-center justify-between"
                >
                    <div class="text-muted-foreground">
                        {{ invitation.email }}
                    </div>

                    <div class="flex items-center">
                        <!-- Cancel Team Invitation -->
                        <button
                            v-if="userPermissions.canRemoveTeamMembers"
                            class="cursor-pointer ms-6 text-sm text-red-500 focus:outline-none hover:text-red-600"
                            @click="cancelTeamInvitation(invitation)"
                        >
                            {{ $t('Cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="team.users.length > 0" class="space-y-6">
            <HeadingSmall
                :title="$t('Team Members')"
                :description="$t('All of the people that are part of this team.')"
            />

            <div class="space-y-4">
                <div v-for="user in team.users" :key="user.id" class="flex items-center justify-between">
                    <div class="flex items-center">
                        <img
                            class="w-8 h-8 rounded-full object-cover"
                            :src="user.profile_photo_url"
                            :alt="user.name"
                        />
                        <div class="ms-4">
                            {{ user.name }}
                        </div>
                    </div>

                    <div class="flex items-center">
                        <!-- Manage Team Member Role -->
                        <button
                            v-if="userPermissions.canUpdateTeamMembers && availableRoles.length"
                            class="ms-2 text-sm text-muted-foreground underline hover:text-foreground"
                            @click="manageRole(user)"
                        >
                            {{ displayableRole(user.membership.role) }}
                        </button>

                        <div v-else-if="availableRoles.length" class="ms-2 text-sm text-muted-foreground">
                            {{ displayableRole(user.membership.role) }}
                        </div>

                        <!-- Leave Team -->
                        <button
                            v-if="$page.props.auth.user.id === user.id"
                            class="cursor-pointer ms-6 text-sm text-red-500 hover:text-red-600"
                            @click="confirmLeavingTeam"
                        >
                            {{ $t('Leave Team') }}
                        </button>

                        <!-- Remove Team Member -->
                        <button
                            v-else-if="userPermissions.canRemoveTeamMembers"
                            class="cursor-pointer ms-6 text-sm text-red-500 hover:text-red-600"
                            @click="confirmTeamMemberRemoval(user)"
                        >
                            {{ $t('Remove') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Management Modal -->
        <Dialog :open="currentlyManagingRole" @update:open="currentlyManagingRole = $event">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ $t('Manage Role') }}</DialogTitle>
                </DialogHeader>

                <div v-if="managingRoleFor">
                    <div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer">
                        <button
                            v-for="(role, i) in availableRoles"
                            :key="role.key"
                            type="button"
                            class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-accent-500 focus:ring-2 focus:ring-accent-500"
                            :class="{
                                'border-t border-gray-200 focus:border-none rounded-t-none': i > 0,
                                'rounded-b-none': i !== Object.keys(availableRoles).length - 1,
                            }"
                            @click="updateRoleForm.role = role.key"
                        >
                            <div :class="{ 'opacity-50': updateRoleForm.role && updateRoleForm.role !== role.key }">
                                <!-- Role Name -->
                                <div class="flex items-center">
                                    <div
                                        class="text-sm text-gray-600"
                                        :class="{ 'font-semibold': updateRoleForm.role === role.key }"
                                    >
                                        {{ role.name }}
                                    </div>

                                    <svg
                                        v-if="updateRoleForm.role == role.key"
                                        class="ms-2 h-5 w-5 text-green-400"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                </div>

                                <!-- Role Description -->
                                <div class="mt-2 text-xs text-gray-600">
                                    {{ role.description }}
                                </div>
                            </div>
                        </button>
                    </div>
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button variant="secondary">{{ $t('Cancel') }}</Button>
                    </DialogClose>

                    <Button
                        :disabled="updateRoleForm.processing"
                        @click="updateRole"
                    >
                        {{ $t('Save') }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Leave Team Confirmation Modal -->
        <Dialog :open="confirmingLeavingTeam" @update:open="confirmingLeavingTeam = $event">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ $t('Leave Team') }}</DialogTitle>
                    <DialogDescription>{{ $t('Are you sure you would like to leave this team?') }}</DialogDescription>
                </DialogHeader>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button variant="secondary">{{ $t('Cancel') }}</Button>
                    </DialogClose>

                    <Button
                        variant="destructive"
                        :disabled="leaveTeamForm.processing"
                        @click="leaveTeam"
                    >
                        {{ $t('Leave') }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Remove Team Member Confirmation Modal -->
        <Dialog :open="!!teamMemberBeingRemoved" @update:open="(val) => !val && (teamMemberBeingRemoved = null)">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ $t('Remove Team Member') }}</DialogTitle>
                    <DialogDescription>{{ $t('Are you sure you would like to remove this person from the team?') }}</DialogDescription>
                </DialogHeader>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button variant="secondary">{{ $t('Cancel') }}</Button>
                    </DialogClose>

                    <Button
                        variant="destructive"
                        :disabled="removeTeamMemberForm.processing"
                        @click="removeTeamMember"
                    >
                        {{ $t('Remove') }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
