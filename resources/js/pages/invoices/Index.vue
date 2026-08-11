<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { type BreadcrumbItem, type InvoiceWithItems } from '@/types';
import { Button } from '@/components/ui/button';
import { formatCurrency, formatDate } from '@/lib/format';
import { getInvoiceStatusColor } from '@/lib/invoice';

interface Props {
    invoices: {
        data: InvoiceWithItems[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
}
interface Props {
    invoices: {
        data: Invoice[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Invoices',
        href: '/invoices',
    },
];


const deleteInvoice = (id: number) => {
    if (confirm('Are you sure you want to delete this invoice?')) {
        router.delete(`/invoices/${id}`);
    }
};
</script>

<template>
    <Head title="Invoices" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold">Invoices</h1>
                <Link href="/invoices/create">
                    <Button>Create Invoice</Button>
                </Link>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Code</TableHead>
                            <TableHead>Customer</TableHead>
                            <TableHead>Amount</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Issue Date</TableHead>
                            <TableHead>Due Date</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="invoice in invoices.data" :key="invoice.id">
                            <TableCell class="font-medium">{{ invoice.code }}</TableCell>
                            <TableCell>
                                <div>
                                    <div class="font-medium">{{ invoice.customer.name }}</div>
                                    <div class="text-sm text-muted-foreground">
                                        {{ invoice.customer.email }}
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell>{{ formatCurrency(invoice.amount) }}</TableCell>
                            <TableCell>
                                <Badge :variant="getInvoiceStatusColor(invoice.status)">
                                    {{ invoice.status }}
                                </Badge>
                            </TableCell>
                            <TableCell>{{ formatDate(invoice.issue_date) }}</TableCell>
                            <TableCell>{{ formatDate(invoice.due_date) }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Link :href="`/invoices/${invoice.id}`">
                                        <Button variant="outline" size="sm">View</Button>
                                    </Link>
                                    <Link :href="`/invoices/${invoice.id}/edit`">
                                        <Button variant="outline" size="sm">Edit</Button>
                                    </Link>
                                    <Button
                                        variant="destructive"
                                        size="sm"
                                        @click="deleteInvoice(invoice.id)"
                                    >
                                        Delete
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div v-if="invoices.last_page > 1" class="flex justify-center gap-2">
                <Link
                    v-for="page in invoices.last_page"
                    :key="page"
                    :href="`/invoices?page=${page}`"
                >
                    <Button
                        :variant="page === invoices.current_page ? 'default' : 'outline'"
                        size="sm"
                    >
                        {{ page }}
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
