<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { type BreadcrumbItem, type InvoiceWithItems } from '@/types';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { formatCurrency, formatDate } from '@/lib/format';
import { getInvoiceStatusColor } from '@/lib/invoice';

interface Props {
    invoice: InvoiceWithItems;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Invoices',
        href: '/invoices',
    },
    {
        title: props.invoice.code,
        href: `/invoices/${props.invoice.id}`,
    },
];

const deleteInvoice = () => {
    if (confirm('Are you sure you want to delete this invoice?')) {
        router.delete(`/invoices/${props.invoice.id}`);
    }
};

const issueInvoice = () => {
    if (confirm('Are you sure you want to issue this invoice? This will generate a bank billet.')) {
        router.post(`/invoices/${props.invoice.id}/issue`, {}, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head :title="`Invoice ${invoice.code}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Invoice {{ invoice.code }}</h1>
                    <Badge :variant="getInvoiceStatusColor(invoice.status)" class="mt-2">
                        {{ invoice.status }}
                    </Badge>
                </div>
                <div class="flex gap-2">
                    <Button
                        v-if="invoice.status !== 'issued' && !invoice.bank_billet"
                        variant="default"
                        @click="issueInvoice"
                    >
                        Issue Invoice
                    </Button>
                    <Link :href="`/invoices/${invoice.id}/edit`">
                        <Button variant="outline">Edit</Button>
                    </Link>
                    <Button variant="destructive" @click="deleteInvoice">Delete</Button>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Customer Information</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-2">
                        <div>
                            <div class="text-sm text-muted-foreground">Name</div>
                            <div class="font-medium">{{ invoice.customer.name }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-muted-foreground">Email</div>
                            <div class="font-medium">{{ invoice.customer.email }}</div>
                        </div>
                        <div v-if="invoice.customer.phone">
                            <div class="text-sm text-muted-foreground">Phone</div>
                            <div class="font-medium">{{ invoice.customer.phone }}</div>
                        </div>
                        <div v-if="invoice.customer.address">
                            <div class="text-sm text-muted-foreground">Address</div>
                            <div class="font-medium">{{ invoice.customer.address }}</div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Invoice Details</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-2">
                        <div>
                            <div class="text-sm text-muted-foreground">Issue Date</div>
                            <div class="font-medium">{{ formatDate(invoice.issue_date, { year: 'numeric', month: 'long', day: 'numeric' }) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-muted-foreground">Due Date</div>
                            <div class="font-medium">{{ formatDate(invoice.due_date, { year: 'numeric', month: 'long', day: 'numeric' }) }}</div>
                        </div>
                        <div v-if="invoice.payment_date">
                            <div class="text-sm text-muted-foreground">Payment Date</div>
                            <div class="font-medium">
                                {{ formatDate(invoice.payment_date, { year: 'numeric', month: 'long', day: 'numeric' }) }}
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-muted-foreground">Total Amount</div>
                            <div class="text-2xl font-bold">
                                {{ formatCurrency(invoice.amount) }}
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card v-if="invoice.bank_billet">
                <CardHeader>
                    <CardTitle>Bank Billet Information</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div>
                        <div class="text-sm text-muted-foreground">Billet Code</div>
                        <div class="font-mono font-medium text-lg">{{ invoice.bank_billet.code }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-muted-foreground">Barcode</div>
                        <div v-if="invoice.bank_billet.barcode" class="font-mono text-sm break-all select-all bg-muted p-2 rounded">
                            {{ invoice.bank_billet.barcode }}
                        </div>
                        <div v-else class="text-sm text-muted-foreground italic">
                            Generating barcode...
                        </div>
                    </div>
                    <div v-if="invoice.bank_billet.expires_at">
                        <div class="text-sm text-muted-foreground">Expires At</div>
                        <div class="font-medium">
                            {{ formatDate(invoice.bank_billet.expires_at, { year: 'numeric', month: 'long', day: 'numeric' }) }}
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Invoice Items</CardTitle>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Description</TableHead>
                                <TableHead class="text-right">Quantity</TableHead>
                                <TableHead class="text-right">Unit Price</TableHead>
                                <TableHead class="text-right">Amount</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="item in invoice.invoice_items" :key="item.id">
                                <TableCell>
                                    <div>
                                        <div class="font-medium">{{ item.title }}</div>
                                        <div v-if="item.subtitle" class="text-sm text-muted-foreground">
                                            {{ item.subtitle }}
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell class="text-right">{{ item.quantity }}</TableCell>
                                <TableCell class="text-right">
                                    {{ formatCurrency(item.unit_price) }}
                                </TableCell>
                                <TableCell class="text-right">
                                    {{ formatCurrency(item.amount) }}
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <div class="mt-4 border-t pt-4 text-right">
                        <div class="text-2xl font-bold">
                            Total: {{ formatCurrency(invoice.amount) }}
                        </div>
                    </div>
                </CardContent>
            </Card>

            <div class="flex justify-between">
                <Link href="/invoices">
                    <Button variant="outline">Back to Invoices</Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
