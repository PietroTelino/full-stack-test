import { defineConfig } from 'vitest/config';
import vue from '@vitejs/plugin-vue';
import { fileURLToPath } from 'node:url';
import { join, dirname } from 'node:path';

const rootDir = dirname(fileURLToPath(import.meta.url));

export default defineConfig({
  plugins: [vue()],
  test: {
    environment: 'jsdom',
    globals: true,
    setupFiles: join(rootDir, 'resources/js/tests/setup.ts'),
    include: ['resources/js/**/*.test.ts'],
    alias: {
      '@': join(rootDir, 'resources/js'),
    },
    coverage: {
      reporter: ['text', 'html'],
      reportsDirectory: join(rootDir, 'coverage'),
      include: ['resources/js/**/*.{ts,vue}'],
      exclude: ['resources/js/tests/**'],
    },
  },
});
