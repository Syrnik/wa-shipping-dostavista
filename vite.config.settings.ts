import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig(({ mode }) => ({
  plugins: [vue()],
  define: {
    'process.env.NODE_ENV':                  JSON.stringify(mode === 'production' ? 'production' : 'development'),
    __VUE_OPTIONS_API__:                     'true',
    __VUE_PROD_DEVTOOLS__:                   mode !== 'production' ? 'true' : 'false',
    __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: mode !== 'production' ? 'true' : 'false',
  },
  build: {
    outDir: resolve(__dirname),
    emptyOutDir: false,
    lib: {
      entry: resolve(__dirname, 'src/settings/index.ts'),
      name: 'ShippingDostavistaPluginSettings',
      formats: ['umd'],
    },
    cssCodeSplit: false,
    minify: mode === 'production' ? 'terser' : false,
    terserOptions: mode === 'production' ? {
      compress: { drop_debugger: true, pure_funcs: ['console.log'] },
      format: { comments: false },
      mangle: true,
    } : undefined,
    sourcemap: mode === 'development',
    rollupOptions: {
      output: {
        entryFileNames: 'js/settings.js',
        assetFileNames: (assetInfo) => {
          const name = assetInfo.names?.[0] ?? assetInfo.name ?? ''
          if (name.endsWith('.css')) return 'css/settings.css'
          return 'assets/[name][extname]'
        },
        inlineDynamicImports: true,
      },
    },
  },
}))
