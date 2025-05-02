import { defineConfig, loadEnv, splitVendorChunkPlugin } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'
import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite'
import { ElementPlusResolver } from 'unplugin-vue-components/resolvers'
// import strip from '@rollup/plugin-strip'
// import cleanup from 'rollup-plugin-cleanup'

// https://vitejs.dev/config/
export default ({ command, mode }) => {
  const env = loadEnv(mode, process.cwd(), '')
  // console.log(env)
  // const baseUrl = mode == 'production' 
  //                   ? env.VITE_BASE_URL_PROD
  //                   : env.VITE_BASE_URL

  const baseUrl = mode == 'production' 
                    ? env.VITE_BASE_URL_PROD
                    : env.VITE_BASE_URL
  const folder = 'assets/vue'
  const outDir = `./public/${folder}/`

  const base = mode === 'production' 
                ? `${baseUrl}${folder}/`
                : './'

  console.log(resolve(__dirname, outDir))
  console.log(baseUrl, __dirname, base)

  return defineConfig({
    optimizeDeps: {
      include: [
        'vue',
        'vue-router',
        '@iconify/vue',
      ]
    },
    plugins: [
      vue(),// ...
      AutoImport({
        resolvers: [ElementPlusResolver()],
      }),
      Components({
        resolvers: [ElementPlusResolver()],
      }),
      splitVendorChunkPlugin(),

    ],
    resolve: {
      alias: {
        '@': resolve(__dirname, './frontend')
      }
    },
    base: base,
    server: {
      host:'127.0.0.1',
      // required to load scripts from custom host
      cors: true,
  
      // we need a strict port to match on PHP side
      // change freely, but update on PHP to match the same port
      strictPort: true,
      port: env.VITE_PORT,
      origin: "http://127.0.0.1:" + env.VITE_PORT,
    },
    build: {
      // output dir for production build
      outDir: resolve(__dirname, outDir),
      assetsDir:'files',
      emptyOutDir: true,
      copyPublicDir: false,
      cssCodeSplit: true,
      // emit manifest so PHP can find the hashed files
      manifest: true,
  
      // esbuild target
      target: 'es2020',
      
      // our entry
      rollupOptions: {
        input: {
          main: resolve(__dirname, './frontend/main.js'),
        },
        plugins: [
          // remove console.*, assets.* (default)
          // strip({
          //   include: ['**/*.js', '**/*.vue'],
          //   sourceMap: false
          // }),
          // cleanup({
          //   comments: 'none',
          //   sourcemap: false,
          //   extensions: ['js', 'vue']
          // })
        ]
      }
    }
  })
}
