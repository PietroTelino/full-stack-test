<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

interface Customer {
    id: number;
    name: string;
    email: string;
    phone?: string;
    address?: string;
    document?: string;
    invoices_count: number;
    created_at: string;
}

interface Props {
    customers: {
        data: Customer[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Customers',
        href: '/customers',
    },
];

const deleteCustomer = (customer: Customer) => {
    if (customer.invoices_count > 0) {
        alert('Cannot delete customer with existing invoices.');
        return;
    }

    if (confirm(`Are you sure you want to delete ${customer.name}?`)) {
        router.delete(`/customers/${customer.id}`);
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>

<template>
    <Head title="Customers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold">Customers</h1>
                <Link href="/customers/create">
                    <Button>Create Customer</Button>
                </Link>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Phone</TableHead>
                            <TableHead>Document</TableHead>
                            <TableHead>Invoices</TableHead>
                            <TableHead>Created</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="customer in customers.data" :key="customer.id">
                            <TableCell class="font-medium">{{ customer.name }}</TableCell>
                            <TableCell>{{ customer.email }}</TableCell>
                            <TableCell>{{ customer.phone || '-' }}</TableCell>
                            <TableCell>{{ customer.document || '-' }}</TableCell>
                            <TableCell>{{ customer.invoices_count }}</TableCell>
                            <TableCell>{{ formatDate(customer.created_at) }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Link :href="`/customers/${customer.id}`">
                                        <Button variant="outline" size="sm">View</Button>
                                    </Link>
                                    <Link :href="`/customers/${customer.id}/edit`">
                                        <Button variant="outline" size="sm">Edit</Button>
                                    </Link>
                                    <Button
                                        variant="destructive"
                                        size="sm"
                                        @click="deleteCustomer(customer)"
                                        :disabled="customer.invoices_count > 0"
                                    >
                                        Delete
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div v-if="customers.last_page > 1" class="flex justify-center gap-2">
                <Link
                    v-for="page in customers.last_page"
                    :key="page"
                    :href="`/customers?page=${page}`"
                >
                    <Button
                        :variant="page === customers.current_page ? 'default' : 'outline'"
                        size="sm"
                    >
                        {{ page }}
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
