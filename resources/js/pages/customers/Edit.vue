<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Customer {
    id: number;
    name: string;
    email: string;
    phone?: string;
    address?: string;
    document?: string;
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
    {
        title: 'Edit',
        href: `/customers/${props.customer.id}/edit`,
    },
];

const form = useForm({
    name: props.customer.name,
    email: props.customer.email,
    phone: props.customer.phone || '',
    address: props.customer.address || '',
    document: props.customer.document || '',
});

const submit = () => {
    form.put(`/customers/${props.customer.id}`);
};
</script>

<template>
    <Head :title="`Edit ${customer.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4 p-4">
            <h1 class="text-3xl font-bold">Edit {{ customer.name }}</h1>

            <form @submit.prevent="submit" class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Customer Information</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="name">Name *</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    placeholder="John Doe"
                                    required
                                />
                                <p v-if="form.errors.name" class="text-sm text-destructive">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="email">Email *</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    v-model="form.email"
                                    placeholder="john@example.com"
                                    required
                                />
                                <p v-if="form.errors.email" class="text-sm text-destructive">
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="phone">Phone</Label>
                                <Input
                                    id="phone"
                                    v-model="form.phone"
                                    placeholder="+1 (555) 123-4567"
                                />
                                <p v-if="form.errors.phone" class="text-sm text-destructive">
                                    {{ form.errors.phone }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="document">Document / Tax ID</Label>
                                <Input
                                    id="document"
                                    v-model="form.document"
                                    placeholder="123-45-6789"
                                />
                                <p v-if="form.errors.document" class="text-sm text-destructive">
                                    {{ form.errors.document }}
                                </p>
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <Label for="address">Address</Label>
                                <Textarea
                                    id="address"
                                    v-model="form.address"
                                    placeholder="123 Main St, Apt 4B&#10;New York, NY 10001"
                                    rows="3"
                                />
                                <p v-if="form.errors.address" class="text-sm text-destructive">
                                    {{ form.errors.address }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div class="flex justify-end gap-2">
                    <Button
                        type="button"
                        variant="outline"
                        @click="router.visit(`/customers/${customer.id}`)"
                    >
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Updating...' : 'Update Customer' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
