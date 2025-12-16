<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Customer {
    id: number;
    name: string;
    email: string;
}

interface InvoiceItem {
    id?: number;
    title: string;
    subtitle: string;
    quantity: number;
    unit_price: number;
}

interface Invoice {
    id: number;
    code: string;
    customer_id: number;
    status: string;
    issue_date: string;
    due_date: string;
    payment_date?: string;
    invoice_items: InvoiceItem[];
}

interface Props {
    invoice: Invoice;
    customers: Customer[];
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
    {
        title: 'Edit',
        href: `/invoices/${props.invoice.id}/edit`,
    },
];

const form = useForm({
    customer_id: props.invoice.customer_id.toString(),
    status: props.invoice.status,
    issue_date: props.invoice.issue_date,
    due_date: props.invoice.due_date,
    payment_date: props.invoice.payment_date || '',
    items: props.invoice.invoice_items.map((item) => ({
        id: item.id,
        title: item.title,
        subtitle: item.subtitle || '',
        quantity: item.quantity,
        unit_price: item.unit_price,
    })),
});

const addItem = () => {
    form.items.push({
        title: '',
        subtitle: '',
        quantity: 1,
        unit_price: 0,
    });
};

const removeItem = (index: number) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

const calculateTotal = () => {
    return form.items.reduce((sum, item) => {
        return sum + item.quantity * item.unit_price;
    }, 0);
};

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount / 100);
};

const submit = () => {
    form.put(`/invoices/${props.invoice.id}`);
};
</script>

<template>
    <Head :title="`Edit Invoice ${invoice.code}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4 p-4">
            <h1 class="text-3xl font-bold">Edit Invoice {{ invoice.code }}</h1>

            <form @submit.prevent="submit" class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Invoice Details</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="customer_id">Customer</Label>
                                <Select v-model="form.customer_id" required>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select a customer" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="customer in customers"
                                            :key="customer.id"
                                            :value="customer.id.toString()"
                                        >
                                            {{ customer.name }} ({{ customer.email }})
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.customer_id" class="text-sm text-destructive">
                                    {{ form.errors.customer_id }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="status">Status</Label>
                                <Select v-model="form.status" required>
                                    <SelectTrigger>
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="draft">Draft</SelectItem>
                                        <SelectItem value="pending">Pending</SelectItem>
                                        <SelectItem value="paid">Paid</SelectItem>
                                        <SelectItem value="overdue">Overdue</SelectItem>
                                        <SelectItem value="cancelled">Cancelled</SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.status" class="text-sm text-destructive">
                                    {{ form.errors.status }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="due_date">Due Date</Label>
                                <Input id="due_date" type="date" v-model="form.due_date" required />
                                <p v-if="form.errors.due_date" class="text-sm text-destructive">
                                    {{ form.errors.due_date }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle>Invoice Items</CardTitle>
                            <Button type="button" @click="addItem" variant="outline" size="sm">
                                Add Item
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div
                            v-for="(item, index) in form.items"
                            :key="index"
                            class="space-y-4 rounded-lg border p-4"
                        >
                            <div class="flex justify-between">
                                <h4 class="font-medium">Item {{ index + 1 }}</h4>
                                <Button
                                    v-if="form.items.length > 1"
                                    type="button"
                                    @click="removeItem(index)"
                                    variant="destructive"
                                    size="sm"
                                >
                                    Remove
                                </Button>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label :for="`title_${index}`">Title</Label>
                                    <Input
                                        :id="`title_${index}`"
                                        v-model="item.title"
                                        required
                                    />
                                    <p
                                        v-if="form.errors[`items.${index}.title`]"
                                        class="text-sm text-destructive"
                                    >
                                        {{ form.errors[`items.${index}.title`] }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label :for="`subtitle_${index}`">Subtitle (Optional)</Label>
                                    <Input :id="`subtitle_${index}`" v-model="item.subtitle" />
                                </div>

                                <div class="space-y-2">
                                    <Label :for="`quantity_${index}`">Quantity</Label>
                                    <Input
                                        :id="`quantity_${index}`"
                                        type="number"
                                        v-model.number="item.quantity"
                                        min="1"
                                        required
                                    />
                                    <p
                                        v-if="form.errors[`items.${index}.quantity`]"
                                        class="text-sm text-destructive"
                                    >
                                        {{ form.errors[`items.${index}.quantity`] }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label :for="`unit_price_${index}`">Unit Price (cents)</Label>
                                    <Input
                                        :id="`unit_price_${index}`"
                                        type="number"
                                        v-model.number="item.unit_price"
                                        min="0"
                                        required
                                    />
                                    <p
                                        v-if="form.errors[`items.${index}.unit_price`]"
                                        class="text-sm text-destructive"
                                    >
                                        {{ form.errors[`items.${index}.unit_price`] }}
                                    </p>
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="text-sm text-muted-foreground">Subtotal: </span>
                                <span class="font-medium">
                                    {{ formatCurrency(item.quantity * item.unit_price) }}
                                </span>
                            </div>
                        </div>

                        <div class="border-t pt-4 text-right">
                            <div class="text-xl font-bold">
                                Total: {{ formatCurrency(calculateTotal()) }}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div class="flex justify-end gap-2">
                    <Button
                        type="button"
                        variant="outline"
                        @click="router.visit(`/invoices/${invoice.id}`)"
                    >
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Updating...' : 'Update Invoice' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
