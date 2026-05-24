import { createApp } from 'vue'
import App from './App.vue'

interface Options {
  info?: Record<string, unknown>
  settings?: Record<string, unknown>
}

export default function (props: Options = {}) {
  const app = createApp(App, {
    info: props.info ?? {},
    settings: props.settings ?? {},
  })
  app.provide('namespace', (props.info as any)?.namespace ?? '')
  app.provide('info', props.info ?? {})
  return app
}
