import { describe, expect, it, vi } from 'vitest';
import { mount } from '@vue/test-utils';

const { postMock, visitMock } = vi.hoisted(() => {
  return {
    postMock: vi.fn(),
    visitMock: vi.fn(),
  };
});

vi.mock('@inertiajs/vue3', () => ({
  Head: { name: 'Head', template: '<div><slot /></div>' },
  router: { visit: visitMock },
  usePage: () => ({
    props: {
      auth: { user: null },
      ziggy: {},
    },
    url: '/',
    component: 'FakeComponent',
  }),
  useForm: (initial: any) => ({
    ...initial,
    errors: {},
    processing: false,
    post: postMock,
    put: vi.fn(),
    delete: vi.fn(),
    reset: vi.fn(),
    clearErrors: vi.fn(),
  }),
}));

import CreateInvoice from './Create.vue';

const mountComponent = () =>
  mount(CreateInvoice, {
    props: {
      customers: [],
    },
    global: {
      stubs: {
        AppLayout: { template: '<div><slot /></div>' },
        Card: { template: '<div><slot /></div>' },
        CardHeader: { template: '<div><slot /></div>' },
        CardContent: { template: '<div><slot /></div>' },
        CardTitle: { template: '<div><slot /></div>' },
        Button: { template: '<button><slot /></button>' },
        Input: { template: '<input />' },
        Label: { template: '<label><slot /></label>' },
        Select: { template: '<select><slot /></select>' },
        SelectTrigger: { template: '<div><slot /></div>' },
        SelectValue: { template: '<span><slot /></span>' },
        SelectContent: { template: '<div><slot /></div>' },
        SelectItem: { template: '<div><slot /></div>' },
      },
    },
  });

describe('Invoices/Create page', () => {
  it('starts with one item and total 0', () => {
    const wrapper = mountComponent();
    const vm: any = wrapper.vm;

    expect(vm.form.items).toHaveLength(1);
    expect(vm.calculateTotal()).toBe(0);
  });

  it('adds and removes items correctly', () => {
    const wrapper = mountComponent();
    const vm: any = wrapper.vm;

    vm.addItem();
    expect(vm.form.items).toHaveLength(2);

    vm.removeItem(0);
    expect(vm.form.items).toHaveLength(1);

    vm.removeItem(0);
    expect(vm.form.items).toHaveLength(1);
  });

  it('calculates total based on items', () => {
    const wrapper = mountComponent();
    const vm: any = wrapper.vm;

    vm.form.items[0].quantity = 2;
    vm.form.items[0].unit_price = 500;

    expect(vm.calculateTotal()).toBe(1000);
  });

  it('submits the form calling form.post with the correct URL', async () => {
    const wrapper = mountComponent();

    await wrapper.find('form').trigger('submit.prevent');

    expect(postMock).toHaveBeenCalledWith('/invoices');
  });
});
