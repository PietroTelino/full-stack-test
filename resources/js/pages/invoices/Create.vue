<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem, type Customer, type InvoiceItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { formatCurrency } from '@/lib/format';

interface Props {
    customers: Customer[];
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Invoices',
        href: '/invoices',
    },
    {
        title: 'Create',
        href: '/invoices/create',
    },
];

const form = useForm({
    customer_id: '',
    status: 'draft',
    issue_date: new Date().toISOString().split('T')[0],
    due_date: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000)
        .toISOString()
        .split('T')[0],
    payment_date: '',
    items: [
        {
            title: '',
            subtitle: '',
            quantity: 1,
            unit_price: 0,
        },
    ] as InvoiceItem[],
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

const submit = () => {
    form.post('/invoices');
};
</script>

<template>
    <Head title="Create Invoice" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4 p-4">
            <h1 class="text-3xl font-bold">Create Invoice</h1>

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
                                        <SelectItem value="issued">Issued</SelectItem>
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
                    <Button type="button" variant="outline" @click="router.visit('/invoices')">
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Creating...' : 'Create Invoice' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
