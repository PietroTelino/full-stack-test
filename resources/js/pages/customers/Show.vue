<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';

interface Invoice {
    id: number;
    code: string;
    amount: number;
    status: string;
    issue_date: string;
    due_date: string;
}

interface Customer {
    id: number;
    name: string;
    email: string;
    phone?: string;
    address?: string;
    document?: string;
    created_at: string;
    invoices?: Invoice[];
}

interface Props {
    customer: Customer;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Customers',
        href: '/customers',
    },
    {
        title: props.customer.name,
        href: `/customers/${props.customer.id}`,
    },
];

const deleteCustomer = () => {
    if (props.customer.invoices && props.customer.invoices.length > 0) {
        alert('Cannot delete customer with existing invoices.');
        return;
    }

    if (confirm(`Are you sure you want to delete ${props.customer.name}?`)) {
        router.delete(`/customers/${props.customer.id}`);
    }
};

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount / 100);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        draft: 'default',
        pending: 'secondary',
        paid: 'default',
        overdue: 'destructive',
        cancelled: 'outline',
    };
    return colors[status] || 'default';
};

const deleteInvoice = (invoice: Invoice) => {
    if (confirm(`Are you sure you want to delete invoice ${invoice.code}?`)) {
        router.delete(`/invoices/${invoice.id}`, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head :title="customer.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold">{{ customer.name }}</h1>
                <div class="flex gap-2">
                    <Link :href="`/customers/${customer.id}/edit`">
                        <Button variant="outline">Edit</Button>
                    </Link>
                    <Button
                        variant="destructive"
                        @click="deleteCustomer"
                        :disabled="customer.invoices && customer.invoices.length > 0"
                    >
                        Delete
                    </Button>
                </div>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Customer Information</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <div class="text-sm text-muted-foreground">Email</div>
                            <div class="font-medium">{{ customer.email }}</div>
                        </div>
                        <div v-if="customer.phone">
                            <div class="text-sm text-muted-foreground">Phone</div>
                            <div class="font-medium">{{ customer.phone }}</div>
                        </div>
                        <div v-if="customer.document">
                            <div class="text-sm text-muted-foreground">Document / Tax ID</div>
                            <div class="font-medium">{{ customer.document }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-muted-foreground">Customer Since</div>
                            <div class="font-medium">{{ formatDate(customer.created_at) }}</div>
                        </div>
                        <div v-if="customer.address" class="md:col-span-2">
                            <div class="text-sm text-muted-foreground">Address</div>
                            <div class="font-medium whitespace-pre-line">{{ customer.address }}</div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card v-if="customer.invoices && customer.invoices.length > 0">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>Recent Invoices</CardTitle>
                        <Link href="/invoices/create">
                            <Button variant="outline" size="sm">Create Invoice</Button>
                        </Link>
                    </div>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Code</TableHead>
                                <TableHead>Amount</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Issue Date</TableHead>
                                <TableHead>Due Date</TableHead>
                                <TableHead class="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="invoice in customer.invoices" :key="invoice.id">
                                <TableCell class="font-medium">{{ invoice.code }}</TableCell>
                                <TableCell>{{ formatCurrency(invoice.amount) }}</TableCell>
                                <TableCell>
                                    <Badge :variant="getStatusColor(invoice.status)">
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
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            @click="deleteInvoice(invoice)"
                                            :disabled="!['draft', 'pending'].includes(invoice.status)"
                                        >
                                            Delete
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <Card v-else>
                <CardContent class="py-8 text-center">
                    <p class="text-muted-foreground mb-4">No invoices yet</p>
                    <Link href="/invoices/create">
                        <Button>Create First Invoice</Button>
                    </Link>
                </CardContent>
            </Card>

            <div class="flex justify-between">
                <Link href="/customers">
                    <Button variant="outline">Back to Customers</Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
