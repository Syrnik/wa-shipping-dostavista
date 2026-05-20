import { createApp } from 'vue'
import App from './LegacyApp.vue'

interface Options {
  el?: string
  info?: Record<string, unknown>
  settings?: Record<string, unknown>
}

export function run(options: Options = {}): void {
  const app = createApp(App, {
    info: options.info ?? {},
    settings: options.settings ?? {},
  })
  app.provide('namespace', (options.info as any)?.namespace ?? '')
  app.provide('info', options.info ?? {})
  app.mount(options.el ?? '#dostavista-shipping-settings')
}
