<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import {
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuSub,
} from '@/components/ui/dropdown-menu';
import { logout } from '@/routes';
import { edit } from '@/routes/profile';
import type { User } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { ArrowRight, CheckIcon, LogOut, Settings, Users2 } from 'lucide-vue-next';
import { DropdownMenuPortal, DropdownMenuSubContent, DropdownMenuSubTrigger } from 'reka-ui';

interface Props {
  user: User;
}

const handleLogout = () => {
  router.flushAll();
};

const switchToTeam = (team) => {
  router.put(
    route('current-team.update'),
    {
      team_id: team.id,
    },
    {
      preserveState: false,
    },
  );
};

defineProps<Props>();
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo :user="user" :show-email="true" />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <DropdownMenuItem :as-child="true">
            <Link class="block w-full" :href="edit()" as="button">
                <Settings class="mr-2 h-4 w-4" />
                Settings
            </Link>
        </DropdownMenuItem>
      <DropdownMenuSub>
        <DropdownMenuItem as-child>
          <DropdownMenuSubTrigger>
            <Users2 class="mr-2 h-4 w-4" />
            Switch Team
            <ArrowRight class="ms-auto h-4 w-4" />
          </DropdownMenuSubTrigger>
        </DropdownMenuItem>
        <DropdownMenuPortal>
          <DropdownMenuSubContent class="min-w-[200px] bg-white p-2">
            <DropdownMenuItem v-for="team in $page.props.auth.teams" :key="team.id" @click="switchToTeam(team)" class="cursor-pointer">
              <div class="flex items-center w-full">
                <div>{{ team.name }}</div>
                <CheckIcon
                    v-if="team.id == $page.props.auth.user.current_team_id"
                    class="ml-6 size-5 text-gray-600"
                />
              </div>
            </DropdownMenuItem>
          </DropdownMenuSubContent>
        </DropdownMenuPortal>
      </DropdownMenuSub>
    </DropdownMenuGroup>
    <DropdownMenuSeparator />
    <DropdownMenuItem :as-child="true">
        <Link
            class="block w-full"
            :href="logout()"
            @click="handleLogout"
            as="button"
            data-test="logout-button"
        >
            <LogOut class="mr-2 h-4 w-4" />
            Log out
        </Link>
    </DropdownMenuItem>
</template>
