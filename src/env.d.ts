/// <reference types="vite/client" />

declare const $_: (key: string) => string
declare const $: JQueryStatic
declare const jQuery: JQueryStatic

declare module 'dateformat' {
  function dateformat(date: Date | string | number, mask: string, utc?: boolean): string
  export = dateformat
}
