import { defineConfig } from 'vitest/config';

export default defineConfig({
    test: {
        environment: 'jsdom',
        setupFiles: ['./resources/js/react/test/setup.js'],
        restoreMocks: true,
    },
});
