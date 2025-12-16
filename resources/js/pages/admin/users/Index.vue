<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import Pagination from '@/components/Pagination.vue';
import { User } from 'lucide-vue-next';

defineProps(['users']);
</script>

<template>
    <AdminLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Usuários</h1>
                    <p class="text-muted-foreground">
                        Gerencie os usuários do sistema
                    </p>
                </div>
            </div>

            <div class="space-y-3">
              <ul role="list" class="divide-y divide-gray-100">
                <li v-for="user in users.data" :key="user.email" class="flex justify-between gap-x-6 py-5">
                  <div class="flex min-w-0 gap-x-4">
                    <div class="min-w-0 flex-auto">
                      <p class="text-sm/6 font-semibold text-gray-900">{{ user.name }}</p>
                      <p class="mt-1 truncate text-xs/5 text-gray-500">{{ user.email }}</p>
                    </div>
                  </div>
                  <div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                    <p class="text-sm/6 text-gray-900">{{ user.role }}</p>
                    <p class="mt-1 text-xs/5 text-gray-500">
                      {{ user.verified ? 'Verificado' : 'Não verificado' }}
                    </p>

                  </div>
                </li>
              </ul>
            </div>

            <div v-if="users.data.length === 0" class="text-center py-12">
                <User class="mx-auto h-12 w-12 text-muted-foreground/50" />
                <h3 class="mt-4 text-lg font-semibold">Nenhum usuário encontrado</h3>
                <p class="text-muted-foreground">
                    Não há usuários cadastrados no sistema.
                </p>
            </div>

            <Pagination :links="users.links" />
        </div>
    </AdminLayout>
</template>

