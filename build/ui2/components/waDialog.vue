<script setup>
import {onMounted, onUnmounted, ref} from "vue";

const props = defineProps({
        lockBodyScroll: {type: Boolean, default: true},
        isAnimated: {type: Boolean, default: true}
    }),
    emit = defineEmits(['close']),
    locked_class = 'is-locked',
    dialog_background = ref(null),
    dialog_body = ref(null),
    dialog_header = ref(null),
    dialog_content = ref(null),
    dialog_footer = ref(null),
    content_class = ref({'is-long-content': false});

function escapeWatcher(event) {
    const escape_code = 27;
    if (event.keyCode === escape_code) emit('close');
}

function onResize() {
    setPosition();
}

function setPosition() {
    let dialog_width = dialog_body.value.offsetWidth,
        dialog_height = dialog_body.value.offsetHeight;
    dialog_body.value.style.height = dialog_content.value.style.height = null;
    const pad = 20,
        position = {
            left: Math.floor((window.innerWidth - dialog_width) / 2),
            top: Math.floor((window.innerHeight - dialog_height) / 2)
        };

    if (position.left > 0) {
        if (position.left + dialog_width > window.innerWidth) {
            position.left = window.innerWidth - dialog_width - pad;
        }
    }

    if (position.top > 0) {
        if (position.top + dialog_height > window.innerHeight) {
            position.top = window.innerHeight - dialog_height - pad;
        }
        content_class.value["is-long-content"] = false;
        dialog_content.value.style.height = null;
    } else {
        position.top = pad;
        content_class.value["is-long-content"] = true;
        dialog_body.value.style.height = `${window.innerHeight - pad * 2}px`;
        dialog_content.value.style.height = `${dialog_body.value.offsetHeight - (dialog_header.value?.offsetHeight ?? 0) - (dialog_footer.value?.offsetHeight ?? 0)}px`
    }
    dialog_body.value.style.left = `${position.left}px`;
    dialog_body.value.style.top = `${position.top}px`;
}

onMounted(() => {
    if (props.lockBodyScroll) jQuery('body').addClass(locked_class);
    document.addEventListener('keyup', escapeWatcher);
    window.addEventListener('resize', onResize);
    dialog_background.value?.addEventListener('click', event => {
        event.stopPropagation();
        event.preventDefault();
    });
    setPosition();
});

onUnmounted(() => {
    jQuery('body').removeClass(locked_class);
    document.removeEventListener('keyup', escapeWatcher);
    window.removeEventListener('resize', onResize);
});
</script>

<template>
    <div class="dialog" style="display: block">
        <div ref="dialog_background" class="dialog-background"></div>
        <div ref="dialog_body" class="dialog-body">
            <a class="dialog-close" href="#" @click.prevent="$emit('close')"><i class="fas fa-times"></i></a>
            <header ref="dialog_header" class="dialog-header">
                <slot name="header"></slot>
            </header>
            <div ref="dialog_content" :class="content_class" class="dialog-content">
                <slot/>
            </div>
            <footer ref="dialog_footer" class="dialog-footer">
                <slot name="footer"/>
            </footer>
        </div>
    </div>
</template>

<style lang="stylus" scoped>

</style>
