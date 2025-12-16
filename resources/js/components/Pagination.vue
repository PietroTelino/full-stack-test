<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { ArrowRightIcon, ArrowLeftIcon } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps(['links']);

const previous = computed(() => props.links[0]);
const next = computed(() => props.links[props.links.length - 1]);

const pages = computed(() =>
    props.links.filter((link) => ![trans('pagination.previous'), trans('pagination.next')].includes(trans(link.label))),
);

const hasPages = computed(() => props.links.length > 3);
</script>

<template>
    <nav class="flex items-center justify-between border-gray-200 px-4 sm:px-0" :class="{ 'border-t': hasPages }">
        <div class="-mt-px flex w-0 flex-1">
            <Link
                v-if="previous.url"
                preserve-state
                preserve-scroll
                :href="previous.url"
                class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700"
            >
                <ArrowLeftIcon class="mr-3 h-5 w-5 text-gray-400" aria-hidden="true" />
                <span class="sr-only">{{ previous.label }}</span>
            </Link>
        </div>
        <div v-if="hasPages" class="hidden md:-mt-px md:flex">
            <template v-for="(page, key) in pages">
                <span
                    v-if="page.url === null"
                    :key="key"
                    class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500"
                >
                    {{ page.label }}
                </span>
                <Link
                    v-else
                    :key="`page-${key}`"
                    preserve-state
                    preserve-scroll
                    class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700"
                    :class="{
                        'border-gray-500 px-4 pt-4 text-gray-600': page.active,
                    }"
                    :href="page.url"
                >
                    {{ page.label }}
                </Link>
            </template>
        </div>
        <div class="-mt-px flex w-0 flex-1 justify-end">
            <Link
                v-if="next.url"
                preserve-state
                preserve-scroll
                :href="next.url"
                class="inline-flex items-center border-t-2 border-transparent pl-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700"
            >
                <span class="sr-only">{{ next.label }}</span>
                <ArrowRightIcon class="ml-3 h-5 w-5 text-gray-400" aria-hidden="true" />
            </Link>
        </div>
    </nav>
</template>
