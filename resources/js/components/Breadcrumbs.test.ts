import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import Breadcrumbs from './Breadcrumbs.vue';

describe('Breadcrumbs', () => {
  it('renders breadcrumb items with last item as page', () => {
    const wrapper = mount(Breadcrumbs, {
      props: {
        breadcrumbs: [
          { title: 'Home', href: '/' },
          { title: 'Invoices', href: '/invoices' },
          { title: 'Show', href: '/invoices/1' },
        ],
      },
      global: {
        stubs: {
          Link: {
            template: '<a><slot /></a>',
          },
        },
      },
    });

    const items = wrapper.findAll('li');
    expect(items.length).toBeGreaterThan(0);

    expect(wrapper.text()).toContain('Home');
    expect(wrapper.text()).toContain('Invoices');
    expect(wrapper.text()).toContain('Show');
  });
});
