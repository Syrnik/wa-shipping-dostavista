<template>
    <div>
        <div class="about block">
            <h2>{{ info.name }} для Webasyst Framework</h2>
            <p>Версия {{ info.version }}<br>
                &copy;Syrnik.com {{ yearRange }}<br>
                <a href="//www.syrnik.com/support/request/" target="_blank">Отправить запрос</a> или <a
                        href="mailto:support@syrnik.com?subject=Плагин расчета доставки Dostavista">написать письмо</a> в
                техподдержку. Мы постоянно дорабатываем наш плагин и будем рады вашим пожеланиям.</p>
            <p><i class="icon16 exclamation"></i>Плагин неофициальный! Не нужно беспокоить техподдержку Dostavista
                вопросами о работе или настройках плагина.</p>
        </div>
        <div class="block padded faq">
            <h3>Частые вопросы</h3>
            <div class="block top-padded">
                <h4>Лог ошибок при работе плагина</h4>
                <p>Плагин умеет записывать ошибки в лог-файл, если в системе включен режим отладки. Чтобы
                    точнее определить источник ошибки и получить дополнительную информацию о причинах ее
                    возникновения выполните такие действия:
                </p>
                <ol style="line-height: 1.5">
                    <li>Установите в Инсталлере
                        <a target="_blank" href="https://www.webasyst.ru/store/app/logs/">бесплатное приложение
                            Логи</a> (если у вас оно еще не установлено)</li>
                    <li>Включите в системе режим отладки. Настройки(наверху)&rarr;отметить пункт &laquo;Режим отладки&raquo; и сохранить
                        настройку</li>
                    <li>Воспроизведите ошибку (выполните расчет доставки или другие действие, при котором
                        возникает ошибка)</li>
                    <li>Выключите режим отладки</li>
                    <li>В приложении Логи найдите лог-файл плагина <b>dostavista.log</b> и посмотрите, нет-ли в нем
                        сообщений о причинах неполадок. Или пришлите этот файл нам.</li>
                </ol>
            </div>
            <about-surcharge carrier="Dostavista"></about-surcharge>
            <about-city-exclusion></about-city-exclusion>
        </div>
    </div>

</template>

<script>
    import AboutSurcharge from './AboutSurcharge.vue'
    import AboutCityExclusion from './AboutCityExclusion.vue'

    export default {
        props: {
            info: {type: Object, required: true}
        },
        components: { AboutSurcharge, AboutCityExclusion },
        computed: {
            yearRange() {
                const y = (new Date()).getFullYear();
                return y == 2019 ? '2019' : '2019—'+y;
            }
        }
    }
</script>

<style lang="stylus" scoped>
    .about
        text-align: center;
        padding: 1em;
        background-color: azure;
        border: 1px solid powderblue;
        width: 50%;
        max-width: 700px;
        margin: 0 auto;

    .faq
        max-width: 1024px; margin: 0 auto

    i.icon16.exclamation
        margin-top 0
        vertical-align middle

    .s-settings-form p
        width initial

</style>
