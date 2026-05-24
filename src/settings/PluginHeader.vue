<script setup lang="ts">
import { computed, inject, ref } from 'vue'

const info = inject<Record<string, string>>('info', {})
const about_tpl = ref<HTMLElement | null>(null)

const yearRange = computed(() => {
  const y = new Date().getFullYear()
  return y === 2019 ? '2019' : '2019—' + y
})

const mailTo = computed(() => 'mailto:support@syrnik.com?subject=' + info.description)

function openDrawer() {
  ($ as any).waDrawer({
    html: [].map.call(about_tpl.value!.children, (e: Element) => e.outerHTML).join('\n'),
    direction: 'right',
  })
}
</script>

<template>
  <div class="flexbox w-syrnik-shipping__plugin-header">
    <div><h2>Плагин «{{ info.name }}» <span class="hint">{{ info.version }}</span></h2></div>
    <div>
      <button class="outlined green button" type="button" @click.prevent="openDrawer">Помощь</button>
    </div>
    <template ref="about_tpl">
      <div id="" class="drawer">
        <div class="drawer-background"></div>
        <div class="drawer-body">
          <a class="drawer-close js-close-drawer" href="#"><i class="fas fa-times"></i></a>
          <div class="drawer-block w-syrnik-shipping-ui" style="line-height: 1.6">
            <header class="drawer-header"><h2>{{ info.description }}</h2></header>
            <div class="drawer-content">
              <div class="box">
                <p class="align-center">©Syrnik.com, {{ yearRange }}</p>
                <p class="align-center"><a href="//www.syrnik.com/support/request/" target="_blank">Отправьте запрос</a>
                  или <a :href="mailTo">напишите письмо</a> в техническую поддержку. Мы постоянно дорабатываем наш
                  плагин и будем рады вашим пожеланиям. Если непонятно назначение или работа какой-то настройки —
                  спрашивайте, поможем и объясним. <b>Мы не настраиваем плагин вместо вас!</b> По крайней мере
                  бесплатно.</p>
              </div>
              <article>
                <h2><span class="smaller">Лог ошибок при работе плагина</span></h2>
                <p>Плагин умеет записывать ошибки в лог-файл, если в системе включен режим отладки.
                  Чтобы точнее определить источник ошибки и получить дополнительную информацию о причинах ее
                  возникновения выполните такие действия:</p>
                <ul>
                  <li>Установите в Инсталлере бесплатное приложение Логи (если у вас оно еще не установлено)</li>
                  <li>Включите в системе режим отладки. Настройки(наверху)→отметить пункт «Режим отладки» и сохранить
                    настройку</li>
                  <li>Воспроизведите ошибку (выполните расчет доставки или другие действие, при котором возникает
                    ошибка)</li>
                  <li>Выключите режим отладки</li>
                  <li>В приложении Логи найдите лог-файл плагина <b>dostavista.log</b> и посмотрите, нет-ли в нем
                    сообщений о причинах неполадок. Или пришлите этот файл нам.</li>
                </ul>
              </article>
            </div>
            <footer class="drawer-footer">
              <button class="js-close-drawer button light-gray">Закрыть</button>
            </footer>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<style lang="stylus">
.w-syrnik-shipping__plugin-header
  div:has(>h2)
    flex-basis 100%
</style>
