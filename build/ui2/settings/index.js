import {createApp} from "vue";
import App from "./App.vue";

const app = (props) => {
  const app = createApp(App, props);
  app.provide('namespace', props.info.namespace);
  app.provide('info', props.info);
  return app;
}

export default app;
