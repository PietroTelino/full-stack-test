<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { TriangleAlert } from 'lucide-vue-next';
</script>

<template>
  <div v-if="$page.props.is_impersonating" class="rounded-md bg-yellow-100 p-4">
    <div class="flex">
      <div class="shrink-0">
        <TriangleAlert class="h-5 w-5 text-yellow-400" aria-hidden="true" />
      </div>
      <div class="ml-3">
        <h3 class="text-sm font-medium text-yellow-800">Atenção, {{ $page.props.impersonator_name }}!</h3>
        <div class="mt-2 text-sm text-yellow-700">
          <Link :href="route('impersonate.leave')">Clique aqui para voltar ao seu usuário</Link>
        </div>
      </div>
    </div>
  </div>
</template>
