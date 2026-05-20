<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue'

const props = withDefaults(defineProps<{
  lockBodyScroll?: boolean
  isAnimated?: boolean
}>(), {
  lockBodyScroll: true,
  isAnimated: true,
})

const emit = defineEmits<{ 'close': [] }>()

const locked_class = 'is-locked'
const dialog_background = ref<HTMLElement | null>(null)
const dialog_body = ref<HTMLElement | null>(null)
const content_class = ref({ 'is-long-content': false })

function escapeWatcher(event: KeyboardEvent) {
  if (event.keyCode === 27) emit('close')
}

function setPosition() {
  const el = dialog_body.value!
  const dialog_width = el.offsetWidth
  const dialog_height = el.offsetHeight
  el.style.height = ''
  const pad = 20
  const position = {
    left: Math.floor((window.innerWidth - dialog_width) / 2),
    top: Math.floor((window.innerHeight - dialog_height) / 2),
  }

  el.style['maxHeight'] = `calc(100% - ${pad * 2}px)`

  if (position.left > 0) {
    if (position.left + dialog_width > window.innerWidth) {
      position.left = window.innerWidth - dialog_width - pad
    }
  }

  if (position.top > 0) {
    if (position.top + dialog_height > window.innerHeight) {
      position.top = window.innerHeight - dialog_height - pad
    }
  } else {
    position.top = pad
    el.style.height = `${window.innerHeight - pad * 2}px`
  }

  el.style.left = `${position.left}px`
  el.style.top = `${position.top}px`
}

onMounted(() => {
  if (props.lockBodyScroll) {
    jQuery('body').addClass(locked_class)
    document.body.style.overflow = 'hidden'
  }
  document.addEventListener('keyup', escapeWatcher)
  window.addEventListener('resize', setPosition)
  dialog_background.value?.addEventListener('click', event => {
    event.stopPropagation()
    event.preventDefault()
  })
  setPosition()
})

onUnmounted(() => {
  jQuery('body').removeClass(locked_class)
  if (props.lockBodyScroll) document.body.style.overflow = ''
  document.removeEventListener('keyup', escapeWatcher)
  window.removeEventListener('resize', setPosition)
})
</script>

<template>
  <div class="dialog w-syrnik-dialog" style="display: block">
    <div ref="dialog_background" class="dialog-background"></div>
    <div ref="dialog_body" class="dialog-body">
      <a class="dialog-close" href="#" @click.prevent="$emit('close')"><i class="fas fa-times"></i></a>
      <header class="dialog-header">
        <slot name="header"></slot>
      </header>
      <div :class="content_class" class="dialog-content">
        <slot />
      </div>
      <footer class="dialog-footer">
        <slot name="footer" />
      </footer>
    </div>
  </div>
</template>

<style lang="stylus">
.w-syrnik-dialog

  > .dialog-body
    display flex
    flex-direction column
    position fixed
    z-index 1053
    background #fff
    overflow auto
    max-width min(920px, 90vw)
    border 1px solid rgba(0, 0, 0, 0.25)
    border-radius 4px
    box-shadow 0 5px 20px rgba(0, 0, 0, 0.4)

    > .dialog-header
      padding 12px 16px
      border-bottom 1px solid #e5e5e5
      flex-shrink 0

      h3
        margin 0

    > .dialog-content
      flex 1
      padding 15px
      overflow auto

    > .dialog-footer
      padding 10px 16px
      border-top 1px solid #e5e5e5
      flex-shrink 0
      display flex
      gap 8px
      align-items center
</style>
